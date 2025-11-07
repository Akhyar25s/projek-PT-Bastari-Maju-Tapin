<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DetailBarangController;
use App\Http\Controllers\RekapController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\BarangRusakController;

Route::get('/', function () {
    // Redirect ke dashboard atau login
    if (session('user_id') || session('id_aktor')) {
        return redirect()->route('dashboard.index');
    }
    return redirect()->route('login');
});

//Route login - hanya bisa diakses jika belum login
Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);
});

// Logout - memerlukan login
Route::middleware('auth')->match(['get', 'post'], 'logout', [LoginController::class, 'logout'])->name('logout');

// Route untuk barang dan detail - memerlukan login
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::get('/barang', [DetailBarangController::class, 'index'])->name('barang.index');
    Route::get('/barang/{kode_barang}', [DetailBarangController::class, 'show'])->name('barang.detail');
    Route::get('/barang/{kode_barang}/detail/create', [DetailBarangController::class, 'create'])->name('barang.detail.create');
    Route::post('/barang/{kode_barang}/detail', [DetailBarangController::class, 'store'])->name('barang.detail.store');

    // Routes untuk barang rusak (CRUD)
    Route::resource('barang-rusak', BarangRusakController::class);

    // Routes untuk rekap SR/GM (digabung dalam satu halaman)
    Route::get('/rekap', [RekapController::class, 'index'])->name('rekap.index');
    Route::get('/rekap/sr/{sr}', [RekapController::class, 'showSr'])->name('rekap.show-sr');
    Route::get('/rekap/gm/{gm}', [RekapController::class, 'showGm'])->name('rekap.show-gm');

    // Routes untuk order
    Route::get('/order', [OrderController::class, 'index'])->name('order.index');
    Route::post('/order', [OrderController::class, 'store'])->name('order.store');
    Route::get('/order/confirm', [OrderController::class, 'confirm'])->name('order.confirm');
    Route::get('/order/confirm/form', [OrderController::class, 'confirmForm'])->name('order.confirm-form');
    Route::post('/order/confirm', [OrderController::class, 'confirmStore'])->name('order.confirm-store');
    Route::post('/order/cancel', [OrderController::class, 'cancel'])->name('order.cancel');
    Route::get('/order/status', [OrderController::class, 'status'])->name('order.status');
    Route::get('/orders/{id_order}', [OrderController::class, 'show'])->name('orders.show');
});
