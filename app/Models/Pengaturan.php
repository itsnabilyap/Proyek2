<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Pengaturan extends Model
{
    protected $table = 'pengaturan';
    protected $primaryKey = 'kunci';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['kunci', 'nilai'];

    /** Nilai awal kalau belum pernah disimpan. */
    public const BAWAAN = [
        'nama_toko' => '@bucket.imy',
        'telepon'   => '',
        'alamat'    => '',
        'logo'      => null,
    ];

    /** Semua pengaturan, disimpan di cache supaya tidak query terus. */
    public static function semua(): array
    {
        return Cache::rememberForever('pengaturan', fn () => array_merge(
            self::BAWAAN,
            static::pluck('nilai', 'kunci')->all()
        ));
    }

    /** Contoh: Pengaturan::ambil('alamat') */
    public static function ambil(string $kunci, mixed $default = null): mixed
    {
        return self::semua()[$kunci] ?? $default;
    }

    public static function simpan(array $data): void
    {
        foreach ($data as $kunci => $nilai) {
            static::updateOrCreate(['kunci' => $kunci], ['nilai' => $nilai]);
        }
        Cache::forget('pengaturan');
    }

    public static function logoUrl(): ?string
    {
        $logo = self::ambil('logo');

        return $logo ? asset('images/toko/' . $logo) : null;
    }
}