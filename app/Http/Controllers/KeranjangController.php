<?php

namespace App\Http\Controllers;

use App\Models\Keranjang;
use App\Models\Produk;
use App\Models\UkuranProduk;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class KeranjangController extends Controller
{
    public function index(Request $request): View
    {
        $items = $request->user()->keranjang()
            ->with(['ukuran.produk.kategori', 'ukuran.produk.ukuran'])
            ->latest()
            ->get();

        return view('pelanggan.keranjang.index', compact('items'));
    }

    /** Dipakai nanti oleh tombol "Tambah ke Keranjang" di halaman detail produk. */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'ukuran_produk_id' => ['required', 'exists:ukuran_produk,id'],
            'jumlah'           => ['required', 'integer', 'min:1', 'max:20'],
            'kartu_ucapan'     => ['nullable', 'string', 'max:300'],
        ]);

        // Varian wajib dipilih kalau produknya punya varian, dan harus salah satu pilihannya.
        $produk = \App\Models\UkuranProduk::findOrFail($data['ukuran_produk_id'])->produk;
        $data += $request->validate([
            'varian' => $produk->punyaVarian()
                ? ['required', Rule::in($produk->varian_pilihan)]
                : ['exclude'],
        ], [
            'varian.required' => 'Pilih ' . strtolower((string) $produk->varian_judul) . ' dulu.',
            'varian.in'       => 'Pilihan ' . strtolower((string) $produk->varian_judul) . ' tidak tersedia.',
        ]);

        // "Harga lain" (Bucket Snack): pelanggan mengetik harga sendiri, minimal harga_bebas_min
        $data['harga_bebas'] = null;
        if ($produk->harga_bebas_min && $request->input('pakai_harga_lain')) {
            $data['harga_bebas'] = (int) $request->validate([
                'harga_lain' => ['required', 'integer', 'min:' . $produk->harga_bebas_min, 'max:5000000'],
            ], [
                'harga_lain.required' => 'Isi harga yang kamu inginkan.',
                'harga_lain.min'      => 'Harga minimal Rp' . number_format($produk->harga_bebas_min, 0, ',', '.') . '.',
            ])['harga_lain'];
        }

        // Warna bunga & kertas (bucket uang tidak memakai pilihan warna)
        if ($produk->pakaiWarna()) {
            $warna = $request->validate([
                'warna_bunga'  => ['nullable', Rule::in(Produk::WARNA_BUNGA)],
                'warna_kertas' => ['nullable', Rule::in(Produk::WARNA_KERTAS)],
            ]);
            $data['warna'] = collect([
                ($warna['warna_bunga'] ?? null) ? 'Bunga ' . strtolower($warna['warna_bunga']) : null,
                ($warna['warna_kertas'] ?? null) ? 'kertas ' . strtolower($warna['warna_kertas']) : null,
            ])->filter()->implode(' · ') ?: null;
        }

        // Produk + ukuran + varian + warna + ucapan yang sama digabung, jumlahnya ditambah.
        $item = $request->user()->keranjang()
            ->where('ukuran_produk_id', $data['ukuran_produk_id'])
            ->where('varian', $data['varian'] ?? null)
            ->where('warna', $data['warna'] ?? null)
            ->where('harga_bebas', $data['harga_bebas'])
            ->where('kartu_ucapan', $data['kartu_ucapan'] ?? null)
            ->first();

        // Stok habis / tidak cukup: tolak (jumlah di keranjang ikut dihitung)
        $sudah = (int) $request->user()->keranjang()
            ->whereHas('ukuran', fn ($q) => $q->where('produk_id', $produk->id))->sum('jumlah');
        if ($produk->stok < 1) {
            return back()->withInput()->withErrors(['jumlah' => "Stok {$produk->nama_produk} sedang habis."]);
        }
        if ($sudah + $data['jumlah'] > $produk->stok) {
            $sisa = max(0, $produk->stok - $sudah);
            return back()->withInput()->withErrors(['jumlah' => match (true) {
                $sudah === 0 => "Stok tinggal {$produk->stok} buket.",
                $sisa > 0    => "Stok tinggal {$produk->stok} buket, di keranjangmu sudah {$sudah}. Maksimal tambah {$sisa} lagi.",
                default      => "Stok tinggal {$produk->stok} buket dan semuanya sudah ada di keranjangmu.",
            }]);
        }

        if ($item) {
            $item->increment('jumlah', $data['jumlah']);
        } else {
            $item = $request->user()->keranjang()->create($data);
        }

        // Tombol "Pesan Sekarang" → langsung checkout item ini saja
        if ($request->boolean('langsung')) {
            return redirect()->route('checkout.index', ['item' => [$item->id]]);
        }

        return back()->with('status', 'Buket ditambahkan ke keranjang.');
    }

    public function update(Request $request, Keranjang $keranjang): RedirectResponse
    {
        abort_unless($keranjang->user_id === $request->user()->id, 404);

        $keranjang->load('ukuran.produk');
        $produk = $keranjang->ukuran->produk;

        $data = $request->validate([
            // Ukuran baru harus milik produk yang sama.
            'ukuran_produk_id' => ['sometimes', Rule::exists('ukuran_produk', 'id')->where('produk_id', $produk->id)],
            'aksi'             => ['sometimes', Rule::in(['tambah', 'kurang'])],
        ]);

        if (isset($data['ukuran_produk_id'])) {
            $keranjang->ukuran_produk_id = $data['ukuran_produk_id'];
            $keranjang->harga_bebas = null; // ganti ke paket → harga ketikan tidak berlaku lagi
        }

        if (($data['aksi'] ?? null) === 'tambah') {
            if ($keranjang->jumlah >= $produk->stok) {
                return back()->with('status', "Stok {$produk->nama_produk} tinggal {$produk->stok}.");
            }
            $keranjang->jumlah++;
        }

        if (($data['aksi'] ?? null) === 'kurang' && $keranjang->jumlah > 1) {
            $keranjang->jumlah--;
        }

        $keranjang->save();

        return back();
    }

    public function destroy(Request $request, Keranjang $keranjang): RedirectResponse
    {
        abort_unless($keranjang->user_id === $request->user()->id, 404);

        $keranjang->delete();

        return back()->with('status', 'Buket dihapus dari keranjang.');
    }
}