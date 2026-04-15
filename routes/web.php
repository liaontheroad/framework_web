<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\BukuController;
use App\Http\Controllers\VisitorController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\StudyCaseController;
use App\Http\Controllers\WilayahController;
use App\Http\Controllers\KasirController;
use App\Http\Controllers\KantinController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;


// --- RUTE CUSTOMER (GUEST) ---
Route::get('/', [KantinController::class, 'index'])->name('cashier.index');
Route::get('/get-menu-vendor/{vendor_id}', [KantinController::class, 'getMenu']);
Route::post('/simpan-pesanan', [KantinController::class, 'simpanPesanan'])->name('cashier.store');
Route::post('/konfirmasi-bayar/{id}', [KantinController::class, 'konfirmasiBayar']);

Auth::routes();

Route::middleware(['auth'])->group(function () {
    // --- RUTE KHUSUS VENDOR ---
    Route::middleware([\App\Http\Middleware\CheckRole::class.':pemilik_vendor'])->group(function () {
        Route::get('/vendor/menu', [KantinController::class, 'manageMenu'])->name('vendor.menu');
        Route::post('/vendor/menu/store', [KantinController::class, 'storeMenu']);
        Route::get('/vendor/pesanan', [KantinController::class, 'pesananMasuk'])->name('vendor.pesanan');
    });
});
// 1. SHARED ROUTES (Dashboard)
Route::middleware(['auth'])->group(function () {
    // Rute untuk Verifikasi OTP
    Route::get('/verify', [App\Http\Controllers\VerificationController::class, 'index'])->name('verify');
    Route::post('/verify', [App\Http\Controllers\VerificationController::class, 'verify']);
    Route::get('/send-otp', [App\Http\Controllers\VerificationController::class, 'sendOtp']);
});

Route::get('auth/google', [LoginController::class, 'redirectToGoogle'])->name('google.login');
Route::get('auth/google/callback', [LoginController::class, 'handleGoogleCallback']);

Route::get ('/register', fn () => view('auth.register'))->name('register');
Route::post('register', [AuthController::class, 'register']);

// 2. ADMIN ROUTES
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('kategori', KategoriController::class);
    Route::resource('buku', BukuController::class);
}); 


// 3. VISITOR ROUTES & DASHBOARD (Dijaga ketat oleh CheckStatus)
Route::middleware(['auth', 'check_status'])->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    
    Route::get('/visitor/kategori', [VisitorController::class, 'kategori'])->name('visitor.kategori');
    Route::get('/visitor/buku', [VisitorController::class, 'buku'])->name('visitor.buku');
});

// Rute untuk Download PDF
Route::get('/cetak-landscape', [App\Http\Controllers\PdfController::class, 'landscape']);
Route::get('/cetak-potrait', [App\Http\Controllers\PdfController::class, 'potrait']);

// Rute untuk CRUD Barang
Route::get('/barang', [BarangController::class, 'index'])->name('barang.index');
Route::get('/barang/create', [BarangController::class, 'create'])->name('barang.create');
Route::post('/barang', [BarangController::class, 'store'])->name('barang.store');

Route::get('/barang/{id}/edit', [BarangController::class, 'edit'])->name('barang.edit');
Route::put('/barang/{id}', [BarangController::class, 'update'])->name('barang.update');

Route::delete('/barang/{id}', [BarangController::class, 'destroy'])->name('barang.destroy');
Route::post('/barang/cetak', [BarangController::class, 'cetak'])->name('barang.cetak');

Route::middleware(['auth'])->group(function () {
    Route::get('/modul-4/tabel-biasa', [StudyCaseController::class, 'tabelBiasa'])->name('modul4.biasa');
    Route::get('/modul-4/tabel-datatables', [StudyCaseController::class, 'tabelDataTables'])->name('modul4.datatables');
    Route::get('/modul-4/select2-kota', [StudyCaseController::class, 'select2Kota'])->name('modul4.select2');
});

Route::middleware(['auth', \App\Http\Middleware\CheckRole::class.':admin'])->group(function () {

    Route::get('/wilayah', [WilayahController::class, 'wilayah'])->name('wilayah.index');
    Route::post('/get-kota', [WilayahController::class, 'getKota'])->name('admin.wilayah.getKota');
    Route::post('/get-kecamatan', [WilayahController::class, 'getKecamatan'])->name('admin.wilayah.getKecamatan');
    Route::post('/get-kelurahan', [WilayahController::class, 'getKelurahan'])->name('admin.wilayah.getKelurahan');

    Route::get('/wilayah-axios', [WilayahController::class, 'index'])->name('wilayah.axios');
    Route::post('/axios-get-kota', [WilayahController::class, 'axiosGetKota'])->name('api.getKota');
    Route::post('/axios-get-kecamatan', [WilayahController::class, 'axiosGetKecamatan'])->name('api.getKecamatan');
    Route::post('/axios-get-kelurahan', [WilayahController::class, 'axiosGetKelurahan'])->name('api.getKelurahan');
    
    // Halaman Kasir
    Route::get('/modul-ajax/kasir', [KasirController::class, 'index'])->name('kasir.index');
    Route::get('/kasir-axios', [KasirController::class, 'kasirAxios'])->name('kasir.axios');
    Route::get('/get-barang/{kode}', [KasirController::class, 'getBarang'])->name('kasir.getBarang');
    Route::post('/simpan-transaksi', [KasirController::class, 'store'])->name('kasir.store');

});

Route::middleware(['auth', \App\Http\Middleware\CheckRole::class.':admin'])->group(function () {
    Route::get('/customer', [CustomerController::class, 'index'])->name('customer.index');
    Route::get('/customer/tambah1', [CustomerController::class, 'tambah1'])->name('customer.tambah1');
    Route::post('/customer/store1', [CustomerController::class, 'store1'])->name('customer.store1');
    Route::get('/customer/tambah2', [CustomerController::class, 'tambah2'])->name('customer.tambah2');
    Route::post('/customer/store2', [CustomerController::class, 'store2'])->name('customer.store2');
});