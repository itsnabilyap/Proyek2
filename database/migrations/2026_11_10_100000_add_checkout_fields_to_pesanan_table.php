<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pesanan', function (Blueprint $table) {
            $table->string('metode_pengambilan', 20)->default('pickup')->after('status_pesanan'); // pickup | kurir
            $table->string('nama_penerima', 150)->nullable()->after('metode_pengambilan');
            $table->string('no_whatsapp', 20)->nullable()->after('nama_penerima');
            $table->date('tanggal_pengiriman')->nullable()->after('no_whatsapp');
            $table->string('slot_waktu', 30)->nullable()->after('tanggal_pengiriman');
            $table->string('layanan_kurir', 20)->nullable()->after('slot_waktu'); // motor | mobil
            $table->decimal('ongkir', 12, 2)->default(0)->after('layanan_kurir');
            $table->string('metode_pembayaran', 20)->nullable()->after('ongkir'); // QRIS | BCA_VA
            $table->dateTime('dibayar_pada')->nullable()->after('metode_pembayaran');
        });
    }

    public function down(): void
    {
        Schema::table('pesanan', function (Blueprint $table) {
            $table->dropColumn([
                'metode_pengambilan', 'nama_penerima', 'no_whatsapp', 'tanggal_pengiriman',
                'slot_waktu', 'layanan_kurir', 'ongkir', 'metode_pembayaran', 'dibayar_pada',
            ]);
        });
    }
};
