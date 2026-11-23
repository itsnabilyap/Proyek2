<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pembayaran extends Model
{
    protected $table = 'pembayaran';

    protected $fillable = [
        'pesanan_id', 'order_id_midtrans', 'qr_url', 'metode', 'bank', 'nomor_va',
        'jumlah', 'status', 'kedaluwarsa_pada', 'dibayar_pada',
    ];

    protected function casts(): array
    {
        return ['jumlah' => 'integer', 'kedaluwarsa_pada' => 'datetime', 'dibayar_pada' => 'datetime'];
    }

    public function pesanan(): BelongsTo
    {
        return $this->belongsTo(Pesanan::class);
    }
}