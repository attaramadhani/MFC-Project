<?php
// pelanggan/pesanan_detail.php
require '../config.php';
require_login();

$id_user    = $_SESSION['user_id'];
$idPesanan  = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($idPesanan <= 0) {
    echo '<div class="text-center text-muted py-4 small">Pesanan tidak ditemukan.</div>';
    exit;
}

// Ambil pesanan + cek kepemilikan user
$stmt = $pdo->prepare("
    SELECT p.*
    FROM pesanan p
    WHERE p.id_pesanan = ? AND p.id_user = ?
    LIMIT 1
");
$stmt->execute([$idPesanan, $id_user]);
$order = $stmt->fetch();

if (!$order) {
    echo '<div class="text-center text-muted py-4 small">Pesanan tidak ditemukan.</div>';
    exit;
}

// Ambil item-item
$stmtItems = $pdo->prepare("
    SELECT 
        dp.jumlah,
        dp.harga,
        dp.catatan_item,
        m.nama
    FROM detail_pesanan dp
    JOIN menu m ON m.id_menu = dp.id_menu
    WHERE dp.id_pesanan = ?
    ORDER BY m.nama
");
$stmtItems->execute([$idPesanan]);
$items = $stmtItems->fetchAll();

// Ambil info pembayaran (jika ada)
$stmtPay = $pdo->prepare("
    SELECT *
    FROM pembayaran
    WHERE id_pesanan = ?
    ORDER BY id_pembayaran DESC
    LIMIT 1
");
$stmtPay->execute([$idPesanan]);
$pembayaran = $stmtPay->fetch();

$paymentStatus  = $order['payment_status'];   // unpaid, pending, paid, ...
$orderStatus    = $order['order_status'];     // created, processing, ready, ...
$paymentMethod = $order['payment_method'] ?? 'midtrans';

$isCompletedAndPaid = ($orderStatus === 'completed' && $paymentStatus === 'paid');


function humanPaymentStatus($status) {
    switch ($status) {
        case 'paid':      return 'Sudah dibayar';
        case 'pending':   return 'Menunggu pembayaran';
        case 'failed':    return 'Pembayaran gagal';
        case 'expired':   return 'Kadaluarsa';
        case 'refunded':  return 'Dikembalikan';
        default:          return 'Belum dibayar';
    }
}

function humanOrderStatus($status) {
    switch ($status) {
        case 'processing': return 'Sedang diproses';
        case 'ready':      return 'Diantar';
        case 'completed':  return 'Selesai';
        case 'canceled':   return 'Dibatalkan';
        default:           return 'Dibuat';
    }
}

$totalHarga = (float)$order['total_harga'];
$kode       = $order['kode_pesanan'];
$tanggal    = date('d M Y, H:i', strtotime($order['created_at']));
?>
<div class="order-detail-wrapper">
  <!-- Bagian atas: info umum -->
  <div class="order-detail-header mb-3">
    <div>
      <div class="small text-muted mb-1">Kode Pesanan</div>
      <div class="fw-semibold">
        <?= htmlspecialchars($kode) ?>
      </div>
      <div class="small text-muted">
        <?= $tanggal ?>
      </div>
    </div>
    <div class="text-end">
      <?php if ($orderStatus === 'canceled'): ?>
        <div class="small">
          <span class="status-pill badge-soft-red">
            <?= humanOrderStatus($orderStatus) ?>
          </span>
        </div>
      <?php else: ?>
        <div class="small mb-1">
          <span class="status-pill badge-soft-gray">
            <?= humanPaymentStatus($paymentStatus) ?>
          </span>
        </div>
        <div class="small">
          <span class="status-pill badge-soft-blue">
            <?= humanOrderStatus($orderStatus) ?>
          </span>
        </div>
      <?php endif; ?>
    </div>
  </div>

    <div class="card border-0 shadow-sm rounded-4 mb-3">
  <div class="card-body">
    <div class="fw-bold mb-2">Info Pengantaran</div>

    <div class="small text-muted mb-1">Metode Pembayaran</div>
    <div class="mb-2">
      <?= ($paymentMethod === 'cash') ? 'COD (Bayar di tempat)' : 'Online' ?>
    </div>

    <div class="small text-muted mb-1">Alamat Pengantaran</div>
    <div class="mb-2">
      <?= nl2br(htmlspecialchars($order['alamat_pengiriman'] ?? '-')) ?>
    </div>

    <div class="row g-2">
      <div class="col-md-6">
        <div class="small text-muted mb-1">Wilayah</div>
        <div><?= htmlspecialchars($order['wilayah_pengiriman'] ?? '-') ?></div>
      </div>
      <div class="col-md-6">
        <div class="small text-muted mb-1">Ongkir</div>
        <div>Rp <?= number_format((int)($order['ongkir'] ?? 0), 0, ',', '.') ?></div>
      </div>
    </div>
  </div>
</div>

  <!-- Daftar item -->
  <div class="order-detail-items mb-3">
    <?php if (!$items): ?>
      <div class="text-muted small">
        Tidak ada item pada pesanan ini.
      </div>
    <?php else: ?>
      <?php foreach ($items as $row): 
        $nama     = $row['nama'];
        $jumlah   = (int)$row['jumlah'];
        $harga    = (float)$row['harga'];
        $subtotal = $jumlah * $harga;
      ?>
        <div class="order-detail-item">
          <div class="order-detail-item-main">
            <div class="fw-semibold">
              <?= htmlspecialchars($nama) ?>
            </div>
            <div class="small text-muted">
              x <?= $jumlah ?> • Rp <?= number_format($harga, 0, ',', '.') ?>
            </div>
          </div>
          <div class="order-detail-item-subtotal">
            Rp <?= number_format($subtotal, 0, ',', '.') ?>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>

    <!-- Ringkasan total + info bayar -->
  <div class="order-detail-summary d-flex justify-content-between align-items-start mt-3">
    <!-- Kiri: total + info pembayaran -->
    <div class="order-detail-summary-left">
      <div class="small text-muted mb-1">Total</div>
      <div class="order-detail-total mb-1">
        Rp <?= number_format($totalHarga, 0, ',', '.') ?>
      </div>

      <?php if ($pembayaran): ?>
        <div class="small text-muted">
          Metode: <?= ($paymentMethod === 'cash')
              ? 'COD'
              : htmlspecialchars($pembayaran['metode'] ?? $pembayaran['provider'])
          ?><br>
          Status pembayaran: <?= humanPaymentStatus($pembayaran['status']) ?>

        </div>
      <?php else: ?>
        <div class="small text-muted">
          Belum ada data pembayaran tercatat.
        </div>
      <?php endif; ?>
    </div>

    <div class="order-detail-summary-right text-end">
      <?php
        $isCanceled = ($orderStatus === 'canceled');
        $isCash = ($paymentMethod === 'cash');
        $canPay = ($paymentStatus !== 'paid') && (!$isCanceled) && (!$isCash);
        ?>

        <?php if ($canPay): ?>
          <button
            type="button"
            class="btn btn-main text-white rounded-pill ms-3"
            id="btn-pay-existing"
            data-order-id="<?= $idPesanan ?>"
          >
            Lanjutkan Pembayaran
          </button>

        <?php elseif ($isCanceled): ?>
          <div class="small text-danger fw-semibold">
            Pesanan dibatalkan
          </div>

        <?php elseif ($isCash && $paymentStatus !== 'paid'): ?>
          <div class="small text-muted fw-semibold">
            COD — Menunggu konfirmasi admin / bayar saat pesanan diterima.
          </div>

        <?php else: ?>
          <div class="small text-success fw-semibold">
            Pesanan ini sudah dibayar.
          </div>

          <?php if ($isCompletedAndPaid): ?>
            <button
              type="button"
              class="btn btn-link btn-sm p-0 mt-1 order-detail-receipt-link"
              onclick="window.location.href='pesanan_struk.php?id=<?= $idPesanan ?>';"
            >
              <span class="me-1">🧾</span>
              <span>Unduh struk</span>
            </button>
          <?php endif; ?>
        <?php endif; ?>

    </div>

  </div>
</div>
