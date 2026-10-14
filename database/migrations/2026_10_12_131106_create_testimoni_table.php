
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('testimoni', function (Blueprint $table) {
            $table->bigIncrements('id_testimoni');

            $table->unsignedBigInteger('id_pelanggan');
            $table->unsignedBigInteger('id_pesanan')->nullable();
            $table->text('isi_testimoni');
            $table->tinyInteger('rating');
            $table->date('tanggal');

            $table->foreign('id_pelanggan')
                ->references('id')
                ->on('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreign('id_pesanan')
                ->references('id_pesanan')
                ->on('pesanan')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('testimoni');
    }
};