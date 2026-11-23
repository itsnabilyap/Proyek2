<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Request custom yang disetujui dibayar lewat checkout → tercatat sebagai pesanan. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('custom', function (Blueprint $table) {
            $table->foreignId('pesanan_id')->nullable()->after('harga_final')->constrained('pesanan')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('custom', function (Blueprint $table) {
            $table->dropConstrainedForeignId('pesanan_id');
        });
    }
};
