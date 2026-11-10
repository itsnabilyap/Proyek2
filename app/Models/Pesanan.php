<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pesanan extends Model
{
    protected $table = 'pesanan';
    protected $primaryKey = 'id_pesanan';

    protected $fillable = [
        'id_pelanggan',
        'tanggal_pesanan',
        'status_pesanan',
        'total_harga',
        'alamat_pengiriman',
        'catatan',
        'metode_pengambilan',
        'nama_penerima',
        'no_whatsapp',
        'tanggal_pengiriman',
        'slot_waktu',
        'layanan_kurir',
        'ongkir',
        'metode_pembayaran',
        'dibayar_pada',
    ];

    public const ALAMAT_TOKO = 'Jl. Tridaya Barat, Karanganyar, Kec. Indramayu, Kabupaten Indramayu, Jawa Barat 45213, Indonesia';

    public const SLOT_WAKTU = [
        '08.00 - 10.00 WIB',
        '10.00 - 12.00 WIB',
        '13.00 - 15.00 WIB',
        '15.00 - 17.00 WIB',
    ];

    public const LAYANAN_KURIR = [
        'motor' => [
            'nama' => 'Kurir Instan Motor',
            'tarif' => 15000,
            'info' => 'Cocok untuk buket ukuran kecil - sedang. Lebih cepat dan ekonomis.',
        ],
        'mobil' => [
            'nama' => 'Kurir Mobil AC Khusus Bucket Besar / Premium',
            'tarif' => 65000,
            'info' => 'Cocok untuk buket ukuran besar/giant bouquet, rangkaian bunga papan/vas premium, atau pengiriman jarak jauh.',
        ],
    ];

    public const PEMBAYARAN = [
        'QRIS' => ['nama' => 'QRIS', 'info' => 'Dana/GoPay/OVO'],
        'BCA_VA' => ['nama' => 'BCA VA', 'info' => 'Virtual Account'],
    ];

    public function getNomorAttribute(): string
    {
        return 'B-' . str_pad((string) $this->id_pesanan, 3, '0', STR_PAD_LEFT);
    }

    protected function casts(): array
    {
        return [
            'tanggal_pesanan' => 'datetime',
            'total_harga' => 'decimal:2',
            'ongkir' => 'decimal:2',
            'tanggal_pengiriman' => 'date',
            'dibayar_pada' => 'datetime',
        ];
    }

    public function pelanggan(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_pelanggan', 'id');
    }

    public function detailPesanan(): HasMany
    {
        return $this->hasMany(
            DetailPesanan::class,
            'id_pesanan',
            'id_pesanan'
        );
    }

    public function customPesanan(): HasOne
    {
        return $this->hasOne(
            CustomPesanan::class,
            'id_pesanan',
            'id_pesanan'
        );
    }
}
