<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UkuranProduk extends Model
{
    protected $table = 'ukuran_produk';

    protected $fillable = ['produk_id', 'nama_ukuran', 'label', 'harga', 'keterangan', 'detail'];

    protected function casts(): array
    {
        return ['harga' => 'integer', 'detail' => 'array'];
    }

    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class);
    }

    /** Resep: bahan yang dipakai untuk 1 buket ukuran ini (jumlah di pivot). */
    public function bahan(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Bahan::class, 'resep_bahan')->withPivot('jumlah')->withTimestamps();
    }
}
