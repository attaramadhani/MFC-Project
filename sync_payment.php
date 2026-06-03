<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

echo "--- MFC AUTO SYNC PAYMENT ---\n";

// Ambil semua pesanan yang statusnya masih pending/unpaid
$pesanans = DB::table('pesanan')
    ->where('payment_status', 'pending')
    ->orWhere('payment_status', 'unpaid')
    ->orderByDesc('id_pesanan')
    ->get();

if ($pesanans->isEmpty()) {
    echo "Tidak ada pesanan pending yang perlu disinkronkan.\n";
    exit;
}

echo "Ditemukan " . $pesanans->count() . " pesanan pending. Memulai pengecekan...\n\n";

$serverKey = env('MIDTRANS_SERVER_KEY');
$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
$baseUrl = $isProduction 
    ? 'https://api.midtrans.com/v2' 
    : 'https://api.sandbox.midtrans.com/v2';

foreach ($pesanans as $pesanan) {
    $id_pesanan = $pesanan->id_pesanan;
    $pembayaran = DB::table('pembayaran')
        ->where('id_pesanan', $id_pesanan)
        ->orderByDesc('id_pembayaran')
        ->first();

    $order_id = $pembayaran->provider_order_id ?? $pesanan->kode_pesanan;
    
    echo "Checking Order ID: $order_id (ID Pesanan: $id_pesanan)... ";

    try {
        $url = "$baseUrl/$order_id/status";
        $response = Http::withoutVerifying()
            ->withBasicAuth($serverKey, '')
            ->get($url);

        if ($response->successful()) {
            $data = $response->json();
            $status = $data['transaction_status'] ?? '';
            echo "Status: $status. ";

            if ($status === 'settlement' || $status === 'capture') {
                DB::beginTransaction();
                DB::table('pesanan')->where('id_pesanan', $id_pesanan)->update([
                    'payment_status' => 'paid',
                    'paid_at' => $data['settlement_time'] ?? now()
                ]);

                if ($pembayaran) {
                    DB::table('pembayaran')->where('id_pembayaran', $pembayaran->id_pembayaran)->update([
                        'status' => 'paid',
                        'settlement_time' => $data['settlement_time'] ?? now(),
                        'provider_transaction_id' => $data['transaction_id'] ?? null,
                        'raw_response' => json_encode($data)
                    ]);
                } else {
                    DB::table('pembayaran')->insert([
                        'id_pesanan' => $id_pesanan,
                        'provider' => 'midtrans',
                        'metode' => $data['payment_type'] ?? 'online',
                        'gross_amount' => $data['gross_amount'] ?? $pesanan->total_harga,
                        'status' => 'paid',
                        'transaction_time' => $data['transaction_time'] ?? now(),
                        'settlement_time' => $data['settlement_time'] ?? now(),
                        'provider_order_id' => $order_id,
                        'provider_transaction_id' => $data['transaction_id'] ?? null,
                        'raw_response' => json_encode($data),
                        'created_at' => now(),
                    ]);
                }
                DB::commit();
                echo "UPDATED TO PAID ✅\n";
            } else {
                echo "No update needed.\n";
            }
        } else {
            echo "Failed to connect (HTTP " . $response->status() . ")\n";
        }
    } catch (\Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
}

echo "\n--- SYNC SELESAI ---\n";
