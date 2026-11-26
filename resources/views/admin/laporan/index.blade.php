@extends('layouts.admin')

@section('title', 'Laporan')
@section('judul', 'Laporan')
@section('subjudul', 'Periode ' . $dari->translatedFormat('j M Y') . ($dari->isSameDay($sampai) ? '' : ' – ' . $sampai->translatedFormat('j M Y')))

@php
    $L = \App\Http\Controllers\Admin\LaporanController::class;
    $S = \App\Http\Controllers\Admin\PesananController::STATUS;
    $rp = fn ($n) => 'Rp' . number_format((int) $n, 0, ',', '.');
    $rpRingkas = fn ($n) => $n >= 1_000_000
        ? 'Rp' . rtrim(rtrim(number_format($n / 1_000_000, 1, ',', '.'), '0'), ',') . 'jt'
        : ($n >= 1000 ? 'Rp' . number_format($n / 1000, 0, ',', '.') . 'rb' : 'Rp' . $n);

    // Query periode yang sedang aktif (dibawa saat ekspor)
    $qPeriode = $periode === 'custom'
        ? ['dari' => $dari->toDateString(), 'sampai' => $sampai->toDateString()]
        : ['periode' => $periode];

    $angka = 'flex flex-col gap-2 rounded-[18px] border border-line bg-white p-5';
    $judul = 'text-[17px] font-bold';
@endphp

@section('aksi')
    <div class="flex gap-2">
        <button type="button" onclick="window.print()" class="btn btn-outline rounded-xl"><i class="bi bi-printer"></i> Cetak</button>
        <a href="{{ route('admin.laporan.ekspor', $qPeriode) }}" class="btn btn-primary rounded-xl">
            <i class="bi bi-download"></i> Ekspor Excel
        </a>
    </div>
@endsection

@section('content')
{{-- ===== GAYA CETAK (A4) ===== --}}
<style>
    @media print {
        @page { size: A4 portrait; margin: 12mm 12mm 14mm; }
        html, body { print-color-adjust: exact; -webkit-print-color-adjust: exact; }
        main { padding: 0 !important; }
        .kartu { border-radius: 12px; padding: 14px 16px !important; box-shadow: none; break-inside: avoid; }
        [data-cetak-angka] > div { padding: 12px 14px; border-radius: 12px; gap: 4px; break-inside: avoid; }
        [data-cetak-angka] strong { font-size: 20px !important; }
        [data-cetak-angka] span, [data-cetak-angka] small { font-size: 10.5px !important; }
        .tabel { min-width: 0; font-size: 10.5px; }
        .tabel th, .tabel td { padding: 6px 8px !important; }
        .tabel thead { display: table-header-group; }   /* judul kolom diulang tiap halaman */
        .tabel tr { break-inside: avoid; }
        .badge { padding: 2px 8px; font-size: 10px; }
        .tabel td:nth-child(2) { white-space: nowrap; }
    }
</style>

{{-- ===== PERIODE ===== --}}
<div class="mb-5.5 flex flex-col items-start gap-2 print:hidden">
    <button type="button" onclick="document.getElementById('modal-periode').showModal()"
            class="btn btn-outline min-h-10 rounded-xl bg-white text-[13px]">
        <i class="bi bi-calendar3 text-maroon"></i> Filter periode
    </button>
    <p class="text-xs text-muted">
        Periode: <b class="text-ink">{{ $dari->translatedFormat('j F Y') }}{{ $dari->isSameDay($sampai) ? '' : ' - ' . $sampai->translatedFormat('j F Y') }}</b>
        @if ($periode !== 'custom') <span>({{ $L::PERIODE[$periode] ?? '' }})</span> @endif
    </p>
</div>

