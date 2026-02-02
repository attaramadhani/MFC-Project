<?php
// admin/tools/seed_pesanan.php
// Jalankan sekali untuk bikin data dummy pesanan + pembayaran Midtrans

require_once __DIR__ . '../../config.php'; // SESUAIKAN kalau letak config beda
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// 1. BERSIHKAN DATA LAMA
$pdo->exec("DELETE FROM riwayat_status_pesanan");
$pdo->exec("DELETE FROM pembayaran");
$pdo->exec("DELETE FROM detail_pesanan");
$pdo->exec("DELETE FROM keranjang");
$pdo->exec("DELETE FROM pesanan");

// 2. DATA MENU & DISTRIBUSI (sesuai warung, tanpa Ala Carte karena nggak ada di DB)
$menuHarga = [
    1  => 10000, // Geprek Hemat
    2  => 14000, // Geprek Jumbo
    3  =>  9000, // Crispy Hemat
    4  => 13000, // Crispy Jumbo
    5  => 12000, // Gangnam Hemat
    6  => 16000, // Gangnam Jumbo
];

$distribusiPorsi = [
    ['id_menu' => 1, 'porsi' => 35],
    ['id_menu' => 2, 'porsi' => 17],
    ['id_menu' => 3, 'porsi' => 15],
    ['id_menu' => 4, 'porsi' => 18],
    ['id_menu' => 5, 'porsi' => 12],
    ['id_menu' => 6, 'porsi' => 10],
];

$metodeList = ['qris', 'gopay', 'shopeepay', 'bca_va', 'bri_va', 'bni_va'];

// Ambil semua pelanggan
$stmt = $pdo->query("SELECT id_user FROM users WHERE role = 'pelanggan'");
$idPelanggan = $stmt->fetchAll(PDO::FETCH_COLUMN);
if (!$idPelanggan) {
    die("Belum ada pelanggan di tabel users.\n");
}

// 3. GENERATE DATA 30 HARI (UBAH JADI 120 KALAU MAU 4 BULAN)
$totalHari = 30; // <- ganti ke 120 kalau mau 4 bulan

$idPesanan   = 1;
$idDetail    = 1;
$idBayar     = 1;

$sqlPesanan = "INSERT INTO pesanan
    (id_pesanan, id_user, kode_pesanan, total_harga, payment_status, order_status,
     created_at, updated_at, paid_at, processed_at, ready_at, completed_at, canceled_at)
    VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)";
$stmtPesanan = $pdo->prepare($sqlPesanan);

$sqlDetail = "INSERT INTO detail_pesanan
    (id_detail_pesanan, id_pesanan, id_menu, jumlah, harga, catatan_item)
    VALUES (?,?,?,?,?,?)";
$stmtDetail = $pdo->prepare($sqlDetail);

$sqlBayar = "INSERT INTO pembayaran
    (id_pembayaran, id_pesanan, provider, metode, gross_amount, status,
     transaction_time, settlement_time, provider_order_id, provider_transaction_id,
     raw_response, created_at, updated_at)
    VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)";
$stmtBayar = $pdo->prepare($sqlBayar);

$pdo->beginTransaction();

for ($offset = $totalHari - 1; $offset >= 0; $offset--) {
    $tanggal = new DateTime("today");
    $tanggal->modify("-{$offset} day");
    $tanggalStr = $tanggal->format('Y-m-d');

    $jamBase = 10; // jam awal
    $i = 0;

    foreach ($distribusiPorsi as $dataMenu) {
        $idMenu = $dataMenu['id_menu'];

        // Biar bervariasi dikit: +/- 20% dari porsi dasar
        $porsiDasar = $dataMenu['porsi'];
        $min = (int)round($porsiDasar * 0.8);
        $max = (int)round($porsiDasar * 1.2);
        if ($min < 1) $min = 1;
        $porsi = rand($min, $max);

        $hargaSatuan = $menuHarga[$idMenu];
        $totalHarga  = $hargaSatuan * $porsi;

        $createdAt   = new DateTime($tanggalStr . sprintf(' %02d:00:00', $jamBase + $i));
        $paidAt      = (clone $createdAt)->modify('+5 minutes');
        $processedAt = (clone $paidAt)->modify('+5 minutes');
        $readyAt     = (clone $processedAt)->modify('+10 minutes');
        $completedAt = (clone $readyAt)->modify('+5 minutes');

        // Pilih pelanggan random (jadi nggak cuma 4 orang)
        $idUser = $idPelanggan[array_rand($idPelanggan)];

        $kode = sprintf('ORD-%s-%04d', $tanggal->format('Ymd'), $idPesanan);

        $stmtPesanan->execute([
            $idPesanan,
            $idUser,
            $kode,
            $totalHarga,
            'paid',
            'completed',
            $createdAt->format('Y-m-d H:i:s'),
            null,
            $paidAt->format('Y-m-d H:i:s'),
            $processedAt->format('Y-m-d H:i:s'),
            $readyAt->format('Y-m-d H:i:s'),
            $completedAt->format('Y-m-d H:i:s'),
            null
        ]);

        $stmtDetail->execute([
            $idDetail,
            $idPesanan,
            $idMenu,
            $porsi,
            $hargaSatuan,
            null
        ]);

        $metode  = $metodeList[array_rand($metodeList)];
        $tTime   = (clone $paidAt)->modify('-1 minute');
        $gross   = $totalHarga;
        $provOrd = sprintf('INV-%s-%04d', $tanggal->format('Ymd'), $idPesanan);
        $provTrx = sprintf('MID-%06d', $idPesanan);
        $raw     = json_encode([
            'transaction_status' => 'settlement',
            'payment_type'       => $metode,
            'gross_amount'       => number_format($gross, 2, '.', '')
        ]);

        $stmtBayar->execute([
            $idBayar,
            $idPesanan,
            'midtrans',
            $metode,
            $gross,
            'paid',
            $tTime->format('Y-m-d H:i:s'),
            $paidAt->format('Y-m-d H:i:s'),
            $provOrd,
            $provTrx,
            $raw,
            $tTime->format('Y-m-d H:i:s'),
            $paidAt->format('Y-m-d H:i:s'),
        ]);

        $idPesanan++;
        $idDetail++;
        $idBayar++;
        $i++;
    }
}

$pdo->commit();

$pdo->exec("ALTER TABLE pesanan AUTO_INCREMENT = " . ($idPesanan));
$pdo->exec("ALTER TABLE detail_pesanan AUTO_INCREMENT = " . ($idDetail));
$pdo->exec("ALTER TABLE pembayaran AUTO_INCREMENT = " . ($idBayar));

echo "SEED PESANAN SELESAI. Dibuat {$totalHari} hari data.\n";