<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Keranjang extends Model
{
    protected $table = 'keranjang';

    protected $fillable = ['user_id', 'ukuran_produk_id', 'jumlah', 'varian', 'warna', 'kartu_ucapan', 'harga_bebas'];

    public function ukuran(): BelongsTo
    {
        return $this->belongsTo(UkuranProduk::class, 'ukuran_produk_id');
    }

    public function getSubtotalAttribute(): int
    {
        return $this->harga_satuan * $this->jumlah;
    }

    /** Harga 1 buket: harga ketikan pelanggan (Bucket Snack "harga lain") atau harga ukuran. */
    public function getHargaSatuanAttribute(): int
    {
        return $this->harga_bebas ?? $this->ukuran->harga;
    }

    /** Nama ukuran untuk tampilan & detail pesanan. */
    public function getNamaUkuranAttribute(): string
    {
        return $this->harga_bebas ? 'Harga pilihan' : $this->ukuran->nama_ukuran;
    }
}
