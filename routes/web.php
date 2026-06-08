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

// Temporary route to migrate and seed production database
Route::get('/run-migrations', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        \Illuminate\Support\Facades\Artisan::call('db:seed', ['--class' => 'AdminSeeder', '--force' => true]);
        return "Migrations and Seeding completed successfully. Admin password has been reset to 'password'.";
    } catch (\Exception $e) {
        return "Error: " . $e->getMessage();
    }
});
