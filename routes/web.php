<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DetailBarangController;
use App\Http\Controllers\RekapController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\BarangRusakController;

/**
 * Route Home - Redirect ke dashboard atau login
 */
Route::get('/', function () {
    if (session('user_id') || session('id_aktor')) {
        return redirect()->route('dashboard.index');
    }
    return redirect()->route('login');
});

/**
 * ============================================
 * AUTHENTICATION ROUTES
 * ============================================
 */
// Route login - hanya bisa diakses jika belum login
Route::middleware('guest')->group(function () {
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
});

// Logout - memerlukan login
Route::middleware('auth')->match(['get', 'post'], 'logout', [LoginController::class, 'logout'])->name('logout');

/**
 * ============================================
 * DASHBOARD ROUTES - Role Based Access
 * ============================================
 * Struktur route: /{role}/dashboard
 */
Route::middleware('auth')->group(function () {
    // Dashboard untuk Admin - Akses Penuh
    Route::middleware('role:Admin,admin')->group(function () {
        Route::get('/admin/dashboard', [DashboardController::class, 'admin'])->name('dashboard.admin');
    });

    // Dashboard untuk Penjaga Gudang - Akses Penuh
    Route::middleware('role:Penjaga Gudang,penjaga gudang,pejaga gudang,pejaga_gudang')->group(function () {
        Route::get('/gudang/dashboard', [DashboardController::class, 'gudang'])->name('dashboard.gudang');
    });

    // Dashboard untuk Direktur - Read Only
    Route::middleware('role:Direktur,direktur')->group(function () {
        Route::get('/direktur/dashboard', [DashboardController::class, 'direktur'])->name('dashboard.direktur');
    });

    // Dashboard untuk Umum - Validasi Order
    Route::middleware('role:Umum,umum')->group(function () {
        Route::get('/umum/dashboard', [DashboardController::class, 'umum'])->name('dashboard.umum');
    });

    // Dashboard untuk Perencanaan - Order Barang
    Route::middleware('role:Perencanaan,perencanaan')->group(function () {
        Route::get('/perencanaan/dashboard', [DashboardController::class, 'perencanaan'])->name('dashboard.perencanaan');
    });

    // Dashboard untuk Keuangan - Validasi Final
    Route::middleware('role:Keuangan,keuangan')->group(function () {
        Route::get('/keuangan/dashboard', [DashboardController::class, 'keuangan'])->name('dashboard.keuangan');
    });

    // Dashboard default (fallback) - redirect ke dashboard sesuai role
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
});

/**
 * ============================================
 * BARANG ROUTES - Role Based Access
 * ============================================
 */
Route::middleware('auth')->group(function () {
    // Semua role bisa melihat daftar barang
Route::get('/barang', [DetailBarangController::class, 'index'])->name('barang.index');
Route::get('/barang/{kode_barang}', [DetailBarangController::class, 'show'])->name('barang.detail');

    // Hanya Admin dan Penjaga Gudang yang bisa menambah detail barang
    Route::middleware('role:Admin,admin,Penjaga Gudang,penjaga gudang,pejaga gudang')->group(function () {
Route::get('/barang/{kode_barang}/detail/create', [DetailBarangController::class, 'create'])->name('barang.detail.create');
Route::post('/barang/{kode_barang}/detail', [DetailBarangController::class, 'store'])->name('barang.detail.store');
    });

    // Routes untuk barang rusak (CRUD) - Hanya Admin dan Penjaga Gudang
    Route::middleware('role:Admin,admin,Penjaga Gudang,penjaga gudang,pejaga gudang')->group(function () {
    Route::resource('barang-rusak', BarangRusakController::class);
    });
});

/**
 * ============================================
 * REKAP ROUTES - Role Based Access
 * ============================================
 */
Route::middleware('auth')->group(function () {
    // Semua role bisa melihat rekap (read-only)
    Route::get('/rekap', [RekapController::class, 'index'])->name('rekap.index');
    Route::get('/rekap/sr/{sr}', [RekapController::class, 'showSr'])->name('rekap.show-sr');
    Route::get('/rekap/gm/{gm}', [RekapController::class, 'showGm'])->name('rekap.show-gm');
});

/**
 * ============================================
 * ORDER ROUTES - Role Based Access
 * ============================================
 */
Route::middleware('auth')->group(function () {
    // Semua role bisa melihat status order
    Route::get('/order/status', [OrderController::class, 'status'])->name('order.status');
    Route::get('/orders/{id_order}', [OrderController::class, 'show'])->name('orders.show');

    // Hanya Perencanaan, Penjaga Gudang, dan Admin yang bisa membuat order
    Route::middleware('role:Perencanaan,perencanaan,Penjaga Gudang,penjaga gudang,pejaga gudang,Admin,admin')->group(function () {
Route::get('/order', [OrderController::class, 'index'])->name('order.index');
Route::post('/order', [OrderController::class, 'store'])->name('order.store');
        Route::post('/order/cancel', [OrderController::class, 'cancel'])->name('order.cancel');
        // Konfirmasi order dengan alamat dan no BPP untuk Perencanaan dan Gudang
    Route::get('/order/confirm', [OrderController::class, 'confirm'])->name('order.confirm');
    Route::get('/order/confirm/form', [OrderController::class, 'confirmForm'])->name('order.confirm-form');
    Route::post('/order/confirm', [OrderController::class, 'confirmStore'])->name('order.confirm-store');
        // Invoice untuk Perencanaan dan Penjaga Gudang
        Route::get('/order/{id_order}/invoice', [OrderController::class, 'invoice'])->name('order.invoice');
    });

    // Hanya Umum yang bisa memvalidasi order dari Gudang (approve/reject)
    Route::middleware('role:Umum,umum')->group(function () {
        Route::post('/order/{id_order}/validate-umum', [OrderController::class, 'validateByUmum'])->name('order.validate-umum');
    });

    // Hanya Gudang yang bisa memvalidasi order dari Perencanaan (approve/reject)
    Route::middleware('role:Penjaga Gudang,penjaga gudang,pejaga gudang,Admin,admin')->group(function () {
        Route::post('/order/{id_order}/validate-gudang', [OrderController::class, 'validateByGudang'])->name('order.validate-gudang');
    });

    // Hanya Keuangan yang bisa memvalidasi final order dari Gudang
    Route::middleware('role:Keuangan,keuangan')->group(function () {
        Route::post('/order/{id_order}/validate-keuangan', [OrderController::class, 'validateByKeuangan'])->name('order.validate-keuangan');
    });
});
