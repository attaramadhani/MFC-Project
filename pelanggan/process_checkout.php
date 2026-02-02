<?php
// pelanggan/process_checkout.php
require '../config.php';
require_login();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$id_user = $_SESSION['user_id'];

// baca body JSON (bisa kosong, bisa ada id_pesanan)
$rawBody  = file_get_contents('php://input');
$bodyData = json_decode($rawBody, true);
if (!is_array($bodyData)) {
    $bodyData = [];
}

$id_pesanan_existing = isset($bodyData['id_pesanan']) ? (int)$bodyData['id_pesanan'] : 0;

// INPUT BARU
$alamat_pengiriman  = trim($bodyData['alamat_pengiriman'] ?? '');
$wilayah_pengiriman = trim($bodyData['wilayah_pengiriman'] ?? '');
$payment_method     = $bodyData['payment_method'] ?? 'midtrans'; // midtrans|cash

if (!in_array($payment_method, ['midtrans', 'cash'], true)) {
    $payment_method = 'midtrans';
}

// --- ambil nama user ---
$stmtUser = $pdo->prepare("SELECT nama_user FROM users WHERE id_user = ?");
$stmtUser->execute([$id_user]);
$user           = $stmtUser->fetch();
$nama_pelanggan = $user ? $user['nama_user'] : 'Pelanggan';

