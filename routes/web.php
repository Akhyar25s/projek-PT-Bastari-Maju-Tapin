<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DetailBarangController;

Route::get('/', function () {
    return view('welcome');
});

// Route untuk barang dan detail
Route::get('/barang', [DetailBarangController::class, 'index'])->name('barang.index');
Route::get('/barang/{kode_barang}', [DetailBarangController::class, 'show'])->name('barang.detail');
Route::get('/barang/{kode_barang}/detail/create', [DetailBarangController::class, 'create'])->name('barang.detail.create');
Route::post('/barang/{kode_barang}/detail', [DetailBarangController::class, 'store'])->name('barang.detail.store');
