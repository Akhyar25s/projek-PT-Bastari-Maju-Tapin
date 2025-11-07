@extends('layouts.app')

@section('page_title', 'Tambah Transaksi Barang')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <h1 class="text-2xl font-bold">Tambah Transaksi Barang</h1>
        <a href="{{ route('barang.detail', $barang->kode_barang) }}" class="text-blue-500 hover:underline">← Kembali</a>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="mb-4">
            <h2 class="text-lg font-semibold">Informasi Barang</h2>
            <p><strong>Kode:</strong> {{ $barang->kode_barang }}</p>
            <p><strong>Nama:</strong> {{ $barang->nama_barang }}</p>
            <p><strong>Satuan:</strong> {{ $barang->satuan }}</p>
            <p><strong>Stok Saat Ini:</strong> {{ $barang->stok }}</p>
        </div>

        <form action="{{ route('barang.detail.store', $barang->kode_barang) }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="tanggal" class="block text-sm font-medium text-gray-700 mb-1">Tanggal *</label>
                    <input type="date" name="tanggal" id="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" 
                           class="w-full p-2 border rounded @error('tanggal') border-red-500 @enderror" required>
                    @error('tanggal')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="no_bukti" class="block text-sm font-medium text-gray-700 mb-1">No. Bukti *</label>
                    <input type="text" name="no_bukti" id="no_bukti" value="{{ old('no_bukti') }}" 
                           class="w-full p-2 border rounded @error('no_bukti') border-red-500 @enderror" required>
                    @error('no_bukti')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="masuk" class="block text-sm font-medium text-gray-700 mb-1">Masuk *</label>
                    <input type="number" name="masuk" id="masuk" value="{{ old('masuk', 0) }}" min="0" 
                           class="w-full p-2 border rounded @error('masuk') border-red-500 @enderror" required>
                    @error('masuk')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="keluar" class="block text-sm font-medium text-gray-700 mb-1">Keluar *</label>
                    <input type="number" name="keluar" id="keluar" value="{{ old('keluar', 0) }}" min="0" 
                           class="w-full p-2 border rounded @error('keluar') border-red-500 @enderror" required>
                    @error('keluar')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="alamat" class="block text-sm font-medium text-gray-700 mb-1">Alamat/Lokasi *</label>
                    <select name="alamat" id="alamat" 
                            class="w-full p-2 border rounded @error('alamat') border-red-500 @enderror" required>
                        <option value="">Pilih Lokasi</option>
                        @foreach($lokasi as $loc)
                            <option value="{{ $loc }}" {{ old('alamat') == $loc ? 'selected' : '' }}>
                                {{ $loc }}
                            </option>
                        @endforeach
                    </select>
                    @error('alamat')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-1">Keterangan *</label>
                    <input type="text" name="keterangan" id="keterangan" value="{{ old('keterangan') }}" 
                           class="w-full p-2 border rounded @error('keterangan') border-red-500 @enderror" required>
                    @error('keterangan')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            @if($errors->any())
                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="flex justify-end space-x-2">
                <a href="{{ route('barang.detail', $barang->kode_barang) }}" 
                   class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                    Batal
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                    Simpan Transaksi
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

