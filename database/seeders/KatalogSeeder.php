<?php

namespace Database\Seeders;

use App\Models\Kategori;
use App\Models\Produk;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class KatalogSeeder extends Seeder
{
    public function run(): void
    {
        // [nama, kategori, harga mulai, gambar, deskripsi]
        $data = [
            ['Bucket Coklat', 'Buket Coklat', 60000, 'bucket-coklat.jpg',
                'Rangkaian coklat favorit dibalut wrapping cantik. Manis untuk ulang tahun, valentine, atau kejutan kecil.'],
            ['Bucket Tangkai Mawar Merah', 'Buket Bunga', 75000, 'bucket-mawar-merah.jpg',
                'Mawar merah pilihan dirangkai rapi, simbol cinta dan ketulusan. Cocok untuk anniversary dan momen romantis.'],
            ['Bucket Uang', 'Buket Uang', 70000, 'bucket-uang.jpg',
                'Uang dilipat dan dirangkai jadi buket unik. Harga adalah biaya rangkai, belum termasuk uang yang dirangkai.'],
            ['Bucket Wisuda', 'Buket Wisuda', 70000, 'bucket-wisuda.jpg',
                'Buket meriah untuk merayakan kelulusan. Bisa ditambah boneka atau kartu ucapan untuk sang wisudawan.'],
            ['Bucket Thumbelina', 'Buket Bunga', 85000, 'bucket-thumbelina.jpg',
                'Rangkaian bunga thumbelina mungil yang lembut dan manis. Hadiah sederhana yang tetap berkesan.'],
            ['Bucket Satin Premium', 'Buket Satin', 95000, 'bucket-satin-premium.jpg',
                'Bunga dari pita satin buatan tangan. Tidak layu, awet disimpan selamanya sebagai kenang-kenangan.'],
        ];

        foreach ($data as [$nama, $kategori, $harga, $gambar, $deskripsi]) {
            $kat = Kategori::firstOrCreate(
                ['slug' => Str::slug($kategori)],
                ['nama_kategori' => $kategori]
            );

            $produk = Produk::create([
                'kategori_id' => $kat->id,
                'nama_produk' => $nama,
                'slug' => Str::slug($nama),
                'deskripsi' => $deskripsi,
                'stok' => 10,
            ]);

            if ($gambar) {
                $produk->galeri()->create(['file' => $gambar, 'urutan' => 0]);
            }

            // Baru ukuran termurah. Medium/Large ditambah saat halaman detail produk dibuat.
            $produk->ukuran()->create(['nama_ukuran' => 'Small', 'harga' => $harga]);
        }
    }
}