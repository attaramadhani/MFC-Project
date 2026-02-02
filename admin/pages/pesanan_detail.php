<?php
// admin/pages/pesanan_detail.php
require_once __DIR__ . '/../auth.php';   // penting: supaya $_SESSION & $pdo ada

$id_user_admin = $_SESSION['user_id'] ?? null;
$idPesanan     = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($idPesanan <= 0) {
    echo '<div class="text-center text-muted small py-3">Pesanan tidak ditemukan.</div>';
    exit;
}

// Ambil pesanan + user
$stmt = $pdo->prepare("
    SELECT p.*, u.nama_user
    FROM pesanan p
    JOIN users u ON u.id_user = p.id_user
    WHERE p.id_pesanan = ?
    LIMIT 1
");
$stmt->execute([$idPesanan]);
$order = $stmt->fetch();

if (!$order) {
    echo '<div class="text-center text-muted small py-3">Pesanan tidak ditemukan.</div>';
    exit;
}

// Item pesanan
$stmtItems = $pdo->prepare("
    SELECT dp.*, m.nama
    FROM detail_pesanan dp
    JOIN menu m ON m.id_menu = dp.id_menu
    WHERE dp.id_pesanan = ?
    ORDER BY m.nama
");
$stmtItems->execute([$idPesanan]);
$items = $stmtItems->fetchAll();

// Pembayaran (terakhir)
$stmtPay = $pdo->prepare("
    SELECT *
    FROM pembayaran
    WHERE id_pesanan = ?
    ORDER BY id_pembayaran DESC
    LIMIT 1
");
$stmtPay->execute([$idPesanan]);
$pay = $stmtPay->fetch();

// Riwayat status
$stmtHist = $pdo->prepare("
    SELECT r.*, u.nama_user
    FROM riwayat_status_pesanan r
    LEFT JOIN users u ON u.id_user = r.diubah_oleh
    WHERE r.id_pesanan = ?
    ORDER BY r.dibuat_pada ASC
");
$stmtHist->execute([$idPesanan]);
$logs = $stmtHist->fetchAll();

function humanPaymentStatusAdmin($s) {
    switch ($s) {
        case 'paid':     return 'Sudah dibayar';
        case 'pending':  return 'Menunggu pembayaran';
        case 'failed':   return 'Gagal';
        case 'expired':  return 'Kadaluarsa';
        default:         return 'Belum dibayar';
    }
}
function humanOrderStatusAdmin($s) {
    switch ($s) {
        case 'waiting_confirmation': return 'Menunggu konfirmasi';
        case 'processing': return 'Sedang diproses';
        case 'ready':      return '';
        case 'completed':  return 'Selesai';
        case 'canceled':   return 'Dibatalkan';
        default:           return 'Dibuat';
    }
}
?>

<div class="order-detail-wrapper">

  <!-- HEADER -->
  <div class="pb-3 mb-3 border-bottom d-flex justify-content-between align-items-start">
    <div>
      <div class="small text-muted">Kode Pesanan</div>
      <div class="fw-semibold fs-6"><?= htmlspecialchars($order['kode_pesanan']) ?></div>
      <div class="small text-muted"><?= date('d M Y, H:i', strtotime($order['created_at'])) ?></div>
      <div class="small text-muted">Pelanggan: <?= htmlspecialchars($order['nama_user']) ?></div>
    </div>

    <div class="text-end small">
      <div><?= humanPaymentStatusAdmin($order['payment_status']) ?></div>
      <div><?= humanOrderStatusAdmin($order['order_status']) ?></div>

      <?php if (!empty($order['paid_at'])): ?>
        <div class="text-muted mt-1">
          Dibayar: <?= date('d M Y, H:i', strtotime($order['paid_at'])) ?>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <div class="card shadow-sm border-0 rounded-4 mb-3">
  <div class="card-body">
    <h6 class="fw-bold mb-2">Info Pengantaran</h6>

    <div class="row g-2">
      <div class="col-md-6">
        <div class="small text-muted mb-1">Metode Pembayaran</div>
        <div>
          <?php if(($order['payment_method'] ?? '') === 'cash'): ?>
            <span class="badge bg-warning text-dark">COD (Bayar di tempat)</span>
          <?php else: ?>
            <span class="badge bg-secondary">Online</span>
          <?php endif; ?>
        </div>
      </div>

      <div class="col-md-6">
        <div class="small text-muted mb-1">Ongkir</div>
        <div>Rp <?= number_format((int)($order['ongkir'] ?? 0), 0, ',', '.') ?></div>
      </div>
    </div>

    <hr>

    <div class="small text-muted mb-1">Wilayah</div>
    <div class="mb-2"><?= htmlspecialchars($order['wilayah_pengiriman'] ?? '-') ?></div>

    <div class="small text-muted mb-1">Alamat Lengkap</div>
    <div><?= nl2br(htmlspecialchars($order['alamat_pengiriman'] ?? '-')) ?></div>
  </div>
</div>



  <!-- ITEM PESANAN -->
  <div class="mb-3 pb-2 border-bottom">
    <div class="small text-muted mb-2 fw-semibold">Item pesanan</div>

    <?php if (!$items): ?>
      <div class="small text-muted">Tidak ada item untuk pesanan ini.</div>
    <?php else: ?>
      <?php foreach ($items as $row): 
        $jumlah = (int)$row['jumlah'];
        $harga  = (int)$row['harga'];
        $sub    = $jumlah * $harga;
      ?>
        <div class="d-flex justify-content-between py-2">
          <div>
            <div class="fw-semibold"><?= htmlspecialchars($row['nama']) ?></div>
            <div class="small text-muted">
              x <?= $jumlah ?> • Rp <?= number_format($harga,0,',','.') ?>
              <?php if (!empty($row['catatan_item'])): ?>
                <br><span class="fst-italic">Catatan: <?= htmlspecialchars($row['catatan_item']) ?></span>
              <?php endif; ?>
            </div>
          </div>

          <div class="fw-semibold small">
            Rp <?= number_format($sub,0,',','.') ?>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>


  <!-- TOTAL + RIWAYAT STATUS (2 KOLOM) -->
  <div class="d-flex gap-4 flex-column flex-md-row">

    <!-- TOTAL -->
    <div class="flex-fill">
      <div class="small text-muted mb-1 fw-semibold">Total</div>
      <div class="fw-semibold fs-6 mb-2">
        Rp <?= number_format((int)$order['total_harga'],0,',','.') ?>
      </div>

      <?php if ($pay): ?>
        <div class="small text-muted">
          Provider: <?= htmlspecialchars($pay['provider']) ?><br>
          Metode: <?= htmlspecialchars($pay['metode']) ?><br>
          Status: <?= humanPaymentStatusAdmin($pay['status']) ?>
        </div>
      <?php else: ?>
        <div class="small text-muted">Belum ada data pembayaran.</div>
      <?php endif; ?>
    </div>

    <!-- RIWAYAT STATUS -->
    <div class="flex-fill">
      <div class="small text-muted fw-semibold mb-1">Riwayat Status</div>

      <?php if (!$logs): ?>
        <div class="small text-muted">Belum ada riwayat.</div>
      <?php else: ?>
        <ul class="list-unstyled small mb-0">
          <?php foreach ($logs as $log): ?>
            <li class="mb-1">
              <?php if (!empty($log['dibuat_pada'])): ?>
                <span class="text-muted"><?= date('d M Y H:i', strtotime($log['dibuat_pada'])) ?> · </span>
              <?php endif; ?>

              <?= htmlspecialchars($log['tipe']) ?>:
              <span class="fw-semibold">
                <?= htmlspecialchars($log['status_lama'] ?? '-') ?>
                →
                <?= htmlspecialchars($log['status_baru'] ?? '-') ?>
                </span>


              <?php if (!empty($log['nama_user'])): ?>
                <span class="text-muted">· oleh <?= htmlspecialchars($log['nama_user']) ?></span>
              <?php endif; ?>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </div>

  </div>

</div>