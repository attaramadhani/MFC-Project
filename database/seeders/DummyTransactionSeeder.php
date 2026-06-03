<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DummyTransactionSeeder extends Seeder
{
    public function run(): void
    {
        // ============================================================
        // 1. INSERT DUMMY BUYER ACCOUNTS
        // ============================================================
        $buyers = [
            ['nama_user' => 'budi_santoso',   'pass_user' => Hash::make('dummy123'), 'role' => 'pelanggan'],
            ['nama_user' => 'siti_rahmawati', 'pass_user' => Hash::make('dummy123'), 'role' => 'pelanggan'],
            ['nama_user' => 'agus_wijaya',    'pass_user' => Hash::make('dummy123'), 'role' => 'pelanggan'],
            ['nama_user' => 'dewi_lestari',   'pass_user' => Hash::make('dummy123'), 'role' => 'pelanggan'],
            ['nama_user' => 'rizky_pratama',  'pass_user' => Hash::make('dummy123'), 'role' => 'pelanggan'],
        ];

        $buyerIds = [];
        foreach ($buyers as $buyer) {
            $exists = DB::table('users')->where('nama_user', $buyer['nama_user'])->first();
            if ($exists) {
                $buyerIds[] = $exists->id_user;
            } else {
                $buyerIds[] = DB::table('users')->insertGetId(array_merge($buyer, [
                    'dibuat_pada' => now(),
                ]), 'id_user');
            }
        }

        // ============================================================
        // 2. Get existing menu items
        // ============================================================
        $menus = DB::table('menu')->get();
        if ($menus->isEmpty()) {
            echo "No menu items found. Please seed menu first.\n";
            return;
        }

        // ============================================================
        // 3. INSERT DUMMY TRANSACTIONS (various statuses & dates)
        // ============================================================
        $wilayahList = ['Kamal', 'Telang', 'Socah'];
        $ongkirMap   = ['Kamal' => 3000, 'Telang' => 5000, 'Socah' => 10000];

        $transactions = [
            // Completed orders (paid & completed) - spread across last 3 months
            [
                'buyer_idx'  => 0, // budi_santoso
                'items'      => [[1, 2], [12, 1]], // Geprek Hemat x2, Le Minerale x1
                'wilayah'    => 'Kamal',
                'payment'    => 'paid',
                'order'      => 'completed',
                'days_ago'   => 45,
                'method'     => 'midtrans',
            ],
            [
                'buyer_idx'  => 1, // siti_rahmawati
                'items'      => [[5, 1], [7, 1], [13, 2]], // Gangnam Hemat, Gangnam Double, Teh Pucuk x2
                'wilayah'    => 'Telang',
                'payment'    => 'paid',
                'order'      => 'completed',
                'days_ago'   => 38,
                'method'     => 'midtrans',
            ],
            [
                'buyer_idx'  => 2, // agus_wijaya
                'items'      => [[2, 3], [8, 2]], // Geprek Jumbo x3, Paha Bawah x2
                'wilayah'    => 'Socah',
                'payment'    => 'paid',
                'order'      => 'completed',
                'days_ago'   => 30,
                'method'     => 'cod',
            ],
            [
                'buyer_idx'  => 3, // dewi_lestari
                'items'      => [[3, 2], [4, 1], [12, 3]], // Crispy Hemat x2, Crispy Jumbo, Le Minerale x3
                'wilayah'    => 'Kamal',
                'payment'    => 'paid',
                'order'      => 'completed',
                'days_ago'   => 22,
                'method'     => 'midtrans',
            ],
            [
                'buyer_idx'  => 4, // rizky_pratama
                'items'      => [[6, 2], [9, 1], [13, 1]], // Gangnam Jumbo x2, Sayap, Teh Pucuk
                'wilayah'    => 'Telang',
                'payment'    => 'paid',
                'order'      => 'completed',
                'days_ago'   => 15,
                'method'     => 'midtrans',
            ],
            [
                'buyer_idx'  => 0, // budi_santoso (repeat)
                'items'      => [[1, 1], [10, 1], [13, 2]], // Geprek Hemat, Paha Atas, Teh Pucuk x2
                'wilayah'    => 'Kamal',
                'payment'    => 'paid',
                'order'      => 'completed',
                'days_ago'   => 10,
                'method'     => 'cod',
            ],
            [
                'buyer_idx'  => 2, // agus_wijaya (repeat)
                'items'      => [[5, 2], [11, 1]], // Gangnam Hemat x2, Dada
                'wilayah'    => 'Socah',
                'payment'    => 'paid',
                'order'      => 'completed',
                'days_ago'   => 7,
                'method'     => 'midtrans',
            ],
            [
                'buyer_idx'  => 1, // siti_rahmawati
                'items'      => [[4, 1], [10, 2], [12, 2]], // Crispy Jumbo, Paha Atas x2, Le Minerale x2
                'wilayah'    => 'Kamal',
                'payment'    => 'paid',
                'order'      => 'completed',
                'days_ago'   => 3,
                'method'     => 'midtrans',
            ],
            // Processing order
            [
                'buyer_idx'  => 3, // dewi_lestari
                'items'      => [[2, 1], [6, 1], [13, 1]], // Geprek Jumbo, Gangnam Jumbo, Teh Pucuk
                'wilayah'    => 'Telang',
                'payment'    => 'paid',
                'order'      => 'processing',
                'days_ago'   => 0,
                'method'     => 'midtrans',
            ],
            // Canceled order (with stock restore)
            [
                'buyer_idx'  => 4, // rizky_pratama
                'items'      => [[1, 3], [12, 2]], // Geprek Hemat x3, Le Minerale x2
                'wilayah'    => 'Socah',
                'payment'    => 'failed',
                'order'      => 'canceled',
                'days_ago'   => 5,
                'method'     => 'midtrans',
            ],
        ];

        $menuMap = $menus->keyBy('id_menu');
        $menuIds = $menus->pluck('id_menu')->toArray();

        $wilayahList = ['Kamal', 'Telang', 'Socah'];
        $ongkirMap   = ['Kamal' => 3000, 'Telang' => 5000, 'Socah' => 10000];

        // Generate 115 random transactions to ensure > 100 orders
        $totalToGenerate = 115;

        for ($i = 0; $i < $totalToGenerate; $i++) {
            $buyerIdx = array_rand($buyerIds);
            $userId  = $buyerIds[$buyerIdx];
            $wilayah = $wilayahList[array_rand($wilayahList)];
            $ongkir  = $ongkirMap[$wilayah];
            
            // Random days ago between 0 to 60 days ago
            $daysAgo = rand(0, 60);
            $baseDate = Carbon::now()->subDays($daysAgo)->subHours(rand(0, 23))->subMinutes(rand(0, 59));

            // Pick 1 to 4 random menu items
            $numItems = rand(1, 4);
            $selectedMenus = array_rand(array_flip($menuIds), min($numItems, count($menuIds)));
            if (!is_array($selectedMenus)) {
                $selectedMenus = [$selectedMenus];
            }

            $items = [];
            foreach ($selectedMenus as $menuId) {
                $items[] = [$menuId, rand(1, 3)]; // Qty 1-3
            }

            // Calculate total
            $subtotal = 0;
            foreach ($items as [$menuId, $qty]) {
                if (isset($menuMap[$menuId])) {
                    $menu = $menuMap[$menuId];
                    $hargaJual = $menu->harga;
                    $diskon = $menu->diskon ?? 0;
                    $hargaFinal = $diskon > 0 ? round($hargaJual * (100 - $diskon) / 100) : $hargaJual;
                    $subtotal += $hargaFinal * $qty;
                }
            }
            $totalHarga = $subtotal + $ongkir;

            // Use unique prefix for these generated dummy transactions
            $kode = 'MFC-GEN-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT);

            $paymentMethod = rand(0, 1) ? 'midtrans' : 'cod';
            $paidAt = $baseDate->copy()->addMinutes(rand(2, 15));
            $processedAt = $paidAt->copy()->addMinutes(rand(5, 10));
            $readyAt = $processedAt->copy()->addMinutes(rand(10, 20));
            $completedAt = $readyAt->copy()->addMinutes(rand(15, 30));

            // Check if already exists
            if (DB::table('pesanan')->where('kode_pesanan', $kode)->exists()) {
                continue;
            }

            $pesananId = DB::table('pesanan')->insertGetId([
                'id_user'            => $userId,
                'kode_pesanan'       => $kode,
                'total_harga'        => $totalHarga,
                'payment_status'     => 'paid',
                'order_status'       => 'completed',
                'stok_dikurangi'     => DB::raw('TRUE'),
                'alamat_pengiriman'  => 'Jl. Random No. ' . ($i + 1) . ', ' . $wilayah,
                'wilayah_pengiriman' => $wilayah,
                'ongkir'             => $ongkir,
                'payment_method'     => $paymentMethod,
                'created_at'         => $baseDate,
                'updated_at'         => $completedAt,
                'paid_at'            => $paidAt,
                'processed_at'       => $processedAt,
                'ready_at'           => $readyAt,
                'completed_at'       => $completedAt,
                'canceled_at'        => null,
            ], 'id_pesanan');

            // Insert detail_pesanan
            foreach ($items as [$menuId, $qty]) {
                if (isset($menuMap[$menuId])) {
                    $menu = $menuMap[$menuId];
                    $hargaJual = $menu->harga;
                    $hargaBeli = $menu->harga_beli ?? 0;
                    $diskon = $menu->diskon ?? 0;
                    $hargaFinal = $diskon > 0 ? round($hargaJual * (100 - $diskon) / 100) : $hargaJual;
                    $diskonAbsolut = $hargaJual - $hargaFinal;

                    DB::table('detail_pesanan')->insert([
                        'id_pesanan'  => $pesananId,
                        'id_menu'     => $menuId,
                        'jumlah'      => $qty,
                        'harga'       => $hargaFinal,
                        'harga_beli'  => $hargaBeli,
                        'diskon'      => $diskonAbsolut,
                        'catatan_item'=> null,
                    ]);
                }
            }

            // Insert pembayaran record
            DB::table('pembayaran')->insert([
                'id_pesanan'              => $pesananId,
                'provider'                => $paymentMethod === 'cod' ? 'cod' : 'midtrans',
                'metode'                  => $paymentMethod === 'cod' ? 'cash' : 'bank_transfer',
                'gross_amount'            => $totalHarga,
                'status'                  => 'paid',
                'transaction_time'        => $baseDate,
                'settlement_time'         => $paidAt,
                'provider_order_id'       => $kode,
                'provider_transaction_id' => 'GEN-TXN-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'raw_response'            => json_encode(['dummy' => true]),
                'created_at'              => $baseDate,
                'updated_at'              => $paidAt,
            ]);
        }

        echo "Over 100 random paid dummy transactions generated and seeded successfully!\n";
    }
}
