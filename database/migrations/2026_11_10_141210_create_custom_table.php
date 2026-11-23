<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('custom', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('nama_pemesan', 100);
            $table->string('no_whatsapp', 20);
            $table->string('jenis_bucket', 60);
            $table->string('budget', 20);                 // kurang_100 / 100_250 / lebih_250
            $table->date('tanggal_dibutuhkan');
            $table->text('catatan')->nullable();
            $table->string('status', 20)->default('baru'); // baru / dihubungi / disetujui / ditolak
            $table->unsignedInteger('harga_final')->nullable(); // diisi admin setelah diskusi
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custom');
    }
};