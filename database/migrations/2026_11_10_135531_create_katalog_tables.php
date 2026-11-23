<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kategori', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kategori', 50);
            $table->string('slug', 60)->unique();
            $table->timestamps();
        });

        Schema::create('produk', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kategori_id')->constrained('kategori')->restrictOnDelete();
            $table->string('nama_produk');
            $table->string('slug')->unique();
            $table->text('deskripsi');
            $table->string('gambar')->nullable();
            $table->string('ukuran_foto', 30)->nullable();
            $table->unsignedInteger('stok')->default(0);
            $table->boolean('aktif')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('ukuran_produk', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produk_id')->constrained('produk')->cascadeOnDelete();
            $table->string('nama_ukuran', 30);               // Small / Medium / Large
            $table->string('label', 30)->nullable();         // Hemat / Paling pas / Paling mewah
            $table->unsignedInteger('harga');                // beda di setiap produk
            $table->string('keterangan')->nullable();
            $table->json('detail')->nullable();              // isi tabel perbandingan
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ukuran_produk');
        Schema::dropIfExists('produk');
        Schema::dropIfExists('kategori');
    }
};