<?php

namespace Database\Seeders;

use App\Models\Bahan;
use Illuminate\Database\Seeder;

class BahanSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['Mawar merah', 'Bunga', 'tangkai', 12, 30],
            ['Lily pink', 'Bunga', 'tangkai', 18, 10],
            ['Gerbera', 'Bunga', 'tangkai', 25, 10],
            ['Bunga matahari', 'Bunga', 'tangkai', 10, 5],
            ['Baby breath', 'Bunga', 'ikat', 14, 5],
            ['Kertas wrapping goni', 'Wrapping', 'lembar', 4, 20],
            ['Kertas wrapping korea', 'Wrapping', 'lembar', 30, 20],
            ['Pita satin dusty pink', 'Pita & Aksesori', 'meter', 12, 10],
            ['Pita satin putih', 'Pita & Aksesori', 'meter', 18, 10],
            ['Kartu ucapan', 'Kartu & Lainnya', 'pcs', 80, 20],
        ];

        foreach ($data as [$nama, $kategori, $satuan, $stok, $minimal]) {
            Bahan::create([
                'nama_bahan'   => $nama,
                'kategori'     => $kategori,
                'satuan'       => $satuan,
                'stok'         => $stok,
                'stok_minimal' => $minimal,
            ]);
        }
    }
}