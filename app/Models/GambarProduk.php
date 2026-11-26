<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Satu foto produk. File ada di public/images/produk. Urutan terkecil = sampul. */
class GambarProduk extends Model
{
    protected $table = 'gambar_produk';

    protected $fillable = ['produk_id', 'ukuran_produk_id', 'file', 'urutan'];

    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class);
    }

    /** Ukuran yang tampil di foto ini (boleh kosong). */
    public function ukuran(): BelongsTo
    {
        return $this->belongsTo(UkuranProduk::class, 'ukuran_produk_id');
    }

    /** URL foto, atau null kalau file-nya tidak ada di folder. */
    public function getUrlAttribute(): ?string
    {
        return is_file(public_path('images/produk/' . $this->file))
            ? asset('images/produk/' . $this->file)
            : null;
    }

    /** Hapus file fisiknya juga. */
    public function hapusFile(): void
    {
        $path = public_path('images/produk/' . $this->file);
        if (is_file($path)) @unlink($path);
    }
}
