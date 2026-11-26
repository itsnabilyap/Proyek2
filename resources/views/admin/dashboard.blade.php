@extends('layouts.admin')

@section('title', 'Ringkasan')
@section('judul', 'Ringkasan Operasional')

@php
    $selisih = $masukHariIni - $masukKemarin;
    $batas = \App\Http\Controllers\Admin\DashboardController::BATAS_STOK;

    $statusAdmin = [
        'menunggu_pembayaran' => ['Menunggu bayar', 'b-kuning'],
        'dirangkai'           => ['Perlu dirangkai', 'b-kuning'],
        'siap_diambil'        => ['Siap diambil', 'b-ungu'],
        'siap_dikirim'        => ['Siap dikirim', 'b-ungu'],
        'dikirim'             => ['Dikirim', 'b-biru'],
        'selesai'             => ['Selesai', 'b-hijau'],
        'dibatalkan'          => ['Dibatalkan', 'b-abu'],
    ];

    $produkSingkat = function ($p) {
        $d = $p->detailPesanan->first();
        if (! $d) return 'Pesanan custom';
        $lain = $p->detailPesanan->count() - 1;
        return $d->nama_produk . ' (' . $d->ukuran_lengkap . ')' . ($lain ? " + {$lain}" : '');
    };

    $judulKartu = 'text-[17px] font-bold';
    $kepalaKartu = 'mb-4 flex flex-wrap items-center justify-between gap-2.5';
    $link = 'text-[13px] font-semibold text-maroon hover:underline';
    $pillMerah = 'rounded-full bg-danger-soft px-2.5 py-0.5 text-xs font-semibold text-danger';

    $maksTerlaris = max(1, (int) ($terlaris->max('total') ?? 1));
@endphp

@section('content')

{{-- ========== KARTU ANGKA ========== --}}
<div class="mb-5.5 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
    @php
        $kpi = [
            ['Pesanan masuk hari ini', '#9B4555', $masukHariIni, ($selisih >= 0 ? '+' : '') . $selisih . ' dibanding kemarin', $selisih >= 0 ? 'text-ok' : 'text-danger'],
            ['Perlu dirangkai', '#C68A10', $perluDirangkai, $dirangkaiBesok . ' untuk jadwal besok', 'text-muted'],
            ['Menunggu pembayaran', '#B3261E', $menungguBayar, 'Batal otomatis jika lewat batas waktu', 'text-muted'],
            ['Siap diambil / diantar', '#5B3E96', $siap->sum(), ($siap['pickup'] ?? 0) . ' pick up · ' . ($siap['kurir'] ?? 0) . ' kurir', 'text-muted'],
        ];
    @endphp
    @foreach ($kpi as [$label, $warna, $angka, $ket, $warnaKet])
        <div class="flex flex-col gap-2 rounded-[18px] border border-line bg-white p-5">
            <span class="flex justify-between gap-2 text-[13px] text-muted">{{ $label }}
                <i class="mt-1 size-2.5 shrink-0 rounded-full" style="background: {{ $warna }}"></i></span>
            <strong class="text-[34px] leading-none font-extrabold">{{ $angka }}</strong>
            <small class="text-xs {{ $warnaKet }}">{{ $ket }}</small>
        </div>
    @endforeach
</div>

