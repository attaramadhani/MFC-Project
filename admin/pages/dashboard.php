<?php
// admin/pages/dashboard.php

// ====== CARD SUMMARY ======

// Pendapatan hari ini
$stmt = $pdo->prepare("
    SELECT COALESCE(SUM(total_harga),0) AS total
    FROM pesanan
    WHERE payment_status = 'paid'
      AND DATE(paid_at) = CURDATE()
");
$stmt->execute();
$todayRevenue = (int)$stmt->fetchColumn();

// Pendapatan bulan ini
$stmt = $pdo->prepare("
    SELECT COALESCE(SUM(total_harga),0) AS total
    FROM pesanan
    WHERE payment_status = 'paid'
      AND YEAR(paid_at) = YEAR(CURDATE())
      AND MONTH(paid_at) = MONTH(CURDATE())
");
$stmt->execute();
$monthRevenue = (int)$stmt->fetchColumn();

// Total transaksi (paid)
$stmt = $pdo->prepare("
    SELECT COUNT(*) 
    FROM pesanan
    WHERE payment_status = 'paid'
");
$stmt->execute();
$totalTransaksiPaid = (int)$stmt->fetchColumn();

// Total pelanggan
$stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'pelanggan'");
$totalPelanggan = (int)$stmt->fetchColumn();

// Total menu
$stmt = $pdo->query("SELECT COUNT(*) FROM menu");
$totalMenu = (int)$stmt->fetchColumn();

// Total pesanan per status
$stmt = $pdo->query("
    SELECT order_status, COUNT(*) AS jml
    FROM pesanan
    GROUP BY order_status
");
$statusCount = [
    'created'    => 0,
    'processing' => 0,
    'ready'      => 0,
    'completed'  => 0,
    'canceled'   => 0,
];
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
    if (isset($statusCount[$row['order_status']])) {
        $statusCount[$row['order_status']] = (int)$row['jml'];
    }
}

// ====== GRAFIK HARIAN (30 HARI) ======

$stmt = $pdo->prepare("
    SELECT 
        DATE(paid_at) AS tgl,
        COALESCE(SUM(total_harga),0) AS total
    FROM pesanan
    WHERE payment_status = 'paid'
      AND DATE(paid_at) >= DATE_SUB(CURDATE(), INTERVAL 29 DAY)
      AND DATE(paid_at) <= CURDATE()
    GROUP BY DATE(paid_at)
    ORDER BY tgl ASC
");
$stmt->execute();
$rowsDaily = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Buat array tanggal lengkap 30 hari ke belakang biar grafik tidak bolong
$dailyLabels = [];
$dailyValues = [];
$mapDaily = [];
foreach ($rowsDaily as $r) {
    $mapDaily[$r['tgl']] = (int)$r['total'];
}

for ($i = 29; $i >= 0; $i--) {
    $tgl = date('Y-m-d', strtotime("-{$i} day"));
    $dailyLabels[] = date('d M', strtotime($tgl));
    $dailyValues[] = isset($mapDaily[$tgl]) ? $mapDaily[$tgl] : 0;
}

// ====== GRAFIK BULANAN (6 BULAN TERAKHIR) ======

$stmt = $pdo->prepare("
    SELECT 
        DATE_FORMAT(paid_at, '%Y-%m') AS ym,
        COALESCE(SUM(total_harga),0) AS total
    FROM pesanan
    WHERE payment_status = 'paid'
      AND paid_at >= DATE_SUB(DATE_FORMAT(CURDATE(), '%Y-%m-01'), INTERVAL 5 MONTH)
    GROUP BY DATE_FORMAT(paid_at, '%Y-%m')
    ORDER BY ym ASC
");
$stmt->execute();
$rowsMonthly = $stmt->fetchAll(PDO::FETCH_ASSOC);

$monthLabels = [];
$monthValues = [];
$mapMonthly   = [];
foreach ($rowsMonthly as $r) {
    $mapMonthly[$r['ym']] = (int)$r['total'];
}

for ($i = 5; $i >= 0; $i--) {
    $firstDay = date('Y-m-01', strtotime("-{$i} month"));
    $ym       = date('Y-m', strtotime($firstDay));
    $monthLabels[] = date('M Y', strtotime($firstDay));
    $monthValues[] = isset($mapMonthly[$ym]) ? $mapMonthly[$ym] : 0;
}
?>

<div class="mb-3">
  <h2 class="h5 mb-1">Dashboard</h2>
  <div class="text-muted small">
    Ringkasan penjualan dan status pesanan toko GeprekinAja.
  </div>
</div>

<div class="row g-3 mb-3">
  <div class="col-md-3">
    <div class="stat-card">
      <div class="stat-label">Pendapatan Hari Ini</div>
      <div class="stat-value">Rp <?= number_format($todayRevenue,0,',','.') ?></div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="stat-card">
      <div class="stat-label">Pendapatan Bulan Ini</div>
      <div class="stat-value">Rp <?= number_format($monthRevenue,0,',','.') ?></div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="stat-card">
      <div class="stat-label">Transaksi Berhasil</div>
      <div class="stat-value"><?= $totalTransaksiPaid ?></div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="stat-card">
      <div class="stat-label">Pelanggan Terdaftar</div>
      <div class="stat-value"><?= $totalPelanggan ?></div>
    </div>
  </div>
</div>

<div class="row g-3 mb-4">
  <div class="col-md-4">
    <div class="stat-card">
      <div class="stat-label mb-1">Total Menu</div>
      <div class="stat-value mb-2"><?= $totalMenu ?></div>
      <div class="small text-muted">
        Pesanan:
        <span class="badge-soft-gray status-pill">Created: <?= $statusCount['created'] ?></span>
        <span class="badge-soft-blue status-pill">Processing: <?= $statusCount['processing'] ?></span>
        <span class="badge-soft-amber status-pill">Ready: <?= $statusCount['ready'] ?></span>
        <span class="badge-soft-green status-pill">Completed: <?= $statusCount['completed'] ?></span>
        <span class="badge-soft-red status-pill">Canceled: <?= $statusCount['canceled'] ?></span>
      </div>
    </div>
  </div>
  <div class="col-md-8">
    <div class="stat-card">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <div class="stat-label">Grafik Harian (30 Hari)</div>
        <div class="small text-muted">Pendapatan berdasarkan tanggal bayar</div>
      </div>
      <canvas id="chartDaily" height="80"></canvas>
    </div>
  </div>
</div>

<div class="row g-3">
  <div class="col-md-12">
    <div class="stat-card">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <div class="stat-label">Grafik Bulanan (6 Bulan)</div>
        <div class="small text-muted">Pendapatan per bulan</div>
      </div>
      <canvas id="chartMonthly" height="90"></canvas>
    </div>
  </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
  // data dari PHP
  const dailyLabels  = <?= json_encode($dailyLabels) ?>;
  const dailyValues  = <?= json_encode($dailyValues) ?>;
  const monthLabels  = <?= json_encode($monthLabels) ?>;
  const monthValues  = <?= json_encode($monthValues) ?>;

  const ctxDaily = document.getElementById('chartDaily').getContext('2d');
  new Chart(ctxDaily, {
    type: 'line',
    data: {
      labels: dailyLabels,
      datasets: [{
        label: 'Pendapatan',
        data: dailyValues,
        tension: 0.3,
        fill: false
      }]
    },
    options: {
      plugins: { legend: { display: false } },
      scales: {
        y: { ticks: { callback: v => 'Rp ' + v.toLocaleString('id-ID') } }
      }
    }
  });

  const ctxMonthly = document.getElementById('chartMonthly').getContext('2d');
  new Chart(ctxMonthly, {
    type: 'bar',
    data: {
      labels: monthLabels,
      datasets: [{
        label: 'Pendapatan',
        data: monthValues
      }]
    },
    options: {
      plugins: { legend: { display: false } },
      scales: {
        y: { ticks: { callback: v => 'Rp ' + v.toLocaleString('id-ID') } }
      }
    }
  });
});
</script>
