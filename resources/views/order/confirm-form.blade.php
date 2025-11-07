@extends('layouts.app')

@section('page_title', 'Form Konfirmasi Order')

@section('content')
    <div style="margin-bottom: 20px;">
        <a href="{{ route('order.confirm') }}" style="color: var(--blue); text-decoration: none; font-weight: 500;">
            ← Kembali
        </a>
    </div>

    <div style="background: white; padding: 30px; border-radius: 8px; border: 2px solid var(--blue);">
        <h2 style="font-size: 24px; font-weight: bold; margin-bottom: 25px; color: #333; padding-bottom: 10px; border-bottom: 3px solid var(--blue);">
            Form Konfirmasi Order
        </h2>

        @if($errors->any())
            <div style="background-color: #f8d7da; color: #721c24; padding: 12px; border-radius: 4px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Info Barang (jika hanya satu order) -->
        @if(count($orders) == 1)
            @php $order = $orders->first(); @endphp
            <div style="margin-bottom: 25px; padding-bottom: 15px; border-bottom: 2px solid var(--accent);">
                <div style="margin-bottom: 8px;">
                    <strong style="color: #333; font-size: 14px;">Kode Barang :</strong>
                    <span style="color: #666; font-size: 14px; margin-left: 10px;">{{ $order->kode_barang }}</span>
                </div>
                <div>
                    <strong style="color: #333; font-size: 14px;">Nama Barang :</strong>
                    <span style="color: #666; font-size: 14px; margin-left: 10px;">{{ $order->nama_barang }}</span>
                </div>
            </div>
        @endif

        <form action="{{ route('order.confirm-store') }}" method="POST">
            @csrf

            <div style="margin-bottom: 20px;">
                <label for="tanggal" style="display: block; margin-bottom: 8px; font-weight: 600; color: #333; font-size: 14px;">
                    Tanggal :
                </label>
                <div style="position: relative;">
                    <input type="date" 
                           name="tanggal" 
                           id="tanggal" 
                           value="{{ old('tanggal', date('Y-m-d')) }}" 
                           required 
                           style="width: 100%; padding: 10px; border: 2px solid #e0e0e0; border-radius: 6px; font-size: 15px;">
                </div>
            </div>

            <div style="margin-bottom: 20px;">
                <label for="no_bukti" style="display: block; margin-bottom: 8px; font-weight: 600; color: #333; font-size: 14px;">
                    No. Bukti :
                </label>
                <input type="text" 
                       name="no_bukti" 
                       id="no_bukti" 
                       value="{{ old('no_bukti') }}" 
                       required 
                       placeholder="Masukkan no bukti"
                       style="width: 100%; padding: 10px; border: 2px solid #e0e0e0; border-radius: 6px; font-size: 15px;">
            </div>

            <div style="margin-bottom: 20px;">
                <label for="alamat" style="display: block; margin-bottom: 8px; font-weight: 600; color: #333; font-size: 14px;">
                    Alamat :
                </label>
                <div style="position: relative;">
                    <select name="alamat" 
                            id="alamat" 
                            required 
                            style="width: 100%; padding: 10px; border: 2px solid #e0e0e0; border-radius: 6px; font-size: 15px; appearance: none; background-image: url('data:image/svg+xml;charset=UTF-8,<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><polyline points=\"6 9 12 15 18 9\"></polyline></svg>'); background-repeat: no-repeat; background-position: right 10px center; background-size: 20px; padding-right: 40px;">
                        <option value="">-- Pilih Alamat --</option>
                        @foreach($lokasi as $loc)
                            <option value="{{ $loc }}" {{ old('alamat') == $loc ? 'selected' : '' }}>{{ $loc }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div style="margin-bottom: 30px;">
                <label for="keterangan" style="display: block; margin-bottom: 8px; font-weight: 600; color: #333; font-size: 14px;">
                    Keterangan :
                </label>
                <div style="position: relative;">
                    <input type="text" 
                           name="keterangan" 
                           id="keterangan" 
                           value="{{ old('keterangan') }}" 
                           placeholder="Masukkan keterangan (opsional)"
                           style="width: 100%; padding: 10px; border: 2px solid #e0e0e0; border-radius: 6px; font-size: 15px;">
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 15px;">
                <a href="{{ route('order.confirm') }}" 
                   style="padding: 12px 24px; background: #6c757d; color: white; text-decoration: none; border-radius: 6px; font-size: 15px; font-weight: 600; display: inline-block;">
                    Batal
                </a>
                <button type="submit" 
                        style="padding: 12px 24px; background: var(--btn); color: white; border: none; border-radius: 6px; font-size: 15px; font-weight: 600; cursor: pointer;">
                    Order
                </button>
            </div>
        </form>
    </div>
@endsection

