@extends('layouts.app')

@section('page_title', 'Edit Barang Rusak')

@section('content')
    <div style="margin-bottom: 20px;">
        <a href="{{ route('barang-rusak.index') }}" style="color: var(--blue); text-decoration: none; font-weight: 500;">
            ← Kembali ke Daftar Barang Rusak
        </a>
    </div>

    <div style="background: white; padding: 30px; border-radius: 8px; max-width: 600px;">
        <h2 style="margin-bottom: 25px; color: #333; font-size: 24px;">Edit Barang Rusak</h2>

        @if($errors->any())
            <div style="background-color: #f8d7da; color: #721c24; padding: 12px; border-radius: 4px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('barang-rusak.update', $barangRusak->kode_barang) }}" method="POST">
            @csrf
            @method('PUT')

            <div style="margin-bottom: 20px;">
                <label for="kode_barang" style="display: block; margin-bottom: 8px; font-weight: 500; color: #333;">
                    Nama Barang *
                </label>
                <select name="kode_barang" 
                        id="kode_barang" 
                        required
                        style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 15px; background: #f9f9f9;"
                        class="@error('kode_barang') border-red-500 @enderror">
                    <option value="">Pilih Barang</option>
                    @foreach($barang as $item)
                        <option value="{{ $item->kode_barang }}" 
                                {{ (old('kode_barang', $barangRusak->kode_barang) == $item->kode_barang) ? 'selected' : '' }}>
                            {{ $item->nama_barang }} ({{ $item->kode_barang }})
                        </option>
                    @endforeach
                </select>
                @error('kode_barang')
                    <span style="color: #dc3545; font-size: 13px; margin-top: 5px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <div style="margin-bottom: 20px;">
                <label for="volume" style="display: block; margin-bottom: 8px; font-weight: 500; color: #333;">
                    Volume *
                </label>
                <input type="number" 
                       name="volume" 
                       id="volume" 
                       value="{{ old('volume', $barangRusak->volume) }}" 
                       min="1" 
                       required
                       style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 15px; background: #f9f9f9;"
                       class="@error('volume') border-red-500 @enderror">
                @error('volume')
                    <span style="color: #dc3545; font-size: 13px; margin-top: 5px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <div style="margin-bottom: 25px;">
                <label for="keterangan" style="display: block; margin-bottom: 8px; font-weight: 500; color: #333;">
                    Keterangan
                </label>
                <input type="text" 
                       name="keterangan" 
                       id="keterangan" 
                       value="{{ old('keterangan', $barangRusak->status) }}" 
                       placeholder="Keterangan"
                       style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 15px; background: #f9f9f9;"
                       class="@error('keterangan') border-red-500 @enderror">
                @error('keterangan')
                    <span style="color: #dc3545; font-size: 13px; margin-top: 5px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <a href="{{ route('barang-rusak.index') }}" 
                   style="padding: 12px 24px; background: #6c757d; color: white; border-radius: 8px; text-decoration: none; display: inline-block;">
                    Batal
                </a>
                <button type="submit" 
                        style="padding: 12px 24px; background: var(--blue); color: white; border: none; border-radius: 8px; font-size: 15px; font-weight: 600; cursor: pointer; transition: all 0.3s ease;">
                    Update
                </button>
            </div>
        </form>
    </div>

    <style>
        select:focus,
        input:focus {
            outline: none;
            border-color: var(--blue) !important;
            background: white !important;
            box-shadow: 0 0 0 3px rgba(63, 192, 214, 0.1);
        }
    </style>
@endsection

