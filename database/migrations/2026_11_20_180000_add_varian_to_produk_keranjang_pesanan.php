<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Pricelist mitra:
 * - ukuran tidak lagi dikunci S/M/L → ukuran_foto jadi teks bebas (sudah string, tidak perlu diubah)
 * - varian per produk, contoh "Merek coklat": Dairy Milk / Silverqueen
 * - varian yang dipilih disimpan di keranjang & detail pesanan
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('produk', function (Blueprint $table) {
            $table->string('varian_judul', 50)->nullable()->after('deskripsi');   // "Merek coklat"
            $table->json('varian_pilihan')->nullable()->after('varian_judul');   // ["Dairy Milk","Silverqueen"]
        });

        Schema::table('keranjang', function (Blueprint $table) {
            $table->string('varian', 50)->nullable()->after('jumlah');
        });

        Schema::table('detail_pesanan', function (Blueprint $table) {
            $table->string('varian', 50)->nullable()->after('nama_ukuran');
        });
    }

    public function down(): void
    {
        Schema::table('detail_pesanan', fn (Blueprint $t) => $t->dropColumn('varian'));
        Schema::table('keranjang', fn (Blueprint $t) => $t->dropColumn('varian'));
        Schema::table('produk', fn (Blueprint $t) => $t->dropColumn(['varian_judul', 'varian_pilihan']));
    }
};
