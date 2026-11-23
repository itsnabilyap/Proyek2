<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use App\Models\Produk;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProdukController extends Controller
{
    public const BATAS = DashboardController::BATAS_STOK;
    /** Nama ukuran yang langsung muncul saat tambah produk baru (boleh diubah/dihapus/ditambah). */
    public const UKURAN_AWAL = ['Small', 'Medium', 'Large'];

    /** Batas jumlah ukuran per produk. */
    public const MAKS_UKURAN = 10;

    public function index(Request $r): View
    {
        $filter = $r->query('filter', 'semua');

        $ringkas = [
            'semua'    => Produk::count(),
            'tampil'   => Produk::where('aktif', true)->count(),
            'sembunyi' => Produk::where('aktif', false)->count(),
            'menipis'  => Produk::where('stok', '<', self::BATAS)->count(),
        ];

        $menipis = Produk::where('stok', '<', self::BATAS)->orderBy('stok')->get(['nama_produk', 'stok']);

        $produk = Produk::with(['kategori', 'ukuran'])
            ->when($r->filled('q'), fn ($q) => $q->where('nama_produk', 'like', '%' . $r->query('q') . '%'))
            ->when($r->filled('kategori'), fn ($q) => $q->where('kategori_id', $r->query('kategori')))
            ->when($filter === 'tampil', fn ($q) => $q->where('aktif', true))
            ->when($filter === 'sembunyi', fn ($q) => $q->where('aktif', false))
            ->when($filter === 'menipis', fn ($q) => $q->where('stok', '<', self::BATAS))
            ->orderBy('nama_produk')
            ->paginate(10)
            ->withQueryString();

        $kategori = Kategori::orderBy('nama_kategori')->get();

        return view('admin.produk.index', compact('produk', 'ringkas', 'menipis', 'kategori', 'filter'));
    }

    public function create(Request $r): View
    {
        $produk = new Produk(['aktif' => true, 'stok' => 0]);
    
        return view($r->ajax() ? 'admin.produk._form' : 'admin.produk.create', [
            'produk' => $produk,
            'kategori' => Kategori::orderBy('nama_kategori')->get(),
        ]);
    }

    public function store(Request $r): RedirectResponse
    {
        $produk = $this->simpan($r, new Produk());

        return redirect()->route('admin.produk.index')->with('success', "Produk {$produk->nama_produk} ditambahkan.");
    }

    public function edit(Request $r, Produk $produk): View
    {
        $produk->load('ukuran');
    
        return view($r->ajax() ? 'admin.produk._form' : 'admin.produk.edit', [
            'produk' => $produk,
            'kategori' => Kategori::orderBy('nama_kategori')->get(),
        ]);
    }

    public function update(Request $r, Produk $produk): RedirectResponse
    {
        $this->simpan($r, $produk);

        return redirect()->route('admin.produk.index')->with('success', "Produk {$produk->nama_produk} diperbarui.");
    }

    public function toggle(Produk $produk): RedirectResponse
    {
        $produk->aktif = ! $produk->aktif;
        $produk->save();

        return back()->with('success', $produk->nama_produk . ($produk->aktif ? ' ditampilkan di katalog.' : ' disembunyikan dari katalog.'));
    }

    public function destroy(Produk $produk): RedirectResponse
    {
        $produk->delete(); // soft delete, riwayat pesanan aman

        return back()->with('success', "Produk {$produk->nama_produk} dihapus.");
    }

    /** Validasi + simpan produk, ukuran (jumlahnya bebas), dan varian. Dipakai store & update. */
    private function simpan(Request $r, Produk $produk): Produk
    {
        // Baris ukuran yang nama & harganya kosong dianggap tidak diisi.
        $r->merge([
            'ukuran' => collect($r->input('ukuran', []))
                ->filter(fn ($u) => filled($u['nama'] ?? null) || filled($u['harga'] ?? null))
                ->values()->all(),
        ]);

        $data = $r->validate([
            'nama_produk'         => ['required', 'string', 'max:100'],
            'kategori_id'         => ['required', 'exists:kategori,id'],
            'deskripsi'           => ['nullable', 'string', 'max:2000'],
            'stok'                => ['required', 'integer', 'min:0'],
            'gambar'              => [$produk->exists ? 'nullable' : 'required', 'image', 'max:2048'],
            'ukuran_foto'         => ['nullable', 'string', 'max:30'],
            'varian_judul'        => ['nullable', 'string', 'max:50', 'required_with:varian_pilihan'],
            'varian_pilihan'      => ['nullable', 'string', 'max:500', 'required_with:varian_judul'],
            'ukuran'              => ['required', 'array', 'min:1', 'max:' . self::MAKS_UKURAN],
            'ukuran.*.id'         => ['nullable', 'integer'],
            'ukuran.*.nama'       => ['required', 'string', 'max:30', 'distinct:ignore_case'],
            'ukuran.*.harga'      => ['required', 'integer', 'min:1000'],
            'ukuran.*.label'      => ['nullable', 'string', 'max:30'],
            'ukuran.*.keterangan' => ['nullable', 'string', 'max:150'],
            'ukuran.*.detail'     => ['nullable', 'string', 'max:1000'],
        ], [
            'gambar.required'               => 'Foto produk wajib diunggah.',
            'gambar.max'                    => 'Ukuran foto maksimal 2 MB.',
            'ukuran.required'               => 'Isi minimal satu ukuran beserta harganya.',
            'ukuran.max'                    => 'Maksimal ' . self::MAKS_UKURAN . ' ukuran per produk.',
            'ukuran.*.nama.required'        => 'Nama ukuran wajib diisi.',
            'ukuran.*.nama.distinct'        => 'Nama ukuran tidak boleh kembar.',
            'ukuran.*.harga.required'       => 'Harga wajib diisi.',
            'ukuran.*.harga.min'            => 'Harga minimal Rp1.000.',
            'varian_judul.required_with'    => 'Isi judul varian, misal "Merek coklat".',
            'varian_pilihan.required_with'  => 'Isi pilihan varian, satu per baris.',
        ]);

        // Slug unik (termasuk produk yang sudah dihapus)
        $slug = Str::slug($data['nama_produk']);
        $dasar = $slug;
        for ($i = 2; Produk::withTrashed()->where('slug', $slug)->where('id', '!=', $produk->id)->exists(); $i++) {
            $slug = "{$dasar}-{$i}";
        }

        // Foto
        if ($r->hasFile('gambar')) {
            $file = $r->file('gambar');
            $nama = $slug . '-' . time() . '.' . $file->extension();
            $file->move(public_path('images/produk'), $nama);

            if ($produk->gambar && is_file(public_path('images/produk/' . $produk->gambar))) {
                @unlink(public_path('images/produk/' . $produk->gambar));
            }
            $produk->gambar = $nama;
        }

        // Varian: satu pilihan per baris, kosong & kembar dibuang
        $pilihan = collect(preg_split('/\r\n|\n/', $data['varian_pilihan'] ?? ''))
            ->map(fn ($v) => trim($v))->filter()->unique()->take(10)->values()->all();

        DB::transaction(function () use ($produk, $data, $slug, $pilihan, $r) {
            $produk->forceFill([
                'nama_produk'    => $data['nama_produk'],
                'slug'           => $slug,
                'kategori_id'    => $data['kategori_id'],
                'deskripsi'      => $data['deskripsi'] ?? null,
                'stok'           => $data['stok'],
                'ukuran_foto'    => $data['ukuran_foto'] ?? null,
                'varian_judul'   => $pilihan ? $data['varian_judul'] : null,
                'varian_pilihan' => $pilihan ?: null,
                'aktif'          => $r->boolean('aktif'),
            ])->save();

            // Ukuran: baris dengan id = ubah, tanpa id = baru, yang tidak dikirim lagi = dihapus.
            // Riwayat pesanan tetap aman karena detail_pesanan menyimpan salinan nama & harga.
            $dipakai = [];
            foreach ($data['ukuran'] as $u) {
                $ukuran = ! empty($u['id'])
                    ? $produk->ukuran()->whereKey($u['id'])->first() ?? $produk->ukuran()->make()
                    : $produk->ukuran()->make();

                $ukuran->forceFill([
                    'nama_ukuran' => $u['nama'],
                    'label'       => $u['label'] ?? null,
                    'harga'       => $u['harga'],
                    'keterangan'  => $u['keterangan'] ?? null,
                    'detail'      => $this->bacaDetail($u['detail'] ?? ''),
                ])->save();

                $dipakai[] = $ukuran->id;
            }
            \App\Models\UkuranProduk::where('produk_id', $produk->id)->whereNotIn('id', $dipakai)->delete();
        });

        return $produk;
    }

    /** "Tinggi: 45 cm" per baris → ['Tinggi' => '45 cm'] */
    private function bacaDetail(string $teks): ?array
    {
        $hasil = collect(preg_split('/\r\n|\n/', $teks))
            ->map(fn ($baris) => array_map('trim', explode(':', $baris, 2)))
            ->filter(fn ($p) => count($p) === 2 && $p[0] !== '')
            ->mapWithKeys(fn ($p) => [$p[0] => $p[1]])
            ->all();

        return $hasil ?: null;
    }

    public function tambahStok(Request $r, Produk $produk): RedirectResponse
    {
        $jumlah = $r->validate(['jumlah' => ['required', 'integer', 'min:1', 'max:999']])['jumlah'];

        $produk->increment('stok', $jumlah);

        return back()->with('success', "Stok {$produk->nama_produk} +{$jumlah}, sekarang {$produk->stok} buket.");
    }
}