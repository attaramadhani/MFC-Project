<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config.php';

\Midtrans\Config::$serverKey = $_ENV['MIDTRANS_SERVER_KEY'];
\Midtrans\Config::$isProduction = $_ENV['MIDTRANS_IS_PRODUCTION'];

// Midtrans akan POST JSON
$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

if (!$data) {
    http_response_code(400);
    echo "Invalid JSON";
    exit;
}

// ambil field penting
$order_id      = $data['order_id'] ?? '';
$status_code   = $data['status_code'] ?? '';
$gross_amount  = $data['gross_amount'] ?? '';
$signature_key = $data['signature_key'] ?? '';
$tx_status     = $data['transaction_status'] ?? '';
$fraud_status  = $data['fraud_status'] ?? null;
$transaction_id = $data['transaction_id'] ?? null;
$settlement_time = $data['settlement_time'] ?? null;
$transaction_time = $data['transaction_time'] ?? null;

// validasi minimal
if ($order_id === '' || $status_code === '' || $gross_amount === '' || $signature_key === '' || $tx_status === '') {
    http_response_code(400);
    echo "Missing fields";
    exit;
}

// 1) VERIFIKASI SIGNATURE (wajib biar aman)
$serverKey = $_ENV['MIDTRANS_SERVER_KEY'] ?? '';
$serverKey = defined('MIDTRANS_SERVER_KEY') ? MIDTRANS_SERVER_KEY : ($_ENV['MIDTRANS_SERVER_KEY'] ?? '');
$expected = hash('sha512', $order_id . $status_code . $gross_amount . $serverKey);
if (!hash_equals($expected, $signature_key)) {
    http_response_code(403);
    echo "Invalid signature";
    exit;
}

// 2) CARI id_pesanan dari pembayaran (karena order_id bisa kode_pesanan atau kode_pesanan-Rxxxx)
$stmt = $pdo->prepare("
    SELECT id_pesanan, id_pembayaran
    FROM pembayaran
    WHERE provider = 'midtrans' AND provider_order_id = ?
    ORDER BY id_pembayaran DESC
    LIMIT 1
");
$stmt->execute([$order_id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

$id_pesanan = $row ? (int)$row['id_pesanan'] : 0;
$id_pembayaran = $row ? (int)$row['id_pembayaran'] : 0;

// fallback kalau record pembayaran belum ada (jarang terjadi)
if ($id_pesanan === 0) {
    $stmt2 = $pdo->prepare("SELECT id_pesanan FROM pesanan WHERE kode_pesanan = ? LIMIT 1");
    $stmt2->execute([$order_id]);
    $r2 = $stmt2->fetch(PDO::FETCH_ASSOC);
    $id_pesanan = $r2 ? (int)$r2['id_pesanan'] : 0;
}

if ($id_pesanan === 0) {
    // order_id nggak dikenal di DB kamu
    http_response_code(404);
    echo "Order not found";
    exit;
}

// 3) MAPPING STATUS MIDTRANS -> STATUS DB
// pesanan.payment_status: unpaid|pending|paid|failed|expired|refunded
$newPesananStatus = 'pending';
$newPembayaranStatus = 'pending';

if ($tx_status === 'settlement') {
    $newPesananStatus = 'paid';
    $newPembayaranStatus = 'paid';
} elseif ($tx_status === 'capture') {
    // kartu kredit: capture bisa sukses, cek fraud_status kalau ada
    if ($fraud_status === null || $fraud_status === '' || $fraud_status === 'accept') {
        $newPesananStatus = 'paid';
        $newPembayaranStatus = 'paid';
    } else {
        $newPesananStatus = 'pending';
        $newPembayaranStatus = 'pending';
    }
} elseif ($tx_status === 'pending') {
    $newPesananStatus = 'pending';
    $newPembayaranStatus = 'pending';
} elseif ($tx_status === 'expire') {
    $newPesananStatus = 'expired';
    $newPembayaranStatus = 'failed';
} elseif ($tx_status === 'cancel' || $tx_status === 'deny') {
    $newPesananStatus = 'failed';
    $newPembayaranStatus = 'failed';
} elseif ($tx_status === 'refund' || $tx_status === 'partial_refund') {
    $newPesananStatus = 'refunded';
    $newPembayaranStatus = 'refunded';
}

// 4) UPDATE DB
try {
    $pdo->beginTransaction();

    // update pesanan
    if ($newPesananStatus === 'paid') {
        $stmtU = $pdo->prepare("
            UPDATE pesanan
            SET payment_status = ?, paid_at = NOW()
            WHERE id_pesanan = ?
        ");
        $stmtU->execute([$newPesananStatus, $id_pesanan]);
    } else {
        $stmtU = $pdo->prepare("
            UPDATE pesanan
            SET payment_status = ?
            WHERE id_pesanan = ?
        ");
        $stmtU->execute([$newPesananStatus, $id_pesanan]);
    }

    // update pembayaran terakhir (kalau ketemu)
    if ($id_pembayaran > 0) {
        // settlement_time dari midtrans kadang null, jadi kalau paid set NOW()
        $stmtB = $pdo->prepare("
            UPDATE pembayaran
            SET status = ?,
                settlement_time = CASE WHEN ? = 'paid' THEN COALESCE(?, NOW()) ELSE settlement_time END,
                provider_transaction_id = COALESCE(?, provider_transaction_id),
                raw_response = ?
            WHERE id_pembayaran = ?
        ");
        $stmtB->execute([
            $newPembayaranStatus,
            $newPembayaranStatus,
            $settlement_time,
            $transaction_id,
            json_encode($data),
            $id_pembayaran
        ]);
    }

    $pdo->commit();

    http_response_code(200);
    echo "OK";
} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    http_response_code(500);
    echo "DB error: " . $e->getMessage();
}