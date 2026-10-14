<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stok', function (Blueprint $table) {
            $table->bigIncrements('id_stok');

            $table->unsignedBigInteger('id_produk');
            $table->integer('jumlah_stok')->default(0);

            $table->foreign('id_produk')
                ->references('id_produk')
                ->on('produk')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stok');
    }
};