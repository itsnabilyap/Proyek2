<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('custom_pesanan', function (Blueprint $table) {
            $table->string('nama_pemesan', 150)->nullable()->after('id_pesanan');
            $table->string('no_whatsapp', 20)->nullable()->after('nama_pemesan');
            $table->string('jenis_bucket', 100)->nullable()->after('no_whatsapp');
            $table->string('budget', 50)->nullable()->after('jenis_bucket');
            $table->date('tanggal_dibutuhkan')->nullable()->after('budget');
        });
    }

    public function down(): void
    {
        Schema::table('custom_pesanan', function (Blueprint $table) {
            $table->dropColumn(['nama_pemesan', 'no_whatsapp', 'jenis_bucket', 'budget', 'tanggal_dibutuhkan']);
        });
    }
};
