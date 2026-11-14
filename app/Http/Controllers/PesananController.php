<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CustomPesanan;
use App\Models\DetailPesanan;
use App\Models\Kategori;
use App\Models\Pesanan;
use App\Models\Produk;
use App\Models\Testimoni;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PesananController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public const BUDGET = ['< Rp 100k', 'Rp 100k - 250k', '> Rp 250k'];

    /**
     * Form pemesanan custom.
     */
    public function custom()
    {
        return view('pelanggan.custom', [
            'jenisBucket' => Kategori::orderBy('id_kategori')->pluck('nama_kategori')->push('Lainnya'),
            'budget' => self::BUDGET,
        ]);
    }

    /**
     * Simpan pesanan custom (pesanan + detail custom).
     */
    public function storeCustom(Request $request)
    {
        $data = $request->validate([
            'nama_pemesan' => 'required|string|max:150',
            'no_whatsapp' => ['required', 'regex:/^[0-9+\s-]{8,20}$/'],
            'jenis_bucket' => 'required|string|max:100',
            'budget' => 'required|in:' . implode(',', self::BUDGET),
            'tanggal_dibutuhkan' => 'required|date|after_or_equal:today',
            'catatan_custom' => 'nullable|string|max:2000',
        ], [
            'no_whatsapp.regex' => 'Nomor WhatsApp tidak valid.',
            'tanggal_dibutuhkan.after_or_equal' => 'Tanggal tidak boleh sebelum hari ini.',
            'budget.required' => 'Pilih salah satu budget.',
        ]);

        DB::transaction(function () use ($request, $data) {
            $pesanan = Pesanan::create([
                'id_pelanggan' => $request->user()->id,
                'tanggal_pesanan' => now(),
                'status_pesanan' => 'Menunggu Konfirmasi',
                'total_harga' => 0, // diisi florist setelah konfirmasi harga
                'alamat_pengiriman' => $request->user()->alamat ?: 'Dikonfirmasi via WhatsApp',
                'catatan' => 'Pesanan custom',
            ]);

            CustomPesanan::create([
                'id_pesanan' => $pesanan->id_pesanan,
                'nama_pemesan' => $data['nama_pemesan'],
                'no_whatsapp' => $data['no_whatsapp'],
                'jenis_bucket' => $data['jenis_bucket'],
                'budget' => $data['budget'],
                'tanggal_dibutuhkan' => $data['tanggal_dibutuhkan'],
                'catatan_custom' => $data['catatan_custom'] ?? null,
                'harga_custom' => 0,
            ]);
        });

        return redirect()->route('pesanan.custom')
            ->with('success', 'Permintaan custom terkirim! Florist akan menghubungi kamu lewat WhatsApp untuk konfirmasi harga.');
    }

    /**
     * Proses checkout dari keranjang (Pick Up atau Kurir).
     *
     * CATATAN: pembayaran masih SIMULASI. Pesanan langsung ditandai lunas.
     * Untuk pembayaran sungguhan sambungkan payment gateway (mis. Midtrans)
     * dan ubah dibayar_pada setelah notifikasi pembayaran diterima.
     */
    public function checkout(Request $request)
    {
        $kurir = $request->input('metode') === 'kurir';

        $rules = [
            'metode' => 'required|in:pickup,kurir',
            'nama_penerima' => 'required|string|max:150',
            'no_whatsapp' => ['required', 'regex:/^[0-9+\s-]{8,20}$/'],
            'tanggal' => 'required|date|after_or_equal:today',
            'slot_waktu' => 'required|in:' . implode(',', Pesanan::SLOT_WAKTU),
            'catatan' => 'nullable|string|max:500',
            'metode_pembayaran' => 'required|in:' . implode(',', array_keys(Pesanan::PEMBAYARAN)),
        ];

        if ($kurir) {
            $rules['alamat'] = 'required|string|min:10|max:1000';
            $rules['patokan'] = 'nullable|string|max:255';
            $rules['layanan_kurir'] = 'required|in:' . implode(',', array_keys(Pesanan::LAYANAN_KURIR));
        }

        $data = $request->validate($rules, [
            'no_whatsapp.regex' => 'Nomor WhatsApp tidak valid.',
            'tanggal.after_or_equal' => 'Tanggal tidak boleh sebelum hari ini.',
            'metode_pembayaran.required' => 'Pilih metode pembayaran.',
        ]);

        $items = Cart::with('produk')->where('id_pelanggan', $request->user()->id)->get();

        if ($items->isEmpty()) {
            return redirect()->route('katalog.index')->with('success', 'Keranjang masih kosong, pilih bucket dulu ya.');
        }

        // Ongkir dihitung ulang di server, bukan dari input pengguna.
        $ongkir = $kurir ? Pesanan::LAYANAN_KURIR[$data['layanan_kurir']]['tarif'] : 0;
        $subtotal = $items->sum(fn ($i) => $i->jumlah * $i->produk->harga);

        $pesanan = DB::transaction(function () use ($request, $data, $items, $kurir, $ongkir, $subtotal) {
            $alamat = $kurir
                ? trim($data['alamat'] . (filled($data['patokan'] ?? null) ? "\nPatokan: " . $data['patokan'] : ''))
                : 'Ambil di toko: ' . Pesanan::ALAMAT_TOKO;

            $pesanan = Pesanan::create([
                'id_pelanggan' => $request->user()->id,
                'tanggal_pesanan' => now(),
                'status_pesanan' => 'Diproses',
                'metode_pengambilan' => $data['metode'],
                'nama_penerima' => $data['nama_penerima'],
                'no_whatsapp' => $data['no_whatsapp'],
                'tanggal_pengiriman' => $data['tanggal'],
                'slot_waktu' => $data['slot_waktu'],
                'layanan_kurir' => $kurir ? $data['layanan_kurir'] : null,
                'ongkir' => $ongkir,
                'metode_pembayaran' => $data['metode_pembayaran'],
                'dibayar_pada' => now(),
                'total_harga' => $subtotal + $ongkir,
                'alamat_pengiriman' => $alamat,
                'catatan' => $data['catatan'] ?? null,
            ]);

            foreach ($items as $item) {
                DetailPesanan::create([
                    'id_pesanan' => $pesanan->id_pesanan,
                    'id_produk' => $item->id_produk,
                    'jumlah' => $item->jumlah,
                    'harga' => $item->produk->harga,
                    'subtotal' => $item->jumlah * $item->produk->harga,
                ]);
            }

            Cart::where('id_pelanggan', $request->user()->id)->delete();

            return $pesanan;
        });

        return redirect()->route('pesanan.sukses', $pesanan->id_pesanan);
    }

    /**
     * Halaman Pembayaran Berhasil.
     */
    public function sukses(Request $request, int $id)
    {
        $pesanan = Pesanan::with('detailPesanan.produk')
            ->where('id_pelanggan', $request->user()->id)
            ->findOrFail($id);

        return view('pelanggan.sukses', ['pesanan' => $pesanan]);
    }

    /**
     * Kelompok status untuk tab filter Riwayat Pesanan.
     */
    public const FILTER_STATUS = [
        'semua' => null,
        'diproses' => ['Menunggu Konfirmasi', 'Diproses'],
        'siap' => ['Siap Diambil', 'Dikirim'],
        'selesai' => ['Selesai'],
    ];

    /**
     * Halaman Riwayat Pesanan pelanggan.
     */
    public function riwayat(Request $request)
    {
        $semua = Pesanan::with(['detailPesanan.produk', 'customPesanan'])
            ->where('id_pelanggan', $request->user()->id)
            ->orderByDesc('tanggal_pesanan')
            ->orderByDesc('id_pesanan')
            ->get();

        $hitung = [];
        foreach (self::FILTER_STATUS as $kunci => $status) {
            $hitung[$kunci] = $status === null
                ? $semua->count()
                : $semua->whereIn('status_pesanan', $status)->count();
        }

        $filter = array_key_exists($request->query('status'), self::FILTER_STATUS)
            ? $request->query('status') : 'semua';

        $pesanan = $filter === 'semua'
            ? $semua
            : $semua->whereIn('status_pesanan', self::FILTER_STATUS[$filter])->values();

        $ulasan = Testimoni::where('id_pelanggan', $request->user()->id)
            ->whereNotNull('id_pesanan')
            ->get()
            ->keyBy('id_pesanan');

        return view('pelanggan.riwayat', [
            'pesanan' => $pesanan,
            'hitung' => $hitung,
            'filter' => $filter,
            'ulasan' => $ulasan,
        ]);
    }

    /**
     * Tombol "Beli Lagi": masukkan ulang item pesanan ke keranjang.
     */
    public function beliLagi(Request $request, int $id)
    {
        $pesanan = Pesanan::with('detailPesanan', 'customPesanan')
            ->where('id_pelanggan', $request->user()->id)
            ->findOrFail($id);

        if ($pesanan->detailPesanan->isEmpty()) {
            return redirect()->route('pesanan.custom')
                ->with('success', 'Pesanan custom tidak bisa dipesan ulang otomatis. Silakan isi formulir custom lagi.');
        }

        foreach ($pesanan->detailPesanan as $d) {
            $produk = Produk::find($d->id_produk);
            if (! $produk) {
                continue;
            }

            $item = Cart::firstOrNew([
                'id_pelanggan' => $request->user()->id,
                'id_produk' => $produk->id_produk,
            ]);
            $item->jumlah = ($item->exists ? $item->jumlah : 0) + $d->jumlah;
            $item->subtotal = $item->jumlah * $produk->harga; // harga terbaru dari katalog
            $item->save();
        }

        return redirect()->route('keranjang.index')
            ->with('success', 'Produk dari pesanan ' . $pesanan->nomor . ' dimasukkan ke keranjang.');
    }

    /**
     * Tombol "Beri Ulasan / Review" pada pesanan yang sudah selesai.
     */
    public function ulasan(Request $request, int $id)
    {
        $pesanan = Pesanan::where('id_pelanggan', $request->user()->id)->findOrFail($id);

        if ($pesanan->status_pesanan !== 'Selesai') {
            return back()->with('success', 'Ulasan hanya bisa diberikan setelah pesanan selesai.');
        }

        $data = $request->validate([
            'rating' => 'required|integer|between:1,5',
            'isi_testimoni' => 'required|string|max:1000',
        ], [
            'rating.required' => 'Pilih rating bintang terlebih dahulu.',
            'isi_testimoni.required' => 'Tulis ulasanmu terlebih dahulu.',
        ]);

        Testimoni::updateOrCreate(
            ['id_pelanggan' => $request->user()->id, 'id_pesanan' => $pesanan->id_pesanan],
            ['rating' => $data['rating'], 'isi_testimoni' => $data['isi_testimoni'], 'tanggal' => now()->toDateString()]
        );

        return redirect()->route('pesanan.riwayat', ['status' => $request->query('status')])
            ->with('success', 'Terima kasih, ulasanmu sudah tersimpan!');
    }
}
