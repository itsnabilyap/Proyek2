<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produk', function (Blueprint $table) {
            $table->id('id_produk');

            $table->foreignId('id_kategori')
                ->constrained('kategori', 'id_kategori')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->string('nama_produk', 150);
            $table->decimal('harga', 12, 2);
            $table->text('deskripsi')->nullable();
            $table->string('gambar')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produk');
    }
};
