<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Fitur stok bahan & resep dihapus. Yang dihitung sekarang hanya stok produk (kolom produk.stok).
 * Aman untuk database lama (tabel ada) maupun database baru (tabel tidak pernah dibuat).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('resep_bahan');
        Schema::dropIfExists('riwayat_stok');
        Schema::dropIfExists('bahan');

        if (Schema::hasColumn('pesanan', 'bahan_dipotong_at')) {
            Schema::table('pesanan', fn (Blueprint $t) => $t->dropColumn('bahan_dipotong_at'));
        }
    }

    public function down(): void
    {
        // Tidak dikembalikan: fitur bahan sudah dihapus dari kode.
    }
};
