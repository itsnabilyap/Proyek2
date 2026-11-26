@extends('layouts.admin')

@section('title', 'Data Pesanan')
@section('judul', 'Data Pesanan')

@section('aksi')
    <a href="{{ route('admin.pesanan.ekspor', request()->query()) }}" class="btn btn-outline rounded-xl">
        <i class="bi bi-download"></i> Ekspor Excel
    </a>
@endsection

@php
    $S = \App\Http\Controllers\Admin\PesananController::STATUS;
    $rp = fn ($n) => 'Rp' . number_format($n, 0, ',', '.');
@endphp

@section('tab-atas')
    <x-admin.tab-pesanan aktif="katalog" />
@endsection

@section('content')

{{-- ===== TAB STATUS (HP: bisa digeser ke samping) ===== --}}
<nav class="mb-5 flex gap-2.5 overflow-x-auto pb-1 sm:flex-wrap sm:overflow-visible sm:pb-0">
    @foreach ($tabs as $kunci => [$label, $jumlah])
        <a href="{{ route('admin.pesanan.index', array_merge(request()->except('status', 'page'), $kunci === 'semua' ? [] : ['status' => $kunci])) }}"
           @if ($status === $kunci) aria-current="page" @endif
           class="group inline-flex shrink-0 items-center gap-2 rounded-full border border-line bg-white px-4 py-2.5 text-sm font-semibold hover:border-maroon aria-[current=page]:border-maroon aria-[current=page]:bg-maroon aria-[current=page]:text-white">
            {{ $label }}
            <span class="min-w-5.5 rounded-full bg-blush px-1.5 py-px text-center text-xs text-wine group-aria-[current=page]:bg-white/25 group-aria-[current=page]:text-white">{{ $jumlah }}</span>
        </a>
    @endforeach
</nav>

{{-- ===== FILTER ===== --}}
<x-admin.cari placeholder="Cari no. pesanan, nama, atau produk">
    @if ($status !== 'semua') <input type="hidden" name="status" value="{{ $status }}"> @endif

    <select name="terima" onchange="this.form.submit()">
        <option value="">Semua cara terima</option>
        <option value="kurir" @selected(request('terima') === 'kurir')>Kurir</option>
        <option value="pickup" @selected(request('terima') === 'pickup')>Pick up</option>
    </select>

    <select name="jadwal" onchange="this.form.submit()">
        <option value="">Semua jadwal</option>
        <option value="hari_ini" @selected(request('jadwal') === 'hari_ini')>Hari ini</option>
        <option value="besok" @selected(request('jadwal') === 'besok')>Besok</option>
        <option value="minggu_ini" @selected(request('jadwal') === 'minggu_ini')>Minggu ini</option>
    </select>
</x-admin.cari>

{{-- ===== TABEL ===== --}}
<section class="kartu mb-5.5 overflow-hidden p-0!">
    <div class="overflow-x-auto">
        <table class="tabel">
            <thead>
                <tr>
                    <th>No. Pesanan</th><th>Pelanggan</th><th>Produk</th><th>Cara Terima</th>
                    <th>Jadwal</th><th class="text-right!">Total</th><th>Status</th><th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pesanan as $p)
                    @php
                        [$label, $kelas] = $S[$p->status_pesanan] ?? [$p->status_pesanan, 'b-abu'];
                        $pickup = $p->metode_pengiriman === 'pickup';
                        $d = $p->detailPesanan->first();
                        $lain = $p->detailPesanan->count() - 1;
                        $bayar = $p->metode_pembayaran === 'qris' ? 'QRIS' : 'VA BCA';
                    @endphp
                    <tr>
                        <td>
                            <strong>{{ $p->kode_pesanan }}</strong>
                            <small>{{ $p->created_at->translatedFormat('j M, H.i') }}</small>
                        </td>
                        <td>
                            <span class="font-semibold">{{ $p->pelanggan?->name ?? $p->nama_penerima }}</span>
                            <small>{{ $p->no_whatsapp }}</small>
                        </td>
                        <td>{{ $d ? "{$d->nama_produk} ({$d->ukuran_lengkap})" : '-' }}{{ $lain > 0 ? " + {$lain}" : '' }}</td>
                        <td><span class="tag {{ $pickup ? 'tag-pickup' : 'tag-kurir' }}">{{ $pickup ? 'Pick up' : 'Kurir' }}</span></td>
                        <td>
                            @if ($p->status_pesanan === 'dibatalkan')
                                -
                            @else
                                {{ $p->tanggal->isToday() ? 'Hari ini' : ($p->tanggal->isTomorrow() ? 'Besok' : $p->tanggal->translatedFormat('j M')) }},
                                {{ str_replace(':', '.', substr($p->slot_waktu, 0, 5)) }}
                            @endif
                        </td>
                        <td class="text-right">
                            <strong>{{ $rp($p->total_harga) }}</strong>
                            @if ($p->status_pesanan === 'menunggu_pembayaran')
                                <small class="text-[#A06A00]!">Belum bayar · {{ $bayar }}</small>
                            @elseif ($p->status_pesanan === 'dibatalkan')
                                <small class="text-danger!">Batal · {{ $bayar }}</small>
                            @else
                                <small class="text-ok!">Lunas · {{ $bayar }}</small>
                            @endif
                        </td>
                        <td><span class="badge {{ $kelas }} min-w-32.5 px-3.5 py-1.5 text-center">{{ $label }}</span></td>
                        <td><a href="{{ route('admin.pesanan.show', $p) }}" data-detail data-kode="{{ $p->kode_pesanan }}" class="btn btn-sm btn-soft rounded-xl">Detail</a></td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="kosong">Tidak ada pesanan yang cocok.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <x-admin.kaki :data="$pesanan" nama="pesanan" />
</section>

{{-- ===== POPUP DETAIL ===== --}}
<x-admin.modal id="modal-detail" judul="Detail Pesanan">
    <div data-modal-isi class="px-4 pt-4 pb-1 sm:px-6 sm:pt-5"></div>
</x-admin.modal>

<script>
    const modal = document.getElementById('modal-detail');
    const isiModal = modal.querySelector('[data-modal-isi]');

    document.querySelectorAll('[data-detail]').forEach(tombol => {
        tombol.addEventListener('click', async (e) => {
            e.preventDefault();
            modal.querySelector('[data-modal-judul]').textContent = 'Pesanan ' + tombol.dataset.kode;
            isiModal.innerHTML = '<p class="kosong">Memuat detail…</p>';
            modal.showModal();

            try {
                const res = await fetch(tombol.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                if (!res.ok) throw new Error();
                isiModal.innerHTML = await res.text();
            } catch {
                isiModal.innerHTML = '<p class="kosong">Gagal memuat detail. Coba lagi.</p>';
            }
        });
    });

    // Klik area gelap di luar kotak → tutup
    modal.addEventListener('click', (e) => { if (e.target === modal) modal.close(); });
</script>
@endsection
