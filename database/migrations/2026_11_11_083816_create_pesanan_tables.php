<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('keranjang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('ukuran_produk_id')->constrained('ukuran_produk')->cascadeOnDelete();
            $table->unsignedSmallInteger('jumlah')->default(1);
            $table->string('kartu_ucapan', 300)->nullable();
            $table->timestamps();
        });

        Schema::create('pesanan', function (Blueprint $table) {
            $table->id();
            $table->string('kode_pesanan', 20)->nullable()->unique();   // BKT-0001, diisi setelah insert
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->string('metode_pengiriman', 10);                     // kurir / pickup
            $table->string('layanan_kurir', 10)->nullable();             // motor / mobil
            $table->string('nama_penerima', 100);
            $table->string('no_whatsapp', 20);
            $table->text('alamat')->nullable();
            $table->string('patokan', 200)->nullable();
            $table->date('tanggal');                                     // tanggal diantar / diambil
            $table->string('slot_waktu', 20);
            $table->string('catatan_florist', 300)->nullable();
            $table->unsignedInteger('subtotal');
            $table->unsignedInteger('ongkir')->default(0);
            $table->unsignedInteger('total_harga');
            $table->string('metode_pembayaran', 10);                     // qris / va
            $table->string('status_pesanan', 30)->default('menunggu_pembayaran')->index();
            $table->timestamps();
        });

        Schema::create('detail_pesanan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pesanan_id')->constrained('pesanan')->cascadeOnDelete();
            $table->foreignId('produk_id')->nullable()->constrained('produk')->nullOnDelete();
            $table->foreignId('ukuran_produk_id')->nullable()->constrained('ukuran_produk')->nullOnDelete();
            $table->string('nama_produk');
            $table->string('nama_ukuran', 30);
            $table->unsignedInteger('harga');
            $table->unsignedSmallInteger('jumlah');
            $table->string('kartu_ucapan', 300)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_pesanan');
        Schema::dropIfExists('pesanan');
        Schema::dropIfExists('keranjang');
    }
};