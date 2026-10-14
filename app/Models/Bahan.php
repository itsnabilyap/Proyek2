
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bahan extends Model
{
    protected $table = 'bahan';
    protected $primaryKey = 'id_bahan';

    protected $fillable = [
        'nama_bahan',
        'satuan',
        'stok',
        'harga',
    ];

    public function detailBahan(): HasMany
    {
        return $this->hasMany(
            DetailBahan::class,
            'id_bahan',
            'id_bahan'
        );
    }
}
