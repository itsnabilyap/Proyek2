<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Revisi dosen: foto produk dipisah ke tabel sendiri.
 * 1 produk punya banyak foto. Foto dengan urutan terkecil = sampul (tampil di kartu katalog).
 * Foto lama di kolom produk.gambar dipindah ke tabel baru, lalu kolomnya dihapus.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gambar_produk', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produk_id')->constrained('produk')->cascadeOnDelete();
            $table->string('file');
            $table->unsignedTinyInteger('urutan')->default(0);
            $table->timestamps();

            $table->index(['produk_id', 'urutan']);
        });

        $sekarang = now();
        DB::table('produk')->whereNotNull('gambar')->where('gambar', '!=', '')->orderBy('id')
            ->each(function ($p) use ($sekarang) {
                DB::table('gambar_produk')->insert([
                    'produk_id' => $p->id, 'file' => $p->gambar, 'urutan' => 0,
                    'created_at' => $sekarang, 'updated_at' => $sekarang,
                ]);
            });

        Schema::table('produk', fn (Blueprint $table) => $table->dropColumn('gambar'));
    }

    public function down(): void
    {
        Schema::table('produk', fn (Blueprint $table) => $table->string('gambar')->nullable()->after('harga_bebas_min'));

        // Sampul dikembalikan ke kolom produk.gambar
        DB::table('gambar_produk')->orderBy('urutan')->get()->groupBy('produk_id')
            ->each(fn ($foto, $id) => DB::table('produk')->where('id', $id)->update(['gambar' => $foto->first()->file]));

        Schema::dropIfExists('gambar_produk');
    }
};