<x-admin.modal id="modal-periode" judul="Filter Periode" class="w-[min(440px,calc(100%-32px))]">
    <div class="p-4 sm:p-6">
        <p class="mb-2.5 text-[13px] font-semibold">Pilih cepat</p>
        <div class="mb-5 flex flex-wrap gap-2">
            @foreach ($L::PERIODE as $kunci => $label)
                <a href="{{ route('admin.laporan.index', ['periode' => $kunci]) }}" @if ($periode === $kunci) aria-current="page" @endif
                   class="rounded-full border border-line px-3.5 py-2 text-[13px] font-semibold hover:border-maroon aria-[current=page]:border-maroon aria-[current=page]:bg-maroon aria-[current=page]:text-white">{{ $label }}</a>
            @endforeach
        </div>

        <form method="GET" action="{{ route('admin.laporan.index') }}">
            <p class="mb-2.5 text-[13px] font-semibold">Atau pilih tanggal</p>
            <div class="grid grid-cols-2 gap-3 text-[13px]">
                <label class="flex flex-col gap-1.5">
                    <span class="text-muted">Dari tanggal</span>
                    <input id="dari" type="date" name="dari" value="{{ $dari->toDateString() }}" max="{{ today()->toDateString() }}" required
                           onchange="document.getElementById('sampai').min = this.value"
                           class="h-10 rounded-xl border border-line px-3 focus:border-maroon focus:outline-none">
                </label>
                <label class="flex flex-col gap-1.5">
                    <span class="text-muted">Sampai tanggal</span>
                    <input id="sampai" type="date" name="sampai" value="{{ $sampai->toDateString() }}" min="{{ $dari->toDateString() }}" max="{{ today()->toDateString() }}" required
                           class="h-10 rounded-xl border border-line px-3 focus:border-maroon focus:outline-none">
                </label>
            </div>
            <div class="mt-5 flex justify-end gap-2">
                <button type="button" onclick="this.closest('dialog').close()" class="btn btn-outline rounded-xl">Batal</button>
                <button type="submit" class="btn btn-primary rounded-xl">Terapkan</button>
            </div>
        </form>
    </div>
</x-admin.modal>
@error('sampai') <div class="mb-5 rounded-xl bg-danger-soft px-4 py-3 text-sm text-danger">{{ $message }}</div> @enderror

{{-- ===== KOP SAAT DICETAK ===== --}}
<div class="mb-4 hidden items-end justify-between gap-4 border-b-2 border-maroon pb-3 print:flex">
    <div>
        <p class="font-serif text-2xl leading-none text-maroon">@bucket.imy</p>
        <h2 class="mt-1.5 text-lg font-bold">Laporan Penjualan</h2>
    </div>
    <div class="text-right text-[11px] leading-relaxed text-muted">
        <p>Periode: <b class="text-ink">{{ $dari->translatedFormat('j F Y') }}{{ $dari->isSameDay($sampai) ? '' : ' – ' . $sampai->translatedFormat('j F Y') }}</b></p>
        <p>Dicetak: {{ now()->translatedFormat('j F Y, H.i') }} · {{ auth()->user()->name }}</p>
    </div>
</div>

{{-- =============================================================
     LAPORAN PENJUALAN
     ============================================================= --}}
<div data-cetak-angka class="mb-5.5 grid gap-4 sm:grid-cols-2 xl:grid-cols-4 print:mb-4 print:grid-cols-4 print:gap-2.5">
    <div class="{{ $angka }}">
        <span class="text-[13px] text-muted">Pendapatan toko</span>
        <strong class="text-[28px] leading-none font-extrabold text-maroon">{{ $rp($ringkas['pendapatan']) }}</strong>
        @if (! is_null($ringkas['perubahan']))
            <small @class(['text-xs', 'text-ok' => $ringkas['perubahan'] >= 0, 'text-danger' => $ringkas['perubahan'] < 0])>
                <i class="bi {{ $ringkas['perubahan'] >= 0 ? 'bi-arrow-up-right' : 'bi-arrow-down-right' }}"></i>
                {{ abs($ringkas['perubahan']) }}% dibanding periode sebelumnya
            </small>
        @else
            <small class="text-xs text-muted">Belum ada data periode sebelumnya</small>
        @endif
    </div>
    <div class="{{ $angka }}">
        <span class="text-[13px] text-muted">Pesanan lunas</span>
        <strong class="text-[34px] leading-none font-extrabold">{{ $ringkas['pesanan'] }}</strong>
        <small class="text-xs text-muted">{{ $ringkas['batal'] }} pesanan dibatalkan</small>
    </div>
    <div class="{{ $angka }}">
        <span class="text-[13px] text-muted">Buket terjual</span>
        <strong class="text-[34px] leading-none font-extrabold">{{ $ringkas['buket'] }}</strong>
        <small class="text-xs text-muted">dari semua pesanan lunas</small>
    </div>
    <div class="{{ $angka }}">
        <span class="text-[13px] text-muted">Rata-rata per pesanan</span>
        <strong class="text-[28px] leading-none font-extrabold">{{ $rp($ringkas['rata']) }}</strong>
        <small class="text-xs text-muted">pendapatan ÷ pesanan lunas</small>
    </div>
