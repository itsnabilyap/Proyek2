@extends('layouts.admin')

@section('title', 'Pengaturan')
@section('judul', 'Pengaturan')
@section('subjudul', 'Atur profil toko, akun kamu, dan pengguna dashboard.')

@php
    $tabs = [
        'profil'   => ['Profil Toko', 'Logo, nama, alamat, telepon'],
        'akun'     => ['Akun Saya', 'Nama, username, password'],
        'pengguna' => ['Kelola Pengguna', 'Akun admin dan owner'],
    ];
    $saya = auth()->user();
    $bisaKelola = $saya->can('kelola-toko');
    $inisial = collect(preg_split('/[\s.@]+/', ltrim($toko['nama_toko'], '@')))->filter()->take(2)->map(fn ($k) => mb_substr($k, 0, 1))->implode('');

    $bukaUlang = $errors->pengguna->any();
    $idLama = old('_user_id');

    // Potongan class yang dipakai berulang di halaman ini
    $kartu  = 'overflow-hidden rounded-[20px] border border-line bg-white';
    $kepala = 'border-b border-line px-4.5 py-5 sm:px-6 [&_h2]:text-lg [&_h2]:font-bold [&_p]:mt-1 [&_p]:text-[13px] [&_p]:text-muted';
    $isi    = 'px-4.5 pt-5.5 pb-2 sm:px-6';
    $kaki   = 'flex justify-end gap-2.5 border-t border-line bg-blush-50 px-4.5 py-4 sm:px-6';
    $dua    = 'grid gap-x-3.5 sm:grid-cols-2';
    $salah  = 'text-xs text-danger';
@endphp

@section('content')
{{-- ===== TAB ===== --}}
<nav class="mb-5 grid gap-1.5 rounded-[18px] border border-line bg-white p-1.5 md:grid-cols-3">
    @foreach ($tabs as $kunci => [$label, $ket])
        <a href="{{ route('admin.pengaturan.index', ['tab' => $kunci]) }}" @if ($tab === $kunci) aria-current="page" @endif
           class="group flex flex-col items-center gap-0.5 rounded-[13px] p-3 text-center hover:bg-cream aria-[current=page]:bg-maroon aria-[current=page]:text-white">
            <strong class="text-sm">{{ $label }}</strong>
            <small class="text-xs text-muted group-aria-[current=page]:text-white/80">{{ $ket }}</small>
        </a>
    @endforeach
</nav>

