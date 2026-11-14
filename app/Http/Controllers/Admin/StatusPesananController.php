<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StatusPesananController extends Controller
{
    private array $daftarStatus = [
        'Menunggu Konfirmasi',
        'Menunggu Dirangkai',
        'Sedang Dirangkai',
        'Siap Kirim',
        'Selesai',
        'Dibatalkan',
    ];

    public function index(Request $request)
    {
        $pesanan = Pesanan::with([
            'pelanggan',
            'detailPesanan',
            'customPesanan',
        ])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;

                $query->where(function ($q) use ($search) {
                    $q->where('id_pesanan', 'like', "%{$search}%")
                        ->orWhereHas('pelanggan', function ($pelanggan) use ($search) {
                            $pelanggan->where('name', 'like', "%{$search}%")
                                ->orWhere('username', 'like', "%{$search}%")
                                ->orWhere('no_hp', 'like', "%{$search}%");
                        });
                });
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status_pesanan', $request->status);
            })
            ->latest('tanggal_pesanan')
            ->paginate(10)
            ->withQueryString();

        return view('admin.pesanan.index', [
            'pesanan' => $pesanan,
            'daftarStatus' => $this->daftarStatus,
        ]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'status_pesanan' => [
                'required',
                Rule::in($this->daftarStatus),
            ],
        ]);

        $pesanan = Pesanan::findOrFail($id);

        $pesanan->update([
            'status_pesanan' => $validated['status_pesanan'],
        ]);

        return back()->with('success', 'Status pesanan berhasil diperbarui.');
    }
}