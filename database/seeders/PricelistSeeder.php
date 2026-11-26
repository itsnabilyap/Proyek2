<?php

namespace Database\Seeders;

use App\Models\Kategori;
use App\Models\Produk;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Isi katalog sesuai pricelist mitra (Nov 2026).
 * Aman dijalankan berulang: produk dicari lewat slug, ukurannya disamakan dengan daftar di bawah.
 *
 *   php artisan db:seed --class=PricelistSeeder
 *
 * Yang harganya "sesuai budget" (bunga Grande/Bulat/Jumbo, uang > 50 lembar, coklat request) TIDAK di sini,
 * tapi lewat halaman Custom (lihat Custom::JENIS).
 *
 */
class PricelistSeeder extends Seeder
{
    private const CATATAN_BUNGA = 'Bunga artificial (bunga palsu), awet dan tidak layu. Bunga dan kertas bisa pilih dari yang ready. Foto hanya contoh ukuran.';
    private const CATATAN_UANG = 'Harga sudah termasuk bahan + jasa, BELUM termasuk uang. Uangnya disiapkan sendiri dan diserahkan ke atelier paling lambat H-1 sebelum jadwal.';
    private const PECAHAN = ['2rb', '5rb', '10rb', '20rb', '50rb', '100rb'];

    public function run(): void
    {
        // [slug, nama, kategori, gambar, deskripsi, [varian judul, pilihan] | null, [[ukuran, harga, keterangan], ...]]
        $data = [
            ['bucket-bunga', 'Bucket Bunga', 'Buket Bunga', 'bucket-mawar-merah.jpg', self::CATATAN_BUNGA, null, [
                ['Small', 50000, '3 tangkai'],
                ['Medium', 75000, '6 tangkai'],
                ['Large', 125000, '10 tangkai'],
            ]],
            ['bucket-coklat', 'Bucket Coklat', 'Buket Coklat', 'bucket-coklat.jpg',
                'Isi coklat kecil 22 gram, sudah termasuk bunga. Mau lebih dari 3 coklat atau isi khusus? Pakai Request Custom (mulai Rp100rb).',
                ['Merek coklat', ['Dairy Milk', 'Silverqueen']], [
                ['1 coklat', 40000, 'Isi 1 coklat 22 gram'],
                ['2 coklat', 70000, 'Isi 2 coklat 22 gram'],
                ['3 coklat', 100000, 'Isi 3 coklat 22 gram'],
            ]],
            ['bucket-uang-kipas', 'Bucket Uang Kipas', 'Buket Uang', 'bucket-uang.jpg', self::CATATAN_UANG,
                ['Pecahan uang', self::PECAHAN], $this->lembar([10 => 70000, 20 => 85000, 30 => 110000, 40 => 170000, 50 => 230000])],
            ['bucket-uang-bulat', 'Bucket Uang Bulat', 'Buket Uang', 'bucket-uang.jpg', self::CATATAN_UANG,
                ['Pecahan uang', self::PECAHAN], $this->lembar([10 => 100000, 20 => 140000, 30 => 180000, 40 => 210000, 50 => 260000])],
            ['bucket-uang-kuncup', 'Bucket Uang Kuncup', 'Buket Uang', 'bucket-uang.jpg', self::CATATAN_UANG,
                ['Pecahan uang', self::PECAHAN], $this->lembar([10 => 90000, 20 => 110000, 30 => 150000, 40 => 200000, 50 => 250000])],
            ['bucket-thumbelina', 'Bucket Thumbelina', 'Buket Bunga', 'bucket-thumbelina.jpg',
                'Rangkaian bunga thumbelina mungil. Pilih paket sesuai budget, makin besar paket makin penuh rangkaiannya.', null, [
                ['Paket 85rb', 85000, null],
                ['Paket 100rb', 100000, null],
                ['Paket 150rb', 150000, null],
                ['Paket 200rb', 200000, null],
                ['Paket 250rb', 250000, null],
                ['Paket 300rb', 300000, null],
            ]],
        ];

        // Bucket Snack: paket + "harga lain" (ketik sendiri, minimal Rp50.000)
        $data[] = ['bucket-snack', 'Bucket Snack', 'Buket Snack', null,
            'Isi snack menyesuaikan harga yang dipilih. Pilih paket atau ketik harga sendiri (minimal Rp50.000). Tulis snack favorit di catatan saat checkout.', null, [
            ['Paket 50rb', 50000, null],
            ['Paket 100rb', 100000, null],
            ['Paket 150rb', 150000, null],
            ['Paket 200rb', 200000, null],
        ]];
        $hargaBebas = ['bucket-snack' => 50000];

        foreach ($data as [$slug, $nama, $kategori, $gambar, $deskripsi, $varian, $ukuran]) {
            $kat = Kategori::firstOrCreate(['slug' => Str::slug($kategori)], ['nama_kategori' => $kategori]);

            $produk = Produk::withTrashed()->firstOrNew(['slug' => $slug]);
            $produk->forceFill([
                'kategori_id'    => $kat->id,
                'nama_produk'    => $nama,
                'deskripsi'      => $deskripsi,
                'varian_judul'   => $varian[0] ?? null,
                'varian_pilihan' => $varian[1] ?? null,
                'harga_bebas_min' => $hargaBebas[$slug] ?? null,
                'stok'           => $produk->exists ? $produk->stok : 10,
                'aktif'          => true,
                'deleted_at'     => null,
            ])->save();

            // Foto: kalau produk belum punya foto sama sekali, pakai foto bawaan seeder
            if ($gambar && ! $produk->galeri()->exists()) {
                $produk->galeri()->create(['file' => $gambar, 'urutan' => 0]);
            }

            $dipakai = [];
            foreach ($ukuran as [$namaUkuran, $harga, $ket]) {
                $u = $produk->ukuran()->firstOrNew(['nama_ukuran' => $namaUkuran]);
                $u->forceFill(['harga' => $harga, 'keterangan' => $ket, 'label' => null, 'detail' => null])->save();
                $dipakai[] = $u->id;
            }
            \App\Models\UkuranProduk::where('produk_id', $produk->id)->whereNotIn('id', $dipakai)->delete();
        }

        // Produk lama "Bucket Uang" sudah dipecah jadi 3 model → disembunyikan dari katalog.
        Produk::where('slug', 'bucket-uang')->update(['aktif' => false]);
    }


    /** [10 => 70000, ...] → [['10 lembar', 70000, 'Bahan + jasa, belum termasuk uang'], ...] */
    private function lembar(array $harga): array
    {
        return collect($harga)->map(fn ($h, $n) => ["{$n} lembar", $h, 'Bahan + jasa, belum termasuk uang'])->values()->all();
    }
}
