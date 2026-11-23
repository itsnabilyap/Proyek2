<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailPesanan extends Model
{
    protected $table = 'detail_pesanan';

    protected $fillable = ['produk_id', 'ukuran_produk_id', 'nama_produk', 'nama_ukuran', 'varian', 'warna', 'harga', 'jumlah', 'kartu_ucapan'];

    /** "Medium", "2 coklat · Silverqueen", atau "Large · Bunga merah · kertas hitam" */
    public function getUkuranLengkapAttribute(): string
    {
        return collect([$this->nama_ukuran, $this->varian, $this->warna])->filter()->implode(' · ');
    }

    public function pesanan(): BelongsTo
    {
        return $this->belongsTo(Pesanan::class);
    }

    public function produk(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Produk::class)->withTrashed();
    }

    public function ukuranProduk(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(UkuranProduk::class);
    }
}
