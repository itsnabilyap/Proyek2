@extends('layouts.app')

@section('title', 'Request Custom')
@section('footer-ringkas', '1')

@php
    $rupiah = fn ($n) => 'Rp' . number_format((int) $n, 0, ',', '.');
@endphp

@section('content')
<div class="wrap max-w-[880px] pt-12 pb-24">
    <section class="flex w-full min-w-0 flex-col gap-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h1 class="text-[22px] font-bold">Request Custom ({{ $riwayat->total() }})</h1>
            <a href="{{ route('custom.create') }}" class="btn btn-primary min-h-10 rounded-full px-5 text-[13px]">
                <i class="bi bi-plus-lg"></i> Request Baru
            </a>
        </div>

        @if ($riwayat->isEmpty())
            <div class="flex flex-col items-center gap-2.5 rounded-[20px] border border-dashed border-line bg-white px-6 py-16 text-center">
                <span class="flex size-21 items-center justify-center rounded-full bg-blush text-3xl text-maroon"><i class="bi bi-stars"></i></span>
                <h2 class="mt-2 text-xl font-bold">Belum ada request custom</h2>
                <p class="max-w-[360px] text-[13px] text-muted">Punya budget atau tema sendiri? Ceritakan, nanti admin balas via WhatsApp dengan harga final.</p>
                <a href="{{ route('custom.create') }}" class="btn btn-primary mt-1 min-h-10 px-5.5 text-[13px]">Buat Request</a>
            </div>
        @else
            <div class="rounded-[20px] border border-line-soft bg-white">
            <ul class="divide-y divide-line-soft">
                @foreach ($riwayat as $c)
                    @php
                        $lunas = $c->pesanan && ! in_array($c->pesanan->status_pesanan, ['menunggu_pembayaran', 'dibatalkan'], true);
                        $menunggu = $c->pesanan?->status_pesanan === 'menunggu_pembayaran';
                        [$label, $kelas] = match (true) {
                            $lunas                      => ['Sudah dibayar', 'st-selesai'],
                            $menunggu                   => ['Menunggu pembayaran', 'st-tunggu'],
                            $c->status === 'disetujui'  => ['Disetujui · siap dibayar', 'st-siap'],
                            $c->status === 'ditolak'    => ['Tidak bisa diproses', 'st-batal'],
                            $c->status === 'dihubungi'  => ['Sedang dibahas via WA', 'st-proses'],
                            default                     => ['Menunggu balasan admin', 'st-tunggu'],
                        };
                    @endphp
                    <li class="flex flex-wrap items-center gap-3 px-4 py-4 sm:px-5.5">
                        <div class="min-w-0 flex-1">
                            <strong class="block text-[15px]">{{ $c->jenis_bucket }} <span class="text-xs font-normal text-muted">#{{ $c->id }}</span></strong>
                            <small class="text-[13px] text-muted">
                                Dibutuhkan {{ $c->tanggal_dibutuhkan->translatedFormat('j M Y') }} ·
                                {{ $c->harga_final ? 'Harga final ' . $rupiah($c->harga_final) : 'Budget ' . $c->budget_label }}
                            </small>
                        </div>
                        <span class="badge {{ $kelas }}">{{ $label }}</span>
                        @if ($menunggu)
                            <a href="{{ route('pembayaran.show', $c->pesanan) }}" class="btn btn-sm btn-primary rounded-full px-4">Lanjutkan Bayar</a>
                        @elseif ($c->bisaDibayar())
                            <a href="{{ route('checkout.index', ['custom' => $c->id]) }}" class="btn btn-sm btn-primary rounded-full px-4">Bayar Sekarang</a>
                        @elseif ($lunas)
                            <span class="text-xs text-muted">Lihat di daftar pesanan: <b class="text-ink">{{ $c->pesanan->kode_pesanan }}</b></span>
                        @endif
                    </li>
                @endforeach
            </ul>
            </div>

            @if ($riwayat->hasPages())
                <nav aria-label="Halaman" class="flex items-center justify-center gap-3.5 text-[13px] text-muted">
                    @if (! $riwayat->onFirstPage()) <a href="{{ $riwayat->previousPageUrl() }}" class="btn min-h-11.5 border border-line font-medium text-muted hover:border-maroon hover:text-maroon">← Sebelumnya</a> @endif
                    <span>Halaman {{ $riwayat->currentPage() }} dari {{ $riwayat->lastPage() }}</span>
                    @if ($riwayat->hasMorePages()) <a href="{{ $riwayat->nextPageUrl() }}" class="btn min-h-11.5 border border-line font-medium text-muted hover:border-maroon hover:text-maroon">Berikutnya →</a> @endif
                </nav>
            @endif
        @endif
    </section>
</div>
@endsection
