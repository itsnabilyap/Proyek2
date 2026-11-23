<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Produk extends Model
{
    use SoftDeletes;

    protected $table = 'produk';

    protected $fillable = ['kategori_id', 'nama_produk', 'slug', 'deskripsi', 'varian_judul', 'varian_pilihan', 'harga_bebas_min', 'gambar', 'stok', 'aktif'];

    protected function casts(): array
    {
        return ['aktif' => 'boolean', 'stok' => 'integer', 'varian_pilihan' => 'array'];
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class);
    }

    public function ukuran(): HasMany
    {
        return $this->hasMany(UkuranProduk::class)->orderBy('harga');
    }

    /** Produk punya pilihan varian (misal merek coklat)? */
    public function punyaVarian(): bool
    {
        return $this->varian_judul && ! empty($this->varian_pilihan);
    }

    /** Bucket uang: harga = bahan + jasa, uangnya disiapkan pelanggan. */
    public function adalahBucketUang(): bool
    {
        return $this->kategori?->slug === 'buket-uang';
    }

    /** Pilihan warna di halaman detail (stok yang biasanya ready). Ubah di sini kalau stok berubah. */
    public const WARNA_BUNGA = ['Pink pastel', 'Merah', 'Putih', 'Ungu', 'Campur'];
    public const WARNA_KERTAS = ['Putih susu', 'Hitam', 'Pink', 'Cokelat kraft'];

    public function adalahCoklat(): bool
    {
        return $this->kategori?->slug === 'buket-coklat';
    }

    public function adalahThumbelina(): bool
    {
        return str_contains($this->slug, 'thumbelina');
    }

    /** Bucket uang tidak memilih warna bunga/kertas. */
    public function pakaiWarna(): bool
    {
        return ! $this->adalahBucketUang();
    }

    /** Label pil di atas judul halaman detail. */
    public function badgeDetail(): string
    {
        return match (true) {
            $this->adalahBucketUang() => 'Harga bahan + jasa',
            $this->adalahCoklat()     => 'Sudah termasuk bunga',
            $this->adalahSnack()      => 'Pilih paket atau harga sendiri',
            default                   => 'Bunga artificial',
        };
    }

    /** Judul langkah memilih ukuran: "Jumlah lembar", "Jumlah coklat", "Pilih paket", "Pilih ukuran". */
    public function judulUkuran(): string
    {
        $nama = strtolower((string) $this->ukuran->first()?->nama_ukuran);

        return match (true) {
            str_contains($nama, 'lembar') => 'Jumlah lembar',
            str_contains($nama, 'coklat') => 'Jumlah coklat',
            str_contains($nama, 'paket')  => 'Pilih paket',
            default                       => 'Pilih ukuran',
        };
    }

    public function adalahSnack(): bool
    {
        return $this->kategori?->slug === 'buket-snack';
    }

    /** Catatan di bawah foto produk. */
    public function catatanFoto(): ?string
    {
        return match (true) {
            $this->adalahBucketUang() => null,
            $this->adalahCoklat()     => 'Coklat ukuran kecil 22 gram. Sudah termasuk rangkaian bunga artificial.',
            $this->adalahSnack()      => 'Isi snack menyesuaikan harga paket. Tulis snack favoritmu di kolom catatan saat checkout.',
            $this->adalahThumbelina() => 'Makin tinggi paketnya, makin besar dan ramai rangkaiannya. Warna bisa dipilih dari stok yang ready.',
            default                   => 'Foto hanya contoh ukuran dan harga. Warna bunga dan kertas bisa kamu pilih dari stok yang ready.',
        };
    }

    /**
     * Ajakan ke Request Custom di bawah pilihan ukuran: [teks, jenis custom] atau null.
     * Jenis harus salah satu kunci Custom::JENIS.
     */
    public function tawaranCustom(): ?array
    {
        return match (true) {
            $this->adalahBucketUang() => ['Mau lebih dari 50 lembar?', 'Bucket Uang (request)'],
            $this->adalahCoklat()     => ['Mau lebih dari 3 coklat atau merek lain (mulai Rp100rb)?', 'Bucket Coklat (request)'],
            $this->adalahThumbelina() => ['Mau lebih besar dari Rp300rb?', 'Lainnya'],
            $this->kategori?->slug === 'buket-bunga' => ['Mau ukuran Grande (15 tangkai), Bulat (20–50 tangkai), atau Jumbo (58 tangkai)?', 'Bucket Bunga Besar'],
            default                   => null,
        };
    }

    /** Label kecil di atas harga kartu katalog. */
    public function labelHarga(): string
    {
        return $this->adalahBucketUang() ? 'Bahan + jasa mulai' : 'Mulai dari';
    }

    /** Info singkat di kartu katalog: "5 ukuran", "3–58 tangkai", "Dairy Milk / Silverqueen". */
    public function chipKatalog(): array
    {
        $chip = [$this->ukuran->count() . ' pilihan'];

        // Rentang angka dari keterangan ("3 tangkai" ... "58 tangkai") atau nama ("10 lembar" ... "100 lembar")
        foreach (['keterangan', 'nama_ukuran'] as $kolom) {
            $angka = $this->ukuran->map(fn ($u) => preg_match('/^(\d+)\s+(\pL+)/u', (string) $u->$kolom, $m) ? [(int) $m[1], $m[2]] : null)->filter();
            if ($angka->count() >= 2 && $angka->count() === $this->ukuran->count()) {
                $chip[] = $angka->min(0) . '–' . $angka->max(0) . ' ' . $angka->first()[1];
                break;
            }
        }

        if ($this->punyaVarian()) {
            $chip[] = count($this->varian_pilihan) <= 2 ? implode(' / ', $this->varian_pilihan) : $this->varian_judul;
        }

        return $chip;
    }

    public function scopeAktif(Builder $query): void
    {
        $query->where('aktif', true);
    }

    /** URL gambar, atau null kalau file belum diupload. */
    public function getGambarUrlAttribute(): ?string
    {
        if (! $this->gambar || ! file_exists(public_path('images/produk/' . $this->gambar))) {
            return null;
        }

        return asset('images/produk/' . $this->gambar);
    }

    public function tabelPerbandingan(): array
    {
        $baris = [];

        foreach ($this->ukuran as $u) {
            foreach ($u->detail ?? [] as $nama => $nilai) {
                $baris[$nama][$u->id] = $nilai;
            }
        }

        return $baris;
    }

    public function produk(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Produk::class)->withTrashed();
    }
}