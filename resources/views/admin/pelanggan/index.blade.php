@extends('layouts.admin')

@section('title', 'Data Pelanggan')
@section('judul', 'Data Pelanggan')

@php
    $rp = fn ($n) => 'Rp' . number_format((int) $n, 0, ',', '.');
    $noWa = fn ($u) => $u->no_hp ?: $u->pesanan->first()?->no_whatsapp;
    $linkWa = function ($hp, $nama) {
        $angka = preg_replace('/\D/', '', (string) $hp);
        if (str_starts_with($angka, '0')) $angka = '62' . substr($angka, 1);
        return 'https://wa.me/' . $angka . '?text=' . urlencode("Halo {$nama}, kami dari @bucket.imy.");
    };
@endphp

@section('content')
{{-- ===== CARI ===== --}}
<x-admin.cari placeholder="Cari ID, nama, email, atau nomor WhatsApp" />

{{-- ===== TABEL ===== --}}
<section class="kartu mb-5.5 overflow-hidden p-0!">
    <div class="overflow-x-auto">
        <table class="tabel">
            <thead>
                <tr>
                    <th>ID Pelanggan</th><th>Pelanggan</th><th>WhatsApp</th><th>Pesanan</th>
                    <th>Total Belanja</th><th>Pesanan Terakhir</th><th class="text-right!">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pelanggan as $u)
                    @php $wa = $noWa($u); @endphp
                    <tr>
                        <td><span class="kode">{{ $u->kode }}</span></td>
                        <td>
                            <span class="font-semibold">{{ $u->name }}</span>
                            <small>{{ $u->email }}</small>
                        </td>
                        <td>{{ $wa ?: '-' }}</td>
                        <td><strong>{{ $u->jumlah_pesanan }} pesanan</strong></td>
                        <td><strong>{{ $rp($u->total_belanja) }}</strong></td>
                        <td>
                            @if ($u->pesanan_terakhir)
                                {{ \Illuminate\Support\Carbon::parse($u->pesanan_terakhir)->translatedFormat('j M Y') }}
                            @elseif ($u->jumlah_custom)
                                Request custom
                            @else
                                <span class="text-xs text-muted">Belum pernah</span>
                            @endif
                        </td>
                        <td>
                            <div class="flex items-center justify-end gap-2">
                                @if ($wa)
                                    <a href="{{ $linkWa($wa, $u->name) }}" target="_blank" rel="noopener noreferrer" title="Chat WhatsApp"
                                       class="flex size-9 items-center justify-center rounded-[10px] bg-ok-soft text-[15px] text-ok hover:bg-[#CDEBD8]">
                                        <i class="bi bi-whatsapp"></i>
                                    </a>
                                @endif
                                <a href="{{ route('admin.pelanggan.show', $u) }}" data-detail class="btn btn-sm btn-soft rounded-xl">Detail</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="kosong">Tidak ada pelanggan yang cocok.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <x-admin.kaki :data="$pelanggan" nama="pelanggan" />
</section>

{{-- ===== POPUP DETAIL (kepala & tombol tutup ada di _detail) ===== --}}
<dialog id="modal-pelanggan" class="modal w-[min(500px,calc(100%-32px))] bg-white">
    <div data-modal-isi class="px-4 py-5 min-[381px]:p-6.5"></div>
</dialog>

<script>
    const modalPl = document.getElementById('modal-pelanggan');
    const isiPl = modalPl.querySelector('[data-modal-isi]');

    document.querySelectorAll('[data-detail]').forEach(tombol => {
        tombol.addEventListener('click', async (e) => {
            e.preventDefault();
            isiPl.innerHTML = '<p class="kosong">Memuat detail…</p>';
            modalPl.showModal();

            try {
                const res = await fetch(tombol.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                if (!res.ok) throw new Error();
                isiPl.innerHTML = await res.text();
            } catch {
                isiPl.innerHTML = '<p class="kosong">Gagal memuat detail. Coba lagi.</p>';
            }
        });
    });

    modalPl.addEventListener('click', (e) => { if (e.target === modalPl) modalPl.close(); });
</script>
@endsection