{{-- ===================== PROFIL TOKO ===================== --}}
@if ($tab === 'profil')
<form method="POST" action="{{ $bisaKelola ? route('admin.pengaturan.profil') : '#' }}" enctype="multipart/form-data" class="{{ $kartu }}">
    @csrf @method('PUT')
    <header class="{{ $kepala }}">
        <h2>Profil toko</h2>
        <p>
            Informasi ini tampil di halaman pelanggan.
            @unless ($bisaKelola) Hanya admin yang bisa mengubahnya. @endunless
        </p>
    </header>

    <fieldset class="m-0 min-w-0 border-0 p-0" @disabled(! $bisaKelola)>
        <div class="{{ $isi }}">
            <div class="mb-5 flex flex-wrap items-center gap-4.5 sm:flex-nowrap">
                <span class="flex size-24 shrink-0 items-center justify-center overflow-hidden rounded-[22px] border border-[#F0C9D0] bg-blush">
                    @if ($logo = \App\Models\Pengaturan::logoUrl())
                        <img src="{{ $logo }}" alt="Logo toko" id="logo-pratinjau" class="size-full object-cover">
                    @else
                        <img src="" alt="" id="logo-pratinjau" class="size-full object-cover" hidden>
                        <b id="logo-inisial" class="font-serif text-[30px] font-semibold text-maroon">{{ strtoupper($inisial) ?: 'BI' }}</b>
                    @endif
                </span>
                <div>
                    <strong class="block text-sm">Logo toko</strong>
                    <small class="mt-1 mb-2.5 block text-xs text-muted">PNG atau JPG, persegi, maksimal 2 MB.</small>
                    @if ($bisaKelola)
                        <label class="btn btn-sm btn-soft cursor-pointer rounded-xl">
                            <i class="bi bi-upload"></i> Ganti Logo
                            <input type="file" name="logo" accept="image/png,image/jpeg" id="logo-input" hidden>
                        </label>
                    @endif
                    @error('logo') <small class="{{ $salah }}" data-error>{{ $message }}</small> @enderror
                </div>
            </div>

            <div class="{{ $dua }}">
                <label class="isian">
                    <span>Nama toko</span>
                    <input type="text" name="nama_toko" value="{{ old('nama_toko', $toko['nama_toko']) }}" required class="input">
                    @error('nama_toko') <small class="{{ $salah }}" data-error>{{ $message }}</small> @enderror
                </label>
                <label class="isian">
                    <span>No. telepon</span>
                    <input type="text" name="telepon" value="{{ old('telepon', $toko['telepon']) }}" placeholder="0812-3456-7890" class="input">
                    @error('telepon') <small class="{{ $salah }}" data-error>{{ $message }}</small> @enderror
                </label>
            </div>

            <label class="isian">
                <span>Alamat toko</span>
                <textarea name="alamat" rows="3" class="input">{{ old('alamat', $toko['alamat']) }}</textarea>
                <small class="text-xs text-muted">Ditampilkan sebagai lokasi pengambilan untuk pesanan ambil di toko.</small>
                @error('alamat') <small class="{{ $salah }}" data-error>{{ $message }}</small> @enderror
            </label>
        </div>
    </fieldset>

    @if ($bisaKelola)
        <footer class="{{ $kaki }}">
            <a href="{{ route('admin.pengaturan.index', ['tab' => 'profil']) }}" class="btn btn-outline rounded-xl">Batal</a>
            <button type="submit" class="btn btn-primary rounded-xl">Simpan Perubahan</button>
        </footer>
    @endif
</form>
@endif

{{-- ===================== AKUN SAYA (admin & owner) ===================== --}}
@if ($tab === 'akun')
<form method="POST" action="{{ route('admin.pengaturan.akun') }}" class="{{ $kartu }}">
    @csrf @method('PUT')
    <header class="{{ $kepala }}">
        <h2>Akun saya</h2>
        <p>Data login kamu di dashboard.</p>
    </header>

    <div class="{{ $isi }}">
        <div class="{{ $dua }}">
            <label class="isian">
                <span>Nama</span>
                <input type="text" name="name" value="{{ old('name', $saya->name) }}" required class="input">
                @error('name') <small class="{{ $salah }}" data-error>{{ $message }}</small> @enderror
            </label>
            <label class="isian">
                <span>Username</span>
                <input type="text" name="username" value="{{ old('username', $saya->username) }}" required class="input">
                @error('username') <small class="{{ $salah }}" data-error>{{ $message }}</small> @enderror
            </label>
        </div>

        <hr class="mt-1.5 mb-5 border-line">

        <div class="mb-3.5 flex flex-col">
            <strong class="text-[15px]">Ganti password</strong>
            <small class="text-xs text-muted">Kosongkan jika tidak ingin mengganti password.</small>
        </div>

        <div class="{{ $dua }}">
            <label class="isian">
                <span>Password lama</span>
                <input type="password" name="password_lama" placeholder="Masukkan password lama" autocomplete="current-password" class="input">
                @error('password_lama') <small class="{{ $salah }}" data-error>{{ $message }}</small> @enderror
            </label>
            <span></span>
        </div>
        <div class="{{ $dua }}">
            <label class="isian">
                <span>Password baru</span>
                <input type="password" name="password" placeholder="Minimal 8 karakter" autocomplete="new-password" class="input">
                @error('password') <small class="{{ $salah }}" data-error>{{ $message }}</small> @enderror
            </label>
            <label class="isian">
                <span>Konfirmasi password baru</span>
                <input type="password" name="password_confirmation" placeholder="Ulangi password baru" autocomplete="new-password" class="input">
            </label>
        </div>
    </div>

    <footer class="{{ $kaki }}">
        <a href="{{ route('admin.pengaturan.index', ['tab' => 'akun']) }}" class="btn btn-outline rounded-xl">Batal</a>
        <button type="submit" class="btn btn-primary rounded-xl">Simpan Perubahan</button>
    </footer>
