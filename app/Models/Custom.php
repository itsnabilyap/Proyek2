<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Custom extends Model
{
    protected $table = 'custom';

    protected $fillable = [
        'user_id', 'nama_pemesan', 'no_whatsapp', 'jenis_bucket',
        'budget', 'tema', 'tanggal_dibutuhkan', 'catatan', 'foto_referensi', 'pesanan_id', 'status', 'harga_final',
    ];

    /**
     * Jenis bucket lewat Custom (harga sesuai budget). Kuncinya disimpan di kolom jenis_bucket.
     * - kisaran : teks pendek di pilihan jenis
     * - info    : penjelasan di bawah pilihan jenis
     * - contoh  : placeholder isian "Warna & tema"
     * - budget  : pilihan budget untuk jenis ini (disimpan apa adanya di kolom budget)
     * - kartu   : [harga, label harga, keterangan, slug kategori] kalau tampil sebagai kartu di katalog
     */
    public const JENIS = [
        'Bucket Bunga Besar' => [
            'kisaran' => 'Rp180–700rb',
            'info'    => 'Grande 15 tangkai (Rp180rb), Bulat 20–50 tangkai (Rp200–600rb), atau Jumbo 58 tangkai (Rp700rb).',
            'contoh'  => 'Contoh: bulat 30 tangkai, mawar merah, kertas hitam',
            'budget'  => ['Grande · Rp180rb', 'Bulat · Rp200–300rb', 'Bulat · Rp300–450rb', 'Bulat · Rp450–600rb', 'Jumbo · Rp700rb'],
            'kartu'   => ['Rp180–700rb', 'Kisaran harga', 'Grande, Bulat, atau Jumbo (15–58 tangkai). Harga final dikonfirmasi admin lewat WhatsApp.', 'buket-bunga'],
        ],
        'Bucket Coklat (request)' => [
            'kisaran' => 'mulai Rp100rb',
            'info'    => 'Lebih dari 3 coklat atau merek lain. Sudah termasuk bunga.',
            'contoh'  => 'Contoh: Ferrero, wrapping hitam',
            'budget'  => ['Rp100rb', 'Rp150rb', 'Rp200rb', 'Rp300rb', 'Lainnya'],
        ],
        'Bucket Uang (request)' => [
            'kisaran' => 'sesuai model',
            'info'    => 'Lebih dari 50 lembar atau model lain. Harga tetap bahan + jasa, uangnya dari kamu.',
            'contoh'  => 'Contoh: 70 lembar pecahan 20rb, model kipas',
            'budget'  => ['Rp100rb', 'Rp200rb', 'Rp300rb', 'Rp500rb', 'Lainnya'],
        ],
        'Lainnya' => [
            'kisaran' => 'sesuai budget',
            'info'    => 'Ide sendiri? Ceritakan di catatan dan lampirkan foto referensi.',
            'contoh'  => 'Contoh: tema wisuda, warna maroon',
            'budget'  => ['Rp100rb', 'Rp200rb', 'Rp300rb', 'Rp500rb', 'Lainnya'],
        ],
    ];

    /** Status request di admin: [label, kelas badge] */
    public const STATUS = [
        'baru'      => ['Baru', 'b-kuning'],
        'dihubungi' => ['Dihubungi', 'b-biru'],
        'disetujui' => ['Disetujui', 'b-hijau'],
        'ditolak'   => ['Ditolak', 'b-abu'],
    ];

    /** Nilai budget lama (sebelum pricelist baru), tetap dibaca supaya data lama tidak rusak. */
    public const BUDGET = [
        'kurang_100' => '< Rp 100k',
        '100_250'    => 'Rp 100k – 250k',
        'lebih_250'  => '> Rp 250k',
    ];

    /** Nama pendek untuk tombol: "Bunga Bulat", "Snack", "Coklat Request", ... */
    public static function namaPendek(string $jenis): string
    {
        return trim(str_replace(['Bucket ', '(request)'], ['', 'Request'], $jenis));
    }

    protected function casts(): array
    {
        return ['tanggal_dibutuhkan' => 'date', 'harga_final' => 'integer'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getBudgetLabelAttribute(): string
    {
        return self::BUDGET[$this->budget] ?? $this->budget;
    }

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class);
    }

    /** Disetujui, ada harga final, dan belum dibayar / tagihannya batal → pelanggan bisa checkout. */
    public function bisaDibayar(): bool
    {
        return $this->status === 'disetujui'
            && $this->harga_final
            && (! $this->pesanan || $this->pesanan->status_pesanan === 'dibatalkan');
    }

    /** Link wa.me ke pelanggan dengan sapaan awal. */
    public function getLinkWaAttribute(): string
    {
        $no = preg_replace('/\D/', '', $this->no_whatsapp);
        if (str_starts_with($no, '0')) $no = '62' . substr($no, 1);

        return "https://wa.me/{$no}?text=" . urlencode("Halo {$this->nama_pemesan}, kami dari @bucket.imy mau menindaklanjuti request custom #{$this->id} ({$this->jenis_bucket}).");
    }

    public function getFotoReferensiUrlAttribute(): ?string
    {
        return $this->foto_referensi ? asset('images/custom/' . $this->foto_referensi) : null;
    }
}