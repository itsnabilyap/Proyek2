
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Produk extends Model
{
    protected $table = 'produk';

    protected $primaryKey = 'id_produk';

    protected $fillable = [
        'id_kategori',
        'nama_produk',
        'harga',
        'deskripsi',
        'gambar',
    ];

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class, 'id_kategori', 'id_kategori');
    }
}

public function stok(): HasMany
{
    return $this->hasMany(Stok::class, 'id_produk', 'id_produk');
}

public function detailBahan(): HasMany
{
    return $this->hasMany(
        DetailBahan::class,
        'id_produk',
        'id_produk'
    );
}