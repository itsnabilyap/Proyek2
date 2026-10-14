<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_bahan', function (Blueprint $table) {
            $table->bigIncrements('id_detail_bahan');

            $table->unsignedBigInteger('id_produk');
            $table->unsignedBigInteger('id_bahan');
            $table->decimal('jumlah_bahan', 12, 2);

            $table->foreign('id_produk')
                ->references('id_produk')
                ->on('produk')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreign('id_bahan')
                ->references('id_bahan')
                ->on('bahan')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_bahan');
    }
};