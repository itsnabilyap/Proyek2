@extends('layouts.admin')

@section('title', 'Request Custom')
@section('judul', 'Data Pesanan')

@php $J = \App\Models\Custom::JENIS; $S = \App\Models\Custom::STATUS; @endphp

@section('tab-atas')
    <x-admin.tab-pesanan aktif="custom" />
@endsection

@section('content')

{{-- ===== TAB STATUS ===== --}}
<nav class="mb-5 flex gap-2.5 overflow-x-auto pb-1 sm:flex-wrap sm:overflow-visible sm:pb-0">
    @foreach ($tabs as $kunci => [$label, $jumlah])
        <a href="{{ route('admin.pesanan.custom.index', array_merge(request()->except('status', 'page'), $kunci === 'semua' ? [] : ['status' => $kunci])) }}"
           @if ($status === $kunci) aria-current="page" @endif
           class="group inline-flex shrink-0 items-center gap-2 rounded-full border border-line bg-white px-4 py-2.5 text-sm font-semibold hover:border-maroon aria-[current=page]:border-maroon aria-[current=page]:bg-maroon aria-[current=page]:text-white">
            {{ $label }}
            <span class="min-w-5.5 rounded-full bg-blush px-1.5 py-px text-center text-xs text-wine group-aria-[current=page]:bg-white/25 group-aria-[current=page]:text-white">{{ $jumlah }}</span>
        </a>
    @endforeach
</nav>

{{-- ===== FILTER ===== --}}
<x-admin.cari placeholder="Cari #ID, nama, atau nomor WhatsApp">
    @if ($status !== 'semua') <input type="hidden" name="status" value="{{ $status }}"> @endif
    <select name="jenis" onchange="this.form.submit()">
        <option value="">Semua jenis</option>
        @foreach ($J as $jenis => $j)
            <option value="{{ $jenis }}" @selected(request('jenis') === $jenis)>{{ $jenis }}</option>
        @endforeach
    </select>
</x-admin.cari>

{{-- ===== TABEL ===== --}}
<section class="kartu mb-5.5 overflow-hidden p-0!">
    <div class="overflow-x-auto">
        <table class="tabel">
            <thead>
                <tr><th>ID</th><th>Pelanggan</th><th>Jenis</th><th>Budget</th><th>Dibutuhkan</th><th>Status</th><th></th></tr>
            </thead>
            <tbody>
                @forelse ($custom as $c)
                    @php [$label, $kelas] = $S[$c->status] ?? [$c->status, 'b-abu']; @endphp
                    <tr>
                        <td><span class="kode">#{{ $c->id }}</span><small>{{ $c->created_at->diffForHumans() }}</small></td>
                        <td><span class="font-semibold">{{ $c->nama_pemesan }}</span><small>{{ $c->no_whatsapp }}</small></td>
                        <td>
                            {{ $c->jenis_bucket }}
                            @if ($c->foto_referensi) <small><i class="bi bi-image"></i> ada foto referensi</small> @endif
                        </td>
                        <td>
                            <strong>{{ $c->budget_label }}</strong>
                            @if ($c->harga_final) <small class="text-ok!">Final Rp{{ number_format($c->harga_final, 0, ',', '.') }}</small> @endif
                            @if ($c->pesanan && $c->pesanan->status_pesanan !== 'dibatalkan') <small>Pesanan {{ $c->pesanan->kode_pesanan }}</small> @endif
                        </td>
                        <td @class(['font-semibold text-danger' => $c->status === 'baru' && $c->tanggal_dibutuhkan->lte(today()->addDay())])>
                            {{ $c->tanggal_dibutuhkan->isToday() ? 'Hari ini' : ($c->tanggal_dibutuhkan->isTomorrow() ? 'Besok' : $c->tanggal_dibutuhkan->translatedFormat('j M Y')) }}
                        </td>
                        <td><span class="badge {{ $kelas }}">{{ $label }}</span></td>
                        <td>
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ $c->link_wa }}" target="_blank" rel="noopener noreferrer" title="Chat WhatsApp"
                                   class="flex size-9 items-center justify-center rounded-[10px] bg-ok-soft text-[15px] text-ok hover:bg-[#CDEBD8]"><i class="bi bi-whatsapp"></i></a>
                                <a href="{{ route('admin.pesanan.custom.show', $c) }}" data-detail data-judul="Request #{{ $c->id }}" class="btn btn-sm btn-soft rounded-xl">Detail</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="kosong">Belum ada request custom.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <x-admin.kaki :data="$custom" nama="request" />
</section>

{{-- ===== POPUP DETAIL ===== --}}
<x-admin.modal id="modal-custom" judul="Request Custom" class="w-[min(560px,calc(100%-32px))] bg-white">
    <div data-modal-isi class="px-4 py-5 sm:px-6"></div>
</x-admin.modal>

<script>
    const modalC = document.getElementById('modal-custom');
    const isiC = modalC.querySelector('[data-modal-isi]');

    document.querySelectorAll('[data-detail]').forEach(tombol => {
        tombol.addEventListener('click', async (e) => {
            e.preventDefault();
            modalC.querySelector('[data-modal-judul]').textContent = tombol.dataset.judul;
            isiC.innerHTML = '<p class="kosong">Memuat detail…</p>';
            modalC.showModal();
            try {
                const res = await fetch(tombol.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                if (!res.ok) throw new Error();
                isiC.innerHTML = await res.text();
            } catch {
                isiC.innerHTML = '<p class="kosong">Gagal memuat detail. Coba lagi.</p>';
            }
        });
    });
    modalC.addEventListener('click', (e) => { if (e.target === modalC) modalC.close(); });

    // Kolom harga final hanya perlu saat status "Disetujui"
    document.addEventListener('change', (e) => {
        if (e.target.name !== 'status') return;
        const harga = e.target.form.querySelector('[data-harga-final]');
        if (harga) harga.hidden = e.target.value !== 'disetujui';
    });
</script>
@endsection
