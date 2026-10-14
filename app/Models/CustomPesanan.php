
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomPesanan extends Model
{
    protected $table = 'custom_pesanan';
    protected $primaryKey = 'id_custom';

    protected $fillable = [
        'id_pesanan',
        'ukuran',
        'bahan',
        'kertas',
        'pita',
        'catatan_custom',
        'harga_custom',
    ];

    protected function casts(): array
    {
        return [
            'harga_custom' => 'decimal:2',
        ];
    }

    public function pesanan(): BelongsTo
    {
        return $this->belongsTo(
            Pesanan::class,
            'id_pesanan',
            'id_pesanan'
        );
    }
}
