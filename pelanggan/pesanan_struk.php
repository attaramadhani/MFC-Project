<?php
// pelanggan/pesanan_struk_pdf.php
require '../config.php';
require_login();

require '../vendor/autoload.php';

use Dompdf\Dompdf;

$id_user    = $_SESSION['user_id'];
$idPesanan  = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($idPesanan <= 0) {
    die('Struk tidak ditemukan.');
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
    die('Struk tidak ditemukan.');
}

// Hanya boleh cetak kalau sudah selesai
if ($order['order_status'] !== 'completed') {
    die('Struk hanya tersedia untuk pesanan yang sudah selesai.');
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

// Helper
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

$kode        = $order['kode_pesanan'];
$tanggal     = date('d M Y, H:i', strtotime($order['created_at']));
$totalHarga  = (float)$order['total_harga'];

// Silakan sesuaikan identitas resto di sini
$namaResto   = 'Nama Restoran Kamu';
$alamatResto = 'Alamat Restoran, Jalan Contoh No. 123';
$telpResto   = '0812-3456-7890';

// --- Bangun HTML struk ---
ob_start();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        @page {
            margin: 6mm 6mm 6mm 6mm;
        }

        html, body {
            margin: 0;
            padding: 0;
        }

        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 11px;
            color: #111;
        }

        .receipt-wrapper {
            width: 100%;
            page-break-inside: avoid;
        }

        .receipt-inner {
            max-width: 210px;      /* biar konten nggak nempel ke pinggir */
            margin: 0 auto;
            padding: 6px 2px 10px;
        }

        /* HEADER */
        .receipt-header {
            text-align: center;
            margin-bottom: 8px;
        }
        .resto-name {
            font-weight: 700;
            font-size: 14px;
            margin-bottom: 2px;
        }
        .resto-info {
            font-size: 10px;
            line-height: 1.3;
        }

        .divider {
            border-top: 1px dashed #777;
            margin: 6px 0;
        }

        /* META (info pesanan) */
        .meta-block {
            font-size: 10px;
        }
        .meta-line {
            display: flex;
            margin-bottom: 1px;
        }
        .meta-label {
            min-width: 52px;   /* lebar label tetap */
            color: #555;
        }
        .meta-value {
            flex: 1;
            word-break: break-all; /* biar kode panjang bisa dipotong */
        }

        .section-title {
            font-size: 10px;
            font-weight: 700;
            margin: 2px 0 4px;
        }

        /* ITEM LIST */
        .item-row {
            font-size: 10px;
            margin-bottom: 4px;
        }
        .item-name {
            font-weight: 500;
        }
        .item-meta {
            display: flex;
            justify-content: space-between;
            margin-top: 1px;
        }
        .item-meta-left {
            font-size: 10px;
        }
        .item-meta-right {
            font-size: 10px;
            text-align: right;
            white-space: nowrap;
        }
        .item-note {
            font-size: 9px;
            color: #555;
            margin-top: 1px;
        }

        /* TOTAL */
        .total-section {
            margin-top: 4px;
            font-size: 10px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            margin-top: 2px;
        }
        .total-main {
            padding-top: 3px;
            border-top: 1px solid #777;
            font-weight: 700;
            font-size: 11px;
        }

        /* FOOTER */
        .footer {
            text-align: center;
            margin-top: 8px;
            font-size: 10px;
            page-break-inside: avoid;
        }
        .footer .thanks {
            font-weight: 700;
            margin-bottom: 3px;
        }
    </style>
</head>
<body>
<div class="receipt-wrapper">
  <div class="receipt-inner">

    <!-- HEADER RESTO -->
    <div class="receipt-header">
        <div class="resto-name">Geprekin Aja</div>
        <div class="resto-info">
            Jl. Trunojoyo No.28, Banyu Ajuh, Kec. Kamal, Kabupaten Bangkalan<br>
            Telp: +62 896-6798-1666
        </div>
    </div>

    <div class="divider"></div>

    <!-- INFO PESANAN -->
    <div class="meta-block">
        <div class="meta-line">
            <div class="meta-label">Kode</div>
            <div class="meta-value">#<?= htmlspecialchars($kode) ?></div>
        </div>
        <div class="meta-line">
            <div class="meta-label">Tanggal</div>
            <div class="meta-value"><?= $tanggal ?></div>
        </div>
    </div>

    <div class="divider"></div>

    <!-- ITEM PESANAN -->
    <div class="section-title">Rincian Pesanan</div>

    <?php foreach ($items as $row):
        $nama     = $row['nama'];
        $jumlah   = (int)$row['jumlah'];
        $harga    = (float)$row['harga'];
        $subtotal = $jumlah * $harga;
    ?>
        <div class="item-row">
            <div class="item-name"><?= htmlspecialchars($nama) ?></div>
            <div class="item-meta">
                <div class="item-meta-left">
                    <?= $jumlah ?> x Rp <?= number_format($harga, 0, ',', '.') ?>
                </div>
                <div class="item-meta-right">
                    Rp <?= number_format($subtotal, 0, ',', '.') ?>
                </div>
            </div>
            <?php if (!empty($row['catatan_item'])): ?>
                <div class="item-note">
                    Catatan: <?= htmlspecialchars($row['catatan_item']) ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>

    <div class="divider"></div>

    <!-- TOTAL -->
    <div class="total-section">
        <div class="total-row total-main">
            <span>Total</span>
            <span>Rp <?= number_format($totalHarga, 0, ',', '.') ?></span>
        </div>

        <?php if ($pembayaran && !empty($pembayaran['reference'])): ?>
            <div class="total-row">
                <span>Ref</span>
                <span><?= htmlspecialchars($pembayaran['reference']) ?></span>
            </div>
        <?php endif; ?>
    </div>

    <div class="divider"></div>

    <!-- FOOTER -->
    <div class="footer">
        <div class="thanks">Terima kasih!</div>
        <div>Silakan datang kembali.</div>
    </div>

  </div>
</div>
</body>
</html>

<?php
$html = ob_get_clean();

// --- Generate PDF ---
$dompdf = new Dompdf();
$dompdf->loadHtml($html);

// Set ukuran kecil mirip struk, misal A7 portrait
$dompdf->setPaper('A7', 'portrait');

$dompdf->render();

// Attachment = true => langsung download
$dompdf->stream("struk-$kode.pdf", ['Attachment' => true]);
exit;