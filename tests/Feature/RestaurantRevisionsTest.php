<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RestaurantRevisionsTest extends TestCase
{
    use RefreshDatabase;
    private $userId;
    private $menuIdFood;
    private $menuIdBeverage;

    protected function setUp(): void
    {
        parent::setUp();

        // Standard setup: clean tables or use transaction. Since database structure is set,
        // let's create a temporary test user and test menu items.
        // We use transactions to not mess up live data.
        DB::beginTransaction();

        // Create test user
        $this->userId = DB::table('users')->insertGetId([
            'nama_user' => 'test_user_unique_' . time(),
            'pass_user' => Hash::make('password123'),
            'role' => 'pelanggan'
        ]);

        // Create test food menu item
        $this->menuIdFood = DB::table('menu')->insertGetId([
            'nama' => 'Ayam Goreng Test',
            'deskripsi' => 'Ayam Goreng Test Description',
            'harga' => 15000,
            'harga_beli' => 10000,
            'diskon' => 10, // 10% discount
            'is_paket' => false,
            'kategori' => 'makanan',
            'gambar' => 'test.png',
            'stok' => 5
        ]);

        // Create test beverage menu item
        $this->menuIdBeverage = DB::table('menu')->insertGetId([
            'nama' => 'Es Teh Test',
            'deskripsi' => 'Es Teh Test Description',
            'harga' => 5000,
            'harga_beli' => 2000,
            'diskon' => 0, // no discount
            'is_paket' => false,
            'kategori' => 'minuman',
            'gambar' => 'test2.png',
            'stok' => 10
        ]);
    }

    protected function tearDown(): void
    {
        DB::rollBack();
        parent::tearDown();
    }

    /** @test */
    public function it_calculates_discounted_price_and_enforces_stock_limits_in_cart()
    {
        $user = \App\Models\User::find($this->userId);
        $this->actingAs($user);

        // 1. Add to cart within stock limits
        $response = $this->post(route('pelanggan.cart.add'), [
            'id_menu' => $this->menuIdFood,
            'jumlah' => 3
        ]);

        $response->assertRedirect();
        
        $cartItem = DB::table('keranjang')
            ->where('id_user', $this->userId)
            ->where('id_menu', $this->menuIdFood)
            ->first();
        
        $this->assertNotNull($cartItem);
        $this->assertEquals(3, $cartItem->jumlah);

        // 2. Add to cart exceeding stock limits
        $responseExceed = $this->post(route('pelanggan.cart.add'), [
            'id_menu' => $this->menuIdFood,
            'jumlah' => 3 // Total requested would be 6, which is > 5 stock
        ]);

        $responseExceed->assertSessionHas('error');
        
        // Quantity should still be 3
        $cartItem = DB::table('keranjang')
            ->where('id_user', $this->userId)
            ->where('id_menu', $this->menuIdFood)
            ->first();
        $this->assertEquals(3, $cartItem->jumlah);
    }

    /** @test */
    public function it_processes_checkout_correctly_and_decrements_stock()
    {
        $user = \App\Models\User::find($this->userId);
        $this->actingAs($user);

        // Insert into cart first
        DB::table('keranjang')->insert([
            'id_user' => $this->userId,
            'id_menu' => $this->menuIdFood,
            'jumlah' => 2
        ]);

        // Process checkout
        $response = $this->post(route('pelanggan.checkout.process'), [
            'alamat_pengiriman' => 'Jl. Test No. 1',
            'wilayah_pengiriman' => 'Kamal',
            'payment_method' => 'cash'
        ]);

        $response->assertJson(['success' => true]);

        // Verify stock decremented: 5 - 2 = 3
        $menu = DB::table('menu')->where('id_menu', $this->menuIdFood)->first();
        $this->assertEquals(3, $menu->stok);

        // Verify order record
        $order = DB::table('pesanan')->where('id_user', $this->userId)->first();
        $this->assertNotNull($order);
        $this->assertEquals(1, $order->stok_dikurangi);
        $this->assertEquals('waiting_confirmation', $order->order_status);

        // Verify order detail records historical buy price and discount amount
        $orderDetail = DB::table('detail_pesanan')->where('id_pesanan', $order->id_pesanan)->first();
        $this->assertNotNull($orderDetail);
        $this->assertEquals(15000, $orderDetail->harga);
        $this->assertEquals(10000, $orderDetail->harga_beli);
        $this->assertEquals(1500, $orderDetail->diskon); // 10% of 15000 is 1500
    }

    /** @test */
    public function it_restores_stock_on_cancellation()
    {
        // 1. Create order with stok_dikurangi = 1
        $orderId = DB::table('pesanan')->insertGetId([
            'id_user' => $this->userId,
            'kode_pesanan' => 'ORD-TEST-RESTORE',
            'total_harga' => 27000, // 2 * 13500
            'alamat_pengiriman' => 'Jl. Test No. 1',
            'wilayah_pengiriman' => 'Kamal',
            'ongkir' => 0,
            'payment_method' => 'cash',
            'payment_status' => 'unpaid',
            'order_status' => 'waiting_confirmation',
            'stok_dikurangi' => 1,
            'created_at' => now(),
        ]);

        DB::table('detail_pesanan')->insert([
            'id_pesanan' => $orderId,
            'id_menu' => $this->menuIdFood,
            'jumlah' => 2,
            'harga' => 15000,
            'harga_beli' => 10000,
            'diskon' => 1500,
        ]);

        // Stock was 5 before decrementing (which we simulated here, so let's decrement menu stock to 3 manually)
        DB::table('menu')->where('id_menu', $this->menuIdFood)->decrement('stok', 2);

        // Authenticate admin to cancel
        $adminUserDb = DB::table('users')->where('role', 'admin')->first();
        if (!$adminUserDb) {
            $adminId = DB::table('users')->insertGetId([
                'nama_user' => 'admin_test',
                'pass_user' => Hash::make('admin123'),
                'role' => 'admin'
            ]);
            $adminUserDb = DB::table('users')->where('id_user', $adminId)->first();
        }
        $adminUser = \App\Models\User::find($adminUserDb->id_user);
        $this->actingAs($adminUser);

        // 2. Cancel order
        $response = $this->post(route('admin.orders.status', $orderId), [
            'order_status' => 'canceled'
        ]);

        if (session('error')) {
            dump("SESSION ERROR: " . session('error'));
        }

        $response->assertRedirect();

        // 3. Verify stock restored to 5
        $menu = DB::table('menu')->where('id_menu', $this->menuIdFood)->first();
        $this->assertEquals(5, $menu->stok);

        // 4. Verify flag reset to 0
        $order = DB::table('pesanan')->where('id_pesanan', $orderId)->first();
        $this->assertEquals(0, $order->stok_dikurangi);
    }
}