</div>

{{-- ===== GRAFIK PENDAPATAN (garis) ===== --}}
@php
    $nilai = $grafik->pluck('nilai')->all();
    $n = count($nilai);
    $maks = max(1, max($nilai ?: [0]));

    // Sumbu Y: 4 jarak dengan angka bulat (1, 2, 2.5 atau 5 × 10^k)
    $kasar = $maks / 4;
    $pangkat = 10 ** floor(log10($kasar));
    $step = collect([1, 1.5, 2, 2.5, 3, 4, 5, 6, 8, 10])->map(fn ($k) => $k * $pangkat)->first(fn ($v) => $v >= $kasar);
    $atas = $step * 4;
    $garisY = range(4, 0, -1); // 4 = paling atas

    // Titik di ruang 0–1000 (x) × 0–100 (y). x di tengah tiap kolom supaya sejajar label.
    $titik = collect($nilai)->map(fn ($v, $i) => [($i + 0.5) / $n * 1000, 100 - $v / $atas * 100])->all();

    // Kurva halus monotone (tidak pernah turun di bawah 0 atau melewati puncak data)
    $kurva = function (array $p) {
        $k = count($p);
        if ($k < 2) return $k ? "M{$p[0][0]},{$p[0][1]}" : '';
        $d = []; $m = [];
        for ($i = 0; $i < $k - 1; $i++) $d[$i] = ($p[$i + 1][1] - $p[$i][1]) / ($p[$i + 1][0] - $p[$i][0]);
        $m[0] = $d[0]; $m[$k - 1] = $d[$k - 2];
        // Kemiringan tiap titik = rata-rata harmonik; 0 kalau naik-turun berganti (puncak/lembah tetap datar)
        for ($i = 1; $i < $k - 1; $i++) $m[$i] = $d[$i - 1] * $d[$i] <= 0 ? 0 : 2 / (1 / $d[$i - 1] + 1 / $d[$i]);
        $s = sprintf('M%.2f,%.2f', $p[0][0], $p[0][1]);
        for ($i = 0; $i < $k - 1; $i++) {
            $h = ($p[$i + 1][0] - $p[$i][0]) / 3;
            $s .= sprintf(' C%.2f,%.2f %.2f,%.2f %.2f,%.2f',
                $p[$i][0] + $h, $p[$i][1] + $m[$i] * $h,
                $p[$i + 1][0] - $h, $p[$i + 1][1] - $m[$i + 1] * $h,
                $p[$i + 1][0], $p[$i + 1][1]);
        }
        return $s;
    };
    $garis = $kurva($titik);
    $area = $n ? $garis . sprintf(' L%.2f,100 L%.2f,100 Z', end($titik)[0], $titik[0][0]) : '';

    $tiapLabel = max(1, (int) ceil($n / 8)); // ±8 label sumbu X
