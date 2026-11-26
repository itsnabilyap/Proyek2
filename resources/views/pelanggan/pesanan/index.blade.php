@extends('layouts.app')

@section('title', 'Pesanan Saya')
@section('footer-ringkas', '1')

@php
    $rupiah = fn ($n) => 'Rp' . number_format((int) $n, 0, ',', '.');
    $wa = fn ($kode) => 'https://wa.me/6283184710305?text=' . urlencode("Halo @bucket.imy, saya mau tanya pesanan {$kode}.");

    $chip = 'inline-flex items-center gap-1.5 rounded-full border px-2.5 py-1 text-xs font-semibold';
    $catatan = 'rounded-xl bg-blush-50 px-4 py-3 text-[13px] text-ink-soft';
    $btnUtama = 'btn btn-primary min-h-11.5 px-5.5';
    $btnGaris = 'btn min-h-11.5 border border-line font-medium text-muted hover:border-maroon hover:text-maroon';
@endphp

@section('content')
{{-- Menu akun pindah ke dropdown profil di header --}}
<div class="wrap max-w-[880px] pt-12 pb-24">
    <section class="flex w-full min-w-0 flex-col gap-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h1 class="text-[22px] font-bold">Pesanan Saya ({{ $pilihan['semua']['jumlah'] }})</h1>
            <form method="GET" action="{{ route('pesanan.index') }}" class="flex items-center gap-2.5 text-[13px] text-muted">
                <label for="status">Tampilkan:</label>
                <select id="status" name="status" onchange="this.form.submit()"
                        class="h-10 rounded-[10px] border border-line bg-white px-3 text-[13px] font-semibold text-ink focus:border-maroon focus:outline-none">
                    @foreach ($pilihan as $key => $p)
                        <option value="{{ $key }}" @selected($filter === $key)>{{ $p['label'] }} ({{ $p['jumlah'] }})</option>
                    @endforeach
                </select>
            </form>
        </div>

        @forelse ($pesanan as $p)
            @php
                $pickup = $p->metode_pengiriman === 'pickup';
                $metode = $p->metode_pembayaran === 'qris' ? 'QRIS' : 'BCA VA';
                $aktif = in_array($p->status_pesanan, ['dirangkai', 'siap_dikirim', 'siap_diambil', 'dikirim', 'selesai'], true);
            @endphp

            <article class="overflow-hidden rounded-3xl border border-line-soft bg-white shadow-[0_10px_28px_rgb(155_69_85/0.05)]">
                {{-- Kepala --}}
                <header class="flex flex-wrap items-center justify-between gap-2.5 border-b border-[#F7EAED] bg-blush-50 px-4 py-4 sm:px-5.5">
                    <div class="flex flex-wrap items-center gap-x-3.5 gap-y-2 text-[13px] text-muted">
                        <strong class="text-ink">{{ $p->kode_pesanan }}</strong>
                        <span>Dipesan {{ $p->created_at->translatedFormat('j M Y') }}</span>
                        <span class="{{ $chip }} border-line bg-white text-ink-soft">
                            <i class="bi {{ $p->metode_pembayaran === 'qris' ? 'bi-qr-code' : 'bi-bank' }} text-maroon"></i> {{ $metode }}
                        </span>
                        <span @class([$chip, 'border-[#F4DFB4] bg-[#FFF6E5] text-[#7A4A00]' => $pickup, 'border-[#CFE0F5] bg-[#EEF5FD] text-[#2D5B94]' => ! $pickup])>
                            <i class="bi {{ $pickup ? 'bi-shop' : 'bi-truck' }}"></i> {{ $pickup ? 'Ambil di Toko' : 'Kurir' }}
                        </span>
                    </div>
                    <span class="badge px-3 font-bold {{ $p->statusClass() }}">{{ $p->statusLabel() }}</span>
                </header>

                {{-- Item --}}
                <div class="flex flex-col gap-4 px-4 py-5 sm:px-5.5">
                    @foreach ($p->detailPesanan as $d)
                        <div class="flex items-center gap-4">
                            @if ($d->produk?->gambar_url)
                                <img src="{{ $d->produk->gambar_url }}" alt="" class="h-17 w-14 shrink-0 rounded-[14px] object-cover sm:h-22 sm:w-18">
                            @else
                                <span class="flex h-17 w-14 shrink-0 items-center justify-center rounded-[14px] bg-blush text-2xl text-rose sm:h-22 sm:w-18"><i class="bi bi-flower1"></i></span>
                            @endif
                            <div class="flex min-w-0 flex-1 flex-col gap-1">
                                <strong class="font-serif text-[19px] font-medium">{{ $d->nama_produk }}</strong>
                                <small class="text-[13px] text-muted">Ukuran {{ $d->ukuran_lengkap }} · {{ $d->jumlah }} buket</small>
                            </div>
                            <b class="text-[15px]">{{ $rupiah($d->harga * $d->jumlah) }}</b>
                        </div>
                    @endforeach

                    {{-- Keterangan status --}}
                    @switch($p->status_pesanan)
                        @case('menunggu_pembayaran')
                            <p class="{{ $catatan }}">Selesaikan pembayaran agar pesanan bisa segera dirangkai.</p>
                            @break
                        @case('siap_diambil')
                            <p class="{{ $catatan }}">Buket sudah siap. Silakan ambil di toko pada {{ $p->tanggal->translatedFormat('j M Y') }}, {{ $p->slot_waktu }}.</p>
                            @break
                        @case('dikirim')
                            <p class="{{ $catatan }}">Kurir sedang menuju alamat penerima.</p>
                            @break
                        @case('dibatalkan')
                            <p class="{{ $catatan }}">Pesanan dibatalkan karena pembayaran tidak diselesaikan sebelum batas waktu.</p>
                            @break
                    @endswitch

                    {{-- Progres --}}
                    @if ($aktif)
                        <ol class="mt-1 flex">
                            @foreach ($p->langkahProgres() as [$label, $lewat])
                                <li @if ($lewat) data-done @endif
                                    class="group relative flex flex-1 flex-col items-center gap-2 text-center text-[10.5px] text-muted sm:text-xs
                                           before:absolute before:top-[7px] before:right-1/2 before:h-0.5 before:w-full before:bg-line first:before:hidden
                                           data-done:font-semibold data-done:text-ink data-done:before:bg-maroon">
                                    <span class="relative z-10 size-3.5 rounded-full border-2 border-line bg-white group-data-done:border-maroon group-data-done:bg-maroon"></span>
                                    {{ $label }}
                                </li>
                            @endforeach
                        </ol>
                    @endif

                    {{-- Detail (bisa dibuka-tutup) --}}
                    <details>
                        <summary class="w-fit text-[13px] font-semibold text-maroon">Lihat detail</summary>
                        <dl class="mt-3 grid gap-3 sm:grid-cols-2">
                            @php
                                $detail = [
                                    [$pickup ? 'Pengambil' : 'Penerima', $p->nama_penerima . ' · ' . $p->no_whatsapp],
                                    [$pickup ? 'Lokasi' : 'Alamat', $pickup ? 'Atelier @bucket.imy, Jl. Tridaya Barat, Indramayu' : $p->alamat],
                                    ['Jadwal', $p->tanggal->translatedFormat('l, j M Y') . ' · ' . $p->slot_waktu],
                                    ['Pembayaran', $metode . ($p->pembayaran->first()?->dibayar_pada ? ', lunas ' . $p->pembayaran->first()->dibayar_pada->translatedFormat('j M Y H.i') : '')],
                                ];
                                if ($p->ongkir) $detail[] = ['Ongkir', $rupiah($p->ongkir)];
                            @endphp
                            @foreach ($detail as [$judul, $isi])
                                <div class="rounded-xl bg-paper px-3.5 py-3">
                                    <dt class="text-xs text-muted">{{ $judul }}</dt>
                                    <dd class="mt-0.5 text-[13.5px] font-semibold">{{ $isi }}</dd>
                                </div>
                            @endforeach
                        </dl>
                    </details>

                    {{-- Ulasan: bisa diisi, lalu diedit lagi kapan saja --}}
                    @if ($p->status_pesanan === 'selesai')
                        @php
                            $t = $p->testimoni;
                            $bag = 'ulasan' . $p->id;
                            $adaError = $errors->getBag($bag)->any();
                            // old() hanya dipakai untuk form yang barusan gagal, supaya tidak menular ke kartu lain
                            $nilai = (int) ($adaError ? old('rating') : $t?->rating);
                            $isi = $adaError ? old('isi_testimoni') : $t?->isi_testimoni;
                        @endphp

                        @if ($t)
                            <div class="rounded-xl bg-ok-soft px-4 py-3 text-[13px] text-ink-soft">
                                <span class="mr-2 tracking-widest text-gold">{{ str_repeat('★', $t->rating) }}{{ str_repeat('☆', 5 - $t->rating) }}</span>
                                {{ $t->isi_testimoni ?: 'Terima kasih atas ulasannya!' }}
                            </div>
                        @endif

                        @if ($t) <details class="group/ulasan" @if ($adaError) open @endif> @else <div> @endif
                            @if ($t)
                                <summary class="inline-flex cursor-pointer list-none items-center gap-1.5 text-[13px] font-semibold text-maroon hover:underline">
                                    <i class="bi bi-pencil"></i> <span class="group-open/ulasan:hidden">Edit ulasan</span><span class="hidden group-open/ulasan:inline">Batal edit</span>
                                </summary>
                            @endif
                            <form method="POST" action="{{ route('pesanan.ulasan', $p) }}" @class(['flex flex-col gap-2 rounded-2xl border border-line-soft p-4', 'mt-2.5' => $t])>
                                @csrf
                                <strong>{{ $t ? 'Ubah ulasanmu' : 'Bagaimana buketnya?' }}</strong>
                                @unless ($t) <small class="text-[12.5px] text-muted">Ulasanmu membantu pelanggan lain memilih buket.</small> @endunless
                                {{-- Bintang: urutan dibalik (5 → 1) supaya bintang di kiri ikut menyala saat dipilih / disorot --}}
                                <fieldset class="flex flex-row-reverse justify-end gap-1">
                                    <legend class="sr-only">Beri bintang</legend>
                                    @for ($i = 5; $i >= 1; $i--)
                                        <input type="radio" id="r{{ $p->id }}-{{ $i }}" name="rating" value="{{ $i }}" @checked($nilai === $i) class="peer sr-only">
                                        <label for="r{{ $p->id }}-{{ $i }}" title="{{ $i }} bintang"
                                               class="peer text-[28px] leading-none text-[#E3CDD2] transition-colors hover:text-gold peer-checked:text-gold peer-hover:text-gold">★</label>
                                    @endfor
                                </fieldset>
                                @error('rating', $bag) <span class="field-error">{{ $message }}</span> @enderror
                                <textarea name="isi_testimoni" maxlength="1000" placeholder="Ceritakan pengalamanmu (opsional)" class="field">{{ $isi }}</textarea>
                                @error('isi_testimoni', $bag) <span class="field-error">{{ $message }}</span> @enderror
                                <button type="submit" class="{{ $btnUtama }} self-end">{{ $t ? 'Simpan Perubahan' : 'Kirim Ulasan' }}</button>
                            </form>
                        @if ($t) </details> @else </div> @endif
                    @endif
                </div>

                {{-- Kaki --}}
                <footer class="flex flex-wrap items-center justify-between gap-3 border-t border-[#F7EAED] px-4 py-4 text-sm text-muted sm:px-5.5">
                    <span>Total: <strong class="text-xl text-maroon">{{ $rupiah($p->total_harga) }}</strong></span>
                    <div class="flex gap-2.5">
                        @switch($p->status_pesanan)
                            @case('menunggu_pembayaran')
                                <a href="{{ route('pembayaran.show', $p) }}" class="{{ $btnUtama }}">Bayar Sekarang</a>
                                @break
                            @case('dikirim')
                                <form method="POST" action="{{ route('pesanan.terima', $p) }}" data-konfirmasi="Buket sudah diterima?" data-nama="{{ $p->kode_pesanan }}" data-pesan="Pastikan buket sudah sampai dengan baik. Status pesanan akan menjadi Selesai." data-tombol="Ya, sudah" data-jenis="selesai">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="{{ $btnUtama }}">Pesanan Diterima</button>
                                </form>
                                @break
                            @case('selesai')
                            @case('dibatalkan')
                                <a href="{{ route('katalog.index') }}" class="{{ $btnGaris }}">Pesan Lagi</a>
                                @break
                            @default
                                <a href="{{ $wa($p->kode_pesanan) }}" target="_blank" rel="noopener noreferrer" class="{{ $btnGaris }}">Hubungi Admin</a>
                        @endswitch
                    </div>
                </footer>
            </article>
        @empty
            <div class="flex flex-col items-center gap-2.5 rounded-[20px] border border-dashed border-line bg-white px-6 py-16 text-center">
                <span class="flex size-21 items-center justify-center rounded-full bg-blush text-3xl text-maroon"><i class="bi bi-bag"></i></span>
                <h2 class="mt-2 text-xl font-bold">Belum ada pesanan{{ $filter !== 'semua' ? ' di status ini' : '' }}</h2>
                <a href="{{ route('katalog.index') }}" class="btn btn-primary min-h-10 px-5.5 text-[13px]">Lihat Katalog</a>
            </div>
        @endforelse

        @if ($pesanan->hasPages())
            <nav aria-label="Halaman" class="flex items-center justify-center gap-3.5 text-[13px] text-muted">
                @if (! $pesanan->onFirstPage()) <a href="{{ $pesanan->previousPageUrl() }}" class="{{ $btnGaris }}">← Sebelumnya</a> @endif
                <span>Halaman {{ $pesanan->currentPage() }} dari {{ $pesanan->lastPage() }}</span>
                @if ($pesanan->hasMorePages()) <a href="{{ $pesanan->nextPageUrl() }}" class="{{ $btnGaris }}">Berikutnya →</a> @endif
            </nav>
        @endif
    </section>
</div>
@endsection
