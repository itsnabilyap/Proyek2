<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengaturan;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class PengaturanController extends Controller
{
    public const TAB = ['profil', 'akun', 'pengguna'];

    public function index(Request $r): View
    {
        $tab = in_array($r->query('tab'), self::TAB) ? $r->query('tab') : 'profil';

        $toko = Pengaturan::semua();
        $pengguna = User::whereIn('role', ['owner', 'admin'])
            ->orderByRaw("role = 'owner' DESC")
            ->orderBy('id')
            ->get();

        return view('admin.pengaturan.index', compact('tab', 'toko', 'pengguna'));
    }

    // ===== PROFIL TOKO =====
    public function updateProfil(Request $r): RedirectResponse
    {
        $data = $r->validate([
            'nama_toko' => ['required', 'string', 'max:60'],
            'telepon'   => ['nullable', 'string', 'max:20'],
            'alamat'    => ['nullable', 'string', 'max:255'],
            'logo'      => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],
        ], ['logo.max' => 'Ukuran logo maksimal 2 MB.']);

        unset($data['logo']);

        if ($r->hasFile('logo')) {
            $file = $r->file('logo');
            $nama = 'logo-' . time() . '.' . $file->extension();
            $file->move(public_path('images/toko'), $nama);

            $lama = Pengaturan::ambil('logo');
            if ($lama && is_file(public_path('images/toko/' . $lama))) {
                @unlink(public_path('images/toko/' . $lama));
            }
            $data['logo'] = $nama;
        }

        Pengaturan::simpan($data);

        return redirect()->route('admin.pengaturan.index', ['tab' => 'profil'])->with('success', 'Profil toko disimpan.');
    }

    // ===== AKUN SAYA =====
    public function updateAkun(Request $r): RedirectResponse
    {
        $user = $r->user();

        $data = $r->validate([
            'name'          => ['required', 'string', 'max:100'],
            'username'      => ['required', 'alpha_dash', 'max:30', Rule::unique('users')->ignore($user->id)],
            'password_lama' => ['nullable', 'required_with:password', 'current_password'],
            'password'      => ['nullable', 'confirmed', Password::min(8)->letters()->numbers()],
        ], [
            'password_lama.required_with'    => 'Isi password lama untuk mengganti password.',
            'password_lama.current_password' => 'Password lama salah.',
            'username.alpha_dash'            => 'Username hanya boleh huruf, angka, titik tidak boleh. Pakai - atau _.',
        ]);

        $user->name = $data['name'];
        $user->username = $data['username'];
        if (! empty($data['password'])) {
            $user->password = $data['password']; // otomatis di-hash (cast 'hashed')
        }
        $user->save();

        return redirect()->route('admin.pengaturan.index', ['tab' => 'akun'])->with('success', 'Akun kamu diperbarui.');
    }

    // ===== KELOLA PENGGUNA =====
    public function storePengguna(Request $r): RedirectResponse
    {
        $data = $r->validateWithBag('pengguna', [
            'name'     => ['required', 'string', 'max:100'],
            'username' => ['required', 'regex:/^[a-z0-9._-]+$/i', 'max:30', Rule::unique('users')],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ], $this->pesanPengguna());

        User::forceCreate([
            'name'     => $data['name'],
            'username' => $data['username'],
            'email'    => strtolower($data['username']) . '@staff.bucket.imy',
            'password' => $data['password'],
            'role'     => 'admin',
        ]);

        return redirect()->route('admin.pengaturan.index', ['tab' => 'pengguna'])->with('success', "Admin {$data['name']} ditambahkan.");
    }

    public function updatePengguna(Request $r, User $user): RedirectResponse
    {
        abort_unless($user->role === 'admin', 403, 'Akun ini tidak bisa diubah dari sini.');

        $data = $r->validateWithBag('pengguna', [
            'name'     => ['required', 'string', 'max:100'],
            'username' => ['required', 'regex:/^[a-z0-9._-]+$/i', 'max:30', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'confirmed', Password::min(8)->letters()->numbers()],
        ], $this->pesanPengguna());

        $user->name = $data['name'];
        $user->username = $data['username'];
        $user->email = strtolower($data['username']) . '@staff.bucket.imy';
        if (! empty($data['password'])) {
            $user->password = $data['password'];
        }
        $user->save();

        return redirect()->route('admin.pengaturan.index', ['tab' => 'pengguna'])->with('success', "Akun {$user->name} diperbarui.");
    }

    public function destroyPengguna(Request $r, User $user): RedirectResponse
    {
        abort_unless($user->role === 'admin', 403, 'Akun ini tidak bisa dihapus.');
        abort_if($user->is($r->user()), 403, 'Kamu tidak bisa menghapus akunmu sendiri.');

        $user->delete();

        return redirect()->route('admin.pengaturan.index', ['tab' => 'pengguna'])->with('success', "Akun {$user->name} dihapus.");
    }

    private function pesanPengguna(): array
    {
        return ['username.regex' => 'Username hanya boleh huruf, angka, titik, - dan _.'];
    }
}