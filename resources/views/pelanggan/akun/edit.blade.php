@extends('layouts.app')

@section('title', 'Pengaturan Akun')
@section('tanpa-flash', '1')

@php
    // 081234567890 → 81234567890 (tanpa 0, karena +62 sudah tampil di depan kolom)
    $hpTanpaNol = ltrim(preg_replace('/^(\+?62|0)/', '', (string) $user->no_hp), '0');

    $kartu = 'flex flex-col gap-4.5 rounded-3xl border border-line-soft bg-white p-5 sm:p-7';
    $label = 'flex flex-col gap-2 text-sm font-semibold';
    $input = 'field h-12 bg-white font-normal';
    $salah = fn ($bag, $kolom) => $errors->{$bag}->has($kolom) ? 'border-danger' : '';
    $berhasil = 'rounded-[10px] bg-ok-soft px-3.5 py-2.5 text-[13px] font-semibold text-ok';
    $aksi = 'flex justify-end gap-2.5 border-t border-line-soft pt-4.5';
@endphp

@section('content')
<div class="wrap flex flex-col items-start gap-8 pt-12 pb-24 md:flex-row">
    @include('pelanggan.akun._menu')

    <section class="flex w-full min-w-0 flex-1 flex-col gap-6">
        <h1 class="text-[22px] font-bold">Pengaturan Akun</h1>

        {{-- ========== PROFIL ========== --}}
        <form method="POST" action="{{ route('akun.profil') }}" class="{{ $kartu }}" novalidate>
            @csrf @method('PATCH')
            <div>
                <h2 class="text-lg font-bold">Profil Saya</h2>
                <p class="mt-1 text-[13px] leading-relaxed text-muted">Nama dan nomor WhatsApp dipakai admin untuk konfirmasi pesanan.</p>
            </div>

            @if (session('status_profil'))
                <p role="status" class="{{ $berhasil }}">{{ session('status_profil') }}</p>
            @endif

            <div class="grid gap-4.5 sm:grid-cols-2">
                <label class="{{ $label }}">Nama Lengkap
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" autocomplete="name" required
                           class="{{ $input }} {{ $salah('profil', 'name') }}">
                    @error('name', 'profil') <span class="field-error">{{ $message }}</span> @enderror
                </label>
                <label class="{{ $label }}">Nomor WhatsApp
                    <span @class(['flex h-12 overflow-hidden rounded-[10px] border bg-white focus-within:border-maroon focus-within:ring-4 focus-within:ring-maroon/15',
                                  'border-danger' => $errors->profil->has('no_hp'), 'border-line' => ! $errors->profil->has('no_hp')])>
                        <span class="flex items-center border-r border-line bg-blush px-3.5 text-sm font-medium text-ink-soft">+62</span>
                        <input type="tel" name="no_hp" value="{{ old('no_hp', $hpTanpaNol) }}" placeholder="81234567890"
                               inputmode="numeric" autocomplete="tel-national" class="min-w-0 flex-1 px-3.5 text-sm font-normal outline-none">
                    </span>
                    @error('no_hp', 'profil') <span class="field-error">{{ $message }}</span> @enderror
                </label>
            </div>

            <label class="{{ $label }}">Email
                <input type="email" name="email" value="{{ old('email', $user->email) }}" autocomplete="email" required
                       class="{{ $input }} {{ $salah('profil', 'email') }}">
                @error('email', 'profil') <span class="field-error">{{ $message }}</span> @enderror
            </label>

            <div class="{{ $aksi }}">
                <a href="{{ route('akun.edit') }}" class="btn min-h-11.5 border border-line font-medium text-muted hover:border-maroon hover:text-maroon">Batal</a>
                <button type="submit" class="btn btn-primary min-h-11.5 px-5.5">Simpan Perubahan</button>
            </div>
        </form>

        {{-- ========== KATA SANDI ========== --}}
        <form method="POST" action="{{ route('akun.password') }}" id="formSandi" class="{{ $kartu }}" novalidate>
            @csrf @method('PUT')
            <h2 class="text-lg font-bold">Ganti Kata Sandi</h2>

            @if (session('status_password'))
                <p role="status" class="{{ $berhasil }}">{{ session('status_password') }}</p>
            @endif

            <label class="{{ $label }} max-w-[460px]">Kata Sandi Saat Ini
                <x-password id="sandiLama" name="password_lama" placeholder="Masukkan kata sandi saat ini" autocomplete="current-password" required
                            class="{{ $input }} {{ $salah('password', 'password_lama') }}" />
                @error('password_lama', 'password') <span class="field-error">{{ $message }}</span> @enderror
            </label>
            <a href="{{ route('password.request') }}" class="-mt-2 w-fit text-[13px] font-semibold text-maroon hover:underline">Lupa kata sandi saat ini?</a>

            <div class="grid gap-4.5 sm:grid-cols-2">
                <label class="{{ $label }}">Kata Sandi Baru
                    <x-password id="sandiBaru" name="password" placeholder="Minimal 8 karakter" autocomplete="new-password" required
                                class="{{ $input }} {{ $salah('password', 'password') }}" />
                    @error('password', 'password') <span class="field-error">{{ $message }}</span> @enderror
                </label>
                <label class="{{ $label }}">Konfirmasi Kata Sandi Baru
                    <x-password id="sandiUlang" name="password_confirmation" placeholder="Ulangi kata sandi baru" autocomplete="new-password" required
                                class="{{ $input }}" />
                </label>
            </div>

            <ul aria-live="polite" class="flex flex-wrap gap-x-5.5 gap-y-2 text-[13px] text-ink-soft">
                @foreach (['panjang' => 'Minimal 8 karakter', 'campur' => 'Huruf dan angka', 'sama' => 'Konfirmasi sama'] as $kunci => $teks)
                    <li data-cek="{{ $kunci }}" class="group flex items-center gap-2 data-ok:text-ok">
                        <i class="bi bi-check inline-flex size-4.5 items-center justify-center rounded-full bg-line text-xs text-white transition-colors group-data-ok:bg-ok"></i> {{ $teks }}
                    </li>
                @endforeach
            </ul>

            <div class="{{ $aksi }}">
                <button type="submit" class="btn btn-primary min-h-11.5 px-5.5">Perbarui Kata Sandi</button>
            </div>
        </form>

        {{-- ========== HAPUS AKUN ========== --}}
        <form method="POST" action="{{ route('akun.destroy') }}" novalidate class="{{ $kartu }} border-[#F2C9C6]"
              data-konfirmasi="Hapus akunmu?" data-pesan="Semua data akunmu akan dihapus dan tindakan ini tidak bisa dibatalkan." data-tombol="Ya, hapus akun">
            @csrf @method('DELETE')
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex-[1_1_320px]">
                    <h2 class="text-lg font-bold text-danger">Hapus Akun</h2>
                    <p class="mt-1 text-[13px] leading-relaxed text-muted">Data diri dan alamat tersimpan akan dihapus permanen, dan akun tidak bisa dipakai masuk lagi.
                       Pesanan yang sedang berjalan harus selesai terlebih dahulu.</p>
                </div>
                <div class="flex w-full flex-col gap-2.5 sm:w-auto sm:flex-row">
                    <input type="password" name="password_hapus" placeholder="Kata sandi untuk konfirmasi" autocomplete="current-password"
                           aria-label="Kata sandi untuk konfirmasi" class="field bg-white sm:w-55 {{ $salah('hapus', 'password_hapus') }}">
                    <button type="submit" class="btn btn-danger">Hapus Akun</button>
                </div>
            </div>
            @error('password_hapus', 'hapus') <span class="field-error">{{ $message }}</span> @enderror
        </form>
    </section>
</div>

<script>
    // Ceklis syarat kata sandi baru
    (() => {
        const baru = document.getElementById('sandiBaru');
        const ulang = document.getElementById('sandiUlang');
        const set = (k, ok) => document.querySelector(`[data-cek="${k}"]`).toggleAttribute('data-ok', ok);

        const cek = () => {
            set('panjang', baru.value.length >= 8);
            set('campur', /[a-zA-Z]/.test(baru.value) && /\d/.test(baru.value));
            set('sama', baru.value !== '' && baru.value === ulang.value);
        };
        baru.addEventListener('input', cek);
        ulang.addEventListener('input', cek);
    })();
</script>
@endsection
@section('footer-ringkas', '1')
