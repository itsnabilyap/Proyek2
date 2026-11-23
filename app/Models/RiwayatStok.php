<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiwayatStok extends Model
{
    protected $table = 'riwayat_stok';

    protected $fillable = ['bahan_id', 'user_id', 'jenis', 'jumlah', 'stok_sebelum', 'stok_sesudah', 'keterangan'];

    public function bahan(): BelongsTo
    {
        return $this->belongsTo(Bahan::class)->withTrashed();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}