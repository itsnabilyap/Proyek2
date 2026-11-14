<?php

namespace Database\Seeders;

use App\Models\Kategori;
use App\Models\Produk;
use Illuminate\Database\Seeder;

class KatalogSeeder extends Seeder
{
    public function run(): void
    {
        $kategori = collect([
            'Bucket Wisuda',
            'Bucket Uang',
            'Bucket Coklat',
            'Bunga Kering / Artificial',
        ])->mapWithKeys(fn ($nama) => [
            $nama => Kategori::firstOrCreate(['nama_kategori' => $nama])->id_kategori,
        ]);

        // gambar = nama file di public/images/produk (boleh dikosongkan dulu)
        $produk = [
            ['Bucket Coklat', 'Bucket Coklat', 60000, 'Bucket coklat manis untuk hadiah orang tersayang.', 'bucket-coklat.jpg'],
            ['Bunga Kering / Artificial', 'Bucket Tangkai Mawar Merah', 75000, 'Mawar merah elegan untuk ungkapan kasih sayang.', 'bucket-mawar-merah.jpg'],
            ['Bucket Uang', 'Bucket Uang', 70000, 'Bucket unik yang cocok untuk hadiah wisuda dan ulang tahun.', 'bucket-uang.jpg'],
            ['Bucket Wisuda', 'Bucket Wisuda', 70000, 'Bucket bunga dan boneka untuk merayakan kelulusan.', 'bucket-wisuda.jpg'],
            ['Bunga Kering / Artificial', 'Bucket Thumbelina', 85000, 'Bucket berwarna pastel dengan tampilan manis.', 'bucket-thumbelina.jpg'],
            ['Bunga Kering / Artificial', 'Bucket Satin Premium', 95000, 'Bucket elegan dengan kesan satin yang mewah.', 'bucket-satin-premium.jpg'],
        ];

        foreach ($produk as [$kat, $nama, $harga, $deskripsi, $gambar]) {
            Produk::updateOrCreate(
                ['nama_produk' => $nama],
                [
                    'id_kategori' => $kategori[$kat],
                    'harga' => $harga,
                    'deskripsi' => $deskripsi,
                    'gambar' => $gambar,
                ]
            );
        }
    }
}
