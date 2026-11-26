<?php

namespace Database\Seeders;

use App\Models\Produk;
use Illuminate\Database\Seeder;

/**
 * Harga Medium/Large dan isi perbandingan masih CONTOH. Sesuaikan dengan harga asli toko.
 * Format setiap ukuran: [nama, label, harga, keterangan, detail perbandingan]
 */
class UkuranProdukSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'bucket-coklat' => ['Medium', [
                ['Small', 'Hemat', 60000, 'Isi 5 coklat batang kecil. Pas untuk kejutan sederhana.',
                    ['Isi coklat' => '5 pcs', 'Jenis coklat' => 'Coklat batang mini', 'Tinggi buket' => '±30 cm', 'Wrapping' => 'Kertas korea + pita', 'Cocok untuk' => 'Hadiah teman']],
                ['Medium', 'Paling pas', 95000, 'Isi 10 coklat campuran. Terlihat penuh dan manis.',
                    ['Isi coklat' => '10 pcs', 'Jenis coklat' => 'Campuran', 'Tinggi buket' => '±40 cm', 'Wrapping' => 'Kertas korea dua lapis + pita', 'Cocok untuk' => 'Ulang tahun, valentine']],
                ['Large', 'Paling mewah', 150000, 'Isi 18 coklat termasuk coklat premium.',
                    ['Isi coklat' => '18 pcs', 'Jenis coklat' => 'Campuran + premium', 'Tinggi buket' => '±50 cm', 'Wrapping' => 'Wrapping premium + pita lebar', 'Cocok untuk' => 'Pasangan, momen besar']],
            ]],

            'bucket-tangkai-mawar-merah' => ['Medium', [
                ['Small', 'Hemat', 75000, '3 tangkai mawar merah. Simpel dan bermakna.',
                    ['Jumlah mawar' => '3 tangkai', 'Tinggi buket' => '±35 cm', 'Daun filler' => 'Sedikit', 'Wrapping' => 'Kertas korea + pita satin', 'Cocok untuk' => 'Hadiah personal']],
                ['Medium', 'Paling pas', 150000, '10 tangkai mawar merah. Penuh dan romantis.',
                    ['Jumlah mawar' => '10 tangkai', 'Tinggi buket' => '±45 cm', 'Daun filler' => 'Lebih banyak', 'Wrapping' => 'Kertas korea dua lapis + pita satin', 'Cocok untuk' => 'Anniversary']],
                ['Large', 'Paling mewah', 275000, '20 tangkai mawar merah. Paling menonjol.',
                    ['Jumlah mawar' => '20 tangkai', 'Tinggi buket' => '±55 cm', 'Daun filler' => 'Penuh', 'Wrapping' => 'Wrapping premium + pita lebar', 'Cocok untuk' => 'Lamaran, momen besar']],
            ]],

            'bucket-uang' => ['Medium', [
                ['Small', 'Hemat', 70000, 'Maksimal 10 lembar uang. Harga belum termasuk uang.',
                    ['Maks. lembar uang' => '10 lembar', 'Bentuk lipatan' => 'Kipas', 'Tinggi buket' => '±35 cm', 'Wrapping' => 'Kertas korea + pita', 'Catatan' => 'Belum termasuk uang']],
                ['Medium', 'Paling pas', 100000, 'Maksimal 20 lembar uang. Harga belum termasuk uang.',
                    ['Maks. lembar uang' => '20 lembar', 'Bentuk lipatan' => 'Kipas + bunga', 'Tinggi buket' => '±45 cm', 'Wrapping' => 'Kertas korea dua lapis + pita', 'Catatan' => 'Belum termasuk uang']],
                ['Large', 'Paling mewah', 150000, 'Maksimal 40 lembar uang. Harga belum termasuk uang.',
                    ['Maks. lembar uang' => '40 lembar', 'Bentuk lipatan' => 'Kipas + bunga + mahkota', 'Tinggi buket' => '±55 cm', 'Wrapping' => 'Wrapping premium + pita lebar', 'Catatan' => 'Belum termasuk uang']],
            ]],

            'bucket-wisuda' => ['Medium', [
                ['Small', 'Hemat', 70000, '5–7 tangkai, tinggi ±35 cm. Pas untuk hadiah personal.',
                    ['Jumlah bunga' => '5–7 tangkai', 'Tinggi buket' => '±35 cm', 'Wrapping' => 'Goni + pita satin', 'Tambahan' => '-', 'Cocok untuk' => 'Hadiah dari teman']],
                ['Medium', 'Paling pas', 120000, '10–12 tangkai, tinggi ±45 cm. Lebih padat di foto.',
                    ['Jumlah bunga' => '10–12 tangkai', 'Tinggi buket' => '±45 cm', 'Wrapping' => 'Goni dua lapis + pita satin', 'Tambahan' => 'Boneka wisuda mini', 'Cocok untuk' => 'Hadiah dari keluarga']],
                ['Large', 'Paling mewah', 190000, '18–20 tangkai, tinggi ±55 cm. Paling menonjol di foto wisuda.',
                    ['Jumlah bunga' => '18–20 tangkai', 'Tinggi buket' => '±55 cm', 'Wrapping' => 'Wrapping premium + pita lebar', 'Tambahan' => 'Boneka wisuda + selempang', 'Cocok untuk' => 'Hadiah dari pasangan']],
            ]],

            'bucket-thumbelina' => ['Medium', [
                ['Small', 'Hemat', 85000, 'Rangkaian thumbelina mungil. Manis dan ringan.',
                    ['Jumlah tangkai' => '7 tangkai', 'Tinggi buket' => '±30 cm', 'Daun filler' => 'Sedikit', 'Wrapping' => 'Kertas korea + pita', 'Cocok untuk' => 'Hadiah teman']],
                ['Medium', 'Paling pas', 130000, 'Lebih penuh dengan tambahan filler.',
                    ['Jumlah tangkai' => '12 tangkai', 'Tinggi buket' => '±40 cm', 'Daun filler' => 'Lebih banyak', 'Wrapping' => 'Kertas korea dua lapis + pita', 'Cocok untuk' => 'Ulang tahun']],
                ['Large', 'Paling mewah', 200000, 'Rangkaian paling penuh dan mengembang.',
                    ['Jumlah tangkai' => '20 tangkai', 'Tinggi buket' => '±50 cm', 'Daun filler' => 'Penuh', 'Wrapping' => 'Wrapping premium + pita lebar', 'Cocok untuk' => 'Momen spesial']],
            ]],

            'bucket-satin-premium' => ['Medium', [
                ['Small', 'Hemat', 95000, '5 bunga satin buatan tangan. Awet selamanya.',
                    ['Jumlah bunga satin' => '5 kuntum', 'Tinggi buket' => '±35 cm', 'Wrapping' => 'Kertas korea + pita satin', 'Ketahanan' => 'Awet, tidak layu', 'Cocok untuk' => 'Kenang-kenangan']],
                ['Medium', 'Paling pas', 160000, '9 bunga satin dengan variasi warna.',
                    ['Jumlah bunga satin' => '9 kuntum', 'Tinggi buket' => '±45 cm', 'Wrapping' => 'Kertas korea dua lapis + pita satin', 'Ketahanan' => 'Awet, tidak layu', 'Cocok untuk' => 'Wisuda, anniversary']],
                ['Large', 'Paling mewah', 250000, '15 bunga satin, rangkaian paling megah.',
                    ['Jumlah bunga satin' => '15 kuntum', 'Tinggi buket' => '±55 cm', 'Wrapping' => 'Wrapping premium + pita lebar', 'Ketahanan' => 'Awet, tidak layu', 'Cocok untuk' => 'Momen besar']],
            ]],
        ];

        foreach ($data as $slug => [$ukuranFoto, $ukuranList]) {
            $produk = Produk::where('slug', $slug)->first();
            if (! $produk) continue;

            $produk->ukuran()->delete();   // hapus ukuran lama (termasuk isi keranjang yang memakainya)

            foreach ($ukuranList as [$nama, $label, $harga, $ket, $detail]) {
                $produk->ukuran()->create([
                    'nama_ukuran' => $nama,
                    'label' => $label,
                    'harga' => $harga,
                    'keterangan' => $ket,
                    'detail' => $detail,
                ]);
            }

            // Foto sampul = foto ukuran ini
            $ukuranId = $produk->ukuran()->where('nama_ukuran', $ukuranFoto)->value('id');
            $produk->galeri()->orderBy('urutan')->first()?->update(['ukuran_produk_id' => $ukuranId]);
        }
    }
}