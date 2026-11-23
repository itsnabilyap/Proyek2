@extends('layouts.app')

@section('title', 'Request Custom')

@php
    $jenisDipilih = $jenisAwal;
    $budgetDipilih = old('budget');

    $judul = 'mb-3 text-[15px] font-bold';
    $label = 'flex flex-col gap-1.5 text-[13px] font-semibold';
    $isian = 'h-11 rounded-xl border border-line bg-white px-3 text-sm font-normal focus:border-maroon focus:ring-4 focus:ring-maroon/15 focus:outline-none';
    $salah = fn ($kolom) => $errors->has($kolom) ? 'border-danger' : '';
    $kotak = 'flex h-full min-h-11 flex-col items-start rounded-[14px] border border-line bg-white px-3.5 py-2.5 text-[13px] font-semibold transition-colors hover:border-maroon peer-checked:border-2 peer-checked:border-maroon peer-checked:bg-[#FFF7F8] peer-checked:px-[13px] peer-checked:py-[9px] peer-focus-visible:ring-4 peer-focus-visible:ring-maroon/20';
    $langkah = ['Kirim jenis, budget, dan referensi.', 'Admin membalas via WhatsApp dengan rancangan dan harga final.', 'Setuju, bayar, lalu buket dirangkai.'];
@endphp

@section('content')
<section class="px-4 pt-12 pb-7 text-center sm:px-6">
    <h1 class="mb-2.5 font-serif text-[34px] leading-tight font-medium md:text-[42px]">Request Buket Custom</h1>
    <p class="mx-auto max-w-[560px] text-[15px] leading-relaxed text-ink-soft">Untuk buket yang harganya menyesuaikan budget. Isi form, admin membalas via WhatsApp dengan harga final dalam ±15 menit.</p>
</section>