{{-- Dua kartu sama lebar (50:50) dan sama tinggi --}}
<div class="mb-5.5 grid gap-5.5 xl:grid-cols-2">
    {{-- ========== STOK MENIPIS ========== --}}
    <section class="kartu">
        <div class="{{ $kepalaKartu }}">
            <h2 class="{{ $judulKartu }}">Perlu Tindakan · Stok Menipis</h2>
            @if ($stokMenipis->isNotEmpty())
                <span class="{{ $pillMerah }}">{{ $stokMenipis->count() }} item</span>
            @endif
        </div>

        <div class="mb-4 flex flex-col gap-3">
            @forelse ($stokMenipis as $p)
                <div class="flex flex-wrap items-center gap-3.5 rounded-[14px] border border-line p-3.5 sm:flex-nowrap">
                    <span class="flex size-9.5 shrink-0 items-center justify-center rounded-[10px] bg-blush text-maroon"><i class="bi bi-flower2"></i></span>
                    <div class="flex min-w-0 flex-1 flex-col gap-1">
                        <strong class="text-sm">{{ $p->nama_produk }}
                            <span class="{{ $pillMerah }} ml-1.5 px-2 text-[11px]">{{ $p->stok == 0 ? 'Habis' : 'Kritis' }}</span>
                        </strong>
                        <small class="text-xs text-muted">Produk siap jual · Sisa <b>{{ $p->stok }} buket</b> · Batas minimum {{ $batas }} buket</small>
                        <span class="mt-1 block h-1.5 overflow-hidden rounded-full bg-[#F7E6EA]"><span class="block h-full rounded-full bg-[#D9534F]" style="width: {{ min(100, $p->stok / $batas * 100) }}%"></span></span>
                    </div>
                    @can('kelola')
                        <a href="{{ route('admin.produk.index') }}" class="btn btn-sm btn-primary rounded-xl">Tambah Stok</a>
                    @endcan
                </div>
            @empty
                <p class="kosong">Semua stok produk aman.</p>
            @endforelse
        </div>

        <a href="{{ route('admin.produk.index') }}" class="{{ $link }}">
            @can('kelola') Kelola semua stok → @else Lihat semua stok → @endcan
        </a>
    </section>

    {{-- ========== JADWAL BESOK ========== --}}
    <section class="kartu">
        <div class="{{ $kepalaKartu }}">
            <h2 class="{{ $judulKartu }}">Jadwal Besok · {{ $besok->translatedFormat('j M') }}</h2>
            <div role="group" aria-label="Filter jadwal" class="flex gap-1 rounded-xl bg-[#FBF1F3] p-1">
                @foreach (['semua' => 'Semua', 'kurir' => 'Kurir', 'pickup' => 'Pick up'] as $nilai => $teks)
                    <button type="button" data-jadwal="{{ $nilai }}" @if ($loop->first) aria-pressed="true" @endif
                            class="min-h-8 rounded-[9px] px-3 text-xs font-semibold text-muted aria-pressed:bg-white aria-pressed:text-maroon aria-pressed:shadow-[0_2px_6px_rgb(42_26_30/0.08)]">{{ $teks }}</button>
                @endforeach
            </div>
        </div>

        <ol class="flex flex-col gap-2.5">
            @forelse ($jadwalBesok as $p)
                @php $pickup = $p->metode_pengiriman === 'pickup'; @endphp
                <li data-metode="{{ $p->metode_pengiriman }}" class="grid grid-cols-[48px_16px_1fr] items-start gap-2.5">
                    <time class="pt-3 text-[13px] font-semibold">{{ substr($p->slot_waktu, 0, 5) }}</time>
                    <span @class(['mt-3.5 size-3 rounded-full border-2 bg-white', 'border-[#C68A10]' => $pickup, 'border-[#2D5B94]' => ! $pickup])></span>
                    <div class="relative flex flex-col gap-0.5 rounded-xl bg-[#FBF4F5] py-2.5 pr-18 pl-3">
                        <strong class="text-[13px]">{{ $p->kode_pesanan }} · {{ $produkSingkat($p) }}</strong>
                        <small class="text-xs text-muted">{{ $p->nama_penerima }} · {{ $pickup ? 'ambil di atelier' : \Illuminate\Support\Str::limit($p->alamat, 30) }}</small>
                        <span class="tag absolute top-2.5 right-2.5 {{ $pickup ? 'tag-pickup' : 'tag-kurir' }}">{{ $pickup ? 'Pick up' : 'Kurir' }}</span>
                    </div>
                </li>
            @empty
                <li class="kosong">Belum ada jadwal untuk besok.</li>
            @endforelse
        </ol>
    </section>
</div>

