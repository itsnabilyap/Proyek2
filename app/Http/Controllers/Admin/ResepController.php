<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bahan;
use App\Models\Produk;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/** Resep bahan per ukuran produk (popup "Resep" di Katalog Produk). Khusus admin. */
class ResepController extends Controller
{
    public function edit(Request $request, Produk $produk): View
    {
        $produk->load('ukuran.bahan');
        $bahan = Bahan::orderBy('kategori')->orderBy('nama_bahan')->get();

        return view($request->ajax() ? 'admin.produk._resep' : 'admin.produk.resep', compact('produk', 'bahan'));
    }

    public function update(Request $request, Produk $produk): RedirectResponse
    {
        $ukuranIds = $produk->ukuran()->pluck('id')->all();

        $data = $request->validate([
            'resep'              => ['nullable', 'array'],
            'resep.*'            => ['array'],
            'resep.*.*.bahan_id' => ['required', 'integer', 'exists:bahan,id'],
            'resep.*.*.jumlah'   => ['required', 'integer', 'min:1', 'max:999'],
        ], [
            'resep.*.*.bahan_id.required' => 'Pilih bahan di setiap baris resep.',
            'resep.*.*.jumlah.required'   => 'Isi jumlah bahan di setiap baris resep.',
            'resep.*.*.jumlah.min'        => 'Jumlah bahan minimal 1.',
        ]);

        DB::transaction(function () use ($produk, $ukuranIds, $data) {
            foreach ($produk->ukuran as $ukuran) {
                // Bahan yang sama di satu ukuran digabung jumlahnya
                $isi = collect($data['resep'][$ukuran->id] ?? [])
                    ->groupBy('bahan_id')
                    ->map(fn ($baris) => ['jumlah' => $baris->sum('jumlah')])
                    ->all();

                $ukuran->bahan()->sync($isi);
            }
        });

        return back()->with('success', "Resep bahan {$produk->nama_produk} disimpan.");
    }
}
