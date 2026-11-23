<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Desain katalog & custom baru:
 * - pelanggan memilih warna bunga & kertas di halaman detail → disimpan di keranjang & detail pesanan
 * - form custom punya isian "Warna & tema" dan upload foto referensi
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('keranjang', function (Blueprint $table) {
            $table->string('warna', 100)->nullable()->after('varian');          // "Bunga pink pastel · kertas putih susu"
        });

        Schema::table('detail_pesanan', function (Blueprint $table) {
            $table->string('warna', 100)->nullable()->after('varian');
        });

        Schema::table('custom', function (Blueprint $table) {
            $table->string('tema', 150)->nullable()->after('budget');           // "pink pastel, kertas putih"
            $table->string('foto_referensi')->nullable()->after('catatan');     // nama file di public/images/custom
        });
    }

    public function down(): void
    {
        Schema::table('custom', fn (Blueprint $t) => $t->dropColumn(['tema', 'foto_referensi']));
        Schema::table('detail_pesanan', fn (Blueprint $t) => $t->dropColumn('warna'));
        Schema::table('keranjang', fn (Blueprint $t) => $t->dropColumn('warna'));
    }
};
