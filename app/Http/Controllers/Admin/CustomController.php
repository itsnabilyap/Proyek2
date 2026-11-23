<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Custom;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/** Tab "Request Custom" di Data Pesanan. Melihat: admin & owner. Mengubah status: admin saja. */
class CustomController extends Controller
{
    public function index(Request $request): View
    {
        $status = array_key_exists($request->query('status'), Custom::STATUS) ? $request->query('status') : 'semua';
        $q = trim((string) $request->query('q'));

        $jumlah = Custom::selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');
        $tabs = ['semua' => ['Semua', $jumlah->sum()]]
            + collect(Custom::STATUS)->map(fn ($s, $k) => [$s[0], $jumlah[$k] ?? 0])->all();

        $custom = Custom::with('pesanan')
            ->when($status !== 'semua', fn ($x) => $x->where('status', $status))
            ->when($request->filled('jenis'), fn ($x) => $x->where('jenis_bucket', $request->query('jenis')))
            ->when($q !== '', fn ($x) => $x->where(fn ($w) => $w
                ->where('nama_pemesan', 'like', "%{$q}%")
                ->orWhere('no_whatsapp', 'like', "%{$q}%")
                ->orWhere('jenis_bucket', 'like', "%{$q}%")
                ->orWhere('id', ltrim($q, '#'))))
            // Yang baru di atas, lalu yang tanggal dibutuhkannya paling dekat
            ->orderByRaw("case when status = 'baru' then 0 else 1 end")
            ->orderBy('tanggal_dibutuhkan')
            ->paginate(10)->withQueryString();

        return view('admin.custom.index', compact('custom', 'tabs', 'status'));
    }

    public function show(Request $request, Custom $custom): View
    {
        return view($request->ajax() ? 'admin.custom._detail' : 'admin.custom.show', compact('custom'));
    }

    public function update(Request $request, Custom $custom): RedirectResponse
    {
        $data = $request->validate([
            'status'      => ['required', Rule::in(array_keys(Custom::STATUS))],
            'harga_final' => ['nullable', 'required_if:status,disetujui', 'integer', 'min:1000', 'max:100000000'],
        ], [
            'harga_final.required_if' => 'Isi harga final sebelum menandai request disetujui.',
            'harga_final.min'         => 'Harga final minimal Rp1.000.',
        ]);

        $custom->update($data);

        return back()->with('success', "Request #{$custom->id} ditandai " . strtolower(Custom::STATUS[$data['status']][0]) . '.');
    }
}
