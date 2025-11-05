<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DetailBarangController;
use App\Http\Controllers\RekapSrController;
use App\Http\Controllers\RekapGmController;
use App\Http\Controllers\OrderController;

Route::get('/', function () {
    return view('welcome');
});

// Route untuk barang dan detail
Route::get('/barang', [DetailBarangController::class, 'index'])->name('barang.index');
Route::get('/barang/{kode_barang}', [DetailBarangController::class, 'show'])->name('barang.detail');
Route::get('/barang/{kode_barang}/detail/create', [DetailBarangController::class, 'create'])->name('barang.detail.create');
Route::post('/barang/{kode_barang}/detail', [DetailBarangController::class, 'store'])->name('barang.detail.store');

// Routes untuk rekap dan order
Route::get('/rekap-sr', [RekapSrController::class, 'index'])->name('rekap.sr.index');
Route::get('/order', [OrderController::class, 'index'])->name('order.index');
Route::post('/order', [OrderController::class, 'store'])->name('order.store');
Route::get('/order/status', [OrderController::class, 'status'])->name('order.status');
Route::get('/rekap-sr/{sr}', [RekapSrController::class, 'show'])->name('rekap.sr.show');

Route::get('/rekap-gm', [RekapGmController::class, 'index'])->name('rekap.gm.index');
Route::get('/rekap-gm/{gm}', [RekapGmController::class, 'show'])->name('rekap.gm.show');

Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
Route::get('/orders/{id_order}', [OrderController::class, 'show'])->name('orders.show');
