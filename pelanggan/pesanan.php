<?php
// pelanggan/pesanan.php
require '../config.php';
require_login();

$id_user = $_SESSION['user_id'];

// Ambil semua pesanan user ini + jumlah item
$stmt = $pdo->prepare("
    SELECT 
        p.id_pesanan,
        p.kode_pesanan,
        p.total_harga,
        p.payment_status,
        p.order_status,
        p.created_at,
        COALESCE(SUM(dp.jumlah), 0) AS total_item
    FROM pesanan p
    LEFT JOIN detail_pesanan dp ON dp.id_pesanan = p.id_pesanan
    WHERE p.id_user = ?
    GROUP BY 
        p.id_pesanan,
        p.kode_pesanan,
        p.total_harga,
        p.payment_status,
        p.order_status,
        p.created_at
    ORDER BY p.created_at DESC
");
$stmt->execute([$id_user]);
$orders = $stmt->fetchAll();

// buat bodyClass kalau mau dipakai di header
$page = 'pelanggan';
include '../partials/header.php';
?>

<div class="page-orders">
  <div class="row justify-content-center">
    <div class="col-lg-9">
      <h1 class="page-orders-title">Pesanan Saya</h1>
      <p class="page-orders-subtitle">
        Lihat riwayat pesanan, status pembayaran, dan detail menu yang pernah kamu pesan.
      </p>

  <?php if (!$orders): ?>
    <!-- EMPTY STATE: BELUM PERNAH PESAN -->
    <div class="text-center py-5">
      <div class="mb-3" style="font-size:2.5rem;">🍗</div>
      <h5 class="mb-2">Belum ada pesanan</h5>
      <p class="text-muted small mb-3">
        Kamu belum pernah melakukan pesanan. Yuk mulai pilih menu favoritmu dulu.
      </p>
      <a href="index.php#menu" class="btn btn-main text-white rounded-pill">
        Lihat Menu
      </a>
    </div>
  <?php else: ?>

    <div class="order-list-wrapper">
      <?php foreach ($orders as $order): 
        $idPesanan      = (int)$order['id_pesanan'];
        $kodePesanan    = $order['kode_pesanan'];
        $totalHarga     = (float)$order['total_harga'];
        $paymentStatus  = $order['payment_status'];   // unpaid, pending, paid, failed, expired, refunded
        $orderStatus    = $order['order_status'];     // created, processing, ready, completed, canceled
        $createdAt      = $order['created_at'];
        $totalItem      = (int)$order['total_item'];

        // Format tanggal simpel
        $tanggalLabel = date('d M Y, H:i', strtotime($createdAt));

        // Badge status bayar
        $badgeBayarClass = 'badge-soft-gray';
        $badgeBayarText  = 'Belum dibayar';

        // Badge status bayar
        switch ($paymentStatus) {
            case 'paid':
                $badgeBayarClass = 'badge-soft-green';
                $badgeBayarText  = 'Sudah dibayar';
                break;
            case 'pending':
                $badgeBayarClass = 'badge-soft-amber';
                $badgeBayarText  = 'Menunggu konfirmasi';
                break;
            case 'failed':
            case 'expired':
            case 'refunded':
                $badgeBayarClass = 'badge-soft-red';
                $badgeBayarText  = ucfirst($paymentStatus);
                break;
            default: // unpaid / null
                $badgeBayarClass = 'badge-soft-gray';
                $badgeBayarText  = 'Belum dibayar';
        }


        // Badge status pesanan
        $badgeOrderClass = 'badge-soft-gray';
        $badgeOrderText  = 'Dibuat';

        switch ($orderStatus) {
          case 'processing':
            $badgeOrderClass = 'badge-soft-amber';
            $badgeOrderText  = 'Sedang diproses';
            break;
          case 'ready':
            $badgeOrderClass = 'badge-soft-blue';
            $badgeOrderText  = 'Diantar';
            break;
          case 'completed':
            $badgeOrderClass = 'badge-soft-green';
            $badgeOrderText  = 'Selesai';
            break;
          case 'canceled':
            $badgeOrderClass = 'badge-soft-red';
            $badgeOrderText  = 'Dibatalkan';
            break;
          default:
            $badgeOrderClass = 'badge-soft-gray';
            $badgeOrderText  = 'Dibuat';
        }
      ?>
        <!-- KARTU SATU PESANAN -->
        <div class="order-card" 
            data-id="<?= $idPesanan ?>"
            data-bs-toggle="modal"
            data-bs-target="#orderDetailModal">
        <div class="order-card-left">
            <div class="order-code-row">
            <span class="order-code-label">Kode Pesanan</span>
            <span class="order-code-value">
                <?= htmlspecialchars($kodePesanan) ?>
            </span>
            </div>
            <div class="order-meta">
            <?= $tanggalLabel ?> • <?= $totalItem ?> item
            </div>
        </div>

        <div class="order-card-right">
            <div class="order-total">
            Rp <?= number_format($totalHarga, 0, ',', '.') ?>
            </div>

            <div class="order-badge-row">
            <?php if ($orderStatus === 'canceled' || $orderStatus === 'completed'): ?>
              <span class="status-pill <?= $badgeOrderClass ?>">
                <?= htmlspecialchars($badgeOrderText) ?>
              </span>
            <?php else: ?>
              <span class="status-pill <?= $badgeBayarClass ?>">
                <?= htmlspecialchars($badgeBayarText) ?>
              </span>
              <span class="status-pill <?= $badgeOrderClass ?>">
                <?= htmlspecialchars($badgeOrderText) ?>
              </span>
            <?php endif; ?>
          </div>

        </div>
        </div>
      <?php endforeach; ?>
    </div>

  <?php endif; ?>
</div>

<!-- MODAL DETAIL PESANAN -->
<div class="modal fade" id="orderDetailModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content rounded-4">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title">Detail Pesanan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>
      <div class="modal-body" id="orderDetailBody">
        <div class="text-center text-muted py-4 small">
          Memuat detail pesanan...
        </div>
      </div>
    </div>
  </div>
</div>

<!-- JS: load detail pesanan ke modal via AJAX -->
<script>
document.addEventListener('DOMContentLoaded', function () {
  const modalBody = document.getElementById('orderDetailBody');
  const cards = document.querySelectorAll('.order-card');

  cards.forEach(card => {
    card.addEventListener('click', function () {
      const idPesanan = this.getAttribute('data-id');
      if (!idPesanan || !modalBody) return;

      // tampilan loading
      modalBody.innerHTML = `
        <div class="text-center text-muted py-4 small">
          Memuat detail pesanan...
        </div>
      `;

      fetch('pesanan_detail.php?id=' + encodeURIComponent(idPesanan))
        .then(res => res.text())
        .then(html => {
          modalBody.innerHTML = html;
        })
        .catch(err => {
          console.error(err);
          modalBody.innerHTML = `
            <div class="text-center text-danger py-4 small">
              Terjadi kesalahan saat memuat detail pesanan.
            </div>
          `;
        });
    });
  });
});
</script>

<script src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="<?= MIDTRANS_CLIENT_KEY ?>"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const modalBody = document.getElementById('orderDetailBody');
  const detailModal = document.getElementById('orderDetailModal');

  // klik card -> load detail
  document.querySelectorAll('.order-card').forEach(card => {
    card.addEventListener('click', function () {
      const idPesanan = this.getAttribute('data-id');
      if (!idPesanan || !modalBody) return;

      modalBody.innerHTML = `
        <div class="text-center text-muted py-4 small">
          Memuat detail pesanan...
        </div>
      `;

      fetch('pesanan_detail.php?id=' + encodeURIComponent(idPesanan))
        .then(res => res.text())
        .then(html => { modalBody.innerHTML = html; })
        .catch(err => {
          console.error(err);
          modalBody.innerHTML = `
            <div class="text-center text-danger py-4 small">
              Terjadi kesalahan saat memuat detail pesanan.
            </div>
          `;
        });
    });
  });

  // delegasi tombol "Lanjutkan Pembayaran" di dalam modal
  if (detailModal) {
    detailModal.addEventListener('click', function (e) {
      const btn = e.target.closest('#btn-pay-existing');
      if (!btn) return;

      const idPesanan = btn.dataset.orderId;

      fetch('process_checkout.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id_pesanan: idPesanan })
      })
      .then(res => res.json())
      .then(data => {
        if (!data.success) {
          console.error(data);
          alert(data.message || 'Gagal membuat transaksi.');
          return;
        }

        window.snap.pay(data.token, {
          onSuccess: function () { window.location.href = 'pesanan.php'; },
          onPending: function () { alert('Pembayaran masih pending.'); },
          onError: function () { alert('Terjadi kesalahan saat pembayaran.'); },
          onClose: function () { console.log('Popup pembayaran ditutup.'); }
        });
      })
      .catch(err => {
        console.error(err);
        alert('Terjadi kesalahan koneksi.');
      });
    });
  }
});
</script>

<?php include '../partials/footer.php'; ?>