<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PelangganController extends Controller
{
    /** Status pesanan yang dihitung sebagai belanja (sudah dibayar). */
    public const LUNAS = ['dirangkai', 'siap_diambil', 'siap_dikirim', 'dikirim', 'selesai'];

    public function index(Request $r): View
    {
        $pelanggan = $this->query()
            ->when($r->filled('q'), function ($q) use ($r) {
                $cari = trim($r->query('q'));

                // "PLG-0003" / "plg3" → cari berdasarkan id
                if (preg_match('/^plg-?0*(\d+)$/i', $cari, $m)) {
                    return $q->where('id', $m[1]);
                }

                $like = "%{$cari}%";
                $q->where(fn ($w) => $w
                    ->where('name', 'like', $like)
                    ->orWhere('email', 'like', $like)
                    ->orWhere('username', 'like', $like)
                    ->orWhere('no_hp', 'like', $like)
                    ->orWhereHas('pesanan', fn ($p) => $p->where('no_whatsapp', 'like', $like)));
            })
            ->orderBy('id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.pelanggan.index', compact('pelanggan'));
    }

    public function show(Request $r, User $pelanggan): View
    {
        abort_unless($pelanggan->role === 'pelanggan', 404);
    
        $pelanggan = $this->query()->findOrFail($pelanggan->id);
        $riwayat = $pelanggan->pesanan()->with('detailPesanan')->latest()->take(3)->get();
        $alamat = $pelanggan->pesanan()->where('metode_pengiriman', 'kurir')->latest()->value('alamat');
    
        $view = $r->ajax() ? 'admin.pelanggan._detail' : 'admin.pelanggan.show';
    
        return view($view, compact('pelanggan', 'riwayat', 'alamat'));
    }

    /** Pelanggan + angka ringkasannya. */
    private function query()
    {
        return User::where('role', 'pelanggan')
            ->withCount([
                'pesanan as jumlah_pesanan' => fn ($q) => $q->where('status_pesanan', '!=', 'dibatalkan'),
                'custom as jumlah_custom',
            ])
            ->withSum(['pesanan as total_belanja' => fn ($q) => $q->whereIn('status_pesanan', self::LUNAS)], 'total_harga')
            ->withMax('pesanan as pesanan_terakhir', 'created_at')
            ->with(['pesanan' => fn ($q) => $q->latest()->limit(1)]); // untuk nomor WA cadangan
    }
}