
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Stok extends Model
{
    protected $table = 'stok';
    protected $primaryKey = 'id_stok';

    protected $fillable = [
        'id_produk',
        'jumlah_stok',
    ];

    public function produk(): BelongsTo
    {
        return $this->belongsTo(
            Produk::class,
            'id_produk',
            'id_produk'
        );
    }
}
