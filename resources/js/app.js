/* ==========================================================================
   app.js · @bucket.imy
   Script kecil yang dipakai di banyak halaman. Setiap bagian hanya jalan
   kalau elemennya ada, jadi aman dimuat di semua layout.
   ========================================================================== */

/* -- 1. Menu navbar ikut bagian beranda yang sedang terlihat ---------------- */
(() => {
    const links = [...document.querySelectorAll('[data-nav] a')];
    const linkBagian = links.filter(a => a.hash && a.pathname === location.pathname);
    if (!linkBagian.length) return; // bukan di beranda → status aktif dari Laravel

    const linkBeranda = links.find(a => !a.hash && a.pathname === location.pathname)
    ?? links.find(a => a.getAttribute('aria-current') === 'page');
    const bagian = linkBagian.map(a => document.querySelector(a.hash));
    const setAktif = (link) => links.forEach(a => {
        if (a === link) a.setAttribute('aria-current', 'page');
        else a.removeAttribute('aria-current');
    });

    let kunci = false; // true selama scroll otomatis setelah menu diklik
    let timer;

    const perbarui = () => {
        if (kunci) return;
        const batas = window.scrollY + 120; // kira-kira tinggi navbar
        const mentok = window.innerHeight + window.scrollY >= document.body.scrollHeight - 2;
        let aktif = linkBeranda;
        bagian.forEach((el, i) => {
            if (el && el.getBoundingClientRect().top + window.scrollY <= batas) aktif = linkBagian[i];
        });
        if (mentok) aktif = linkBagian[linkBagian.length - 1];
        setAktif(aktif);
    };

    [linkBeranda, ...linkBagian].forEach(link => link?.addEventListener('click', () => {
        setAktif(link);
        kunci = true;
    }));

    window.addEventListener('scroll', () => {
        if (kunci) {
            clearTimeout(timer);
            timer = setTimeout(() => { kunci = false; }, 150);
            return;
        }
        perbarui();
    }, { passive: true });

    perbarui();
})();

/* -- 2. Ikon mata di kolom password ---------------------------------------- */
document.querySelectorAll('[data-toggle-password]').forEach(button => {
    button.addEventListener('click', () => {
        const input = document.getElementById(button.dataset.togglePassword);
        if (!input) return;
        const tampil = input.type === 'password';
        input.type = tampil ? 'text' : 'password';
        button.querySelector('i').className = tampil ? 'bi bi-eye-slash' : 'bi bi-eye';
        button.setAttribute('aria-label', tampil ? 'Sembunyikan password' : 'Tampilkan password');
    });
});

/* -- 3. Dropdown akun di navbar -------------------------------------------- */
(() => {
    const tombol = document.getElementById('accountButton');
    const menu = document.getElementById('accountDropdown');
    if (!tombol || !menu) return;

    const tutup = () => {
        menu.hidden = true;
        tombol.setAttribute('aria-expanded', 'false');
    };

    tombol.addEventListener('click', e => {
        e.stopPropagation();
        const buka = menu.hidden;
        menu.hidden = !buka;
        tombol.setAttribute('aria-expanded', String(buka));
    });
    menu.addEventListener('click', e => e.stopPropagation());
    document.addEventListener('click', tutup);
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape' && !menu.hidden) { tutup(); tombol.focus(); }
    });
})();

// Toast: tutup sendiri saat garis waktunya habis, atau lewat tombol ×
document.querySelectorAll('[data-toast]').forEach(toast => {
    const tutup = () => {
        toast.setAttribute('data-keluar', '');
        setTimeout(() => toast.remove(), 200);
    };
    toast.querySelector('[data-waktu-toast]')?.addEventListener('animationend', tutup);
    toast.querySelector('[data-tutup-toast]')?.addEventListener('click', tutup);
});

// Popup konfirmasi (pengganti confirm() bawaan browser) untuk form dengan data-konfirmasi
(() => {
    const dialog = document.getElementById('dialogKonfirmasi');
    if (!dialog) return;

    const JENIS = {
        hapus:   { ikon: 'bi-trash3',     warnaIkon: 'bg-danger-soft text-danger ring-danger-soft/50', tombol: 'bg-danger hover:bg-[#931F18]' },
        tanya:   { ikon: 'bi-arrow-repeat', warnaIkon: 'bg-blush text-maroon ring-blush/50',          tombol: 'bg-maroon hover:bg-maroon-dark' },
        selesai: { ikon: 'bi-bag-check',  warnaIkon: 'bg-ok-soft text-ok ring-ok-soft/50',            tombol: 'bg-ok hover:bg-[#1F4E32]' },
    };
    const el = k => dialog.querySelector('[data-k-' + k + ']');
    let formAktif = null;

    document.addEventListener('submit', e => {
        const form = e.target;
        if (!form.matches('[data-konfirmasi]') || form.dataset.dikonfirmasi) return;
        e.preventDefault();
        formAktif = form;

        const j = JENIS[form.dataset.jenis] || JENIS.hapus;
        el('ikon').className = 'mx-auto mb-4.5 flex size-16 items-center justify-center rounded-full text-[26px] ring-8 ' + j.warnaIkon;
        el('ikon').innerHTML = '<i class="bi ' + j.ikon + '"></i>';
        el('judul').textContent = form.dataset.konfirmasi;
        el('nama').textContent = form.dataset.nama || '';
        el('nama').hidden = !form.dataset.nama;
        el('pesan').textContent = form.dataset.pesan || '';
        el('ya').className = 'btn min-h-11 rounded-full text-white ' + j.tombol;
        el('ya').textContent = form.dataset.tombol || 'Ya, lanjutkan';

        dialog.showModal();
        el('batal').focus(); // tombol aman yang difokuskan dulu
    }, true);

    el('ya').addEventListener('click', () => {
        dialog.close();
        if (!formAktif) return;
        formAktif.dataset.dikonfirmasi = '1';
        formAktif.requestSubmit();
    });
    el('batal').addEventListener('click', () => dialog.close());
    dialog.addEventListener('click', e => { if (e.target === dialog) dialog.close(); });   // klik di luar kotak
})();