<?php
// admin/pages/laporan.php
require_once __DIR__ . '/../../config.php';
require_admin();

// Default 7 hari terakhir
$defaultFrom = date('Y-m-d', strtotime('-6 days'));
$defaultTo   = date('Y-m-d');

$from = $_GET['from'] ?? $defaultFrom;
$to   = $_GET['to']   ?? $defaultTo;

// Pastikan from <= to
if ($from > $to) { [$from, $to] = [$to, $from]; }

/*
  Query aman (tidak dobel count transaksi):
  - Aggregate detail_pesanan per pesanan (id_pesanan) dulu
  - Lalu baru group per tanggal paid_at
*/
$stmt = $pdo->prepare("
  SELECT 
    DATE(p.paid_at) AS tgl,
    COUNT(*) AS total_transaksi,
    SUM(p.total_harga) AS total_pendapatan,
    SUM(oi.total_item) AS total_item,
    SUM(oi.makanan_qty) AS makanan_qty,
    SUM(oi.minuman_qty) AS minuman_qty,
    SUM(oi.tambahan_qty) AS tambahan_qty
  FROM pesanan p
  JOIN (
      SELECT 
        dp.id_pesanan,
        SUM(dp.jumlah) AS total_item,

        SUM(CASE 
              WHEN LOWER(TRIM(m.kategori)) IN ('geprek','crispy','gangnam') THEN dp.jumlah 
              ELSE 0 
            END) AS makanan_qty,

        SUM(CASE 
              WHEN LOWER(TRIM(m.kategori)) = 'minuman' THEN dp.jumlah 
              ELSE 0 
            END) AS minuman_qty,

        SUM(CASE 
              WHEN LOWER(TRIM(m.kategori)) = 'tambahan' THEN dp.jumlah 
              ELSE 0 
            END) AS tambahan_qty

      FROM detail_pesanan dp
      JOIN menu m ON m.id_menu = dp.id_menu
      GROUP BY dp.id_pesanan
  ) oi ON oi.id_pesanan = p.id_pesanan
  WHERE p.payment_status = 'paid'
    AND DATE(p.paid_at) BETWEEN ? AND ?
  GROUP BY DATE(p.paid_at)
  ORDER BY tgl ASC
");
$stmt->execute([$from, $to]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Total periode
$totalPendapatan = 0;
$totalTransaksi  = 0;
$totalItem       = 0;
$totalMakanan    = 0;
$totalMinuman    = 0;
$totalTambahan   = 0;

foreach ($rows as $r) {
  $totalPendapatan += (int)$r['total_pendapatan'];
  $totalTransaksi  += (int)$r['total_transaksi'];
  $totalItem       += (int)$r['total_item'];
  $totalMakanan    += (int)$r['makanan_qty'];
  $totalMinuman    += (int)$r['minuman_qty'];
  $totalTambahan   += (int)$r['tambahan_qty'];
}

include __DIR__ . '/../partials/admin_header.php';
?>

<div class="container py-4">

  <div class="mb-3 d-flex justify-content-between align-items-center">
    <div>
      <h3 class="fw-bold mb-1">Laporan Penjualan</h3>
      <div class="text-muted small">Rekap transaksi (paid) berdasarkan tanggal.</div>
    </div>

    <a class="btn btn-sm btn-outline-secondary"
       href="index.php?page=laporan_export&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>"
       target="_blank">
      Export PDF
    </a>
  </div>

  <!-- FILTER -->
  <form method="get" class="row g-2 align-items-end mb-3">
    <input type="hidden" name="page" value="laporan">
    <div class="col-md-3">
      <label class="form-label small mb-1">Dari</label>
      <input type="date" name="from" class="form-control form-control-sm" value="<?= htmlspecialchars($from) ?>">
    </div>
    <div class="col-md-3">
      <label class="form-label small mb-1">Sampai</label>
      <input type="date" name="to" class="form-control form-control-sm" value="<?= htmlspecialchars($to) ?>">
    </div>
    <div class="col-md-2">
      <button class="btn btn-sm btn-main w-100 text-white">Tampilkan</button>
    </div>
  </form>

  <!-- RINGKAS PER KATEGORI (PERIODE) -->
  <div class="row g-2 mb-3">
    <div class="col-md-4">
      <div class="card border-0 shadow-sm">
        <div class="card-body py-2">
          <div class="small text-muted">Makanan Terjual</div>
          <div class="fw-bold"><?= $totalMakanan ?> item</div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card border-0 shadow-sm">
        <div class="card-body py-2">
          <div class="small text-muted">Minuman Terjual</div>
          <div class="fw-bold"><?= $totalMinuman ?> item</div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card border-0 shadow-sm">
        <div class="card-body py-2">
          <div class="small text-muted">Tambahan Terjual</div>
          <div class="fw-bold"><?= $totalTambahan ?> item</div>
        </div>
      </div>
    </div>
  </div>

  <!-- TABEL PER HARI + BREAKDOWN -->
  <div class="card shadow-sm border-0 rounded-4">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead class="small text-muted">
            <tr>
              <th>Tanggal</th>
              <th class="text-end">Transaksi</th>
              <th class="text-end">Makanan</th>
              <th class="text-end">Minuman</th>
              <th class="text-end">Tambahan</th>
              <th class="text-end">Total Item</th>
              <th class="text-end">Pendapatan</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!$rows): ?>
              <tr>
                <td colspan="7" class="text-center text-muted py-4">Tidak ada transaksi pada periode ini.</td>
              </tr>
            <?php else: ?>
              <?php foreach ($rows as $r): ?>
                <tr>
                  <td><?= date('d M Y', strtotime($r['tgl'])) ?></td>
                  <td class="text-end"><?= (int)$r['total_transaksi'] ?></td>
                  <td class="text-end"><?= (int)$r['makanan_qty'] ?></td>
                  <td class="text-end"><?= (int)$r['minuman_qty'] ?></td>
                  <td class="text-end"><?= (int)$r['tambahan_qty'] ?></td>
                  <td class="text-end"><?= (int)$r['total_item'] ?></td>
                  <td class="text-end">Rp <?= number_format((int)$r['total_pendapatan'], 0, ',', '.') ?></td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>

          <?php if ($rows): ?>
          <tfoot class="small text-muted">
            <tr>
              <th>Total</th>
              <th class="text-end"><?= $totalTransaksi ?></th>
              <th class="text-end"><?= $totalMakanan ?></th>
              <th class="text-end"><?= $totalMinuman ?></th>
              <th class="text-end"><?= $totalTambahan ?></th>
              <th class="text-end"><?= $totalItem ?></th>
              <th class="text-end">Rp <?= number_format($totalPendapatan, 0, ',', '.') ?></th>
            </tr>
          </tfoot>
          <?php endif; ?>
        </table>
      </div>
    </div>
  </div>

</div>