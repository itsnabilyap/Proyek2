<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Revisi dosen #2 (lanjutan): tiap foto bisa ditandai "foto ukuran apa".
 * Saat pelanggan memilih ukuran di halaman detail, foto utama ikut berganti.
 * Kolom lama produk.ukuran_foto (satu label per produk) dipindah ke foto sampul, lalu dihapus.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gambar_produk', function (Blueprint $table) {
            $table->foreignId('ukuran_produk_id')->nullable()->after('produk_id')
                ->constrained('ukuran_produk')->nullOnDelete();
        });

        DB::table('produk')->whereNotNull('ukuran_foto')->where('ukuran_foto', '!=', '')->orderBy('id')
            ->each(function ($p) {
                $ukuranId = DB::table('ukuran_produk')->where('produk_id', $p->id)
                    ->whereRaw('LOWER(nama_ukuran) = ?', [mb_strtolower(trim($p->ukuran_foto))])->value('id');
                $sampulId = DB::table('gambar_produk')->where('produk_id', $p->id)->orderBy('urutan')->orderBy('id')->value('id');
                if ($ukuranId && $sampulId) {
                    DB::table('gambar_produk')->where('id', $sampulId)->update(['ukuran_produk_id' => $ukuranId]);
                }
            });

        Schema::table('produk', fn (Blueprint $table) => $table->dropColumn('ukuran_foto'));
    }

    public function down(): void
    {
        Schema::table('produk', fn (Blueprint $table) => $table->string('ukuran_foto', 30)->nullable());

        DB::table('gambar_produk')->join('ukuran_produk', 'ukuran_produk.id', '=', 'gambar_produk.ukuran_produk_id')
            ->orderBy('gambar_produk.urutan')->get(['gambar_produk.produk_id', 'ukuran_produk.nama_ukuran'])
            ->unique('produk_id')
            ->each(fn ($r) => DB::table('produk')->where('id', $r->produk_id)->update(['ukuran_foto' => $r->nama_ukuran]));

        Schema::table('gambar_produk', fn (Blueprint $table) => $table->dropConstrainedForeignId('ukuran_produk_id'));
    }
};
