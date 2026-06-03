<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$pesanans = DB::table('pesanan')
    ->leftJoin('pembayaran', 'pesanan.id_pesanan', '=', 'pembayaran.id_pesanan')
    ->whereNull('pembayaran.id_pembayaran')
    ->select('pesanan.*')
    ->get();

$inserts = [];
foreach($pesanans as $p) {
    $inserts[] = [
        'id_pesanan' => $p->id_pesanan,
        'provider' => 'midtrans',
        'metode' => 'qris',
        'gross_amount' => $p->total_harga,
        'status' => 'paid',
        'transaction_time' => $p->created_at,
        'settlement_time' => $p->created_at,
        'created_at' => $p->created_at,
        'updated_at' => $p->created_at
    ];
}

if(!empty($inserts)) {
    DB::table('pembayaran')->insert($inserts);
}

echo 'Inserted ' . count($inserts) . " missing payments.\n";
