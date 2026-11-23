<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AuthController extends Controller
{
    // ===== DAFTAR (pelanggan) =====

    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'min:4', 'max:30', 'alpha_dash', 'unique:users,username'],
            'email'    => ['required', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'setuju'   => ['accepted'],
        ], [
            'name.required'      => 'Nama lengkap wajib diisi.',
            'username.required'  => 'Username wajib diisi.',
            'username.min'       => 'Username minimal 4 karakter.',
            'username.alpha_dash'=> 'Username hanya boleh huruf, angka, - dan _.',
            'username.unique'    => 'Username sudah dipakai.',
            'email.required'     => 'Email wajib diisi.',
            'email.email'        => 'Format email tidak valid.',
            'email.unique'       => 'Email sudah terdaftar.',
            'password.required'  => 'Password wajib diisi.',
            'password.min'       => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak sama.',
            'setuju.accepted'    => 'Kamu harus menyetujui Syarat & Ketentuan.',
        ]);

        unset($data['setuju']);
        $data['role'] = 'pelanggan'; // role TIDAK diambil dari form, supaya orang tidak bisa daftar sebagai admin

        $user = User::create($data);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('home')->with('status', 'Akun berhasil dibuat. Selamat datang!');
    }

    // ===== MASUK (pelanggan) =====

    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'username.required' => 'Username wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        // Hanya akun pelanggan yang boleh masuk lewat form ini.
        if (! Auth::attempt([...$credentials, 'role' => 'pelanggan'], $request->boolean('remember'))) {
            return back()
                ->withErrors(['username' => 'Username atau password salah.'])
                ->onlyInput('username');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('home'));
    }

    // ===== MASUK (admin & owner) =====

    public function showAdminLogin(): View
    {
        return view('auth.admin-login');
    }

    public function adminLogin(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
            'role'     => ['required', 'in:admin,owner'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withErrors(['username' => 'Username, password, atau role salah.'])
                ->onlyInput('username', 'role');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard'));
    }

    // ===== KELUAR =====
    public function logout(Request $request): RedirectResponse
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        $staff = $user?->isStaff();

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route($staff ? 'admin.login' : 'home');
    }
}