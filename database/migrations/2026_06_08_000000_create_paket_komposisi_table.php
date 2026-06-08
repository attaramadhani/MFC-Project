<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paket_komposisi', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_menu_paket');
            $table->unsignedBigInteger('id_menu_komponen');
            $table->integer('jumlah')->default(1);

            $table->foreign('id_menu_paket')->references('id_menu')->on('menu')->onDelete('cascade');
            $table->foreign('id_menu_komponen')->references('id_menu')->on('menu')->onDelete('cascade');
        });

        Schema::table('detail_pesanan', function (Blueprint $table) {
            $table->unsignedBigInteger('id_menu_paket')->nullable()->after('id_menu');
        });
    }

    public function down(): void
    {
        Schema::table('detail_pesanan', function (Blueprint $table) {
            $table->dropColumn('id_menu_paket');
        });

        Schema::dropIfExists('paket_komposisi');
    }
};
