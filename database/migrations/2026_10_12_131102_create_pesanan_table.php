
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pesanan', function (Blueprint $table) {
            $table->bigIncrements('id_pesanan');

            $table->unsignedBigInteger('id_pelanggan');
            $table->dateTime('tanggal_pesanan');
            $table->string('status_pesanan', 50)->default('Menunggu Konfirmasi');
            $table->decimal('total_harga', 12, 2)->default(0);
            $table->text('alamat_pengiriman');
            $table->text('catatan')->nullable();

            $table->foreign('id_pelanggan')
                ->references('id')
                ->on('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pesanan');
    }
};