@extends('layouts.pelanggan')

@section('title', 'Katalog Produk')

@section('topbar-search')
    <form class="search" method="GET" action="{{ route('katalog.index') }}" role="search">
        @if($kategoriAktif)<input type="hidden" name="kategori" value="{{ $kategoriAktif }}">@endif
        <i class="bi bi-search"></i>
        <input type="search" name="q" value="{{ request('q') }}" placeholder="Temukan bucket favoritmu di sini...">
    </form>
@endsection

@push('styles')
<style>
    .chips { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 26px; }
    .chip {
        padding: 6px 16px; border: 1px solid var(--line); border-radius: 20px;
        background: #fff3f5; color: var(--rose); font-size: 12px; transition: all .15s;
    }
    .chip:hover { background: var(--rose-soft); }
    .chip.active { background: var(--rose); border-color: var(--rose); color: #fff; }

    .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(215px, 1fr)); gap: 22px 20px; }
    .card {
        display: flex; flex-direction: column;
        padding: 9px; border: 1px solid var(--line); border-radius: 16px; background: var(--card);
        transition: transform .18s, box-shadow .18s;
    }
    .card:hover { transform: translateY(-3px); box-shadow: 0 10px 22px rgba(140,59,80,.12); }
    .thumb {
        display: grid; place-items: center; aspect-ratio: 1 / 1; overflow: hidden;
        border-radius: 12px; background: linear-gradient(145deg, #fbd9e2, #f2c0cf); color: #fff;
    }
    .thumb img { width: 100%; height: 100%; object-fit: cover; display: block; }
    .thumb i { font-size: 42px; opacity: .85; }
    .card h3 { margin: 12px 4px 4px; font-size: 14px; font-weight: 700; }
    .card .desc { margin: 0 4px 12px; color: var(--muted); font-size: 11.5px; line-height: 1.5; flex: 1; }
    .buy { display: flex; align-items: flex-end; justify-content: space-between; gap: 8px; padding: 0 4px 4px; }
    .buy small { display: block; color: var(--muted); font-size: 10px; }
    .buy strong { color: var(--rose); font-size: 13px; }

    .custom-card {
        padding: 22px 20px; border: 2px dashed #eeb6c4; border-radius: 16px;
        background: linear-gradient(160deg, #fff3f6, #fde7ed);
        display: flex; flex-direction: column;
    }
    .custom-card .ico {
        display: grid; place-items: center; width: 40px; height: 40px;
        border-radius: 50%; background: #f9d4de; color: var(--rose); font-size: 17px;
    }
    .custom-card .tag {
        align-self: flex-start; margin: 14px 0 8px; padding: 3px 10px;
        border: 1px solid var(--line); border-radius: 20px; background: #fff; font-size: 10px; color: var(--muted);
    }
    .custom-card h3 { margin: 0 0 6px; font-size: 16px; }
    .custom-card p { margin: 0 0 18px; color: #6d565c; line-height: 1.55; flex: 1; }
    .custom-card .btn { width: 100%; }

    .empty { padding: 50px 0; text-align: center; color: var(--muted); }
</style>
@endpush

@section('content')
    <h1>Katalog Koleksi Rangkaian Bunga</h1>
    <p class="lead">Pilih rangkaian segar buatan florist kami atau pesan custom sesuai tema acaramu.</p>

    <nav class="chips" aria-label="Filter kategori">
        <a href="{{ route('katalog.index', array_filter(['q' => request('q')])) }}"
           class="chip {{ $kategoriAktif ? '' : 'active' }}">Semua Koleksi</a>
        @foreach($kategori as $k)
            <a href="{{ route('katalog.index', array_filter(['kategori' => $k->id_kategori, 'q' => request('q')])) }}"
               class="chip {{ (string) $kategoriAktif === (string) $k->id_kategori ? 'active' : '' }}">{{ $k->nama_kategori }}</a>
        @endforeach
    </nav>

    <section class="grid">
        @forelse($produk as $p)
            <article class="card">
                <div class="thumb">
                    @if($p->gambar && file_exists(public_path('images/produk/'.$p->gambar)))
                        <img src="{{ asset('images/produk/'.$p->gambar) }}" alt="{{ $p->nama_produk }}" loading="lazy">
                    @else
                        <i class="bi bi-flower1"></i>
                    @endif
                </div>
                <h3>{{ $p->nama_produk }}</h3>
                <p class="desc">{{ $p->deskripsi }}</p>
                <div class="buy">
                    <div>
                        <small>Mulai dari</small>
                        <strong>Rp {{ number_format($p->harga, 0, ',', '.') }}</strong>
                    </div>
                    <form method="POST" action="{{ route('keranjang.store') }}">
                        @csrf
                        <input type="hidden" name="id_produk" value="{{ $p->id_produk }}">
                        <button type="submit" class="btn">Pesan Sekarang</button>
                    </form>
                </div>
            </article>
        @empty
            <p class="empty" style="grid-column:1/-1">Belum ada produk yang cocok.</p>
        @endforelse

        <aside class="custom-card">
            <span class="ico"><i class="bi bi-pencil"></i></span>
            <span class="tag">Request Bebas</span>
            <h3>Desain Buket Impianmu</h3>
            <p>Punya referensi bunga, warna wrap, budget khusus? Kami siap merangkai sesuai keinginan mu!</p>
            <a href="{{ route('pesanan.custom') }}" class="btn">Isi Form Custom Buket <i class="bi bi-arrow-right"></i></a>
        </aside>
    </section>
@endsection
