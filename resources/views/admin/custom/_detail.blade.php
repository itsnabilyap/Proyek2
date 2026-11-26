@php
    [$label, $kelas] = \App\Models\Custom::STATUS[$custom->status] ?? [$custom->status, 'b-abu'];
    $daftar = 'mb-4 text-sm [&>div]:flex [&>div]:justify-between [&>div]:gap-5 [&>div]:border-b [&>div]:border-line [&>div]:py-2.5 [&>div:last-child]:border-0 [&_dt]:shrink-0 [&_dt]:text-muted [&_dd]:text-right [&_dd]:font-semibold';
@endphp

<div>
    <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
        <div>
            <strong class="text-lg">{{ $custom->nama_pemesan }}</strong>
            <small class="block text-xs text-muted">Dikirim {{ $custom->created_at->translatedFormat('j M Y, H.i') }}</small>
        </div>
        <span class="badge {{ $kelas }}">{{ $label }}</span>
    </div>

    <dl class="{{ $daftar }}">
        <div><dt>Jenis</dt><dd>{{ $custom->jenis_bucket }}</dd></div>
        <div><dt>Budget</dt><dd>{{ $custom->budget_label }}</dd></div>
        <div><dt>Dibutuhkan</dt><dd>{{ $custom->tanggal_dibutuhkan->translatedFormat('l, j F Y') }}</dd></div>
        <div><dt>WhatsApp</dt><dd>{{ $custom->no_whatsapp }}</dd></div>
        @if ($custom->tema) <div><dt>Warna &amp; tema</dt><dd>{{ $custom->tema }}</dd></div> @endif
        @if ($custom->harga_final) <div><dt>Harga final</dt><dd class="text-maroon">Rp{{ number_format($custom->harga_final, 0, ',', '.') }}</dd></div> @endif
    </dl>

    @if ($custom->catatan)
        <p class="mb-4 rounded-xl bg-cream px-3.5 py-3 text-[13px] leading-relaxed">“{{ $custom->catatan }}”</p>
    @endif

    @if ($custom->foto_referensi_url)
        <a href="{{ $custom->foto_referensi_url }}" target="_blank" rel="noopener" class="mb-4 block overflow-hidden rounded-xl border border-line">
            <img src="{{ $custom->foto_referensi_url }}" alt="Foto referensi" class="max-h-64 w-full object-cover">
        </a>
    @endif

    {{-- Pembayaran: setelah disetujui, pelanggan membayar lewat web (jadi pesanan & tercatat di laporan) --}}
    @if ($custom->pesanan && $custom->pesanan->status_pesanan !== 'dibatalkan')
        @php [$lp, $kp] = \App\Http\Controllers\Admin\PesananController::STATUS[$custom->pesanan->status_pesanan] ?? [$custom->pesanan->status_pesanan, 'b-abu']; @endphp
        <div class="mb-4 flex items-center justify-between gap-3 rounded-xl bg-ok-soft px-3.5 py-3 text-[13px]">
            <span>Sudah jadi pesanan <a href="{{ route('admin.pesanan.show', $custom->pesanan) }}" class="font-bold text-ok underline">{{ $custom->pesanan->kode_pesanan }}</a></span>
            <span class="badge {{ $kp }}">{{ $lp }}</span>
        </div>
        <a href="{{ $custom->link_wa }}" target="_blank" rel="noopener noreferrer" class="btn btn-soft mb-4 w-full rounded-xl"><i class="bi bi-whatsapp"></i> Chat via WhatsApp</a>
    @elseif ($custom->bisaDibayar())
        @php
            $linkBayar = route('checkout.index', ['custom' => $custom->id]);
            $no = preg_replace('/\D/', '', $custom->no_whatsapp);
            if (str_starts_with($no, '0')) $no = '62' . substr($no, 1);
            $waBayar = "https://wa.me/{$no}?text=" . urlencode("Halo {$custom->nama_pemesan}, request custom #{$custom->id} ({$custom->jenis_bucket}) sudah kami setujui dengan harga Rp" . number_format($custom->harga_final, 0, ',', '.') . ".\n\nSilakan lanjutkan pembayaran di sini:\n{$linkBayar}\n\nTerima kasih, @bucket.imy");
        @endphp
        <div class="mb-4 rounded-xl border border-line p-3.5 text-[13px]">
            <p class="mb-2 font-semibold">Link pembayaran untuk pelanggan</p>
            <input type="text" readonly value="{{ $linkBayar }}" onclick="this.select()" class="input mb-2.5 text-xs">
            <p class="mb-3 text-xs text-muted">Pelanggan juga melihat tombol "Bayar Sekarang" di halaman Pesanan Saya.</p>
            <a href="{{ $waBayar }}" target="_blank" rel="noopener noreferrer" class="btn btn-primary w-full rounded-xl"><i class="bi bi-whatsapp"></i> Kirim Link Bayar via WhatsApp</a>
        </div>
    @else
        <a href="{{ $custom->link_wa }}" target="_blank" rel="noopener noreferrer" class="btn btn-soft mb-4 w-full rounded-xl"><i class="bi bi-whatsapp"></i> Chat via WhatsApp</a>
    @endif

    @if ($custom->pesanan && $custom->pesanan->status_pesanan !== 'dibatalkan')
        <p class="text-xs text-muted">Request ini sudah menjadi pesanan. Ubah statusnya dari Pesanan Katalog.</p>
    @elsecan('kelola')
        <form method="POST" action="{{ route('admin.pesanan.custom.update', $custom) }}" class="rounded-[14px] border border-line p-4">
            @csrf @method('PATCH')
            <label class="isian">
                <span>Ubah status</span>
                <select name="status" class="input">
                    @foreach (\App\Models\Custom::STATUS as $k => [$l]) <option value="{{ $k }}" @selected($custom->status === $k)>{{ $l }}</option> @endforeach
                </select>
            </label>
            <label class="isian" data-harga-final @if ($custom->status !== 'disetujui') hidden @endif>
                <span>Harga final (Rp)</span>
                <input type="number" name="harga_final" min="1000" step="500" value="{{ $custom->harga_final }}" class="input" placeholder="Contoh: 350000">
            </label>
            <button class="btn btn-primary w-full rounded-xl">Simpan</button>
        </form>
    @else
        <p class="text-xs text-muted">Hanya admin yang bisa mengubah status request.</p>
    @endcan
</div>
