<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin | Bucket Imy</title>

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: Arial, sans-serif;
            background: #fff5f6;
            color: #49353a;
        }

        header {
            background: #984653;
            color: white;
            padding: 20px 6%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
        }

        header h2 { font-size: 23px; }

        .logout button {
            border: 1px solid white;
            border-radius: 5px;
            padding: 9px 15px;
            background: transparent;
            color: white;
            cursor: pointer;
        }

        main { padding: 35px 6%; }

        .welcome { margin-bottom: 25px; }

        .welcome h1 {
            font-family: Georgia, serif;
            font-size: 32px;
            margin-bottom: 8px;
        }

        .welcome p { color: #826d72; }

        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
            gap: 20px;
        }

        .card {
            background: white;
            padding: 24px;
            border-radius: 10px;
            border: 1px solid #f0d9dd;
        }

        .card h3 { margin-bottom: 10px; color: #984653; }

        .card p {
            font-size: 14px;
            color: #826d72;
            margin-bottom: 18px;
        }

        .card a {
            display: inline-block;
            color: #984653;
            font-weight: bold;
            text-decoration: none;
        }

        @media (max-width: 500px) {
            header h2 { font-size: 18px; }
            .welcome h1 { font-size: 26px; }
        }
    </style>
</head>
<body>

<header>
    <h2>Bucket Imy | Admin</h2>

    <form class="logout" method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit">Logout</button>
    </form>
</header>

<main>
    <section class="welcome">
        <h1>Dashboard Admin</h1>
        <p>Selamat datang, {{ auth()->user()->name }}. Kelola toko Bucket Imy di sini.</p>
    </section>

    <section class="cards">
        <article class="card">
            <h3>Kategori</h3>
            <p>Kelola kategori produk buket.</p>
            <a href="#">Kelola kategori →</a>
        </article>

        <article class="card">
            <h3>Produk</h3>
            <p>Tambah, edit, dan hapus produk buket.</p>
            <a href="#">Kelola produk →</a>
        </article>

        <article class="card">
            <h3>Bahan & Stok</h3>
            <p>Kelola bahan dan persediaan toko.</p>
            <a href="#">Kelola stok →</a>
        </article>

        <article class="card">
            <h3>Pesanan</h3>
            <p>Periksa dan kelola pesanan pelanggan.</p>
            <a href="#">Kelola pesanan →</a>
        </article>

        <article class="card">
            <h3>Testimoni</h3>
            <p>Lihat testimoni dari pelanggan.</p>
            <a href="#">Lihat testimoni →</a>
        </article>
    </section>
</main>

</body>
</html>