<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pesanan extends Model
{
    protected $table = 'pesanan';

    protected $fillable = [
        'user_id', 'metode_pengiriman', 'layanan_kurir', 'nama_penerima', 'no_whatsapp',
        'alamat', 'patokan', 'tanggal', 'slot_waktu', 'catatan_florist',
        'subtotal', 'ongkir', 'total_harga', 'metode_pembayaran', 'status_pesanan',
    ];

    /** Harga kurir ditentukan di sini, bukan dari form. */
    public const KURIR = [
        'motor' => ['nama' => 'Kurir Instan Motor', 'ongkir' => 15000,
                    'ket' => 'Cocok untuk buket ukuran Small sampai Medium. Lebih cepat dan ekonomis.'],
        'mobil' => ['nama' => 'Kurir Mobil AC (Buket Besar / Premium)', 'ongkir' => 65000,
                    'ket' => 'Untuk buket Large, giant bouquet, rangkaian papan, atau pengiriman jarak jauh.'],
    ];

    public const SLOT = ['09.00 – 11.00 WIB', '11.00 – 13.00 WIB', '13.00 – 15.00 WIB', '15.00 – 17.00 WIB', '17.00 – 19.00 WIB'];

    protected function casts(): array
    {
        return ['tanggal' => 'date', 'subtotal' => 'integer', 'ongkir' => 'integer', 'total_harga' => 'integer'];
    }

    protected static function booted(): void
    {
        // Kode dibuat dari id, jadi diisi setelah baris tersimpan.
        static::created(function (Pesanan $pesanan) {
            $pesanan->kode_pesanan = 'BKT-' . str_pad((string) $pesanan->id, 4, '0', STR_PAD_LEFT);
            $pesanan->saveQuietly();
        });
    }

    public function pelanggan(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function detailPesanan(): HasMany
    {
        return $this->hasMany(DetailPesanan::class);
    }

    public function pembayaran(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Pembayaran::class);
    }
    
    /** Batalkan pesanan + kembalikan stok. */
    public function batalkan(): void
    {
        if ($this->status_pesanan !== 'menunggu_pembayaran') {
            return;
        }
    
        foreach ($this->detailPesanan as $item) {
            if ($item->produk_id) {
                Produk::whereKey($item->produk_id)->increment('stok', $item->jumlah);
            }
        }
    
        $this->update(['status_pesanan' => 'dibatalkan']);

        // Tagihan request custom batal → lepaskan, supaya pelanggan bisa bayar ulang
        Custom::where('pesanan_id', $this->id)->update(['pesanan_id' => null]);
    }

        /** Label dan warna setiap status. */
    public const STATUS = [
        'menunggu_pembayaran' => ['Menunggu Pembayaran', 'st-tunggu'],
        'dirangkai'           => ['Diproses', 'st-proses'],
        'siap_dikirim'        => ['Siap Dikirim', 'st-siap'],
        'siap_diambil'        => ['Siap Diambil', 'st-siap'],
        'dikirim'             => ['Dikirim', 'st-kirim'],
        'selesai'             => ['Selesai', 'st-selesai'],
        'dibatalkan'          => ['Dibatalkan', 'st-batal'],
    ];
    
    public function testimoni(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Testimoni::class);
    }
    
    public function statusLabel(): string
    {
        return self::STATUS[$this->status_pesanan][0] ?? $this->status_pesanan;
    }
    
    public function statusClass(): string
    {
        return self::STATUS[$this->status_pesanan][1] ?? '';
    }
    
    /**
     * Langkah progres: [label, keterangan, sudah lewat?]
     */
    public function langkahProgres(): array
    {
        $urutan = $this->metode_pengiriman === 'pickup'
            ? ['dirangkai' => 'Dirangkai', 'siap_diambil' => 'Siap Diambil', 'selesai' => 'Selesai']
            : ['dirangkai' => 'Dirangkai', 'siap_dikirim' => 'Siap Dikirim', 'dikirim' => 'Dikirim', 'selesai' => 'Selesai'];
    
        $posisi = array_search($this->status_pesanan, array_keys($urutan), true);
    
        $langkah = [['Dibayar', true]];
        $i = 0;
        foreach ($urutan as $label) {
            $langkah[] = [$label, $posisi !== false && $i <= $posisi];
            $i++;
        }
    
        return $langkah;
    }
}
