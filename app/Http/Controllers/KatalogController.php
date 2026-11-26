<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KatalogController extends Controller
{
    public function index(Request $request): View
    {
        $kategoriAktif = $request->query('kategori');
        $urut = in_array($request->query('urut'), ['termurah', 'termahal', 'terbaru'], true)
            ? $request->query('urut')
            : 'termurah';

        $produk = Produk::aktif()
            ->with(['kategori', 'ukuran', 'galeri'])
            ->withMin('ukuran as harga_mulai', 'harga')
            ->when($kategoriAktif, fn ($q) => $q->whereHas('kategori', fn ($k) => $k->where('slug', $kategoriAktif)))
            ->when($urut === 'termurah', fn ($q) => $q->orderBy('harga_mulai'))
            ->when($urut === 'termahal', fn ($q) => $q->orderByDesc('harga_mulai'))
            ->when($urut === 'terbaru', fn ($q) => $q->latest())
            ->get();

        return view('pelanggan.katalog.index', [
            'produk' => $produk,
            'kategori' => Kategori::orderBy('nama_kategori')->get(),
            'kategoriAktif' => $kategoriAktif,
            'urut' => $urut,
        ]);
    }

    public function show(Produk $produk): View
    {
        abort_unless($produk->aktif, 404);
    
        $produk->load(['kategori', 'ukuran', 'galeri']);

    
        $lainnya = Produk::aktif()
            ->whereKeyNot($produk->id)
            ->withMin('ukuran as harga_mulai', 'harga')
            ->inRandomOrder()
            ->take(3)
            ->get();
    
        return view('pelanggan.katalog.show', compact('produk', 'lainnya'));
    }
}