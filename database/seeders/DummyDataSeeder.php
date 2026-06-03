<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Menu;
use Carbon\Carbon;
use Illuminate\Support\Str;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        $faker = \Faker\Factory::create('id_ID');
        $usersData = [];
        $accountsDoc = "Daftar Akun Pembeli:\n\n";

        // Tambah 100 akun pembeli
        for ($i = 0; $i < 100; $i++) {
            $username = $faker->unique()->userName;
            $password = Str::random(8);
            
            $user = User::create([
                'nama_user' => $username,
                'pass_user' => Hash::make($password),
                'role' => 'pelanggan'
            ]);

            $usersData[] = $user;
            $accountsDoc .= "Username: " . $username . "\nPassword: " . $password . "\n\n";
        }

        // Simpan dokumen ke file
        file_put_contents(base_path('akun_pembeli.txt'), $accountsDoc);

        $menus = Menu::all();
        $statuses = ['created', 'waiting_confirmation', 'processing', 'ready', 'completed', 'canceled'];
        $paymentStatuses = ['unpaid', 'pending', 'paid', 'failed', 'expired', 'refunded'];

        // Tambah pesanan random
        for ($i = 0; $i < 200; $i++) { // 200 pesanan random
            $user = $faker->randomElement($usersData);
            $orderStatus = $faker->randomElement($statuses);
            $paymentStatus = $faker->randomElement($paymentStatuses);
            
            // Adjust logic so it makes sense (e.g. completed must be paid)
            if ($orderStatus == 'completed') $paymentStatus = 'paid';
            if ($orderStatus == 'ready' || $orderStatus == 'processing') $paymentStatus = 'paid';

            $numItems = rand(1, 5);
            $totalPrice = 0;
            $items = [];

            for ($j = 0; $j < $numItems; $j++) {
                $menu = $faker->randomElement($menus);
                $qty = rand(1, 4);
                $subtotal = $menu->harga * $qty;
                $totalPrice += $subtotal;

                $items[] = [
                    'id_menu' => $menu->id_menu,
                    'jumlah' => $qty,
                    'harga' => $menu->harga,
                    'catatan_item' => $faker->optional(0.3)->sentence()
                ];
            }

            $date = $faker->dateTimeBetween('-1 month', 'now');

            $pesananId = DB::table('pesanan')->insertGetId([
                'id_user' => $user->id_user,
                'kode_pesanan' => 'ORD-' . strtoupper(Str::random(8)),
                'total_harga' => $totalPrice,
                'payment_status' => $paymentStatus,
                'order_status' => $orderStatus,
                'created_at' => $date,
                'updated_at' => $date,
                'paid_at' => ($paymentStatus == 'paid') ? $date : null,
                'processed_at' => in_array($orderStatus, ['processing', 'ready', 'completed']) ? $date : null,
                'ready_at' => in_array($orderStatus, ['ready', 'completed']) ? $date : null,
                'completed_at' => ($orderStatus == 'completed') ? $date : null,
                'canceled_at' => ($orderStatus == 'canceled') ? $date : null,
            ]);

            foreach ($items as &$item) {
                $item['id_pesanan'] = $pesananId;
            }
            
            DB::table('detail_pesanan')->insert($items);

            if ($paymentStatus != 'unpaid') {
                DB::table('pembayaran')->insert([
                    'id_pesanan' => $pesananId,
                    'provider' => 'midtrans',
                    'metode' => $faker->randomElement(['qris', 'bank_transfer', 'gopay', 'shopeepay']),
                    'gross_amount' => $totalPrice,
                    'status' => $paymentStatus == 'paid' ? 'paid' : ($paymentStatus == 'failed' ? 'failed' : 'pending'),
                    'transaction_time' => $date,
                    'settlement_time' => ($paymentStatus == 'paid') ? $date : null,
                    'created_at' => $date,
                    'updated_at' => $date,
                ]);
            }
        }
    }
}
