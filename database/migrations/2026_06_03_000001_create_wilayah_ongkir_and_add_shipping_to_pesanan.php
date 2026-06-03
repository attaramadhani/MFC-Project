<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Create wilayah_ongkir table if not exists
        if (!Schema::hasTable('wilayah_ongkir')) {
            Schema::create('wilayah_ongkir', function (Blueprint $table) {
                $table->id('id_wilayah');
                $table->string('nama_wilayah', 50)->unique();
                $table->integer('ongkir');
            });

            // Seed default values
            DB::table('wilayah_ongkir')->insert([
                ['nama_wilayah' => 'Kamal', 'ongkir' => 3000],
                ['nama_wilayah' => 'Telang', 'ongkir' => 5000],
                ['nama_wilayah' => 'Socah', 'ongkir' => 10000],
            ]);
        }

        // 2. Add columns to pesanan table if not exists
        Schema::table('pesanan', function (Blueprint $table) {
            if (!Schema::hasColumn('pesanan', 'alamat_pengiriman')) {
                $table->text('alamat_pengiriman')->nullable();
            }
            if (!Schema::hasColumn('pesanan', 'wilayah_pengiriman')) {
                $table->string('wilayah_pengiriman', 50)->nullable();
            }
            if (!Schema::hasColumn('pesanan', 'ongkir')) {
                $table->integer('ongkir')->default(0);
            }
            if (!Schema::hasColumn('pesanan', 'payment_method')) {
                $table->string('payment_method', 20)->default('midtrans');
            }
        });

        // 3. Create riwayat_status_pesanan table if not exists
        if (!Schema::hasTable('riwayat_status_pesanan')) {
            Schema::create('riwayat_status_pesanan', function (Blueprint $table) {
                $table->id('id_riwayat');
                $table->unsignedBigInteger('id_pesanan');
                $table->enum('tipe', ['payment', 'order']);
                $table->string('status_lama', 50)->nullable();
                $table->string('status_baru', 50);
                $table->unsignedBigInteger('diubah_oleh')->nullable();
                $table->string('keterangan', 255)->nullable();
                $table->timestamp('dibuat_pada')->useCurrent();

                $table->foreign('id_pesanan')
                    ->references('id_pesanan')
                    ->on('pesanan')
                    ->onDelete('cascade');

                $table->foreign('diubah_oleh')
                    ->references('id_user')
                    ->on('users')
                    ->onDelete('set null');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_status_pesanan');
        Schema::dropIfExists('wilayah_ongkir');

        Schema::table('pesanan', function (Blueprint $table) {
            $table->dropColumn(['alamat_pengiriman', 'wilayah_pengiriman', 'ongkir', 'payment_method']);
        });
    }
};
