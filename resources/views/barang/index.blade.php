@extends('layouts.app')

@section('page_title','Daftar Barang')

@section('content')
    <div class="panel-header">
        <form method="GET" action="{{ route('barang.index') }}" class="search-area" style="align-items:center;flex:1">
            <label style="font-weight:700;color:#666;margin-right:8px">Cari Barang</label>
            <input type="text" name="q" placeholder="Ketik nama/kode..." id="q" value="{{ request('q') }}" />
            <input type="hidden" name="sort" value="{{ request('sort','popular') }}" />
            <button type="submit">Cari</button>
        </form>

        <div style="margin-left:12px;align-self:flex-start">
            <span class="btn-cta">Terpopuler</span>
        </div>
    </div>

    <div class="table-wrapper">
        <div style="background:#fff;padding:8px;border-radius:6px;display:flex;flex-direction:column;height:100%">
            <table>
                <thead>
                    <tr>
                        <th style="width:90px">Kode</th>
                        <th>Nama Barang</th>
                        <th style="width:120px">Satuan</th>
                        @if(session('role') === 'keuangan')
                        <th style="width:120px">Harga (Rp)</th>
                        @endif
                        <th style="width:90px">Stok</th>
                        <th style="width:90px">Sisa</th>
                        <th style="width:120px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalStok = $barang->sum('stok');
                        $totalSisa = $barang->sum(function($b){ return $b->sisa ?? $b->stok; });
                    @endphp
                    @foreach($barang as $item)
                    @php $sisa = $item->sisa ?? $item->stok ?? 0; @endphp
                    <tr>
                        <td>{{ $item->kode_barang }}</td>
                        <td>{{ $item->nama_barang }}</td>
                        <td>{{ $item->satuan }}</td>
                        <td>{{ $item->stok }}</td>
                        @if(session('role') === 'keuangan')
                        <td>{{ $item->harga !== null ? number_format($item->harga,2,',','.') : '-' }}</td>
                        @endif
                        <td>{{ $sisa }}</td>
                        <td>
                            <a href="{{ route('barang.detail', $item->kode_barang) }}" class="btn-cta">Detail</a>
                            @if(session('role') === 'keuangan')
                                <a href="{{ route('barang.harga.edit', $item->kode_barang) }}" class="btn-cta" style="background:#5cb85c">Edit Harga</a>
                            @endif
                        </td>
                    </tr>
                    @endforeach

                    <tr class="total-row">
                        <td colspan="3">Jumlah</td>
                        <td>{{ $totalStok }}</td>
                        <td>{{ $totalSisa }}</td>
                        @if(session('role') === 'keuangan')
                        <td></td>
                        @endif
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Server-side search: form above submits to the same page with q param --}}
@endsection

@section('style')
<style>
    /* Mengatasi masalah posisi tulisan yang nyangkut */
    .panel-header {
        padding-bottom: 20px; /* Memberikan jarak di bawah header */
        border-bottom: 2px solid #ccc; /* Menambahkan border pada panel header */
    }

    .table-wrapper table {
        background-color: #fff !important; /* Pastikan latar belakang tabel tidak transparan */
        border-collapse: collapse; /* Menggabungkan border sel */
        width: 100%;
        margin-top: 20px; /* Memberikan jarak atas pada tabel */
    }

    /* Menambahkan border pada setiap sel tabel */
    .table-wrapper table th,
    .table-wrapper table td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: center;
    }

    .search-area {
        border: 1px solid #ccc;
        padding: 10px;
        border-radius: 5px;
    }

    .btn-cta {
        background-color: #f0ad4e;
        color: #fff;
        padding: 5px 10px;
        border-radius: 5px;
        text-decoration: none;
    }

    .table-wrapper {
        max-height: 400px; /* Batasi tinggi tabel */
        overflow-y: auto; /* Aktifkan scroll jika konten melebihi batas */
    }

    /* Menambahkan efek hover pada baris tabel */
    .table-wrapper table tr:hover {
        background-color: #f1f1f1;
    }
</style>
@endsection