<div class="wrap pb-20">
    <div class="flex flex-wrap items-start gap-7">
        {{-- ===== FORM ===== --}}
        <form method="POST" action="{{ route('custom.store') }}" id="customForm" enctype="multipart/form-data" novalidate
              class="min-w-0 flex-[999_1_560px] rounded-[20px] border border-line-soft bg-white p-5 sm:p-7">
            @csrf

            {{-- 1. Jenis --}}
            <fieldset>
                <legend class="{{ $judul }}">1. Jenis buket</legend>
                <div class="grid grid-cols-2 gap-2.5 sm:grid-cols-[repeat(auto-fill,minmax(150px,1fr))]">
                    @foreach ($jenisList as $jenis => $j)
                        <label class="relative block">
                            <input type="radio" name="jenis_bucket" value="{{ $jenis }}" @checked($jenisDipilih === $jenis) required class="peer sr-only">
                            <span class="{{ $kotak }}">
                                <strong class="text-sm">{{ \App\Models\Custom::namaPendek($jenis) }}</strong>
                                <span class="mt-0.5 text-xs font-normal text-muted">{{ $j['kisaran'] }}</span>
                            </span>
                        </label>
                    @endforeach
                </div>
                @error('jenis_bucket') <span class="field-error">{{ $message }}</span> @enderror
                <p id="infoJenis" class="mt-2 mb-5.5 text-[12.5px] text-muted">{{ $jenisList[$jenisDipilih]['info'] }}</p>
            </fieldset>

            {{-- 2. Budget: pilihannya berbeda per jenis. Grup jenis lain disembunyikan & dimatikan. --}}
            <fieldset class="mb-5.5">
                <legend class="{{ $judul }}">2. Budget kamu</legend>
                @foreach ($jenisList as $jenis => $j)
                    <div data-budget="{{ $jenis }}" class="flex flex-wrap gap-2" @if ($jenis !== $jenisDipilih) hidden @endif>
                        @foreach ($j['budget'] as $b)
                            <label class="relative">
                                <input type="radio" name="budget" value="{{ $b }}" required class="peer sr-only"
                                       @checked($jenis === $jenisDipilih && $budgetDipilih === $b) @disabled($jenis !== $jenisDipilih)>
                                <span class="{{ $kotak }} justify-center">{{ $b }}</span>
                            </label>
                        @endforeach
                    </div>
                @endforeach
                @error('budget') <span class="field-error">{{ $message }}</span> @enderror
            </fieldset>

            {{-- 3. Detail --}}
            <fieldset class="flex flex-col gap-3.5">
                <legend class="{{ $judul }}">3. Detail pesanan</legend>

                <div class="grid gap-3.5 sm:grid-cols-2">
                    <label class="{{ $label }}">Dibutuhkan tanggal
                        <input type="date" name="tanggal_dibutuhkan" value="{{ old('tanggal_dibutuhkan') }}" min="{{ now()->toDateString() }}" required
                               class="{{ $isian }} {{ $salah('tanggal_dibutuhkan') }}">
                        @error('tanggal_dibutuhkan') <span class="field-error">{{ $message }}</span> @enderror
                    </label>
                    <label class="{{ $label }}">No. WhatsApp
                        <input type="tel" name="no_whatsapp" value="{{ old('no_whatsapp', auth()->user()->no_hp) }}" placeholder="08xxxxxxxxxx" autocomplete="tel" required
                               class="{{ $isian }} {{ $salah('no_whatsapp') }}">
                        @error('no_whatsapp') <span class="field-error">{{ $message }}</span> @enderror
                    </label>
                </div>

                <label class="{{ $label }}">Warna &amp; tema
                    <input type="text" name="tema" id="tema" value="{{ old('tema') }}" maxlength="150" placeholder="{{ $jenisList[$jenisDipilih]['contoh'] }}"
                           class="{{ $isian }} {{ $salah('tema') }}">
                    @error('tema') <span class="field-error">{{ $message }}</span> @enderror
                </label>

                <div class="{{ $label }}">
                    <span>Foto referensi <span class="font-normal text-muted">(opsional)</span></span>
                    <label class="relative flex min-h-21 cursor-pointer items-center justify-center gap-2 overflow-hidden rounded-[14px] border-2 border-dashed border-[#E2C3CA] bg-cream px-4 text-center font-medium text-muted transition-colors hover:border-maroon focus-within:border-maroon">
                        <i class="bi bi-upload text-lg text-maroon"></i>
                        <span id="namaFoto">Pilih foto (maks. 2 MB)</span>
                        <input type="file" name="foto_referensi" id="fotoReferensi" accept="image/*" class="absolute inset-0 cursor-pointer opacity-0">
                    </label>
                    @error('foto_referensi') <span class="field-error">{{ $message }}</span> @enderror
                </div>

                <label class="{{ $label }}">Catatan
                    <textarea name="catatan" rows="3" maxlength="1000" placeholder="Contoh: snack kesukaan, isi kartu ucapan, atau detail lain"
                              class="field font-normal {{ $salah('catatan') }}">{{ old('catatan') }}</textarea>
                    @error('catatan') <span class="field-error">{{ $message }}</span> @enderror
                </label>
            </fieldset>

            <button type="submit" class="btn btn-primary mt-6 h-12 w-full rounded-full text-[15px] font-bold">Kirim Request Custom</button>
        </form>

        {{-- ===== SAMPING ===== --}}
        <aside class="flex min-w-0 flex-[1_1_300px] flex-col gap-4" aria-live="polite">
            @if (session('custom_terkirim'))
                <section class="flex flex-col items-center gap-3 rounded-[20px] border border-line-soft bg-white p-6 text-center">
                    <span class="flex size-16 items-center justify-center rounded-full bg-ok-soft text-2xl text-ok"><i class="bi bi-check-lg"></i></span>
                    <h2 class="text-lg font-bold">Request terkirim!</h2>
                    <p class="text-[13px] leading-relaxed text-muted">Kami hubungi via WhatsApp. Biar lebih cepat, kirim ringkasannya sekarang.</p>
                    <a href="{{ session('custom_terkirim') }}" target="_blank" rel="noopener noreferrer" class="btn btn-primary min-h-11 rounded-full px-5 text-[13px]">
                        <i class="bi bi-whatsapp"></i> Lanjut Chat WhatsApp
                    </a>
                </section>
            @endif

            <section class="rounded-[20px] border border-line-soft bg-white p-5.5">
                <h2 class="mb-3.5 text-[15px] font-bold">Cara kerjanya</h2>
                <ol class="flex flex-col gap-3.5">
                    @foreach ($langkah as $i => $teks)
                        <li class="flex gap-3 text-[13px] leading-relaxed">
                            <span class="flex size-6.5 shrink-0 items-center justify-center rounded-full bg-blush font-extrabold text-maroon">{{ $i + 1 }}</span>{{ $teks }}
                        </li>
                    @endforeach
                </ol>
            </section>

            <section class="rounded-[20px] bg-blush-100 p-5.5">
                <h2 class="mb-2 text-[15px] font-bold">Harga sudah pasti?</h2>
                <p class="mb-3.5 text-[13px] leading-relaxed text-ink-soft">Bucket bunga Small–Large, coklat 1–3, bucket uang 10–50 lembar, snack, dan Thumbelina bisa langsung dipesan dari katalog.</p>
                <a href="{{ route('katalog.index') }}" class="text-[13px] font-bold text-maroon hover:underline">Lihat katalog</a>
            </section>
        </aside>
    </div>
</div>

<script>
    // Ganti info, pilihan budget, dan contoh "warna & tema" sesuai jenis yang dipilih
    (() => {
        const form = document.getElementById('customForm');
        const data = @json($jenisList);

        form.addEventListener('change', e => {
            if (e.target.name === 'jenis_bucket') {
                const j = e.target.value;
                document.getElementById('infoJenis').textContent = data[j].info;
                document.getElementById('tema').placeholder = data[j].contoh;
                form.querySelectorAll('[data-budget]').forEach(grup => {
                    const aktif = grup.dataset.budget === j;
                    grup.hidden = !aktif;
                    grup.querySelectorAll('input').forEach(i => { i.disabled = !aktif; if (!aktif) i.checked = false; });
                });
            }
            if (e.target.id === 'fotoReferensi') {
                document.getElementById('namaFoto').textContent = e.target.files[0]?.name || 'Pilih foto (maks. 2 MB)';
            }
        });
    })();
</script>
@endsection
