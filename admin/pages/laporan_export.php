<?php
require __DIR__ . '/../../vendor/autoload.php'; // pastikan composer dompdf sudah di-install
use Dompdf\Dompdf;

$defaultFrom = date('Y-m-d', strtotime('-6 days'));
$defaultTo   = date('Y-m-d');

$from = $_GET['from'] ?? $defaultFrom;
$to   = $_GET['to']   ?? $defaultTo;

if ($from > $to) {
    $tmp  = $from;
    $from = $to;
    $to   = $tmp;
}

$stmt = $pdo->prepare("
    SELECT 
        DATE(p.paid_at) AS tgl,
        COUNT(*) AS total_transaksi,
        SUM(p.total_harga) AS total_pendapatan,
        SUM(dp_total.total_item) AS total_item
    FROM pesanan p
    JOIN (
        SELECT id_pesanan, SUM(jumlah) AS total_item
        FROM detail_pesanan
        GROUP BY id_pesanan
    ) dp_total ON dp_total.id_pesanan = p.id_pesanan
    WHERE p.payment_status = 'paid'
      AND DATE(p.paid_at) BETWEEN ? AND ?
    GROUP BY DATE(p.paid_at)
    ORDER BY tgl ASC
");
$stmt->execute([$from, $to]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalPendapatan = 0;
$totalTransaksi  = 0;
$totalItem       = 0;
foreach ($rows as $r) {
    $totalPendapatan += (int)$r['total_pendapatan'];
    $totalTransaksi  += (int)$r['total_transaksi'];
    $totalItem       += (int)$r['total_item'];
}

ob_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Laporan Penjualan <?= htmlspecialchars($from) ?> s/d <?= htmlspecialchars($to) ?></title>
  <style>
    * { box-sizing: border-box; }
    body {
        font-family: Arial, Helvetica, sans-serif;
        font-size: 11px;
        margin: 25px 30px;
    }

    /* ==== HEADER / KOP SURAT ==== */
    .header {
        width: 100%;
        display: table;
        margin-bottom: 6px;
    }
    .header-left,
    .header-right {
        display: table-cell;
        vertical-align: top;
    }
    .header-left {
        width: 60%;
        padding-right: 10px;
    }
    .header-right {
        width: 40%;
        text-align: right;
    }

    .brand-name {
        font-size: 18px;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 2px;
    }
    .brand-tagline {
        font-size: 10px;
        font-style: italic;
        color: #555;
        margin-bottom: 4px;
    }
    .brand-info {
        font-size: 9px;
        color: #444;
        line-height: 1.4;
    }

    .report-title {
        font-size: 12px;
        font-weight: bold;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        margin-bottom: 4px;
    }
    .report-meta {
        display: inline-block;
        font-size: 9px;
        text-align: right;
    }
    .report-meta table {
        border-collapse: collapse;
    }
    .report-meta td {
        padding: 1px 0 1px 6px;
        font-size: 9px;
    }
    .report-meta td:first-child {
        padding-left: 0;
    }

    .line-bold {
        border: 0;
        border-top: 2px solid #000;
        margin: 4px 0 1px;
    }
    .line-thin {
        border: 0;
        border-top: 1px solid #000;
        margin: 0 0 10px;
    }

    /* ==== TABEL UTAMA ==== */
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 4px;
        font-size: 10px;
    }
    th, td {
        border: 1px solid #000;
        padding: 4px 5px;
    }
    th {
        background: #f0f0f0;
        text-align: center;
    }
    .text-right { text-align: right; }
    .text-center { text-align: center; }

    /* ==== RINGKASAN ==== */
    .summary {
        margin-top: 10px;
        font-size: 10px;
    }
    .summary table {
        width: 45%;
        border: none;
    }
    .summary td {
        border: none;
        padding: 2px 0;
    }

    .footer-note {
        margin-top: 8px;
        font-size: 9px;
        color: #555;
    }

    /* ==== TTD ==== */
    .ttd-wrapper { margin-top: 40px; width:100%; }
    .ttd {
        width: 220px;
        float:right;
        text-align:center;
        font-size:11px;
    }
    .line {
        margin-top: 40px;
        border-top: 1px solid #000;
        padding-top: 3px;
        font-weight: bold;
    }
    .report-meta table {
    border-collapse: collapse;
    width: auto;           
    }

    .report-meta td {
        border: none;           /* hilangkan border */
        padding: 1px 0 1px 6px; /* padding minimal tapi rapi */
        font-size: 9px;
    }

    .report-meta td:first-child {
        padding-left: 0;
        font-weight: bold;      /* biar labelnya tegas */
    }

  </style>
</head>
<body>

  <!-- ===== HEADER YANG LEBIH "HIDUP" & PROFESIONAL ===== -->
  <div class="header">
      <div class="header-left">
          <div class="brand-name">GeprekinAja</div>
          <div class="brand-tagline">Laporan Operasional & Penjualan</div>
          <div class="brand-info">
              Jl. Trunojoyo No.28, Banyu Ajuh, Kec. Kamal, Kabupaten Bangkalan, Jawa Timur 69162<br>
              Telp: 0896-6798-1666 · Email: geprekin@gmail.com
          </div>
      </div>
      <div class="header-right">
        <div class="report-title">Laporan Penjualan</div>
        <div class="report-meta">
            <table>
                <tr>
                    <td>Periode</td>
                    <td>:</td>
                    <td><?= htmlspecialchars($from) ?> s/d <?= htmlspecialchars($to) ?></td>
                </tr>
                <tr>
                    <td>Dicetak</td>
                    <td>:</td>
                    <td><?= date('d-m-Y H:i') ?></td>
                </tr>
            </table>
        </div>
    </div>


  </div>
  <hr class="line-bold">
  <hr class="line-thin">

  <table>
    <thead>
      <tr>
        <th style="width: 5%;">No</th>
        <th style="width: 18%;">Tanggal</th>
        <th style="width: 20%;">Jumlah Transaksi</th>
        <th style="width: 20%;">Item Terjual</th>
        <th>Pendapatan (Rp)</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($rows): ?>
        <?php $no = 1; foreach ($rows as $r): ?>
          <tr>
            <td class="text-center"><?= $no++ ?></td>
            <td class="text-center"><?= date('d M Y', strtotime($r['tgl'])) ?></td>
            <td class="text-right"><?= (int)$r['total_transaksi'] ?></td>
            <td class="text-right"><?= (int)$r['total_item'] ?></td>
            <td class="text-right">Rp <?= number_format($r['total_pendapatan'], 0, ',', '.') ?></td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr><td colspan="5" class="text-center">Tidak ada transaksi pada periode ini.</td></tr>
      <?php endif; ?>
    </tbody>
    <?php if ($rows): ?>
    <tfoot>
      <tr>
        <th colspan="2">TOTAL</th>
        <th class="text-right"><?= $totalTransaksi ?></th>
        <th class="text-right"><?= $totalItem ?></th>
        <th class="text-right">Rp <?= number_format($totalPendapatan, 0, ',', '.') ?></th>
      </tr>
    </tfoot>
    <?php endif; ?>
  </table>

  <?php if ($rows): ?>
  <div class="summary">
    <table>
      <tr><td>Jumlah hari dengan transaksi</td><td>:</td><td><?= count($rows) ?> hari</td></tr>
      <tr><td>Total transaksi</td><td>:</td><td><?= $totalTransaksi ?> transaksi</td></tr>
      <tr><td>Total item terjual</td><td>:</td><td><?= $totalItem ?> item</td></tr>
      <tr><td>Total pendapatan</td><td>:</td><td>Rp <?= number_format($totalPendapatan, 0, ',', '.') ?></td></tr>
    </table>
  </div>
  <?php endif; ?>

  <?php $admin = $_SESSION['username'] ?? 'Administrator'; ?>

  <div class="ttd-wrapper">
      <div class="ttd">
          <?php
          $bulanIndo = [
              1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
              'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
          ];

          $hari = date('d');
          $bulan = date('n'); 
          $tahun = date('Y');

          $tanggalIndo = $hari . ' ' . $bulanIndo[$bulan] . ' ' . $tahun;
          ?>
          <?= 'Bangkalan, ' . $tanggalIndo ?><br>

          Mengetahui,<br><br><br><br>

          <div style="border-top:1px solid #000; padding-top:3px; margin-top:40px; font-weight:bold;">
              <?= htmlspecialchars($admin) ?>
          </div>
      </div>
  </div>

</body>
</html>
<?php
$html = ob_get_clean();

// ----- generate & kirim PDF -----
$dompdf = new Dompdf(['isRemoteEnabled' => true]);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

$filename = "laporan-{$from}-{$to}.pdf";
$dompdf->stream($filename, ['Attachment' => true]);
exit;