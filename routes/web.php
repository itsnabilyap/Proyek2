<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\KatalogController;
use App\Http\Controllers\CustomController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\KeranjangController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\MidtransWebhookController;
use App\Http\Controllers\AkunController;
use App\Http\Controllers\PesananController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PelangganController;
use App\Http\Controllers\Admin\PesananController as AdminPesananController;
use App\Http\Controllers\Admin\ProdukController;
use App\Http\Controllers\Admin\PengaturanController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\CustomController as AdminCustomController;

Route::redirect('/', '/beranda');
Route::view('/beranda', 'beranda')->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:5,1')
        ->name('login.process');

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');

    Route::get('/admin/login', [AuthController::class, 'showAdminLogin'])->name('admin.login');
    Route::post('/admin/login', [AuthController::class, 'adminLogin'])
        ->middleware('throttle:5,1')
        ->name('admin.login.process');
});

// Request custom: form boleh dibuka tanpa login. Kirim request wajib login (dicek di CustomController::store).
Route::get('/custom', [CustomController::class, 'create'])->name('custom.create');
Route::post('/custom', [CustomController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('custom.store');

Route::middleware('auth')->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])
        ->middleware('throttle:10,1')
        ->name('checkout.store');
    Route::get('/keranjang', [KeranjangController::class, 'index'])->name('keranjang.index');
    Route::post('/keranjang', [KeranjangController::class, 'store'])->name('keranjang.store');
    Route::patch('/keranjang/{keranjang}', [KeranjangController::class, 'update'])->name('keranjang.update');
    Route::delete('/keranjang/{keranjang}', [KeranjangController::class, 'destroy'])->name('keranjang.destroy');
    Route::get('/pembayaran/{pesanan:kode_pesanan}', [PembayaranController::class, 'show'])->name('pembayaran.show');
    Route::post('/pembayaran/{pesanan:kode_pesanan}/cek', [PembayaranController::class, 'cekStatus'])
    ->middleware('throttle:10,1')->name('pembayaran.cek');
    Route::post('/pembayaran/{pesanan:kode_pesanan}/metode', [PembayaranController::class, 'ubahMetode'])
    ->middleware('throttle:5,1')->name('pembayaran.metode');
    Route::get('/pembayaran/{pesanan:kode_pesanan}/berhasil', [PembayaranController::class, 'berhasil'])->name('pembayaran.berhasil');
    Route::get('/akun', [AkunController::class, 'edit'])->name('akun.edit');
    Route::patch('/akun/profil', [AkunController::class, 'updateProfil'])->name('akun.profil');
    Route::put('/akun/password', [AkunController::class, 'updatePassword'])->middleware('throttle:5,1')->name('akun.password');
    Route::delete('/akun', [AkunController::class, 'destroy'])->middleware('throttle:3,1')->name('akun.destroy');
    Route::get('/custom/riwayat', [CustomController::class, 'riwayat'])->name('custom.riwayat');
    Route::get('/pesanan', [PesananController::class, 'index'])->name('pesanan.index');
    Route::patch('/pesanan/{pesanan:kode_pesanan}/diterima', [PesananController::class, 'terima'])->name('pesanan.terima');
    Route::post('/pesanan/{pesanan:kode_pesanan}/ulasan', [PesananController::class, 'ulasan'])->name('pesanan.ulasan');
});

Route::middleware(['auth', 'role:admin,owner'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // ===== LIHAT: admin & owner =====
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('/pesanan', [AdminPesananController::class, 'index'])->name('pesanan.index');
        Route::get('/pesanan/ekspor', [AdminPesananController::class, 'ekspor'])->name('pesanan.ekspor');
        Route::get('/pesanan/{pesanan:kode_pesanan}', [AdminPesananController::class, 'show'])->name('pesanan.show');

        Route::get('/produk', [ProdukController::class, 'index'])->name('produk.index');
        Route::get('/pelanggan', [PelangganController::class, 'index'])->name('pelanggan.index');
        Route::get('/pelanggan/{pelanggan}', [PelangganController::class, 'show'])->name('pelanggan.show');

        Route::get('/pengaturan', [PengaturanController::class, 'index'])->name('pengaturan.index');
        Route::put('/pengaturan/akun', [PengaturanController::class, 'updateAkun'])->name('pengaturan.akun');
        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/ekspor', [LaporanController::class, 'ekspor'])->name('laporan.ekspor'); // akun sendiri boleh
        Route::get('/custom', [AdminCustomController::class, 'index'])->name('pesanan.custom.index');
        Route::get('/custom/{custom}', [AdminCustomController::class, 'show'])->name('pesanan.custom.show');

        // ===== UBAH: admin saja =====
        Route::middleware('role:admin')->group(function () {
            Route::patch('/pesanan/{pesanan:kode_pesanan}/status', [AdminPesananController::class, 'updateStatus'])->name('pesanan.status');

            Route::get('/produk/tambah', [ProdukController::class, 'create'])->name('produk.create');
            Route::post('/produk', [ProdukController::class, 'store'])->name('produk.store');
            Route::get('/produk/{produk}/edit', [ProdukController::class, 'edit'])->name('produk.edit');
            Route::put('/produk/{produk}', [ProdukController::class, 'update'])->name('produk.update');
            Route::patch('/produk/{produk}/tampil', [ProdukController::class, 'toggle'])->name('produk.toggle');
            Route::patch('/produk/{produk}/stok', [ProdukController::class, 'tambahStok'])->name('produk.stok');
            Route::delete('/produk/{produk}', [ProdukController::class, 'destroy'])->name('produk.destroy');

            Route::patch('/custom/{custom}', [AdminCustomController::class, 'update'])->name('pesanan.custom.update');
            Route::post('/custom/{custom}/balas', [AdminCustomController::class, 'balas'])->name('pesanan.custom.balas');
        });
        
            Route::middleware('role:owner')->group(function () {
            Route::put('/pengaturan/profil', [PengaturanController::class, 'updateProfil'])->name('pengaturan.profil');
            Route::post('/pengaturan/pengguna', [PengaturanController::class, 'storePengguna'])->name('pengaturan.pengguna.store');
            Route::put('/pengaturan/pengguna/{user}', [PengaturanController::class, 'updatePengguna'])->name('pengaturan.pengguna.update');
            Route::delete('/pengaturan/pengguna/{user}', [PengaturanController::class, 'destroyPengguna'])->name('pengaturan.pengguna.destroy');
        });
    });

Route::get('/lupa-password', [PasswordController::class, 'showForgot'])->name('password.request');
Route::post('/lupa-password', [PasswordController::class, 'sendLink'])
    ->middleware('throttle:3,1')
    ->name('password.email');
Route::get('/reset-password/{token}', [PasswordController::class, 'showReset'])->name('password.reset');
Route::post('/reset-password', [PasswordController::class, 'reset'])->name('password.update');

Route::get('/katalog', [KatalogController::class, 'index'])->name('katalog.index');
Route::get('/katalog/{produk:slug}', [KatalogController::class, 'show'])->name('katalog.show');

Route::post('/midtrans/notification', MidtransWebhookController::class)->name('midtrans.notification');

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');