@endphp
<section class="kartu mb-5.5 break-inside-avoid print:mb-4">
    <div class="mb-5 flex flex-wrap items-baseline justify-between gap-2">
        <h2 class="{{ $judul }}">Pendapatan {{ $perBulan ? 'per Bulan' : 'Harian' }}</h2>
        <small class="text-xs text-muted">Hanya pesanan lunas<span class="print:hidden">. Arahkan kursor ke grafik untuk detail</span>.</small>
    </div>

    <div class="grid grid-cols-[auto_minmax(0,1fr)] gap-x-3">
        {{-- Sumbu Y --}}
        <div class="relative h-60 print:h-40 text-right text-[11px] text-muted tabular-nums">
            @foreach ($garisY as $j)
                <span class="absolute right-0 -translate-y-1/2 whitespace-nowrap" style="top: {{ 100 - $j * 25 }}%">{{ $rpRingkas($step * $j) }}</span>
            @endforeach
            <span class="invisible">{{ $rpRingkas($atas) }}</span>
        </div>

        {{-- Area plot --}}
        <div class="relative h-60 print:h-40">
            {{-- Garis bantu horizontal (dasar = garis padat) --}}
            @foreach ($garisY as $j)
                <div @class(['pointer-events-none absolute inset-x-0 border-t', 'border-line' => $j === 0, 'border-dashed border-line-soft' => $j > 0]) style="top: {{ 100 - $j * 25 }}%"></div>
            @endforeach

            {{-- Garis + area --}}
            <svg viewBox="0 0 1000 100" preserveAspectRatio="none" class="absolute inset-0 size-full overflow-visible" aria-hidden="true">
                <path d="{{ $area }}" fill="var(--color-maroon)" fill-opacity="0.08"/>
                <path d="{{ $garis }}" fill="none" stroke="var(--color-maroon)" stroke-width="2" stroke-linejoin="round" stroke-linecap="round" vector-effect="non-scaling-stroke"/>
            </svg>

            {{-- Kolom hover: garis vertikal + titik + tooltip --}}
            <div class="absolute inset-0 flex">
                @foreach ($grafik as $i => $g)
                    @php $y = $titik[$i][1]; @endphp
                    <div class="group relative h-full flex-1 outline-none" tabindex="0" aria-label="{{ $g['lengkap'] }}: {{ $rp($g['nilai']) }}">
                        <div class="pointer-events-none absolute inset-y-0 left-1/2 hidden border-l border-dashed border-maroon/40 group-hover:block group-focus:block"></div>
                        {{-- Lebih dari 31 titik: titik disembunyikan, muncul saat disorot --}}
                        <span @class(['pointer-events-none absolute left-1/2 size-2 -translate-1/2 rounded-full border-2 border-maroon bg-white transition-transform group-hover:scale-150 group-focus:scale-150', 'opacity-0 group-hover:opacity-100 group-focus:opacity-100' => $n > 31]) style="top: {{ $y }}%"></span>
                        <div @class([
                                'pointer-events-none absolute z-10 mb-2.5 hidden -translate-y-full rounded-lg bg-ink px-2.5 py-1.5 text-[11px] whitespace-nowrap text-white shadow-pop group-hover:block group-focus:block',
                                'left-1/2 -translate-x-1/2' => $i > 0 && $i < $n - 1,
                                'left-0' => $i === 0,
                                'right-0' => $i === $n - 1 && $n > 1,
                             ]) style="top: calc({{ $y }}% - 8px)">
                            <span class="text-white/70">{{ $g['lengkap'] }}</span><br><b class="tabular-nums">{{ $rp($g['nilai']) }}</b>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Sumbu X --}}
        <div></div>
        <div class="mt-2 flex text-[11px] text-muted">
            @foreach ($grafik as $i => $g)
                {{-- Di HP label dijarangkan 2× supaya tidak bertumpuk --}}
                <span @class(['min-w-0 flex-1 text-center whitespace-nowrap', 'max-sm:invisible' => ($i / $tiapLabel) % 2 === 1])>{{ $i % $tiapLabel === 0 ? $g['label'] : '' }}</span>
            @endforeach
        </div>
    </div>
</section>

