<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Produk;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Halaman Check Out & Keranjang (tab Pick Up / Kurir).
     */
    public function index(Request $request)
    {
        $items = Cart::with('produk')
            ->where('id_pelanggan', $request->user()->id)
            ->orderBy('id_cart')
            ->get();

        return view('pelanggan.checkout', [
            'items' => $items,
            'subtotal' => $items->sum('subtotal'),
            'metode' => $request->query('metode') === 'kurir' ? 'kurir' : 'pickup',
        ]);
    }

    /**
     * Hapus item dari keranjang.
     */
    public function destroy(Request $request, int $id)
    {
        Cart::where('id_pelanggan', $request->user()->id)
            ->where('id_cart', $id)
            ->delete();

        return back()->with('success', 'Produk dihapus dari keranjang.');
    }

    /**
     * Tambahkan produk dari katalog ke keranjang.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'id_produk' => 'required|exists:produk,id_produk',
        ]);

        $produk = Produk::findOrFail($data['id_produk']);

        $item = Cart::firstOrNew([
            'id_pelanggan' => $request->user()->id,
            'id_produk' => $produk->id_produk,
        ]);

        $item->jumlah = ($item->exists ? $item->jumlah : 0) + 1;
        $item->subtotal = $item->jumlah * $produk->harga;
        $item->save();

        return back()->with('success', $produk->nama_produk . ' ditambahkan ke keranjang.');
    }
}
