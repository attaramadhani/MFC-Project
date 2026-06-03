<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('menu', function (Blueprint $table) {
            $table->integer('stok')->default(0)->after('is_paket');
            $table->decimal('harga_beli', 10, 2)->default(0)->after('harga');
            $table->integer('diskon')->default(0)->after('harga_beli');
        });

        Schema::table('detail_pesanan', function (Blueprint $table) {
            $table->decimal('harga_beli', 10, 2)->default(0)->after('harga');
            $table->decimal('diskon', 10, 2)->default(0)->after('harga_beli');
        });

        Schema::table('pesanan', function (Blueprint $table) {
            $table->boolean('stok_dikurangi')->default(false)->after('payment_status');
        });
    }

    public function down(): void
    {
        Schema::table('menu', function (Blueprint $table) {
            $table->dropColumn(['stok', 'harga_beli', 'diskon']);
        });

        Schema::table('detail_pesanan', function (Blueprint $table) {
            $table->dropColumn(['harga_beli', 'diskon']);
        });

        Schema::table('pesanan', function (Blueprint $table) {
            $table->dropColumn('stok_dikurangi');
        });
    }
};
