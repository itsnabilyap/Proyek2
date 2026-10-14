
@extends('layouts.app')

@section('title', 'Beranda')
@section('main-class', 'main-home')

@section('content')

<style>
    .home-page {
        --pink: #fff0f3;
        --pink-light: #fff8f8;
        --pink-card: #fce8ed;
        --rose: #a34a60;
        --rose-dark: #873b50;
        --text: #302126;
        --muted: #817176;
        color: var(--text);
        background: var(--pink-light);
        font-family: 'DM Sans', sans-serif;
        font-size: 13px;
        line-height: 1.7;
        overflow: hidden;
    }

    .home-page *,
    .home-page *::before,
    .home-page *::after {
        box-sizing: border-box;
    }

    .home-page h1,
    .home-page h2,
    .home-page h3,
    .home-page p {
        margin-top: 0;
    }

    .home-page a {
        text-decoration: none;
    }

    .home-container {
        width: min(1120px, 90%);
        margin: 0 auto;
    }

    .home-eyebrow {
        display: inline-block;
        color: var(--rose);
        font-size: 10px;
        letter-spacing: 1px;
        text-transform: uppercase;
        margin-bottom: 10px;
    }

    .home-section-heading {
        text-align: center;
        margin-bottom: 35px;
    }

    .home-section-heading h2,
    .home-why-title,
    .home-cta h2 {
        font-family: 'Playfair Display', Georgia, serif;
        font-size: clamp(25px, 3vw, 34px);
        line-height: 1.3;
        font-weight: 600;
        letter-spacing: -0.7px;
        margin-bottom: 10px;
    }

    .home-section-heading h2 em,
    .home-why-title em {
        color: var(--rose);
    }

    .home-section-heading p {
        max-width: 650px;
        margin: 12px auto 0;
        color: var(--muted);
        font-size: 12px;
        line-height: 1.8;
    }

    /* HERO */

    .home-hero {
        text-align: center;
        padding: 45px 20px 30px;
        background:
            radial-gradient(ellipse at 10% 35%, #ffedf0, transparent 45%),
            radial-gradient(ellipse at 90% 70%, #ffedf0, transparent 45%),
            var(--pink-light);
    }

    .home-hero .home-eyebrow {
        padding: 4px 12px;
        border: 1px solid #f5e6e9;
        border-radius: 30px;
        background: #fff8f9;
        margin-bottom: 14px;
    }

    .home-hero h1 {
        font-family: 'Playfair Display', Georgia, serif;
        font-size: clamp(34px, 5vw, 58px);
        line-height: 1.15;
        font-weight: 600;
        letter-spacing: -1.5px;
        margin-bottom: 16px;
    }

    .home-hero h1 em {
        color: var(--rose);
        font-weight: 500;
    }

    .home-hero-description {
        max-width: 600px;
        margin: 0 auto;
        color: #65585c;
        font-size: 13px;
        line-height: 1.8;
    }

    .home-hero-description strong {
        color: var(--text);
    }

    .home-hero-image {
        display: block;
        width: min(760px, 100%);
        height: 310px;
        margin: 42px auto 0;
        object-fit: cover;
        object-position: center 55%;
        border: 4px solid #fff;
        border-radius: 9px;
        box-shadow: 0 12px 30px #a34a6010;
    }

    .home-hero-features {
        max-width: 850px;
        margin: 30px auto 0;
        padding-top: 22px;
        border-top: 1px solid #f8e9ec;
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 25px;
        text-align: left;
    }

    .home-feature-mini {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .home-round-icon {
        flex-shrink: 0;
        width: 39px;
        height: 39px;
        display: grid;
        place-items: center;
        border-radius: 50%;
        background: #fbe7ec;
        color: var(--rose);
        font-size: 17px;
    }

    .home-round-icon.green {
        background: #dcebd4;
        color: #52744c;
    }

    .home-feature-mini strong {
        display: block;
        font-size: 12px;
        font-weight: 600;
    }

    .home-feature-mini p {
        color: var(--muted);
        font-size: 10px;
        margin-bottom: 0;
        line-height: 1.6;
    }

    /* PROFIL TOKO */

    .home-profile {
        padding: 55px 0;
        background: #fff0f3;
    }

    .home-info-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 25px;
    }

    .home-info-card {
        padding: 25px;
        min-height: 190px;
        background: #fff;
        border-radius: 11px;
        box-shadow: 0 5px 20px #a34a6008;
    }

    .home-info-card h3 {
        font-size: 14px;
        margin: 13px 0 7px;
    }

    .home-info-card p {
        color: var(--muted);
        font-size: 11px;
        line-height: 1.8;
        margin-bottom: 0;
    }

    /* KEUNGGULAN */

    .home-why {
        padding: 65px 0;
        background: #fff9f9;
    }

    .home-why-title {
        margin-bottom: 25px;
    }

    .home-why-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 22px;
    }

    .home-why-card {
        padding: 23px;
        min-height: 215px;
        background: #fff;
        border-radius: 11px;
        box-shadow: 0 5px 20px #a34a6008;
    }

    .home-number {
        display: block;
        color: #d17c91;
        font-family: 'Playfair Display', Georgia, serif;
        font-size: 15px;
        margin-bottom: 12px;
    }

    .home-why-card h3 {
        font-size: 12px;
        margin: 12px 0 7px;
    }

    .home-why-card p {
        color: var(--muted);
        font-size: 10px;
        line-height: 1.8;
        margin-bottom: 0;
    }

    /* CARA PEMESANAN */

    .home-ordering {
        padding: 55px 0 45px;
        background: #fff0f3;
    }

    .home-steps-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 18px;
    }

    .home-step-card {
        padding: 20px;
        min-height: 190px;
        background: #fff;
        border-radius: 10px;
    }

    .home-step-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 13px;
    }

    .home-step-number {
        display: grid;
        place-items: center;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: var(--rose);
        color: white;
        font-weight: 700;
        font-size: 11px;
    }

    .home-step-symbol {
        color: var(--rose);
        font-size: 15px;
    }

    .home-step-card h3 {
        font-size: 12px;
        margin-bottom: 8px;
    }

    .home-step-card p {
        color: var(--muted);
        font-size: 10px;
        line-height: 1.8;
        margin-bottom: 0;
    }

    .home-same-day {
        width: fit-content;
        max-width: 100%;
        margin: 24px auto 0;
        padding: 8px 18px;
        text-align: center;
        color: #75646a;
        background: #fff9f9;
        border: 1px solid #f5e6e9;
        border-radius: 30px;
        font-size: 10px;
    }

    /* CTA WHATSAPP */

    .home-contact-section {
        padding: 25px 0 75px;
        background: #fff8f9;
    }

    .home-cta {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 30px;
        padding: 38px;
        border-radius: 15px;
        background: #fbe5eb;
    }

    .home-cta-copy {
        max-width: 600px;
    }

    .home-cta .home-eyebrow {
        margin-bottom: 8px;
    }

    .home-cta h2 {
        font-size: clamp(23px, 3vw, 30px);
        margin-bottom: 8px;
    }

    .home-cta-copy p {
        color: #76646a;
        font-size: 11px;
        line-height: 1.8;
        margin-bottom: 0;
    }

    .home-cta-action {
        flex-shrink: 0;
        text-align: center;
    }

    .home-whatsapp-btn {
        display: inline-block;
        padding: 12px 25px;
        border-radius: 30px;
        background: var(--rose);
        color: #fff !important;
        font-size: 11px;
        font-weight: 600;
        box-shadow: 0 5px 15px #a34a6030;
        transition: background .2s, transform .2s;
    }

    .home-whatsapp-btn:hover {
        background: var(--rose-dark);
        transform: translateY(-2px);
    }

    .home-cta-action small {
        display: block;
        margin-top: 10px;
        color: var(--muted);
        font-size: 10px;
    }

    /* RESPONSIVE */

    @media (max-width: 800px) {
        .home-hero {
            padding-top: 40px;
        }

        .home-hero-image {
            height: 260px;
        }

        .home-info-grid {
            grid-template-columns: 1fr;
            gap: 16px;
        }

        .home-why-grid,
        .home-steps-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .home-cta {
            align-items: flex-start;
            flex-direction: column;
            padding: 30px;
        }

        .home-cta-action {
            text-align: left;
        }
    }

    @media (max-width: 500px) {
        .home-container {
            width: 90%;
        }

        .home-hero {
            padding: 35px 15px 25px;
        }

        .home-hero h1 {
            letter-spacing: -1px;
        }

        .home-hero-description {
            font-size: 12px;
        }

        .home-hero-image {
            height: 210px;
            margin-top: 28px;
        }

        .home-hero-features {
            grid-template-columns: 1fr;
            gap: 15px;
        }

        .home-profile,
        .home-why,
        .home-ordering {
            padding: 42px 0;
        }

        .home-why-grid,
        .home-steps-grid {
            grid-template-columns: 1fr;
        }

        .home-info-card,
        .home-why-card,
        .home-step-card {
            min-height: auto;
            padding: 22px;
        }

        .home-why-title {
            font-size: 26px;
        }

        .home-cta {
            padding: 26px 22px;
        }

        .home-same-day {
            border-radius: 12px;
            line-height: 1.8;
        }
    }