try {
    $pdo->beginTransaction();

    $item_details    = [];
    $subtotal_items  = 0;   // total item saja
    $ongkir          = 0;   // dari wilayah
    $grand_total     = 0;   // subtotal_items + ongkir

    $kode_pesanan    = '';
    $id_pesanan      = 0;
    $oldPaymentStatus = 'unpaid';
    $order_id        = '';

    // Ambil ongkir dari tabel master wilayah_ongkir (kalau tidak ada, ongkir = 0)
    if ($wilayah_pengiriman !== '') {
        $stO = $pdo->prepare("SELECT ongkir FROM wilayah_ongkir WHERE nama_wilayah = ? LIMIT 1");
        $stO->execute([$wilayah_pengiriman]);
        $rowO = $stO->fetch(PDO::FETCH_ASSOC);
        if ($rowO) {
            $ongkir = (int)$rowO['ongkir'];
        }
    }
    if ($ongkir < 0) $ongkir = 0;

    /*
    |--------------------------------------------------------------------------
    | MODE 1: LANJUTKAN PEMBAYARAN PESANAN YANG SUDAH ADA
    | dipakai ketika body JSON kirim: { "id_pesanan": 123, ... }
    |--------------------------------------------------------------------------
    */
    if ($id_pesanan_existing > 0) {

        // cek pesanan milik user
        $stmtPes = $pdo->prepare("
            SELECT * FROM pesanan
            WHERE id_pesanan = ? AND id_user = ?
        ");
        $stmtPes->execute([$id_pesanan_existing, $id_user]);
        $pesanan = $stmtPes->fetch(PDO::FETCH_ASSOC);

        if (!$pesanan) {
            $pdo->rollBack();
            echo json_encode(['success' => false, 'message' => 'Pesanan tidak ditemukan.']);
            exit;
        }

        // blok kalau sudah dibayar
        if (($pesanan['payment_status'] ?? '') === 'paid') {
            $pdo->rollBack();
            echo json_encode(['success' => false, 'message' => 'Pesanan ini sudah dibayar.']);
            exit;
        }

        // (opsional tapi aman) blok kalau canceled
        if (($pesanan['order_status'] ?? '') === 'canceled') {
            $pdo->rollBack();
            echo json_encode(['success' => false, 'message' => 'Pesanan dibatalkan, tidak bisa dibayar.']);
            exit;
        }

        $id_pesanan        = (int)$pesanan['id_pesanan'];
        $kode_pesanan      = $pesanan['kode_pesanan'];
        $oldPaymentStatus  = $pesanan['payment_status'] ?? 'unpaid';
        $payment_method = $pesanan['payment_method'] ?? $payment_method;


        // ambil detail_pesanan + nama menu
        $stmtDet = $pdo->prepare("
            SELECT d.id_menu, d.jumlah, d.harga, m.nama
            FROM detail_pesanan d
            JOIN menu m ON m.id_menu = d.id_menu
            WHERE d.id_pesanan = ?
        ");
        $stmtDet->execute([$id_pesanan]);
        $items = $stmtDet->fetchAll(PDO::FETCH_ASSOC);

        if (!$items) {
            $pdo->rollBack();
            echo json_encode(['success' => false, 'message' => 'Detail pesanan kosong.']);
            exit;
        }

        // Hitung subtotal items + item_details
        foreach ($items as $item) {
            $qty      = (int)$item['jumlah'];
            $harga    = (int)$item['harga'];
            $subtotal = $qty * $harga;
            $subtotal_items += $subtotal;

            $item_details[] = [
                'id'       => $item['id_menu'],
                'price'    => $harga,
                'quantity' => $qty,
                'name'     => substr($item['nama'], 0, 50),
            ];
        }

        // Tambahkan ongkir sebagai item agar terlihat di Midtrans (optional)
        if ($ongkir > 0) {
            $item_details[] = [
                'id'       => 'ONGKIR',
                'price'    => $ongkir,
                'quantity' => 1,
                'name'     => 'Ongkos Kirim',
            ];
        }

        $grand_total = $subtotal_items + $ongkir;

        // Simpan ulang total + alamat/wilayah/method ke pesanan (butuh kolom di DB)
        // Kalau kolom belum ada, comment blok ini sementara.
        $stmtUpdTotal = $pdo->prepare("
            UPDATE pesanan
            SET total_harga = ?,
                ongkir = ?,
                alamat_pengiriman = ?,
                wilayah_pengiriman = ?,
                payment_method = ?
            WHERE id_pesanan = ?
        ");
        $stmtUpdTotal->execute([
            $grand_total,
            $ongkir,
            $alamat_pengiriman,
            $wilayah_pengiriman,
            $payment_method,
            $id_pesanan
        ]);

        // order_id untuk Midtrans dibuat unik supaya tidak bentrok
        $order_id = $kode_pesanan . '-R' . time(); // R = re-pay / retry
    }

    /*
    |--------------------------------------------------------------------------
    | MODE 2: CHECKOUT BARU DARI KERANJANG
    | (perilaku lama, kalau id_pesanan tidak dikirim)
    |--------------------------------------------------------------------------
    */
    else {

        // Ambil item keranjang + harga menu
        $sql = "SELECT k.*, m.nama, m.harga
                FROM keranjang k
                JOIN menu m ON m.id_menu = k.id_menu
                WHERE k.id_user = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_user]);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!$items) {
            $pdo->rollBack();
            echo json_encode(['success' => false, 'message' => 'Keranjang kosong']);
            exit;
        }

        // Hitung subtotal item dan siapkan item_details untuk Midtrans
        foreach ($items as $item) {
            $qty      = (int)$item['jumlah'];
            $harga    = (int)$item['harga'];
            $subtotal = $qty * $harga;
            $subtotal_items += $subtotal;

            $item_details[] = [
                'id'       => $item['id_menu'],
                'price'    => $harga,
                'quantity' => $qty,
                'name'     => substr($item['nama'], 0, 50),
            ];
        }

        // Tambahkan ongkir sebagai item agar terlihat di Midtrans (optional)
        if ($ongkir > 0) {
            $item_details[] = [
                'id'       => 'ONGKIR',
                'price'    => $ongkir,
                'quantity' => 1,
                'name'     => 'Ongkos Kirim',
            ];
        }

        $grand_total = $subtotal_items + $ongkir;

        // Kode pesanan unik
        $kode_pesanan = 'ORD-' . date('YmdHis') . '-' . $id_user;

        // status awal untuk Level 2
        $order_status_awal = ($payment_method === 'cash') ? 'waiting_confirmation' : 'created';

        // Insert ke pesanan (butuh kolom baru di DB)
        // Kalau kolom belum ada, kamu harus ALTER TABLE dulu.
        $stmtPesanan = $pdo->prepare("
            INSERT INTO pesanan (
                id_user, kode_pesanan, total_harga,
                alamat_pengiriman, wilayah_pengiriman, ongkir,
                payment_method,
                payment_status, order_status
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, 'unpaid', ?)
        ");
        $stmtPesanan->execute([
            $id_user,
            $kode_pesanan,
            $grand_total,
            $alamat_pengiriman,
            $wilayah_pengiriman,
            $ongkir,
            $payment_method,
            $order_status_awal
        ]);
        $id_pesanan = (int)$pdo->lastInsertId();

        // detail_pesanan
        $stmtDetail = $pdo->prepare("
            INSERT INTO detail_pesanan (id_pesanan, id_menu, jumlah, harga)
            VALUES (?, ?, ?, ?)
        ");
        foreach ($items as $item) {
            $stmtDetail->execute([
                $id_pesanan,
                $item['id_menu'],
                $item['jumlah'],
                $item['harga']
            ]);
        }

        // riwayat status pesanan
        $stmtRiwayatOrder = $pdo->prepare("
            INSERT INTO riwayat_status_pesanan (id_pesanan, tipe, status_lama, status_baru, diubah_oleh, keterangan)
            VALUES (?, 'order', NULL, ?, ?, ?)
        ");
        $stmtRiwayatOrder->execute([
            $id_pesanan,
            $order_status_awal,
            $id_user,
            ($payment_method === 'cash')
                ? 'Pesanan COD dibuat, menunggu konfirmasi admin.'
                : 'Pesanan dibuat oleh pelanggan.'
        ]);

        // Kosongkan keranjang
        $stmtDel = $pdo->prepare("DELETE FROM keranjang WHERE id_user = ?");
        $stmtDel->execute([$id_user]);

        // order_id untuk Midtrans (pertama kali)
        $order_id = $kode_pesanan;
    }

    /*
    |--------------------------------------------------------------------------
    | JALUR CASH (COD) - Level 2 (MENUNGGU KONFIRMASI ADMIN)
    |--------------------------------------------------------------------------
    */
    if ($payment_method === 'cash') {

        // Pastikan status pesanan waiting_confirmation (untuk MODE 1 juga)
        $pdo->prepare("
            UPDATE pesanan
            SET order_status = 'waiting_confirmation',
                payment_status = 'unpaid'
            WHERE id_pesanan = ?
        ")->execute([$id_pesanan]);

        // Simpan pembayaran cash (biar tercatat metodenya)
        $stmtBayarCash = $pdo->prepare("
            INSERT INTO pembayaran (
                id_pesanan, provider, metode, gross_amount, status,
                transaction_time, settlement_time,
                provider_order_id, provider_transaction_id, raw_response
            ) VALUES (
                    ?, 'cash', 'cod', ?, 'pending',
                    NOW(), NULL,
                    NULL, NULL, NULL
                    )

        ");
        $stmtBayarCash->execute([$id_pesanan, $grand_total]);

        // riwayat payment (optional)
        $stmtRiwayatPay = $pdo->prepare("
            INSERT INTO riwayat_status_pesanan (id_pesanan, tipe, status_lama, status_baru, diubah_oleh, keterangan)
            VALUES (?, 'payment', ?, 'unpaid', ?, ?)
        ");
        $stmtRiwayatPay->execute([
            $id_pesanan,
            $oldPaymentStatus,
            null,
            'Metode pembayaran COD. Menunggu konfirmasi admin.'
        ]);

        $pdo->commit();

        echo json_encode([
            'success'      => true,
            'mode'         => 'cash',
            'message'      => 'Pesanan COD dibuat. Menunggu konfirmasi admin.',
            'kode_pesanan' => $kode_pesanan,
            'id_pesanan'   => $id_pesanan,
            'ongkir'       => $ongkir,
            'total'        => $grand_total,
        ]);
        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | BAGIAN UMUM: PANGGIL MIDTRANS SNAP & SIMPAN PEMBAYARAN
    |--------------------------------------------------------------------------
    */

    // Payload ke Midtrans (gross_amount = grand_total)
    $payload = [
        'transaction_details' => [
            'order_id'     => $order_id,
            'gross_amount' => (int)$grand_total,
        ],
        'customer_details' => [
            'first_name' => $nama_pelanggan,
        ],
        'item_details' => $item_details,
    ];

    $ch = curl_init(MIDTRANS_API_URL);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_HTTPHEADER     => [
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: Basic ' . base64_encode(MIDTRANS_SERVER_KEY . ':'),
        ],
        CURLOPT_POSTFIELDS     => json_encode($payload),
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if ($response === false) {
        $error = curl_error($ch);
        curl_close($ch);
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Error cURL: ' . $error]);
        exit;
    }

    curl_close($ch);
    $data = json_decode($response, true);

    if ($httpCode >= 200 && $httpCode < 300 && isset($data['token'])) {

        // Update status pembayaran jadi pending
        $stmtUpdate = $pdo->prepare("
            UPDATE pesanan
            SET payment_status = 'pending'
            WHERE id_pesanan = ?
        ");
        $stmtUpdate->execute([$id_pesanan]);

        // Simpan pembayaran (boleh banyak record, kalau user coba bayar berkali-kali)
        $stmtBayar = $pdo->prepare("
            INSERT INTO pembayaran (
                id_pesanan, provider, metode, gross_amount, status,
                transaction_time, settlement_time,
                provider_order_id, provider_transaction_id, raw_response
            ) VALUES (
                ?, ?, ?, ?, ?,
                NOW(), NULL,
                ?, ?, ?
            )
        ");

        $provider           = 'midtrans';
        $metode             = 'qris';
        $statusBayar        = 'pending';
        $provider_order_id  = $order_id;
        $provider_trans_id  = $data['token'] ?? null;
        $raw_response       = json_encode($data);

        $stmtBayar->execute([
            $id_pesanan,
            $provider,
            $metode,
            $grand_total,
            $statusBayar,
            $provider_order_id,
            $provider_trans_id,
            $raw_response
        ]);

        // riwayat payment (status lama -> pending)
        $stmtRiwayatPay = $pdo->prepare("
            INSERT INTO riwayat_status_pesanan (id_pesanan, tipe, status_lama, status_baru, diubah_oleh, keterangan)
            VALUES (?, 'payment', ?, ?, ?, ?)
        ");
        $stmtRiwayatPay->execute([
            $id_pesanan,
            $oldPaymentStatus,
            'pending',
            null,
            'Membuat transaksi ke payment gateway.'
        ]);

        $pdo->commit();

        echo json_encode([
            'success'      => true,
            'token'        => $data['token'],
            'redirect_url' => $data['redirect_url'] ?? null,
            'kode_pesanan' => $kode_pesanan,
            'id_pesanan'   => $id_pesanan,
            'ongkir'       => $ongkir,
            'total'        => $grand_total,
        ]);
        exit;
    }

    $pdo->rollBack();
    echo json_encode([
        'success'           => false,
        'message'           => 'Gagal membuat transaksi Midtrans',
        'midtrans_response' => $data,
    ]);
    exit;

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}