<div class="mb-5.5 grid gap-5.5 xl:grid-cols-2 print:mb-4 print:grid-cols-2 print:gap-2.5">
    {{-- ===== PRODUK TERLARIS ===== --}}
    <section class="kartu break-inside-avoid">
        <h2 class="{{ $judul }} mb-4">Produk Terlaris</h2>
        @php $maksBuket = max(1, $terlaris->max('buket')); @endphp
        @forelse ($terlaris as $i => $t)
            <div class="mb-3.5 grid grid-cols-[22px_1fr_auto] items-center gap-x-3">
                <span class="text-sm font-bold text-muted">{{ $i + 1 }}</span>
                <div>
                    <div class="flex justify-between gap-2 text-[13px]"><strong>{{ $t->nama_produk }}</strong><span class="text-muted">{{ $t->buket }} buket</span></div>
                    <span class="mt-1 block h-2 overflow-hidden rounded-full bg-[#F7E6EA]"><span class="block h-full rounded-full bg-maroon" style="width: {{ $t->buket / $maksBuket * 100 }}%"></span></span>
                </div>
                <strong class="min-w-24 text-right text-[13px] print:min-w-0">{{ $rp($t->pendapatan) }}</strong>
            </div>
        @empty
            <p class="kosong">Belum ada penjualan di periode ini.</p>
        @endforelse
    </section>

    {{-- ===== METODE ===== --}}
    <section class="kartu break-inside-avoid">
        <h2 class="{{ $judul }} mb-4">Pembayaran &amp; Pengiriman</h2>
        @php $totalPesanan = max(1, $ringkas['pesanan']); @endphp
        <dl class="text-sm [&>div]:flex [&>div]:justify-between [&>div]:gap-3 [&>div]:border-b [&>div]:border-line [&>div]:py-2.75 [&>div:last-child]:border-0 [&_dt]:text-muted [&_dd]:text-right [&_dd]:font-semibold">
            @foreach (['qris' => 'QRIS', 'va' => 'Virtual Account BCA'] as $k => $label)
                <div><dt>{{ $label }}</dt><dd>{{ $bayar[$k]->jumlah ?? 0 }} pesanan · {{ $rp($bayar[$k]->total ?? 0) }}</dd></div>
            @endforeach
            @foreach (['kurir' => 'Diantar kurir', 'pickup' => 'Pick up di atelier'] as $k => $label)
                <div><dt>{{ $label }}</dt><dd>{{ $terima[$k] ?? 0 }} pesanan ({{ round(($terima[$k] ?? 0) / $totalPesanan * 100) }}%)</dd></div>
            @endforeach
        </dl>
    </section>
</div>

{{-- ===== DAFTAR TRANSAKSI ===== --}}
<section class="kartu mb-5.5 overflow-hidden p-0! print:mb-0 print:break-inside-auto print:overflow-visible">
    <div class="flex flex-wrap items-baseline justify-between gap-2 border-b border-line px-5 py-4.5">
        <h2 class="{{ $judul }}">Daftar Transaksi</h2>
        <small class="text-xs text-muted">Semua pesanan di periode ini, termasuk yang belum bayar & batal<span class="hidden print:inline"> · {{ $cetak->count() }} transaksi</span></small>
    </div>
    <div class="overflow-x-auto">
        <table class="tabel">
            <thead>
                <tr><th>No. Pesanan</th><th>Tanggal</th><th>Pelanggan</th><th>Produk</th><th>Pembayaran</th><th class="text-right!">Total</th><th>Status</th></tr>
            </thead>
            <tbody>
                {{-- Layar: 10 per halaman. Cetak: semua transaksi di periode. --}}
                @php $diHalaman = $transaksi->pluck('id')->flip(); @endphp
                @forelse ($cetak as $p)
                    @php
                        [$label, $kelas] = $S[$p->status_pesanan] ?? [$p->status_pesanan, 'b-abu'];
                        $d = $p->detailPesanan->first();
                        $lain = $p->detailPesanan->count() - 1;
                    @endphp
                    <tr @class(['hidden print:table-row' => ! isset($diHalaman[$p->id])])>
                        <td><strong>{{ $p->kode_pesanan }}</strong></td>
                        <td>{{ $p->created_at->translatedFormat('j M Y, H.i') }}</td>
                        <td>{{ $p->pelanggan?->name ?? $p->nama_penerima }}</td>
                        <td>{{ $d?->nama_produk ?? '-' }}{{ $lain > 0 ? " + {$lain}" : '' }}</td>
                        <td>{{ $p->metode_pembayaran === 'qris' ? 'QRIS' : 'VA BCA' }}</td>
                        <td @class(['text-right font-semibold', 'text-muted line-through' => $p->status_pesanan === 'dibatalkan'])>
                            {{ $rp($p->total_harga) }}
                           
                        </td>
                        <td><span class="badge {{ $kelas }}">{{ $label }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="kosong">Tidak ada transaksi di periode ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="print:hidden"><x-admin.kaki :data="$transaksi" nama="transaksi" /></div>
</section>
@endsection
