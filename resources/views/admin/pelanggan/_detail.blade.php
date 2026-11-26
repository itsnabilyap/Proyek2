@php
    $S = \App\Http\Controllers\Admin\PesananController::STATUS;
    $rp = fn ($n) => 'Rp' . number_format((int) $n, 0, ',', '.');
    $wa = $pelanggan->no_hp ?: $pelanggan->pesanan->first()?->no_whatsapp;
    $angkaWa = preg_replace('/\D/', '', (string) $wa);
    if (str_starts_with($angkaWa, '0')) $angkaWa = '62' . substr($angkaWa, 1);
@endphp

<div>
    <header class="mb-4.5 flex items-start justify-between gap-4">
        <div>
            <h2 class="text-[21px] font-bold">{{ $pelanggan->name }}</h2>
            <span class="text-[13px] font-bold text-maroon">{{ $pelanggan->kode }}</span>
        </div>
        {{-- Tombol × hanya tampil di popup --}}
        <button type="button" aria-label="Tutup" onclick="this.closest('dialog')?.close()"
                class="hidden size-10 shrink-0 items-center justify-center rounded-xl border border-line bg-white text-[15px] hover:border-maroon hover:text-maroon in-[dialog]:flex">
            <i class="bi bi-x-lg"></i>
        </button>
    </header>

    <dl class="mb-5.5 text-sm [&>div]:flex [&>div]:justify-between [&>div]:gap-6 [&>div]:border-b [&>div]:border-line [&>div]:py-2.75 [&>div:last-child]:border-0 [&_dt]:shrink-0 [&_dt]:text-muted [&_dd]:text-right [&_dd]:font-semibold">
        <div><dt>Email</dt><dd>{{ $pelanggan->email }}</dd></div>
        <div><dt>WhatsApp</dt><dd>{{ $wa ?: '-' }}</dd></div>
        <div><dt>Alamat terakhir</dt><dd>{{ $alamat ?: '-' }}</dd></div>
        <div>
            <dt>Total belanja</dt>
            <dd class="font-bold! text-maroon">{{ $rp($pelanggan->total_belanja) }} · {{ $pelanggan->jumlah_pesanan }} pesanan</dd>
        </div>
    </dl>

    <h3 class="mb-2.5 text-sm font-bold">Pesanan terakhir</h3>
    <div class="mb-4.5 flex flex-col gap-2">
        @forelse ($riwayat as $p)
            @php
                [$label, $kelas] = $S[$p->status_pesanan] ?? [$p->status_pesanan, 'b-abu'];
                $d = $p->detailPesanan->first();
                $lain = $p->detailPesanan->count() - 1;
            @endphp
            <div class="flex items-center justify-between gap-3 rounded-xl bg-cream px-3.5 py-3">
                <div>
                    <strong class="block text-[13px]">{{ $p->kode_pesanan }} · {{ $d?->nama_produk ?? '-' }}{{ $lain > 0 ? " + {$lain}" : '' }}</strong>
                    <small class="text-xs text-muted">{{ $p->created_at->translatedFormat('j M') }}</small>
                </div>
                <span class="badge {{ $kelas }} shrink-0 px-2.25 py-0.75 text-[11px]">{{ $label }}</span>
            </div>
        @empty
            <p class="kosong">
                {{ $pelanggan->jumlah_custom ? 'Belum ada pesanan katalog, tapi pernah mengirim request custom.' : 'Belum pernah memesan.' }}
            </p>
        @endforelse
    </div>

    {{-- 2 tombol berdampingan; kalau cuma 1, penuh --}}
    <div class="grid gap-2.5 sm:grid-cols-2 [&>.btn]:min-h-12 [&>.btn:only-child]:col-span-full">
        @if ($wa)
            <a href="https://wa.me/{{ $angkaWa }}?text={{ urlencode("Halo {$pelanggan->name}, kami dari @bucket.imy.") }}"
               target="_blank" rel="noopener noreferrer" class="btn btn-primary rounded-xl">Chat via WhatsApp</a>
        @endif
        <a href="{{ route('admin.pesanan.index', ['q' => $pelanggan->name]) }}" class="btn btn-outline rounded-xl">Lihat semua pesanan</a>
    </div>
</div>
