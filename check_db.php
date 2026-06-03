<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    $pesanan_count = DB::table('pesanan')->count();
    $last_orders = DB::table('pesanan')->orderByDesc('id_pesanan')->limit(5)->get();
    $last_pay   = DB::table('pembayaran')->orderByDesc('id_pembayaran')->first();
    
    echo "TOTAL PESANAN: " . $pesanan_count . "\n";
    echo "\nLAST 5 ORDERS:\n";
    foreach($last_orders as $o) {
        echo "ID: {$o->id_pesanan}, Total: {$o->total_harga}, Ongkir: {$o->ongkir}, Status: {$o->payment_status}\n";
    }
    echo "\nLAST PAYMENT:\n";
    print_r($last_pay);
    
    $tables = DB::select('SHOW TABLES');
    echo "\nTABLES:\n";
    print_r($tables);
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
