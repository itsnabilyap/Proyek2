<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;



class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'no_hp',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed', // password otomatis di-hash saat disimpan
        ];
    }

    public function isPelanggan(): bool
    {
        return $this->role === 'pelanggan';
    }

    /** PLG-0001, dibuat dari id */
    public function getKodeAttribute(): string
    {
        return 'PLG-' . str_pad($this->id, 4, '0', STR_PAD_LEFT);
    }

    public function isStaff(): bool
    {
        return in_array($this->role, ['admin', 'owner'], true);
    }

    public function Custom(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
    return $this->hasMany(Custom::class);
    }

    public function keranjang(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Keranjang::class);
    }

    public function pesanan(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Pesanan::class);
    }

}