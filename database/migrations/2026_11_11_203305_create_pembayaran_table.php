<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pesanan_id')->constrained('pesanan')->cascadeOnDelete();
            $table->string('order_id_midtrans', 60)->unique();
            $table->string('qr_url')->nullable();
            $table->string('metode', 10)->nullable();
            $table->string('bank', 20)->nullable();
            $table->string('nomor_va', 40)->nullable();
            $table->unsignedInteger('jumlah');
            $table->string('status', 15)->default('pending');
            $table->timestamp('kedaluwarsa_pada')->nullable();
            $table->timestamp('dibayar_pada')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran');
    }
};