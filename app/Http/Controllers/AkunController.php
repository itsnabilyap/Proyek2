<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AkunController extends Controller
{
    public function edit(Request $request): View
    {
        return view('pelanggan.akun.edit', ['user' => $request->user()]);
    }

    public function updateProfil(Request $request): RedirectResponse
    {
        $user = $request->user();

        $data = $request->validateWithBag('profil', [
            'name'  => ['required', 'string', 'max:100'],
            'no_hp' => ['nullable', 'regex:/^8[0-9]{7,12}$/'],        // tanpa 0 / +62 di depan
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user->id)],
        ], [
            'name.required'  => 'Nama wajib diisi.',
            'no_hp.regex'    => 'Nomor WhatsApp harus diawali 8, contoh: 81234567890.',
            'email.required' => 'Email wajib diisi.',
            'email.email'    => 'Format email tidak valid.',
            'email.unique'   => 'Email sudah dipakai akun lain.',
        ]);

        // Disimpan dengan format 08xx, sama seperti di form checkout.
        $data['no_hp'] = $data['no_hp'] ? '0' . $data['no_hp'] : null;

        $user->update($data);

        return back()->with('status_profil', 'Profil berhasil disimpan.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validateWithBag('password', [
            'password_lama' => ['required', 'current_password'],
            'password'      => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ], [
            'password_lama.required'         => 'Masukkan kata sandi saat ini.',
            'password_lama.current_password' => 'Kata sandi saat ini salah.',
            'password.required'              => 'Kata sandi baru wajib diisi.',
            'password.confirmed'             => 'Konfirmasi kata sandi tidak sama.',
            'password.min'                   => 'Kata sandi baru minimal 8 karakter.',
            'password.letters'               => 'Kata sandi baru harus mengandung huruf.',
            'password.numbers'               => 'Kata sandi baru harus mengandung angka.',
        ]);

        $request->user()->update(['password' => $request->password]); // di-hash otomatis oleh cast

        return back()->with('status_password', 'Kata sandi berhasil diperbarui.');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('hapus', [
            'password_hapus' => ['required', 'current_password'],
        ], [
            'password_hapus.required'         => 'Masukkan kata sandi untuk konfirmasi.',
            'password_hapus.current_password' => 'Kata sandi salah.',
        ]);

        $user = $request->user();

        $masihBerjalan = $user->pesanan()->whereNotIn('status_pesanan', ['selesai', 'dibatalkan'])->exists();
        if ($masihBerjalan) {
            return back()->withErrors(['password_hapus' => 'Masih ada pesanan yang berjalan. Tunggu sampai selesai dulu.'], 'hapus');
        }

        // Data pribadi dihapus, riwayat pesanan tetap ada untuk laporan penjualan toko.
        $user->keranjang()->delete();
        $user->forceFill([
            'name'           => 'Akun dihapus',
            'username'       => 'dihapus-' . $user->id,
            'email'          => 'dihapus-' . $user->id . '@bucketimy.local',
            'no_hp'          => null,
            'password'       => Str::random(40),
            'remember_token' => null,
        ])->save();

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('status', 'Akun kamu sudah dihapus. Terima kasih sudah berbelanja di @bucket.imy.');
    }
}