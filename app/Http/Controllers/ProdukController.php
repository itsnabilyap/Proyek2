<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Produk;
use Illuminate\Http\Request;

class ProdukController extends Controller
{
    /**
     * Katalog koleksi bucket untuk pelanggan.
     */
    public function index(Request $request)
    {
        $kategori = Kategori::orderBy('id_kategori')->get();

        $produk = Produk::with('kategori')
            ->when($request->filled('kategori'), fn ($q) => $q->where('id_kategori', $request->integer('kategori')))
            ->when($request->filled('q'), fn ($q) => $q->where('nama_produk', 'like', '%' . $request->query('q') . '%'))
            ->orderBy('id_produk')
            ->get();

        return view('pelanggan.katalog', [
            'kategori' => $kategori,
            'produk' => $produk,
            'kategoriAktif' => $request->query('kategori'),
        ]);
    }
}