{{-- ========== PESANAN TERBARU ========== --}}
<section class="kartu mb-5.5">
    <div class="{{ $kepalaKartu }}">
        <h2 class="{{ $judulKartu }}">Pesanan Terbaru</h2>
        <a href="{{ route('admin.pesanan.index') }}" class="{{ $link }}">Lihat semua pesanan →</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full min-w-[720px] border-collapse text-[13px]">
            <thead>
                <tr class="border-b border-line text-left text-muted">
                    @foreach (['No. Pesanan', 'Pelanggan', 'Produk', 'Cara Terima', 'Jadwal', 'Status'] as $kolom)
                        <th class="px-3 py-2.5 font-medium">{{ $kolom }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse ($terbaru as $p)
                    @php [$label, $kelas] = $statusAdmin[$p->status_pesanan] ?? [$p->status_pesanan, 'b-abu']; @endphp
                    <tr class="border-b border-[#F7EEF0] hover:bg-blush-50">
                        <td class="p-3"><strong>{{ $p->kode_pesanan }}</strong></td>
                        <td class="p-3">{{ $p->pelanggan?->name ?? '-' }}</td>
                        <td class="p-3">{{ $produkSingkat($p) }}</td>
                        <td class="p-3"><span class="tag {{ $p->metode_pengiriman === 'pickup' ? 'tag-pickup' : 'tag-kurir' }}">{{ $p->metode_pengiriman === 'pickup' ? 'Pick up' : 'Kurir' }}</span></td>
                        <td class="p-3">{{ $p->tanggal->isToday() ? 'Hari ini' : $p->tanggal->translatedFormat('j M') }}, {{ substr($p->slot_waktu, 0, 5) }}</td>
                        <td class="p-3"><span class="badge {{ $kelas }}">{{ $label }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="kosong">Belum ada pesanan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>

{{-- Dua kartu sama lebar (50:50) dan sama tinggi --}}
<div class="mb-5.5 grid gap-5.5 xl:grid-cols-2">
    {{-- ========== REQUEST CUSTOM ========== --}}
    <section class="kartu">
        <div class="{{ $kepalaKartu }}">
            <h2 class="{{ $judulKartu }}">Request Custom Baru</h2>
            <small class="text-xs text-muted">Target balas &lt; 15 menit</small>
        </div>
        @forelse ($customBaru as $c)
            <article class="mb-3 flex flex-col items-start gap-1.5 rounded-[14px] border border-line p-3.5">
                <div class="flex w-full justify-between gap-2.5">
                    <strong class="text-sm">{{ $c->nama_pemesan }} · {{ $c->jenis_bucket }}</strong>
                    <small @class(['text-xs', 'text-danger' => $c->created_at->diffInMinutes() > 15, 'text-muted' => $c->created_at->diffInMinutes() <= 15])>{{ $c->created_at->diffForHumans() }}</small>
                </div>
                <small class="text-xs text-muted">Budget {{ $c->budget_label }} · Dibutuhkan {{ $c->tanggal_dibutuhkan->translatedFormat('j M') }}</small>
                @if ($c->catatan) <p class="text-[13px] text-ink-soft">“{{ \Illuminate\Support\Str::limit($c->catatan, 120) }}”</p> @endif
                @if ($c->tema) <small class="text-xs text-muted">Warna &amp; tema: <b class="text-ink">{{ $c->tema }}</b></small> @endif
                @if ($c->foto_referensi_url) <a href="{{ $c->foto_referensi_url }}" target="_blank" rel="noopener" class="text-xs font-semibold text-maroon hover:underline"><i class="bi bi-image"></i> Lihat foto referensi</a> @endif
                {{-- Admin: klik → status jadi "Dihubungi", WA terbuka di tab baru, kartu hilang dari daftar --}}
                @can('kelola')
                    <form method="POST" action="{{ route('admin.pesanan.custom.balas', $c) }}" target="_blank" data-balas>
                        @csrf
                        <button type="submit" class="btn btn-sm btn-primary rounded-xl">Balas via WhatsApp</button>
                    </form>
                @else
                    <a href="{{ $c->link_wa }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-primary rounded-xl">Balas via WhatsApp</a>
                @endcan
            </article>
        @empty
            <p class="kosong">Tidak ada request custom baru.</p>
        @endforelse
    </section>

    {{-- ========== TERLARIS ========== --}}
    <section class="kartu">
        <div class="mb-4 flex flex-col gap-0.5">
            <h2 class="{{ $judulKartu }}">Paling Banyak Dipesan</h2>
            <small class="text-xs text-muted">7 hari terakhir, jumlah buket. Bantu siapkan stok bunga.</small>
        </div>
        @forelse ($terlaris as $t)
            <div class="mb-3.5">
                <div class="flex justify-between text-[13px]"><span>{{ $t->nama_produk }}</span><strong>{{ $t->total }} buket</strong></div>
                <span class="mt-1 block h-2 overflow-hidden rounded-full bg-[#F7E6EA]"><span class="block h-full rounded-full bg-maroon" style="width: {{ $t->total / $maksTerlaris * 100 }}%"></span></span>
            </div>
        @empty
            <p class="kosong">Belum ada penjualan 7 hari terakhir.</p>
        @endforelse
    </section>
</div>

{{-- ========== ULASAN ========== --}}
<section class="kartu">
    <div class="{{ $kepalaKartu }}">
        <h2 class="{{ $judulKartu }}">Ulasan Terbaru</h2>
        @if ($ulasan->isNotEmpty()) <strong class="text-[#C68A10]">★ {{ number_format($rataRating, 1, ',', '') }}</strong> @endif
    </div>
    @forelse ($ulasan as $u)
        <div class="border-b border-line py-3 last:border-0">
            <div class="flex justify-between text-[13px]"><strong>{{ $u->user?->name ?? 'Pelanggan' }}</strong><span class="tracking-[2px] text-gold">{{ str_repeat('★', $u->rating) }}{{ str_repeat('☆', 5 - $u->rating) }}</span></div>
            <p class="mt-1.5 text-[13px] text-ink-soft">{{ $u->isi_testimoni ?: 'Tanpa komentar.' }}</p>
        </div>
    @empty
        <p class="kosong">Belum ada ulasan.</p>
    @endforelse
</section>

<script>
    // Request custom dibalas: muat ulang dashboard supaya kartunya hilang (WA sudah terbuka di tab baru)
    document.querySelectorAll('[data-balas]').forEach(f => f.addEventListener('submit', () => setTimeout(() => location.reload(), 800)));

    // Filter jadwal besok: Semua / Kurir / Pick up
    document.querySelectorAll('[data-jadwal]').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('[data-jadwal]').forEach(b => b.setAttribute('aria-pressed', String(b === btn)));
            document.querySelectorAll('li[data-metode]').forEach(li => {
                li.hidden = btn.dataset.jadwal !== 'semua' && li.dataset.metode !== btn.dataset.jadwal;
            });
        });
    });
</script>
@endsection
