@php($role = session('role'))
@if($role !== 'keuangan')
    <div class="alert alert-danger">Akses ditolak.</div>
@else
    <h1>Edit Harga Barang</h1>
    <a href="{{ url()->previous() }}" class="btn btn-secondary btn-sm">&larr; Kembali</a>
    <hr>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form method="POST" action="{{ route('barang.harga.update', $barang->kode_barang) }}" class="mt-3" novalidate>
        @csrf
        <div class="mb-3">
            <label class="form-label">Kode Barang</label>
            <input type="text" class="form-control" value="{{ $barang->kode_barang }}" disabled>
        </div>
        <div class="mb-3">
            <label class="form-label">Nama Barang</label>
            <input type="text" class="form-control" value="{{ $barang->nama_barang }}" disabled>
        </div>
        <div class="mb-3">
            <label for="harga" class="form-label">Harga Satuan (Rp)</label>
            <input type="number" min="0" step="0.01" name="harga" id="harga" class="form-control @error('harga') is-invalid @enderror" value="{{ old('harga', $barang->harga) }}" required>
            @error('harga')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit" class="btn btn-primary">Simpan</button>
    </form>
@endif