</style>

<div class="home-page">

    {{-- HERO --}}
    <section class="home-hero">
        <div class="home-container">

            <span class="home-eyebrow">✦ Buket.imy ✦</span>

            <h1>
                Merangkai Makna,<br>
                <em>Bukan Sekadar Bunga.</em>
            </h1>

            <p class="home-hero-description">
                <strong>@bucket.imy</strong> adalah studio bunga independen
                yang menghadirkan rangkaian buket segar pilihan dan gubahan
                <em>custom</em> dengan sentuhan personal untuk merayakan
                setiap momen berharga Anda.
            </p>

            <img
                class="home-hero-image"
                src="https://images.unsplash.com/photo-1490750967868-88aa4486c946?auto=format&fit=crop&w=1500&q=85"
                alt="Rangkaian bunga dan buket cantik"
            >

            <div class="home-hero-features">

                <div class="home-feature-mini">
                    <span class="home-round-icon">❀</span>
                    <div>
                        <strong>100% Handmade</strong>
                        <p>Dibuat secara detail dan penuh ketelitian.</p>
                    </div>
                </div>

                <div class="home-feature-mini">
                    <span class="home-round-icon">♧</span>
                    <div>
                        <strong>Handcrafted Teliti</strong>
                        <p>Dibuat dengan teliti.</p>
                    </div>
                </div>

                <div class="home-feature-mini">
                    <span class="home-round-icon">♧</span>
                    <div>
                        <strong>Antar Tepat Waktu</strong>
                        <p>Kurir berusaha agar buket aman terjaga.</p>
                    </div>
                </div>

            </div>
        </div>
    </section>


    {{-- PROFIL TOKO --}}
    <section class="home-profile" id="tentang-kami">
        <div class="home-container">

            <div class="home-section-heading">
                <span class="home-eyebrow">Profil Store</span>

                <h2>
                    Mengenal Lebih Dekat <em>@bucket.imy</em>
                </h2>

                <p>
                    Berawal dari kecintaan tulus pada seni merangkai flora,
                    kami mendedikasikan setiap tangkai dan kelopak untuk
                    mewakili perasaan Anda. Bunga kami dipilih dan dirawat
                    segar setiap hari, dirangkai langsung oleh florist kami
                    tanpa perantara pihak ketiga.
                </p>
            </div>

            <div class="home-info-grid">

                <article class="home-info-card">
                    <span class="home-round-icon">⌖</span>

                    <h3>Lokasi &amp; Area Layanan</h3>

                    <p>
                        Toko kami berlokasi di Jl. Tridaya Barat,
                        Karanganyar, Indramayu. Melayani pengiriman buket
                        ke seluruh wilayah Indramayu dan sekitarnya dengan
                        proses yang aman.
                    </p>
                </article>

                <article class="home-info-card">
                    <span class="home-round-icon green">◷</span>

                    <h3>Jam Operasional</h3>

                    <p>
                        Kami siap merespons kebutuhan hadiah Anda.
                        <br>
                        <strong>Senin–Sabtu: 08.00–16.00 WIB.</strong>
                        <br>
                        Pesanan mendesak dapat dikoordinasikan langsung
                        melalui chat.
                    </p>
                </article>

                <article class="home-info-card">
                    <span class="home-round-icon">▧</span>

                    <h3>Jaminan Mutu &amp; Transparansi</h3>

                    <p>
                        Hasil pengerjaan dipastikan rapi, awet, dan sesuai
                        pesanan. Foto buket akan dikirimkan kepada Anda
                        sebelum dikirim atau diambil.
                    </p>
                </article>

            </div>
        </div>
    </section>


    {{-- KEUNGGULAN --}}
    <section class="home-why">
        <div class="home-container">

            <h2 class="home-why-title">
                Mengapa Memilih <em>@bucket.imy?</em>
            </h2>

            <div class="home-why-grid">

                <article class="home-why-card">
                    <span class="home-number">01</span>
                    <span class="home-round-icon">❀</span>

                    <h3>Dibuat Rapi &amp; Detail</h3>

                    <p>
                        Setiap buket dibuat satu per satu dengan penuh
                        ketelitian. Bentuknya rapi, cantik, dan dirangkai
                        dengan perhatian pada setiap detail.
                    </p>
                </article>

                <article class="home-why-card">
                    <span class="home-number">02</span>
                    <span class="home-round-icon">⚙</span>

                    <h3>Bebas Request &amp; Custom</h3>

                    <p>
                        Kamu bebas memilih warna wrapping, tema buket,
                        sampai mendiskusikan budget. Kami bantu merangkai
                        konsep yang paling sesuai.
                    </p>
                </article>

                <article class="home-why-card">
                    <span class="home-number">03</span>
                    <span class="home-round-icon">♡</span>

                    <h3>Free Kartu Ucapan</h3>

                    <p>
                        Pesanan sudah termasuk kartu ucapan gratis.
                        Kamu bisa menambahkan pesan personal agar hadiah
                        semakin berkesan.
                    </p>
                </article>

                <article class="home-why-card">
                    <span class="home-number">04</span>
                    <span class="home-round-icon">✳</span>

                    <h3>Aman Sampai Tujuan</h3>

                    <p>
                        Buket dapat dikirim melalui kurir atau diambil
                        langsung di toko. Kami mengemasnya dengan rapi
                        agar tetap cantik saat diterima.
                    </p>
                </article>

            </div>
        </div>
    </section>


    {{-- CARA PEMESANAN --}}
    <section class="home-ordering" id="cara-pesan">
        <div class="home-container">

            <div class="home-section-heading">
                <span class="home-eyebrow">Proses Praktis</span>

                <h2>Cara Pemesanan yang Mudah</h2>

                <p>
                    Pesan buket impian tanpa ribet hanya dalam
                    4 tahapan santai.
                </p>
            </div>

            <div class="home-steps-grid">

                <article class="home-step-card">
                    <div class="home-step-top">
                        <span class="home-step-number">1</span>
                        <span class="home-step-symbol">▤</span>
                    </div>

                    <h3>Pilih / Request Konsep</h3>

                    <p>
                        Informasikan momen spesial seperti wisuda,
                        ulang tahun, atau anniversary, beserta foto
                        referensi buket yang diinginkan.
                    </p>
                </article>

                <article class="home-step-card">
                    <div class="home-step-top">
                        <span class="home-step-number">2</span>
                        <span class="home-step-symbol">▣</span>
                    </div>

                    <h3>Konfirmasi &amp; Pembayaran</h3>

                    <p>
                        Kami merincikan estimasi biaya secara transparan.
                        Setelah disetujui, pembayaran dilakukan sesuai
                        kesepakatan.
                    </p>
                </article>

                <article class="home-step-card">
                    <div class="home-step-top">
                        <span class="home-step-number">3</span>
                        <span class="home-step-symbol">▧</span>
                    </div>

                    <h3>Rangkai &amp; Kirim Foto</h3>

                    <p>
                        Bunga dirangkai dengan penuh ketelitian. Foto
                        hasil rangkaian dikirimkan terlebih dahulu untuk
                        diperiksa sebelum pengiriman.
                    </p>
                </article>

                <article class="home-step-card">
                    <div class="home-step-top">
                        <span class="home-step-number">4</span>
                        <span class="home-step-symbol">✉</span>
                    </div>

                    <h3>Diantar ke Penerima</h3>

                    <p>
                        Buket diantar sesuai kesepakatan kepada penerima,
                        lengkap dengan kartu ucapan dari Anda.
                    </p>
                </article>

            </div>

            <div class="home-same-day">
                ◷ &nbsp; Butuh buket hari ini (Same-day)?
                Konsultasikan ketersediaan bunga via WhatsApp.
            </div>

        </div>
    </section>


    {{-- CTA WHATSAPP --}}
    <section class="home-contact-section">
        <div class="home-container">

            <div class="home-cta">

                <div class="home-cta-copy">
                    <span class="home-eyebrow">
                        Diskusi Bebas Biaya
                    </span>

                    <h2>Punya Rencana Hadiah Khusus?</h2>

                    <p>
                        Hubungi kami melalui WhatsApp untuk diskusi,
                        menanyakan ketersediaan stok bunga, atau bertukar
                        ide mengenai konsep buket yang Anda inginkan.
                    </p>
                </div>

                <div class="home-cta-action">
                    <a
                        class="home-whatsapp-btn"
                        href="https://wa.me/62895325820465"
                        target="_blank"
                        rel="noopener noreferrer"
                    >
                        ▣ &nbsp; Chat Sekarang
                    </a>

                    <small>Respon dalam &lt; 15 Menit</small>
                </div>

            </div>
        </div>
    </section>

</div>

@endsection
