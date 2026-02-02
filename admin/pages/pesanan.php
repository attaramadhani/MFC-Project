<?php
// admin/pages/pesanan.php

// ========================
// 1) HANDLE UPDATE STATUS
// ========================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action     = $_POST['action'] ?? '';
    $idPesanan  = (int)($_POST['id_pesanan'] ?? 0);
    $newStatus  = $_POST['order_status'] ?? '';

    if ($action === 'update_status' && $idPesanan > 0 && $newStatus !== '') {
        // Ambil pesanan
        $stmt = $pdo->prepare("SELECT * FROM pesanan WHERE id_pesanan = ?");
        $stmt->execute([$idPesanan]);
        $order = $stmt->fetch();

        if ($order) {
            $currentStatus  = $order['order_status'];
            $paymentStatus  = $order['payment_status'];
            $paymentMethod = $order['payment_method'] ?? 'midtrans';

            // Mapping status yang boleh
            $allowedTransitions = [
              'created'              => ['processing','canceled'],
              'waiting_confirmation' => ['processing','canceled'],
              'processing'           => ['ready','canceled'],
              'ready'                => ['completed','canceled'],
              'completed'            => [],
              'canceled'             => [],
            ];



            $can = isset($allowedTransitions[$currentStatus]) &&
                   in_array($newStatus, $allowedTransitions[$currentStatus], true);

            $needPaid = in_array($newStatus, ['processing', 'ready', 'completed'], true);
            // Midtrans wajib paid, COD tidak wajib (karena bayar di akhir)
            if ($needPaid && $paymentMethod !== 'cash' && $paymentStatus !== 'paid') {
                $can = false;
                $errorMsg = 'Pesanan belum dibayar, tidak bisa diproses.';
            }


            if ($can) {
                try {
                    $pdo->beginTransaction();

                    // Tentukan kolom timestamp
                    $timestampColumn = null;
                    switch ($newStatus) {
                        case 'processing': $timestampColumn = 'processed_at'; break;
                        case 'ready':      $timestampColumn = 'ready_at';     break;
                        case 'completed':  $timestampColumn = 'completed_at'; break;
                        case 'canceled':   $timestampColumn = 'canceled_at';  break;
                    }

                    // Update pesanan
                    if ($timestampColumn) {
                        $sqlUpdate = "
                            UPDATE pesanan
                            SET order_status = ?, {$timestampColumn} = NOW()
                            WHERE id_pesanan = ?
                        ";
                        $stmtUp = $pdo->prepare($sqlUpdate);
                        $stmtUp->execute([$newStatus, $idPesanan]);
                    } else {
                        $stmtUp = $pdo->prepare("
                            UPDATE pesanan SET order_status = ? WHERE id_pesanan = ?
                        ");
                        $stmtUp->execute([$newStatus, $idPesanan]);
                    }

                    // COD: kalau pesanan selesai, uang dianggap diterima
                    if ($newStatus === 'completed' && $paymentMethod === 'cash' && $paymentStatus !== 'paid') {
                        $pdo->prepare("
                            UPDATE pesanan
                            SET payment_status = 'paid', paid_at = NOW()
                            WHERE id_pesanan = ?
                        ")->execute([$idPesanan]);

                        // optional: simpan riwayat payment juga biar rapi
                        $pdo->prepare("
                            INSERT INTO riwayat_status_pesanan
                                (id_pesanan, tipe, status_lama, status_baru, diubah_oleh, keterangan)
                            VALUES
                                (?, 'payment', ?, 'paid', ?, ?)
                        ")->execute([
                            $idPesanan,
                            $paymentStatus,
                            $_SESSION['user_id'],
                            'COD diterima saat pesanan selesai.'
                        ]);
                    }


                    // Insert ke riwayat_status_pesanan
                    $stmtHistory = $pdo->prepare("
                        INSERT INTO riwayat_status_pesanan
                            (id_pesanan, tipe, status_lama, status_baru, diubah_oleh, keterangan)
                        VALUES
                            (?, 'order', ?, ?, ?, ?)
                    ");
                    $keterangan = 'Perubahan status oleh admin.';
                    $stmtHistory->execute([
                        $idPesanan,
                        $currentStatus,
                        $newStatus,
                        $_SESSION['user_id'],
                        $keterangan
                    ]);

                    $pdo->commit();
                    $successMsg = 'Status pesanan berhasil diperbarui.';
                } catch (Exception $e) {
                    if ($pdo->inTransaction()) {
                        $pdo->rollBack();
                    }
                    $errorMsg = 'Gagal mengupdate status: ' . $e->getMessage();
                }
            } else {
                if (empty($errorMsg)) {
                    $errorMsg = 'Transisi status tidak diizinkan.';
                }
            }
        } else {
            $errorMsg = 'Pesanan tidak ditemukan.';
        }
    }
}

// ========================
// 2) FILTER LIST PESANAN
// ========================
$statusFilter  = $_GET['status'] ?? '';
$payFilter     = $_GET['pay_status'] ?? '';
$dateFrom      = $_GET['from'] ?? '';
$dateTo        = $_GET['to'] ?? '';

$sql = "
    SELECT p.*, u.nama_user
    FROM pesanan p
    JOIN users u ON u.id_user = p.id_user
    WHERE 1=1
";
$params = [];

if ($statusFilter !== '') {
    $sql .= " AND p.order_status = ? ";
    $params[] = $statusFilter;
}
if ($payFilter !== '') {
    $sql .= " AND p.payment_status = ? ";
    $params[] = $payFilter;
}
if ($dateFrom !== '') {
    $sql .= " AND DATE(p.created_at) >= ? ";
    $params[] = $dateFrom;
}
if ($dateTo !== '') {
    $sql .= " AND DATE(p.created_at) <= ? ";
    $params[] = $dateTo;
}

$sql .= " ORDER BY p.created_at DESC LIMIT 100";

$stmtList = $pdo->prepare($sql);
$stmtList->execute($params);
$orders = $stmtList->fetchAll(PDO::FETCH_ASSOC);

function badgePayment($status) {
    switch ($status) {
        case 'paid':     return '<span class="status-pill badge-soft-green">Sudah dibayar</span>';
        case 'pending':  return '<span class="status-pill badge-soft-amber">Menunggu</span>';
        case 'failed':   return '<span class="status-pill badge-soft-red">Gagal</span>';
        case 'expired':  return '<span class="status-pill badge-soft-gray">Kadaluarsa</span>';
        default:         return '<span class="status-pill badge-soft-gray">Belum dibayar</span>';
    }
}
function badgeOrder($status) {
  switch ($status) {
    case 'waiting_confirmation': return '<span class="status-pill badge-soft-amber">Menunggu konfirmasi</span>';
    case 'processing': return '<span class="status-pill badge-soft-blue">Diproses</span>';
    case 'ready':      return '<span class="status-pill badge-soft-amber">Diantar</span>';
    case 'completed':  return '<span class="status-pill badge-soft-green">Selesai</span>';
    case 'canceled':   return '<span class="status-pill badge-soft-red">Dibatalkan</span>';
    default:           return '<span class="status-pill badge-soft-gray">Dibuat</span>';
  }
}
?>
<div class="mb-3">
  <h2 class="h5 mb-1">Kelola Pesanan</h2>
  <div class="text-muted small">
    Lihat semua pesanan, status pembayaran, dan ubah status proses di sini.
  </div>
</div>

<?php if (!empty($errorMsg)): ?>
  <div class="alert alert-danger py-2"><?= htmlspecialchars($errorMsg) ?></div>
<?php endif; ?>
<?php if (!empty($successMsg)): ?>
  <div class="alert alert-success py-2"><?= htmlspecialchars($successMsg) ?></div>
<?php endif; ?>

<!-- FILTER -->
<form method="get" class="row g-2 align-items-end mb-3">
  <input type="hidden" name="page" value="pesanan">
  <div class="col-md-3">
    <label class="form-label small mb-1">Status Pesanan</label>
    <select name="status" class="form-select form-select-sm">
      <option value="">Semua</option>
      <?php
      $optStatus = [
        'created'              => 'Dibuat',
        'waiting_confirmation' => 'Menunggu konfirmasi',
        'processing'           => 'Diproses',
        'ready'                => 'Siap',
        'completed'            => 'Selesai',
        'canceled'             => 'Dibatalkan'
      ];
      foreach ($optStatus as $val => $label):
      ?>
        <option value="<?= $val ?>" <?= $statusFilter===$val?'selected':'' ?>><?= $label ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="col-md-3">
    <label class="form-label small mb-1">Status Pembayaran</label>
    <select name="pay_status" class="form-select form-select-sm">
      <option value="">Semua</option>
      <?php
      $optPay = ['unpaid'=>'Belum dibayar','pending'=>'Pending','paid'=>'Sudah dibayar','failed'=>'Gagal','expired'=>'Kadaluarsa'];
      foreach ($optPay as $val => $label):
      ?>
        <option value="<?= $val ?>" <?= $payFilter===$val?'selected':'' ?>><?= $label ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="col-md-2">
    <label class="form-label small mb-1">Dari Tanggal</label>
    <input type="date" name="from" class="form-control form-control-sm" value="<?= htmlspecialchars($dateFrom) ?>">
  </div>
  <div class="col-md-2">
    <label class="form-label small mb-1">Sampai</label>
    <input type="date" name="to" class="form-control form-control-sm" value="<?= htmlspecialchars($dateTo) ?>">
  </div>
  <div class="col-md-2">
    <button class="btn btn-main text-white">Tampilkan</button>
  </div>
</form>

<div class="table-responsive">
  <table class="table table-sm align-middle">
    <thead class="table-light">
      <tr>
        <th>Kode</th>
        <th>Pelanggan</th>
        <th>Tgl & Jam</th>
        <th>Total</th>
        <th>Pembayaran</th>
        <th>Status Pesanan</th>
        <th class="text-end">Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!$orders): ?>
        <tr>
          <td colspan="7" class="text-center text-muted py-3">Belum ada pesanan.</td>
        </tr>
      <?php else: ?>
        <?php foreach ($orders as $o): ?>
          <tr>
            <td class="fw-semibold"><?= htmlspecialchars($o['kode_pesanan']) ?></td>
            <td><?= htmlspecialchars($o['nama_user']) ?></td>
            <td>
              <div><?= date('d M Y, H:i', strtotime($o['created_at'])) ?></div>
              <!-- <div class="small text-muted">
                <?= (int)$o['total_harga'] ?? '' ?> item
              </div> -->
            </td>
            <td>Rp <?= number_format($o['total_harga'], 0, ',', '.') ?></td>
            <td><?= badgePayment($o['payment_status']) ?></td>
            <td><?= badgeOrder($o['order_status']) ?></td>
            <td class="text-end">
              <button
                type="button"
                class="btn btn-sm btn-outline-secondary me-1 btn-order-detail"
                data-id="<?= $o['id_pesanan'] ?>">
                Detail
              </button>

              <!-- Form ubah status (next step) -->
              <form method="post" class="d-inline-block">
                <input type="hidden" name="action" value="update_status">
                <input type="hidden" name="id_pesanan" value="<?= $o['id_pesanan'] ?>">
                <select name="order_status" class="form-select form-select-sm d-inline w-auto"
                        onchange="this.form.submit()">
                  <option value="">Status…</option>
                  <?php
                  $current = $o['order_status'];
                  $allowed = [
                    'created'              => ['processing','canceled'],
                    'waiting_confirmation' => ['processing','canceled'], // COD level 2
                    'processing'           => ['ready','canceled'],
                    'ready'                => ['completed','canceled'],
                    'completed'            => [],
                    'canceled'             => [],
                  ];
                  foreach ($allowed[$current] ?? [] as $opt) {
                    $label = [
                    'processing' => 'Diproses',
                    'ready'      => 'Diantar',
                    'completed'  => 'Selesai',
                    'canceled'   => 'Dibatalkan',
                  ][$opt] ?? $opt;
                    echo '<option value="'.$opt.'">'.$label.'</option>';
                  }
                  ?>
                </select>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<!-- MODAL DETAIL PESANAN -->
<div class="modal fade" id="orderDetailModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content rounded-4">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title">Detail Pesanan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="orderDetailBody">
        <div class="text-center text-muted small py-4">
          Memuat detail...
        </div>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const modalEl   = document.getElementById('orderDetailModal');
  const modalBody = document.getElementById('orderDetailBody');
  const bsModal   = new bootstrap.Modal(modalEl);

  document.querySelectorAll('.btn-order-detail').forEach(btn => {
    btn.addEventListener('click', function () {
      const id = this.dataset.id;
      modalBody.innerHTML = '<div class="text-center text-muted small py-4">Memuat detail...</div>';
      fetch('pages/pesanan_detail.php?id=' + encodeURIComponent(id))
        .then(res => res.text())
        .then(html => {
          modalBody.innerHTML = html;
          bsModal.show();
        })
        .catch(err => {
          console.error(err);
          modalBody.innerHTML = '<div class="text-danger small">Gagal memuat detail.</div>';
          bsModal.show();
        });
    });
  });
});
</script>