</form>
@endif

{{-- ===================== KELOLA PENGGUNA ===================== --}}
@if ($tab === 'pengguna')
<section class="{{ $kartu }}">
    <header class="{{ $kepala }} flex flex-col items-start justify-between gap-4 md:flex-row md:items-center">
        <div>
            <h2>Kelola pengguna</h2>
            <p>Siapa saja yang bisa masuk ke dashboard.</p>
        </div>
        @can('kelola-toko')
            <button type="button" class="btn btn-primary rounded-xl" data-tambah-pengguna><i class="bi bi-plus-lg"></i> Tambah Pengguna</button>
        @endcan
    </header>

    <div class="overflow-x-auto">
        <table class="tabel">
            <thead>
                <tr>
                    <th>No</th><th>Nama</th><th>Username</th><th>Role</th>
                    @if ($bisaKelola) <th class="text-right!">Aksi</th> @endif
                </tr>
            </thead>
            <tbody>
                @foreach ($pengguna as $i => $u)
                    @php
                        $owner = $u->role === 'owner';
                        $diriSendiri = $u->is($saya);
                    @endphp
                    <tr>
                        <td class="text-muted">{{ $i + 1 }}</td>
                        <td><span class="font-semibold">{{ $u->name }}</span> @if ($diriSendiri) <span class="text-xs text-muted">(kamu)</span> @endif</td>
                        <td>{{ $u->username }}</td>
                        <td><span @class(['inline-block rounded-full px-3 py-0.5 text-xs font-semibold', 'bg-wine text-white' => $owner, 'bg-blush text-maroon' => ! $owner])>{{ ucfirst($u->role) }}</span></td>

                        @if ($bisaKelola)
                            <td>
                                <div class="flex justify-end gap-2">
                                    @if ($diriSendiri)
                                        <a href="{{ route('admin.pengaturan.index', ['tab' => 'akun']) }}" class="btn btn-sm btn-outline rounded-xl">Ubah</a>
                                    @else
                                        <button type="button" class="btn btn-sm btn-outline rounded-xl" @disabled($owner)
                                                title="{{ $owner ? 'Akun owner hanya bisa diubah oleh owner' : '' }}"
                                                data-ubah-pengguna="{{ json_encode(['id' => $u->id, 'url' => route('admin.pengaturan.pengguna.update', $u), 'name' => $u->name, 'username' => $u->username]) }}">
                                            Ubah
                                        </button>
                                    @endif

                                    @if ($owner || $diriSendiri)
                                        <button type="button" class="btn btn-sm rounded-xl border border-[#F5CFCB] bg-danger-soft text-danger enabled:hover:bg-[#FAE0DD]" disabled>Hapus</button>
                                    @else
                                        <form method="POST" action="{{ route('admin.pengaturan.pengguna.destroy', $u) }}"
                                              data-konfirmasi="Hapus akun pengguna?" data-nama="{{ $u->name }}" data-pesan="Ia tidak bisa masuk dashboard lagi." data-tombol="Ya, hapus">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm rounded-xl border border-[#F5CFCB] bg-danger-soft text-danger enabled:hover:bg-[#FAE0DD]">Hapus</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>

@can('kelola-toko')
{{-- Popup tambah / ubah pengguna --}}
<x-admin.modal id="modal-pengguna" :judul="$idLama ? 'Ubah Pengguna' : 'Tambah Pengguna'" class="w-[min(520px,calc(100%-32px))] bg-white">
    <form method="POST" class="px-4 py-5 sm:px-6"
          data-store-url="{{ route('admin.pengaturan.pengguna.store') }}"
          action="{{ $idLama ? route('admin.pengaturan.pengguna.update', $idLama) : route('admin.pengaturan.pengguna.store') }}">
        @csrf
        <input type="hidden" name="_method" value="PUT" @disabled(! $idLama)>
        <input type="hidden" name="_user_id" value="{{ $idLama }}">

        <label class="isian">
            <span>Nama</span>
            <input type="text" name="name" value="{{ old('name') }}" required class="input">
            @error('name', 'pengguna') <small class="{{ $salah }}" data-error>{{ $message }}</small> @enderror
        </label>
        <label class="isian">
            <span>Username</span>
            <input type="text" name="username" value="{{ old('username') }}" placeholder="contoh: rina.admin" required class="input">
            @error('username', 'pengguna') <small class="{{ $salah }}" data-error>{{ $message }}</small> @enderror
        </label>

        <div class="{{ $dua }}">
            <label class="isian">
                <span data-label-password>{{ $idLama ? 'Password baru (opsional)' : 'Password' }}</span>
                <input type="password" name="password" placeholder="Minimal 8 karakter" autocomplete="new-password" class="input">
                @error('password', 'pengguna') <small class="{{ $salah }}" data-error>{{ $message }}</small> @enderror
            </label>
            <label class="isian">
                <span>Konfirmasi password</span>
                <input type="password" name="password_confirmation" autocomplete="new-password" class="input">
            </label>
        </div>
        <small class="-mt-1 mb-4.5 block text-xs text-muted">Pengguna baru dibuat sebagai <b>admin</b>. Password minimal 8 karakter, berisi huruf dan angka.</small>

        <div class="flex justify-end gap-2.5">
            <button type="button" class="btn btn-outline rounded-xl" onclick="this.closest('dialog').close()">Batal</button>
            <button type="submit" class="btn btn-primary rounded-xl">Simpan</button>
        </div>
    </form>
</x-admin.modal>

<script>
    const modalPg = document.getElementById('modal-pengguna');
    const formPg = modalPg.querySelector('form');

    function bukaFormPengguna(data) {
        modalPg.querySelector('[data-modal-judul]').textContent = data ? 'Ubah ' + data.name : 'Tambah Pengguna';
        modalPg.querySelector('[data-label-password]').textContent = data ? 'Password baru (opsional)' : 'Password';
        formPg.action = data ? data.url : formPg.dataset.storeUrl;
        formPg.elements['_method'].disabled = !data;
        formPg.elements['_user_id'].value = data ? data.id : '';
        formPg.elements['name'].value = data ? data.name : '';
        formPg.elements['username'].value = data ? data.username : '';
        formPg.elements['password'].value = '';
        formPg.elements['password_confirmation'].value = '';
        formPg.elements['password'].required = !data;
        formPg.querySelectorAll('[data-error]').forEach(el => el.remove());
        modalPg.showModal();
    }

    document.querySelector('[data-tambah-pengguna]')?.addEventListener('click', () => bukaFormPengguna(null));
    document.querySelectorAll('[data-ubah-pengguna]').forEach(b =>
        b.addEventListener('click', () => bukaFormPengguna(JSON.parse(b.dataset.ubahPengguna))));

    @if ($bukaUlang) modalPg.showModal(); @endif
</script>
@endcan
@endif

<script>
    // Pratinjau logo
    document.getElementById('logo-input')?.addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (!file) return;
        const img = document.getElementById('logo-pratinjau');
        img.src = URL.createObjectURL(file);
        img.hidden = false;
        document.getElementById('logo-inisial')?.remove();
    });
</script>
@endsection
