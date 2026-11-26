<?php

namespace App\Http\Controllers;

use App\Models\Custom;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CustomController extends Controller
{
    /** Riwayat request custom milik pelanggan (menu profil > Request Custom). */
    public function riwayat(Request $request): View
    {
        $riwayat = $request->user()->Custom()->with('pesanan')->latest()->paginate(10);

        return view('pelanggan.custom.riwayat', compact('riwayat'));
    }

    public function create(Request $request): View
    {
        // Dibuka dari kartu katalog / link "Request Custom": jenis langsung terpilih (?jenis=...)
        // Isian yang disimpan sebelum login dipulihkan ke form (foto tidak bisa ikut, harus dipilih ulang).
        if ($request->user() && $draft = $request->session()->pull('draft_custom')) {
            $request->session()->flashInput($draft);
        }

        $jenis = old('jenis_bucket', $request->query('jenis'));

        return view('pelanggan.custom.create', [
            'jenisList'   => Custom::JENIS,
            'jenisAwal'   => array_key_exists((string) $jenis, Custom::JENIS) ? $jenis : array_key_first(Custom::JENIS),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        // Belum login: simpan isian dulu, minta masuk, lalu kembali ke form custom.
        if (! $request->user()) {
            $request->session()->put('draft_custom', $request->except(['_token', 'foto_referensi']));

            return redirect()->guest(route('login'))
                ->with('status', 'Masuk dulu untuk mengirim request custom. Isian kamu sudah disimpan.');
        }

        $jenis = $request->input('jenis_bucket');
        $budgetValid = Custom::JENIS[$jenis]['budget'] ?? [];

        $data = $request->validate([
            'jenis_bucket'       => ['required', Rule::in(array_keys(Custom::JENIS))],
            'budget'             => ['required', Rule::in($budgetValid)],
            'tanggal_dibutuhkan' => ['required', 'date', 'after_or_equal:today'],
            'no_whatsapp'        => ['required', 'regex:/^(\+62|62|0)8[0-9]{7,12}$/'],
            'tema'               => ['nullable', 'string', 'max:150'],
            'foto_referensi'     => ['nullable', 'image', 'max:2048'],
            'catatan'            => ['nullable', 'string', 'max:1000'],
        ], [
            'jenis_bucket.required'             => 'Pilih jenis buket.',
            'jenis_bucket.in'                   => 'Jenis buket tidak valid.',
            'budget.required'                   => 'Pilih budget.',
            'budget.in'                         => 'Pilih budget dari pilihan yang ada.',
            'tanggal_dibutuhkan.required'       => 'Tanggal dibutuhkan wajib diisi.',
            'tanggal_dibutuhkan.after_or_equal' => 'Tanggal tidak boleh sebelum hari ini.',
            'no_whatsapp.required'              => 'Nomor WhatsApp wajib diisi.',
            'no_whatsapp.regex'                 => 'Nomor WhatsApp harus diawali 08 atau 628.',
            'tema.max'                          => 'Warna & tema maksimal 150 karakter.',
            'foto_referensi.image'              => 'Foto referensi harus berupa gambar.',
            'foto_referensi.max'                => 'Ukuran foto maksimal 2 MB.',
            'catatan.max'                       => 'Catatan maksimal 1000 karakter.',
        ]);

        // Foto referensi disimpan di public/images/custom (sama seperti foto produk)
        if ($request->hasFile('foto_referensi')) {
            $file = $request->file('foto_referensi');
            $nama = now()->format('Ymd-His') . '-' . Str::random(6) . '.' . $file->extension();
            $file->move(public_path('images/custom'), $nama);
            $data['foto_referensi'] = $nama;
        }

        $user = $request->user();
        $data['nama_pemesan'] = $user->name;
        $permintaan = $user->Custom()->create($data);

        // Simpan nomor WA ke akun kalau belum ada, supaya berikutnya terisi otomatis.
        if (! $user->no_hp) {
            $user->update(['no_hp' => $data['no_whatsapp']]);
        }

        $pesan = "Halo @bucket.imy, saya sudah kirim request custom #{$permintaan->id}:\n"
            . "- Nama: {$permintaan->nama_pemesan}\n"
            . "- Jenis: {$permintaan->jenis_bucket}\n"
            . "- Budget: {$permintaan->budget_label}\n"
            . "- Dibutuhkan: {$permintaan->tanggal_dibutuhkan->translatedFormat('d F Y')}\n"
            . ($permintaan->tema ? "- Warna & tema: {$permintaan->tema}\n" : '')
            . ($permintaan->catatan ? "- Catatan: {$permintaan->catatan}\n" : '')
            . ($permintaan->foto_referensi_url ? "- Foto referensi: {$permintaan->foto_referensi_url}" : '');

        return redirect()->route('custom.create')
            ->with('custom_terkirim', 'https://wa.me/62895325820465?text=' . urlencode(trim($pesan)));
    }
}
