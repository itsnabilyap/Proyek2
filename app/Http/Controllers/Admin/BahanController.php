<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bahan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BahanController extends Controller
{
    public function index(Request $r): View
    {
        $bahan = Bahan::query()
            ->when($r->filled('q'), function ($q) use ($r) {
                $cari = trim($r->query('q'));
                if (preg_match('/^bhn-?0*(\d+)$/i', $cari, $m)) {
                    return $q->where('id', $m[1]);
                }
                $q->where('nama_bahan', 'like', "%{$cari}%");
            })
            ->when($r->filled('kategori'), fn ($q) => $q->where('kategori', $r->query('kategori')))
            ->when($r->filled('kategori'), fn ($q) => $q->where('kategori', $r->query('kategori')))
            ->when($r->query('status') === 'menipis', fn ($q) => $q->menipis())   // ← tambahkan
            ->orderBy('id')
            ->paginate(15)
            ->withQueryString();

        $menipis = Bahan::menipis()->orderBy('stok')->get(['nama_bahan', 'stok', 'satuan']);

        return view('admin.bahan.index', compact('bahan', 'menipis'));
    }

    public function store(Request $r): RedirectResponse
    {
        $data = $this->validasi($r);

        $bahan = Bahan::create([...$data, 'stok' => 0]);
        $bahan->ubahStok($data['stok'], 'masuk', 'Stok awal');

        return redirect()->route('admin.bahan.index')->with('success', "Bahan {$bahan->nama_bahan} ditambahkan.");
    }

    public function update(Request $r, Bahan $bahan): RedirectResponse
    {
        $data = $this->validasi($r);

        $bahan->update(collect($data)->except('stok')->all());
        $bahan->ubahStok($data['stok'] - $bahan->stok, 'penyesuaian', 'Koreksi stok lewat edit');

        return redirect()->route('admin.bahan.index')->with('success', "Bahan {$bahan->nama_bahan} diperbarui.");
    }

    public function tambahStok(Request $r, Bahan $bahan): RedirectResponse
    {
        $jumlah = $r->validate(['jumlah' => ['required', 'integer', 'min:1', 'max:9999']])['jumlah'];

        $bahan->ubahStok($jumlah, 'masuk', 'Tambah stok');

        return back()->with('success', "Stok {$bahan->nama_bahan} +{$jumlah}, sekarang {$bahan->stok} {$bahan->satuan}.");
    }

    public function destroy(Bahan $bahan): RedirectResponse
    {
        $bahan->delete(); // soft delete, riwayat stok tetap tersimpan

        return back()->with('success', "Bahan {$bahan->nama_bahan} dihapus.");
    }

    private function validasi(Request $r): array
    {
        return $r->validate([
            'nama_bahan'   => ['required', 'string', 'max:100'],
            'kategori'     => ['required', Rule::in(Bahan::KATEGORI)],
            'satuan'       => ['required', Rule::in(Bahan::SATUAN)],
            'stok'         => ['required', 'integer', 'min:0', 'max:99999'],
            'stok_minimal' => ['required', 'integer', 'min:0', 'max:99999'],
        ]);
    }
}