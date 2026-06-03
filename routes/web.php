<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MidtransController;

use App\Http\Controllers\Pelanggan\HomeController as PelangganHomeController;
use App\Http\Controllers\Pelanggan\CartController;
use App\Http\Controllers\Pelanggan\CheckoutController;
use App\Http\Controllers\Pelanggan\OrderController as PelangganOrderController;

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ProfileController;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');

    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.process');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');


Route::middleware(['auth', 'role:pelanggan'])->prefix('pelanggan')->name('pelanggan.')->group(function () {
    Route::get('/', [PelangganHomeController::class, 'index'])->name('index');

    Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::get('/cart/content', [CartController::class, 'content'])->name('cart.content');

    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
    Route::post('/checkout/process', [CheckoutController::class, 'process'])->name('checkout.process');

    Route::get('/pesanan', [PelangganOrderController::class, 'index'])->name('orders.index');
    Route::get('/pesanan/{id}', [PelangganOrderController::class, 'show'])->name('orders.show');
    Route::get('/pesanan/{id}/check', [PelangganOrderController::class, 'checkStatus'])->name('orders.check');
    Route::get('/pesanan/{id}/struk', [PelangganOrderController::class, 'receipt'])->name('orders.receipt');

    Route::get('/profile', [\App\Http\Controllers\Pelanggan\ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [\App\Http\Controllers\Pelanggan\ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/password', [\App\Http\Controllers\Pelanggan\ProfileController::class, 'updatePassword'])->name('profile.password');
});


Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/menu', [MenuController::class, 'index'])->name('menu.index');
    Route::get('/menu/create', [MenuController::class, 'create'])->name('menu.create');
    Route::post('/menu', [MenuController::class, 'store'])->name('menu.store');
    Route::get('/menu/{id}/edit', [MenuController::class, 'edit'])->name('menu.edit');
    Route::put('/menu/{id}', [MenuController::class, 'update'])->name('menu.update');
    Route::delete('/menu/{id}', [MenuController::class, 'destroy'])->name('menu.destroy');

    Route::get('/pesanan', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/pesanan/{id}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::post('/pesanan/{id}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.status');

    Route::get('/laporan', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/laporan/export', [ReportController::class, 'export'])->name('reports.export');

    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users/{id}/role', [UserController::class, 'changeRole'])->name('users.role');
    Route::post('/users/{id}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset');

    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
});

Route::post('/midtrans/notification', [MidtransController::class, 'notification'])->name('midtrans.notification');

Route::get('/run-migration-temp', function() {
    try {
        // 1. Run migrations
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        $output = "Migration run successfully:\n" . \Illuminate\Support\Facades\Artisan::output() . "\n";

        // 2. Seed menus if menu table is empty
        $menuCount = \Illuminate\Support\Facades\DB::table('menu')->count();
        if ($menuCount === 0) {
            $jsonPath = base_path('menus_dump.json');
            if (file_exists($jsonPath)) {
                $menus = json_decode(file_get_contents($jsonPath), true);
                foreach ($menus as $menu) {
                    \Illuminate\Support\Facades\DB::table('menu')->insert([
                        'id_menu' => $menu['id_menu'],
                        'nama' => $menu['nama'],
                        'deskripsi' => $menu['deskripsi'],
                        'harga' => $menu['harga'],
                        'is_paket' => (bool)($menu['is_paket'] ?? 0),
                        'kategori' => $menu['kategori'],
                        'gambar' => $menu['gambar'],
                        'dibuat_pada' => $menu['dibuat_pada'] ?? now(),
                        // Add default stock, buy price, and discount for the new revision features
                        'stok' => 20,
                        'harga_beli' => round($menu['harga'] * 0.7, 2),
                        'diskon' => 0,
                    ]);
                }
                $output .= "Seeded " . count($menus) . " menus from menus_dump.json successfully.\n";
            } else {
                $output .= "Warning: menus_dump.json not found, skipping menu seeding.\n";
            }
        } else {
            $output .= "Menu table is not empty ($menuCount items), skipping menu seeding.\n";
        }

        // 3. Seed default admin & pelanggan if users table is empty
        $userCount = \Illuminate\Support\Facades\DB::table('users')->count();
        if ($userCount === 0) {
            \Illuminate\Support\Facades\DB::table('users')->insert([
                [
                    'nama_user' => 'admin',
                    'pass_user' => \Illuminate\Support\Facades\Hash::make('admin123'),
                    'role' => 'admin',
                ],
                [
                    'nama_user' => 'pembeli',
                    'pass_user' => \Illuminate\Support\Facades\Hash::make('pembeli123'),
                    'role' => 'pelanggan',
                ]
            ]);
            $output .= "Seeded default users (admin/admin123, pembeli/pembeli123) successfully.\n";
        } else {
            $output .= "Users table is not empty ($userCount users), skipping user seeding.\n";
        }

        return "<pre>" . htmlspecialchars($output) . "</pre>";
    } catch (\Exception $e) {
        return "Error: " . $e->getMessage() . "\nTrace:\n" . $e->getTraceAsString();
    }
});