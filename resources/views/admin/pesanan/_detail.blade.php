@php
    $C = \App\Http\Controllers\Admin\PesananController::class;
    [$label, $kelas] = $C::STATUS[$pesanan->status_pesanan] ?? [$pesanan->status_pesanan, 'b-abu'];
    $rp = fn ($n) => 'Rp' . number_format($n, 0, ',', '.');
    $pickup = $pesanan->metode_pengiriman === 'pickup';

    $wa = preg_replace('/\D/', '', $pesanan->no_whatsapp);
    if (str_starts_with($wa, '0')) $wa = '62' . substr($wa, 1);

    // Daftar "label ... nilai" (dl)
    $daftar = 'mb-4 text-sm [&>div]:flex [&>div]:justify-between [&>div]:gap-4 [&>div]:py-1.5 [&_dt]:text-muted [&_dd]:text-right [&_dd]:font-semibold';
    $kepala = 'mb-4 flex flex-wrap items-center justify-between gap-2.5';
@endphp

{{-- Di popup & halaman penuh: 2 kolom mulai tablet (≥768px), 1 kolom di HP --}}
<div class="grid items-start gap-5.5 md:grid-cols-[1fr_300px] xl:not-in-[dialog]:grid-cols-[1fr_340px]">
    <div>
        {{-- Produk --}}
        <section class="kartu mb-5.5">
            <h2 class="mb-4 text-[17px] font-bold">Produk Dipesan</h2>
            @foreach ($pesanan->detailPesanan as $d)
                <div class="flex justify-between gap-4 border-b border-line py-3.5">
                    <div>
                        <strong>{{ $d->nama_produk }}</strong>
                        <small class="mt-0.5 block text-xs text-muted">Ukuran {{ $d->ukuran_lengkap }} · {{ $d->jumlah }} × {{ $rp($d->harga) }}</small>
                        @if ($d->kartu_ucapan)
                            <p class="mt-2 rounded-[10px] bg-cream px-3 py-2 text-[13px]"><i class="bi bi-envelope-heart"></i> “{{ $d->kartu_ucapan }}”</p>
                        @endif
                    </div>
                    <strong class="whitespace-nowrap">{{ $rp($d->harga * $d->jumlah) }}</strong>
                </div>
            @endforeach

            <dl class="{{ $daftar }} mt-3.5 mb-0">
                <div><dt>Subtotal</dt><dd>{{ $rp($pesanan->subtotal) }}</dd></div>
                <div><dt>Ongkir</dt><dd>{{ $pesanan->ongkir ? $rp($pesanan->ongkir) : 'Gratis' }}</dd></div>
                <div class="mt-1.5 border-t border-line pt-3! text-base"><dt>Total</dt><dd class="text-maroon">{{ $rp($pesanan->total_harga) }}</dd></div>
            </dl>
        </section>

        {{-- Pengiriman --}}
        <section class="kartu mb-5.5">
            <h2 class="mb-4 text-[17px] font-bold">{{ $pickup ? 'Pengambilan' : 'Pengiriman' }}</h2>
            <dl class="{{ $daftar }} mb-0">
                <div><dt>Cara terima</dt><dd>{{ $pickup ? 'Pick up di atelier' : 'Kurir ' . ($pesanan->layanan_kurir === 'mobil' ? '(mobil)' : '(motor)') }}</dd></div>
                <div><dt>Jadwal</dt><dd>{{ $pesanan->tanggal->translatedFormat('l, j F Y') }} · {{ substr($pesanan->slot_waktu, 0, 5) }}</dd></div>
                <div><dt>Penerima</dt><dd>{{ $pesanan->nama_penerima }}</dd></div>
                <div><dt>WhatsApp</dt><dd>{{ $pesanan->no_whatsapp }}</dd></div>
                @unless ($pickup)
                    <div><dt>Alamat</dt><dd>{{ $pesanan->alamat }}</dd></div>
                    @if ($pesanan->patokan) <div><dt>Patokan</dt><dd>{{ $pesanan->patokan }}</dd></div> @endif
                @endunless
                @if ($pesanan->catatan_florist) <div><dt>Catatan florist</dt><dd>{{ $pesanan->catatan_florist }}</dd></div> @endif
            </dl>
        </section>
    </div>

    {{-- Samping: status --}}
    <aside>
        <section class="kartu mb-5.5">
            <div class="{{ $kepala }}"><h2 class="text-[17px] font-bold">Status</h2><span class="badge {{ $kelas }}">{{ $label }}</span></div>

            @if ($berikutnya)
                @can('kelola')
                    <form method="POST" action="{{ route('admin.pesanan.status', $pesanan) }}">
                        @csrf @method('PATCH')
                        <input type="hidden" name="dari" value="{{ $pesanan->status_pesanan }}">
                        <button class="btn btn-primary w-full rounded-xl">{{ $C::TOMBOL[$berikutnya] }}</button>
                    </form>
                @else
                    <p class="text-xs text-muted">Hanya admin yang bisa mengubah status pesanan.</p>
                @endcan
            @elseif ($pesanan->status_pesanan === 'menunggu_pembayaran')
                <p class="text-xs text-muted">Status berubah otomatis setelah pelanggan membayar.</p>
            @else
                <p class="text-xs text-muted">Pesanan sudah {{ strtolower($label) }}.</p>
            @endif
        </section>

        <section class="kartu mb-5.5">
            <h2 class="mb-4 text-[17px] font-bold">Pelanggan</h2>
            <dl class="{{ $daftar }}">
                <div><dt>Nama</dt><dd>{{ $pesanan->pelanggan?->name ?? '-' }}</dd></div>
                <div><dt>Dipesan</dt><dd>{{ $pesanan->created_at->translatedFormat('j M Y, H.i') }}</dd></div>
                <div><dt>Pembayaran</dt><dd>{{ $pesanan->metode_pembayaran === 'qris' ? 'QRIS' : 'Virtual Account BCA' }}</dd></div>
            </dl>
            <a href="https://wa.me/{{ $wa }}?text={{ urlencode("Halo {$pesanan->nama_penerima}, kami dari @bucket.imy terkait pesanan {$pesanan->kode_pesanan}.") }}"
               target="_blank" rel="noopener noreferrer" class="btn btn-soft w-full rounded-xl"><i class="bi bi-whatsapp"></i> Hubungi via WhatsApp</a>
        </section>
    </aside>
</div>
