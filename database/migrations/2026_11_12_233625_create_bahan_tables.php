<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bahan', function (Blueprint $table) {
            $table->id();
            $table->string('nama_bahan', 100);
            $table->string('kategori', 30);        // Bunga, Wrapping, Pita & Aksesori, Kartu & Lainnya
            $table->string('satuan', 20);          // tangkai, ikat, lembar, meter, pcs, roll
            $table->unsignedInteger('stok')->default(0);
            $table->unsignedInteger('stok_minimal')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('riwayat_stok', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bahan_id')->constrained('bahan')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('jenis', ['masuk', 'keluar', 'penyesuaian']);
            $table->integer('jumlah');             // + masuk, - keluar
            $table->unsignedInteger('stok_sebelum');
            $table->unsignedInteger('stok_sesudah');
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_stok');
        Schema::dropIfExists('bahan');
    }
};