<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * "Harga lain" untuk produk tertentu (Bucket Snack): pelanggan boleh mengetik harga sendiri,
 * minimal produk.harga_bebas_min. Harga ketikan disimpan di keranjang.harga_bebas.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('produk', function (Blueprint $table) {
            $table->unsignedInteger('harga_bebas_min')->nullable()->after('varian_pilihan');
        });
        Schema::table('keranjang', function (Blueprint $table) {
            $table->unsignedInteger('harga_bebas')->nullable()->after('ukuran_produk_id');
        });
    }

    public function down(): void
    {
        Schema::table('keranjang', fn (Blueprint $t) => $t->dropColumn('harga_bebas'));
        Schema::table('produk', fn (Blueprint $t) => $t->dropColumn('harga_bebas_min'));
    }
};
