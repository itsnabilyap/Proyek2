
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('custom_pesanan', function (Blueprint $table) {
            $table->bigIncrements('id_custom');

            $table->unsignedBigInteger('id_pesanan')->unique();
            $table->string('ukuran', 100)->nullable();
            $table->text('bahan')->nullable();
            $table->string('kertas', 100)->nullable();
            $table->string('pita', 100)->nullable();
            $table->text('catatan_custom')->nullable();
            $table->decimal('harga_custom', 12, 2)->default(0);

            $table->foreign('id_pesanan')
                ->references('id_pesanan')
                ->on('pesanan')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custom_pesanan');
    }
};