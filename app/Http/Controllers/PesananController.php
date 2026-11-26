<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PesananController extends Controller
{
    /** Pilihan "Tampilkan": key => [label, status di database] */
    private const FILTER = [
        'semua'    => ['Semua', null],
        'bayar'    => ['Menunggu Pembayaran', ['menunggu_pembayaran']],
        'proses'   => ['Diproses', ['dirangkai']],
        'siap'     => ['Siap Diambil / Dikirim', ['siap_diambil', 'siap_dikirim']],
        'dikirim'  => ['Dikirim', ['dikirim']],
        'selesai'  => ['Selesai', ['selesai']],
        'batal'    => ['Dibatalkan', ['dibatalkan']],
    ];

    public function index(Request $request): View
    {
        $user = $request->user();
        $filter = array_key_exists($request->query('status'), self::FILTER) ? $request->query('status') : 'semua';

        // Jumlah per status untuk dropdown
        $hitung = $user->pesanan()->selectRaw('status_pesanan, count(*) as total')
            ->groupBy('status_pesanan')->pluck('total', 'status_pesanan');

        $pilihan = collect(self::FILTER)->map(fn ($f, $key) => [
            'label' => $f[0],
            'jumlah' => $f[1] ? collect($f[1])->sum(fn ($s) => $hitung[$s] ?? 0) : $hitung->sum(),
        ]);

        $pesanan = $user->pesanan()
            ->when(self::FILTER[$filter][1], fn ($q, $status) => $q->whereIn('status_pesanan', $status))
            ->with(['detailPesanan.produk', 'testimoni', 'pembayaran' => fn ($q) => $q->where('status', 'lunas')])
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('pelanggan.pesanan.index', compact('pesanan', 'pilihan', 'filter'));
    }

    /** Pelanggan menandai pesanan kurir sudah diterima. */
    public function terima(Request $request, Pesanan $pesanan): RedirectResponse
    {
        abort_unless($pesanan->user_id === $request->user()->id, 404);
        abort_unless($pesanan->status_pesanan === 'dikirim', 403);

        $pesanan->update(['status_pesanan' => 'selesai']);

        return back()->with('status', "Pesanan {$pesanan->kode_pesanan} selesai. Jangan lupa beri ulasan, ya!");
    }

    public function ulasan(Request $request, Pesanan $pesanan): RedirectResponse
    {
        abort_unless($pesanan->user_id === $request->user()->id, 404);
        abort_unless($pesanan->status_pesanan === 'selesai', 403);

        $data = $request->validateWithBag('ulasan' . $pesanan->id, [
            'rating'        => ['required', 'integer', Rule::in([1, 2, 3, 4, 5])],
            'isi_testimoni' => ['nullable', 'string', 'max:1000'],
        ], [
            'rating.required' => 'Pilih bintang dulu.',
        ]);

        // Satu pesanan satu ulasan: kalau sudah ada, ulasannya diperbarui (bisa diedit pelanggan)
        $sudahAda = $pesanan->testimoni()->exists();
        $pesanan->testimoni()->updateOrCreate([], [...$data, 'user_id' => $request->user()->id]);

        return back()->with('status', $sudahAda ? 'Ulasan berhasil diperbarui.' : 'Terima kasih atas ulasannya!');
    }
}