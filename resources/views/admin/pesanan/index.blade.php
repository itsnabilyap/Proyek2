
@extends('admin.layout')

@section('title', 'Status Pesanan')

@section('content')

<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <h1 class="page-title mb-0">Status Pesanan</h1>
</div>

<div class="content-card">

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
        <div>
            <h4 class="mb-1">Status Pesanan</h4>
            <small class="text-muted">
                Kelola semua data pesanan Bucket Imyu.
            </small>
        </div>

        <form action="{{ route('admin.pesanan.index') }}" method="GET">
            <div class="input-group">
                <input
                    type="text"
                    name="search"
                    class="form-control search-input"
                    placeholder="Cari ID / pemesan..."
                    value="{{ request('search') }}"
                >
                <button class="btn btn-maroon" type="submit">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </form>
    </div>

    <hr>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="d-flex flex-wrap gap-2 mb-3">
        <a href="{{ route('admin.pesanan.index') }}"
           class="filter-link {{ !request('status') ? 'active' : '' }}">
            Semua
        </a>

        @foreach($daftarStatus as $status)
            <a href="{{ route('admin.pesanan.index', ['status' => $status, 'search' => request('search')]) }}"
               class="filter-link {{ request('status') === $status ? 'active' : '' }}">
                {{ $status }}
            </a>
        @endforeach
    </div>

    <div class="table-wrap">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>ID Pesanan</th>
                    <th>Nama Pemesan</th>
                    <th>Rangkaian Bucket</th>
                    <th>Tanggal Pesanan</th>
                    <th>Total Harga</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse($pesanan as $item)
                    <tr>
                        <td>
                            <strong>#{{ $item->id_pesanan }}</strong>
                        </td>

                        <td>
                            <strong>
                                {{ $item->pelanggan->name ?? 'Pelanggan tidak ditemukan' }}
                            </strong>
                            <div class="small text-muted">
                                {{ $item->pelanggan->no_hp ?? '-' }}
                            </div>
                        </td>

                        <td>
                            @forelse($item->detailPesanan as $detail)
                                <div>{{ $detail->nama_produk }}</div>
                                <small class="text-muted">
                                    Jumlah: {{ $detail->jumlah }}
                                </small>
                            @empty
                                <span class="text-muted">Pesanan custom / belum ada detail</span>
                            @endforelse
                        </td>

                        <td>
                            {{ $item->tanggal_pesanan?->format('d-m-Y H:i') }}
                        </td>

                        <td>
                            Rp {{ number_format((float) $item->total_harga, 0, ',', '.') }}
                        </td>

                        <td>
                            <span class="status-badge">
                                {{ $item->status_pesanan }}
                            </span>
                        </td>

                        <td>
                            <button
                                type="button"
                                class="btn btn-update"
                                data-bs-toggle="modal"
                                data-bs-target="#updateModal{{ $item->id_pesanan }}"
                            >
                                Update
                            </button>

                            <div class="modal fade"
                                 id="updateModal{{ $item->id_pesanan }}"
                                 tabindex="-1">

                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <form
                                            action="{{ route('admin.pesanan.update', $item->id_pesanan) }}"
                                            method="POST"
                                        >
                                            @csrf
                                            @method('PATCH')

                                            <div class="modal-header">
                                                <h5 class="modal-title">
                                                    Update Status Pesanan #{{ $item->id_pesanan }}
                                                </h5>
                                                <button type="button"
                                                        class="btn-close"
                                                        data-bs-dismiss="modal"></button>
                                            </div>

                                            <div class="modal-body">
                                                <label class="form-label">
                                                    Pilih Status Pesanan
                                                </label>

                                                <select name="status_pesanan"
                                                        class="form-select"
                                                        required>
                                                    @foreach($daftarStatus as $status)
                                                        <option
                                                            value="{{ $status }}"
                                                            {{ $item->status_pesanan === $status ? 'selected' : '' }}
                                                        >
                                                            {{ $status }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="modal-footer">
                                                <button type="button"
                                                        class="btn btn-light"
                                                        data-bs-dismiss="modal">
                                                    Batal
                                                </button>

                                                <button type="submit" class="btn btn-maroon">
                                                    Simpan Perubahan
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            Data pesanan tidak ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $pesanan->links('pagination::bootstrap-5') }}
    </div>

</div>

@endsection