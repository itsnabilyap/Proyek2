@extends('layouts.pelanggan')

@section('title', 'Form Custom Bucket')

@section('topbar-search')
    <form class="search" method="GET" action="{{ route('katalog.index') }}" role="search">
        <i class="bi bi-search"></i>
        <input type="search" name="q" placeholder="Temukan bucket favoritmu di sini...">
    </form>
@endsection

@push('styles')
<style>
    .custom-grid { display: grid; grid-template-columns: minmax(300px, 440px) 1fr; gap: 24px; align-items: start; }
    .panel { background: #fff; border-radius: 16px; box-shadow: 0 2px 12px rgba(140,59,80,.06); }
    .form-panel { padding: 22px 24px; }
    .field { margin-bottom: 16px; }
    .field > label, .field > .lbl { display: block; margin-bottom: 6px; color: var(--rose); font-size: 11px; font-weight: 700; }
    .field input[type=text], .field input[type=tel], .field input[type=date], .field select, .field textarea {
        width: 100%; padding: 0 12px; height: 38px;
        border: 1px solid #d5d8de; border-radius: 6px; background: #fff;
        font: inherit; font-size: 12px; color: var(--text);
    }
    .field select, .field textarea { background: #e4e7ec; border-color: #e4e7ec; }
    .field textarea { height: 96px; padding: 10px 12px; resize: vertical; }
    .field input:focus, .field select:focus, .field textarea:focus { outline: 2px solid #e3a9b8; outline-offset: 0; }
    .field .err { margin-top: 4px; color: #b42347; font-size: 11px; }

    .budget { display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; }
    .budget input { position: absolute; opacity: 0; pointer-events: none; }
    .budget span {
        display: block; padding: 10px 4px; border-radius: 6px; background: #e4e7ec;
        text-align: center; font-size: 11px; cursor: pointer; transition: all .15s;
    }
    .budget span:hover { background: #d9dde4; }
    .budget input:checked + span { background: var(--rose); color: #fff; }
    .budget input:focus-visible + span { outline: 2px solid #e3a9b8; outline-offset: 2px; }
    .budget label { position: relative; }

    .submit {
        display: flex; align-items: center; justify-content: center; gap: 8px; width: 100%;
        height: 42px; margin-top: 6px; border: 0; border-radius: 6px;
        background: var(--rose); color: #fff; font-size: 12px; font-weight: 600; cursor: pointer;
    }
    .submit:hover { background: var(--rose-dark); }

    .preview { display: flex; flex-direction: column; justify-content: center; min-height: 420px; padding: 28px; }
    .preview-empty { text-align: center; }
    .preview-empty .cam {
        display: grid; place-items: center; width: 62px; height: 62px; margin: 0 auto 14px;
        border-radius: 50%; background: #fdeaee; color: var(--rose); font-size: 26px;
    }
    .preview-empty h3 { margin: 0 0 8px; font-size: 14px; }
    .preview-empty p { max-width: 330px; margin: 0 auto; color: var(--muted); font-size: 11px; line-height: 1.6; }
    .preview-data h3 { margin: 0 0 14px; font-size: 14px; }
    .preview-data dl { margin: 0; display: grid; grid-template-columns: 120px 1fr; gap: 10px 12px; font-size: 12px; }
    .preview-data dt { color: var(--muted); }
    .preview-data dd { margin: 0; font-weight: 600; overflow-wrap: anywhere; }

    .banner {
        display: flex; align-items: center; gap: 14px; margin-top: 24px; padding: 14px 18px;
        border-radius: 14px; background: #fde6ec;
    }
    .banner .ico {
        display: grid; place-items: center; width: 34px; height: 34px; flex-shrink: 0;
        border-radius: 50%; background: #fff; color: var(--rose);
    }
    .banner strong { display: block; font-size: 12px; }
    .banner small { color: #6d565c; font-size: 11px; }
    .banner .text { flex: 1; }
    .banner button {
        padding: 9px 20px; border: 1px solid var(--line); border-radius: 20px;
        background: #fff; color: var(--text); font-size: 11.5px; font-weight: 600; cursor: pointer;
    }
    .banner button:hover { background: #fff5f7; }

    @media (max-width: 900px) { .custom-grid { grid-template-columns: 1fr; } .preview { min-height: 260px; } }
    @media (max-width: 560px) { .banner { flex-wrap: wrap; } }
</style>
@endpush

@section('content')
    <h1>Form Custom Bucket</h1>
    <p class="lead">Wujudkan bucket impianmu sesuai selera.</p>

    <div class="custom-grid">
        <form class="panel form-panel" id="customForm" method="POST" action="{{ route('pesanan.custom.store') }}" novalidate>
            @csrf

            <div class="field">
                <label for="nama_pemesan">Nama Pemesan</label>
                <input id="nama_pemesan" type="text" name="nama_pemesan" required
                    value="{{ old('nama_pemesan', auth()->user()->name) }}" placeholder="Masukkan nama lengkap pemesan">
                @error('nama_pemesan')<div class="err">{{ $message }}</div>@enderror
            </div>

            <div class="field">
                <label for="no_whatsapp">Nomor WhatsApp</label>
                <input id="no_whatsapp" type="tel" name="no_whatsapp" required
                    value="{{ old('no_whatsapp', auth()->user()->no_hp) }}" placeholder="cth: 081234567890">
                @error('no_whatsapp')<div class="err">{{ $message }}</div>@enderror
            </div>

            <div class="field">
                <label for="jenis_bucket">Pilih Jenis Bucket</label>
                <select id="jenis_bucket" name="jenis_bucket" required>
                    <option value="">Pilih jenis buket bunga / hadiah</option>
                    @foreach($jenisBucket as $j)
                        <option value="{{ $j }}" @selected(old('jenis_bucket') === $j)>{{ $j }}</option>
                    @endforeach
                </select>
                @error('jenis_bucket')<div class="err">{{ $message }}</div>@enderror
            </div>

            <div class="field">
                <span class="lbl">Pilih Budget (Rp)</span>
                <div class="budget">
                    @foreach($budget as $b)
                        <label>
                            <input type="radio" name="budget" value="{{ $b }}" @checked(old('budget') === $b)>
                            <span>{{ $b }}</span>
                        </label>
                    @endforeach
                </div>
                @error('budget')<div class="err">{{ $message }}</div>@enderror
            </div>

            <div class="field">
                <label for="tanggal_dibutuhkan">Tanggal Dibutuhkan</label>
                <input id="tanggal_dibutuhkan" type="date" name="tanggal_dibutuhkan" required
                    min="{{ now()->toDateString() }}" value="{{ old('tanggal_dibutuhkan') }}">
                @error('tanggal_dibutuhkan')<div class="err">{{ $message }}</div>@enderror
            </div>

            <div class="field">
                <label for="catatan_custom">Catatan / Deskripsi Bucket</label>
                <textarea id="catatan_custom" name="catatan_custom"
                    placeholder="Tuliskan tema warna, jenis bunga favorit, ucapan khusus, atau referensi lainnya...">{{ old('catatan_custom') }}</textarea>
                @error('catatan_custom')<div class="err">{{ $message }}</div>@enderror
            </div>

            <button type="submit" class="submit">Kirim Permintaan Custom <i class="bi bi-arrow-right"></i></button>
        </form>

        <aside class="panel preview" aria-live="polite">
            <div class="preview-empty" id="previewEmpty">
                <div class="cam"><i class="bi bi-camera"></i></div>
                <h3>Pratinjau Desain Rangkaian</h3>
                <p>Detail buket kustom dan estimasi komponen buket Anda akan tampil di panel ini setelah Anda mengisi preferensi formulir di samping.</p>
            </div>
            <div class="preview-data" id="previewData" hidden>
                <h3>Ringkasan Pesanan Custom</h3>
                <dl id="previewList"></dl>
            </div>
        </aside>
    </div>

    <div class="banner">
        <span class="ico"><i class="bi bi-pencil"></i></span>
        <div class="text">
            <strong>Ingin request bunga atau tema warna khusus?</strong>
            <small>Anda dapat langsung mengisi preferensi pada menu Form Custom di samping!</small>
        </div>
        <button type="button" id="focusForm">Buat Custom Buket</button>
    </div>
@endsection

@push('scripts')
<script>
    (function () {
        const form = document.getElementById('customForm');
        const empty = document.getElementById('previewEmpty');
        const data = document.getElementById('previewData');
        const list = document.getElementById('previewList');

        const rows = [
            ['Nama Pemesan', () => form.nama_pemesan.value.trim()],
            ['WhatsApp', () => form.no_whatsapp.value.trim()],
            ['Jenis Bucket', () => form.jenis_bucket.value],
            ['Budget', () => (form.querySelector('input[name=budget]:checked') || {}).value || ''],
            ['Dibutuhkan', () => {
                const v = form.tanggal_dibutuhkan.value;
                if (!v) return '';
                return new Date(v + 'T00:00:00').toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
            }],
            ['Catatan', () => form.catatan_custom.value.trim()],
        ];

        function render() {
            list.textContent = '';
            // nama & WA sudah terisi otomatis dari akun, jadi pratinjau baru tampil setelah preferensi buket diisi
            const prefs = [form.jenis_bucket.value, form.querySelector('input[name=budget]:checked'),
                           form.tanggal_dibutuhkan.value, form.catatan_custom.value.trim()];
            const anyPref = prefs.some(Boolean);
            empty.hidden = anyPref;
            data.hidden = !anyPref;
            if (!anyPref) return;

            rows.forEach(([label, get]) => {
                const val = get();
                if (!val) return;
                const dt = document.createElement('dt'); dt.textContent = label;
                const dd = document.createElement('dd'); dd.textContent = val;
                list.append(dt, dd);
            });
        }

        form.addEventListener('input', render);
        form.addEventListener('change', render);
        render();

        document.getElementById('focusForm').addEventListener('click', () => {
            form.scrollIntoView({ behavior: 'smooth', block: 'start' });
            form.jenis_bucket.focus({ preventScroll: true });
        });
    })();
</script>
@endpush
