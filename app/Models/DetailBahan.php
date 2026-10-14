
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailBahan extends Model
{
    protected $table = 'detail_bahan';
    protected $primaryKey = 'id_detail_bahan';

    protected $fillable = [
        'id_produk',
        'id_bahan',
        'jumlah_bahan',
    ];

    public function produk(): BelongsTo
    {
        return $this->belongsTo(
            Produk::class,
            'id_produk',
            'id_produk'
        );
    }

    public function bahan(): BelongsTo
    {
        return $this->belongsTo(
            Bahan::class,
            'id_bahan',
            'id_bahan'
        );
    }
}
