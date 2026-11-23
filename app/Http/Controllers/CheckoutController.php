<?php

namespace App\Http\Controllers;

use App\Models\Custom;
use App\Models\Pesanan;
use App\Models\Produk;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        // Bayar request custom yang sudah disetujui (?custom=ID)
        if ($request->filled('custom')) {
            $custom = $this->customSiapBayar($request);

            return view('pelanggan.checkout.index', [
                'custom' => $custom,
                'items' => collect([$this->itemCustom($custom)]),
                'subtotal' => $custom->harga_final,
                'kurir' => Pesanan::KURIR,
                'slot' => Pesanan::SLOT,
            ]);
        }

        $ids = array_filter((array) $request->query('item'));

        $items = $request->user()->keranjang()
            ->when($ids, fn ($q) => $q->whereIn('id', $ids))
            ->with('ukuran.produk')
            ->get();

        if ($items->isEmpty()) {
            return redirect()->route('katalog.index')->with('status', 'Keranjang masih kosong. Pilih buket dulu, ya.');
        }

        return view('pelanggan.checkout.index', [
            'items' => $items,
            'subtotal' => $items->sum->subtotal,
            'kurir' => Pesanan::KURIR,
            'slot' => Pesanan::SLOT,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'metode_pengiriman' => ['required', Rule::in(['kurir', 'pickup'])],
            'nama_penerima'     => ['required', 'string', 'max:100'],
            'no_whatsapp'       => ['required', 'regex:/^(\+62|62|0)8[0-9]{7,12}$/'],
            'alamat'            => ['required_if:metode_pengiriman,kurir', 'nullable', 'string', 'max:500'],
            'patokan'           => ['nullable', 'string', 'max:200'],
            'layanan_kurir'     => ['required_if:metode_pengiriman,kurir', 'nullable', Rule::in(array_keys(Pesanan::KURIR))],
            'tanggal'           => ['required', 'date', 'after_or_equal:today'],
            'slot_waktu'        => ['required', Rule::in(Pesanan::SLOT)],
            'catatan_florist'   => ['nullable', 'string', 'max:300'],
            'metode_pembayaran' => ['required', Rule::in(['qris', 'va'])],
            'item'      => ['required_without:custom_id', 'array'],
            'item.*'    => ['integer'],
            'custom_id' => ['nullable', 'integer'],
        ], [
            'nama_penerima.required'     => 'Nama wajib diisi.',
            'no_whatsapp.required'       => 'Nomor WhatsApp wajib diisi.',
            'no_whatsapp.regex'          => 'Nomor WhatsApp harus diawali 08 atau 628.',
            'alamat.required_if'         => 'Alamat pengiriman wajib diisi.',
            'layanan_kurir.required_if'  => 'Pilih layanan kurir.',
            'tanggal.required'           => 'Pilih tanggal.',
            'tanggal.after_or_equal'     => 'Tanggal tidak boleh sebelum hari ini.',
            'slot_waktu.required'        => 'Pilih slot waktu.',
            'metode_pembayaran.required' => 'Pilih metode pembayaran.',
        ]);

        $user = $request->user();
        $pickup = $data['metode_pengiriman'] === 'pickup';

        // Data kurir dibuang kalau ambil di toko.
        if ($pickup) {
            $data['alamat'] = $data['patokan'] = $data['layanan_kurir'] = null;
        }

        if (! empty($data['custom_id'])) {
            $pesanan = $this->buatPesananCustom($request, $data, $pickup);
        } else {
        $pesanan = DB::transaction(function () use ($user, $data, $pickup) {
            $items = $user->keranjang()->whereIn('id', $data['item'])->with('ukuran.produk.kategori')->get();

            if ($items->isEmpty()) {
                throw ValidationException::withMessages(['keranjang' => 'Keranjang kosong.']);
            }

            $subtotal = $titipan = 0;
            foreach ($items as $item) {
                // Kunci baris produk supaya dua pembeli tidak mengambil stok terakhir bersamaan.
                $produk = Produk::lockForUpdate()->findOrFail($item->ukuran->produk_id);

                if ($produk->stok < $item->jumlah) {
                    throw ValidationException::withMessages([
                        'keranjang' => "Stok {$produk->nama_produk} tinggal {$produk->stok}.",
                    ]);
                }

                $item->setRelation('produk', $produk);
                $subtotal += $item->harga_satuan * $item->jumlah;
                $titipan += $item->nilai_uang * $item->jumlah;
            }

            $ongkir = $pickup ? 0 : Pesanan::KURIR[$data['layanan_kurir']]['ongkir'];

            $pesanan = $user->pesanan()->create([
                ...collect($data)->except('item')->all(),
                'subtotal' => $subtotal,
                'uang_titipan' => $titipan,     // bagian subtotal yang berupa uang bucket uang
                'ongkir' => $ongkir,
                'total_harga' => $subtotal + $ongkir,
            ]);

            foreach ($items as $item) {
                $pesanan->detailPesanan()->create([
                    'produk_id' => $item->produk->id,
                    'ukuran_produk_id' => $item->ukuran->id,
                    'nama_produk' => $item->produk->nama_produk,
                    'nama_ukuran' => $item->nama_ukuran,
                    'varian' => $item->varian,
                    'warna' => $item->warna,
                    'harga' => $item->harga_satuan,
                    'uang_titipan' => $item->nilai_uang,
                    'jumlah' => $item->jumlah,
                    'kartu_ucapan' => $item->kartu_ucapan,
                ]);
                $item->produk->decrement('stok', $item->jumlah);
            }

            $user->keranjang()->whereIn('id', $items->pluck('id'))->delete();

            return $pesanan->fresh();
        });
        }

        if (! $user->no_hp) {
            $user->update(['no_hp' => $data['no_whatsapp']]);
        }

        return redirect()->route('pembayaran.show', $pesanan);
    }

    /* ================================================================
       REQUEST CUSTOM → PESANAN
       ================================================================ */

    /** Request custom milik user yang sudah disetujui admin dan belum dibayar. */
    private function customSiapBayar(Request $request, ?int $id = null): Custom
    {
        $custom = $request->user()->Custom()->with('pesanan')->findOrFail($id ?? (int) $request->query('custom'));

        // Sudah ada tagihan yang masih berjalan → lanjutkan ke halaman bayarnya
        if ($custom->pesanan && $custom->pesanan->status_pesanan === 'menunggu_pembayaran') {
            abort(redirect()->route('pembayaran.show', $custom->pesanan));
        }

        abort_unless($custom->bisaDibayar(), 404);

        return $custom;
    }

    /** Bentuk "item keranjang" untuk ringkasan checkout. */
    private function itemCustom(Custom $custom): object
    {
        return (object) [
            'id' => null,
            'ukuran' => (object) [
                'nama_ukuran' => 'Custom',
                'produk' => (object) ['nama_produk' => $custom->jenis_bucket, 'gambar_url' => $custom->foto_referensi_url],
            ],
            'varian' => null,
            'warna' => $custom->tema,
            'jumlah' => 1,
            'kartu_ucapan' => null,
            'subtotal' => $custom->harga_final,
        ];
    }

    private function buatPesananCustom(Request $request, array $data, bool $pickup): Pesanan
    {
        return DB::transaction(function () use ($request, $data, $pickup) {
            $custom = Custom::lockForUpdate()->findOrFail($data['custom_id']);
            $custom = $this->customSiapBayar($request, $custom->id);

            $ongkir = $pickup ? 0 : Pesanan::KURIR[$data['layanan_kurir']]['ongkir'];

            $pesanan = $request->user()->pesanan()->create([
                ...collect($data)->except(['item', 'custom_id'])->all(),
                'subtotal' => $custom->harga_final,
                'ongkir' => $ongkir,
                'total_harga' => $custom->harga_final + $ongkir,
            ]);

            // Produk custom tidak punya stok katalog: produk_id & ukuran_produk_id dibiarkan kosong
            $pesanan->detailPesanan()->create([
                'nama_produk' => $custom->jenis_bucket,
                'nama_ukuran' => 'Custom',
                'warna' => $custom->tema ? \Illuminate\Support\Str::limit($custom->tema, 100, '') : null,
                'harga' => $custom->harga_final,
                'jumlah' => 1,
            ]);

            $custom->update(['pesanan_id' => $pesanan->id]);

            return $pesanan->fresh();
        });
    }
}