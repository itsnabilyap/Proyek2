<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Testimoni extends Model
{
    protected $table = 'testimoni';
    protected $primaryKey = 'id_testimoni';

    protected $fillable = [
        'id_pelanggan',
        'id_pesanan',
        'isi_testimoni',
        'rating',
        'tanggal',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'tanggal' => 'date',
        ];
    }

    public function pelanggan(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_pelanggan', 'id');
    }

    public function pesanan(): BelongsTo
    {
        return $this->belongsTo(Pesanan::class, 'id_pesanan', 'id_pesanan');
    }
}