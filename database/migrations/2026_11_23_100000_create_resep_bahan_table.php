<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Resep bahan per ukuran produk, contoh: Bucket Bunga Medium = 6 tangkai mawar + 1 lembar kertas.
 * Stok bahan dipotong sesuai resep saat pesanan selesai dirangkai.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resep_bahan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ukuran_produk_id')->constrained('ukuran_produk')->cascadeOnDelete();
            $table->foreignId('bahan_id')->constrained('bahan')->cascadeOnDelete();
            $table->unsignedInteger('jumlah');
            $table->timestamps();
            $table->unique(['ukuran_produk_id', 'bahan_id']);
        });

        // Penanda supaya stok bahan satu pesanan tidak terpotong dua kali
        Schema::table('pesanan', function (Blueprint $table) {
            $table->timestamp('bahan_dipotong_at')->nullable()->after('status_pesanan');
        });
    }

    public function down(): void
    {
        Schema::table('pesanan', fn (Blueprint $t) => $t->dropColumn('bahan_dipotong_at'));
        Schema::dropIfExists('resep_bahan');
    }
};
