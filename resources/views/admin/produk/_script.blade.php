<script>
    // Pratinjau foto baru yang dipilih (bisa banyak sekaligus)
    document.addEventListener('change', (e) => {
        if (!e.target.matches('[data-foto-baru]')) return;
        const wadah = e.target.closest('form').querySelector('[data-pratinjau-baru]');
        wadah.innerHTML = '';
        [...e.target.files].forEach((file) => {
            const img = document.createElement('img');
            img.src = URL.createObjectURL(file);
            img.className = 'aspect-square w-full rounded-lg object-cover';
            wadah.append(img);
        });
    });

    // Tombol Batal di dalam popup → tutup popup saja
    document.addEventListener('click', (e) => {
        const dialog = e.target.closest('[data-tutup]')?.closest('dialog');
        if (dialog) { e.preventDefault(); dialog.close(); }
    });
</script>
<script>
    // Ukuran: tambah / hapus kotak (juga untuk form yang dimuat lewat fetch)
    document.addEventListener('click', (e) => {
        const tambah = e.target.closest('[data-tambah-ukuran]');
        if (tambah) {
            const form = tambah.closest('form');
            const daftar = form.querySelector('[data-daftar-ukuran]');
            if (daftar.children.length >= Number(daftar.dataset.maks)) {
                alert('Maksimal ' + daftar.dataset.maks + ' ukuran per produk.');
                return;
            }
            const html = form.querySelector('[data-cetakan-ukuran]').innerHTML.replaceAll('__I__', 'b' + Date.now());
            daftar.insertAdjacentHTML('beforeend', html);
            daftar.lastElementChild.querySelector('input[type=text]').focus();
            return;
        }

        const hapus = e.target.closest('[data-hapus-ukuran]');
        if (hapus) {
            const daftar = hapus.closest('[data-daftar-ukuran]');
            if (daftar.children.length <= 1) { alert('Minimal satu ukuran.'); return; }
            hapus.closest('[data-ukuran]').remove();
        }
    });
</script>
