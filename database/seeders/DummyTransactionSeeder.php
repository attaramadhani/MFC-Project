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

        foreach ($transactions as $idx => $tx) {
            $userId  = $buyerIds[$tx['buyer_idx']];
            $wilayah = $tx['wilayah'];
            $ongkir  = $ongkirMap[$wilayah];
            $baseDate = Carbon::now()->subDays($tx['days_ago']);

            // Calculate total
            $subtotal = 0;
            foreach ($tx['items'] as [$menuId, $qty]) {
                if (isset($menuMap[$menuId])) {
                    $menu = $menuMap[$menuId];
                    $hargaJual = $menu->harga;
                    $diskon = $menu->diskon ?? 0;
                    $hargaFinal = $diskon > 0 ? round($hargaJual * (100 - $diskon) / 100) : $hargaJual;
                    $subtotal += $hargaFinal * $qty;
                }
            }
            $totalHarga = $subtotal + $ongkir;

            $kode = 'MFC-DUMMY-' . str_pad($idx + 1, 3, '0', STR_PAD_LEFT);

            $paidAt = in_array($tx['payment'], ['paid']) ? $baseDate->copy()->addMinutes(5) : null;
            $processedAt = in_array($tx['order'], ['processing', 'ready', 'completed']) ? $baseDate->copy()->addMinutes(10) : null;
            $readyAt = in_array($tx['order'], ['ready', 'completed']) ? $baseDate->copy()->addMinutes(20) : null;
            $completedAt = $tx['order'] === 'completed' ? $baseDate->copy()->addMinutes(35) : null;
            $canceledAt = $tx['order'] === 'canceled' ? $baseDate->copy()->addMinutes(15) : null;

            // Check if already exists
            if (DB::table('pesanan')->where('kode_pesanan', $kode)->exists()) {
                continue;
            }

            $pesananId = DB::table('pesanan')->insertGetId([
                'id_user'            => $userId,
                'kode_pesanan'       => $kode,
                'total_harga'        => $totalHarga,
                'payment_status'     => $tx['payment'],
                'order_status'       => $tx['order'],
                'stok_dikurangi'     => DB::raw($tx['payment'] === 'paid' ? 'TRUE' : 'FALSE'),
                'alamat_pengiriman'  => 'Jl. Dummy No. ' . ($idx + 1) . ', ' . $wilayah,
                'wilayah_pengiriman' => $wilayah,
                'ongkir'             => $ongkir,
                'payment_method'     => $tx['method'],
                'created_at'         => $baseDate,
                'updated_at'         => $baseDate,
                'paid_at'            => $paidAt,
                'processed_at'       => $processedAt,
                'ready_at'           => $readyAt,
                'completed_at'       => $completedAt,
                'canceled_at'        => $canceledAt,
            ], 'id_pesanan');

            // Insert detail_pesanan
            foreach ($tx['items'] as [$menuId, $qty]) {
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

            // Insert pembayaran record for paid orders
            if ($tx['payment'] === 'paid' || $tx['payment'] === 'pending') {
                DB::table('pembayaran')->insert([
                    'id_pesanan'              => $pesananId,
                    'provider'                => $tx['method'] === 'cod' ? 'cod' : 'midtrans',
                    'metode'                  => $tx['method'] === 'cod' ? 'cash' : 'bank_transfer',
                    'gross_amount'            => $totalHarga,
                    'status'                  => $tx['payment'] === 'paid' ? 'paid' : 'pending',
                    'transaction_time'        => $baseDate,
                    'settlement_time'         => $tx['payment'] === 'paid' ? $paidAt : null,
                    'provider_order_id'       => $kode,
                    'provider_transaction_id' => 'DUMMY-TXN-' . str_pad($idx + 1, 3, '0', STR_PAD_LEFT),
                    'raw_response'            => json_encode(['dummy' => true]),
                    'created_at'              => $baseDate,
                    'updated_at'              => $paidAt ?? $baseDate,
                ]);
            }
        }

        echo "Dummy transactions seeded successfully!\n";
    }
}
