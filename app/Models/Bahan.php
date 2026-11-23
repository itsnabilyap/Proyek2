<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class Bahan extends Model
{
    use SoftDeletes;

    protected $table = 'bahan';

    protected $fillable = ['nama_bahan', 'kategori', 'satuan', 'stok', 'stok_minimal'];

    public const KATEGORI = ['Bunga', 'Wrapping', 'Pita & Aksesori', 'Kartu & Lainnya'];
    public const SATUAN = ['tangkai', 'ikat', 'lembar', 'meter', 'pcs', 'roll'];

    public function riwayat(): HasMany
    {
        return $this->hasMany(RiwayatStok::class);
    }

    /** BHN-001 */
    public function getKodeAttribute(): string
    {
        return 'BHN-' . str_pad($this->id, 3, '0', STR_PAD_LEFT);
    }

    public function scopeMenipis(Builder $q): Builder
    {
        return $q->whereColumn('stok', '<', 'stok_minimal');
    }

    /** [label, class badge] */
    public function status(): array
    {
        return match (true) {
            $this->stok == 0                  => ['Habis', 'b-merah'],
            $this->stok < $this->stok_minimal => ['Menipis', 'b-kuning'],
            default                           => ['Aman', 'b-hijau'],
        };
    }

    /**
     * Ubah stok + catat ke riwayat_stok.
     * $selisih positif = masuk, negatif = keluar.
     */
    public function ubahStok(int $selisih, string $jenis, ?string $keterangan = null): void
    {
        if ($selisih === 0) return;

        DB::transaction(function () use ($selisih, $jenis, $keterangan) {
            $bahan = static::lockForUpdate()->findOrFail($this->id); // aman kalau 2 admin klik bersamaan
            $sebelum = $bahan->stok;
            $sesudah = max(0, $sebelum + $selisih);

            $bahan->stok = $sesudah;
            $bahan->save();

            $bahan->riwayat()->create([
                'user_id'      => Auth::id(),
                'jenis'        => $jenis,
                'jumlah'       => $sesudah - $sebelum,
                'stok_sebelum' => $sebelum,
                'stok_sesudah' => $sesudah,
                'keterangan'   => $keterangan,
            ]);

            $this->stok = $sesudah;
        });
    }
}