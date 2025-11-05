@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Detail Barang: {{ $barang->nama_barang }} ({{ $barang->kode_barang }})</span>
                        <a href="{{ route('barang.detail.create', $barang->kode_barang) }}" 
                           class="btn btn-primary btn-sm">
                            Tambah Transaksi
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Info Barang -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <strong>Kode:</strong> {{ $barang->kode_barang }}
                        </div>
                        <div class="col-md-3">
                            <strong>Nama:</strong> {{ $barang->nama_barang }}
                        </div>
                        <div class="col-md-3">
                            <strong>Satuan:</strong> {{ $barang->satuan }}
                        </div>
                        <div class="col-md-3">
                            <strong>Stok Saat Ini:</strong> {{ $barang->stok }}
                        </div>
                    </div>

                    <!-- Statistik -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Total Masuk</h5>
                                    <p class="card-text h3">{{ $totalMasuk }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-danger text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Total Keluar</h5>
                                    <p class="card-text h3">{{ $totalKeluar }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Sisa Stok</h5>
                                    <p class="card-text h3">{{ $barang->stok }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabel Riwayat -->
                    <h5 class="mb-3">Riwayat Transaksi</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>No Bukti</th>
                                    <th>Masuk</th>
                                    <th>Keluar</th>
                                    <th>Sisa</th>
                                    <th>Lokasi</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($details as $detail)
                                <tr>
                                    <td>{{ date('d/m/Y', strtotime($detail->tanggal)) }}</td>
                                    <td>{{ $detail->no_bukti }}</td>
                                    <td class="text-success">{{ $detail->masuk ?: '-' }}</td>
                                    <td class="text-danger">{{ $detail->keluar ?: '-' }}</td>
                                    <td>{{ $detail->sisa }}</td>
                                    <td>{{ $detail->alamat }}</td>
                                    <td>{{ $detail->keterangan }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Distribusi per Lokasi -->
                    <h5 class="mb-3 mt-4">Distribusi per Lokasi</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead>
                                <tr>
                                    <th>Lokasi</th>
                                    <th>Total Masuk</th>
                                    <th>Total Keluar</th>
                                    <th>Selisih</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($distribusiLokasi as $dist)
                                <tr>
                                    <td>{{ $dist->alamat }}</td>
                                    <td class="text-success">{{ $dist->total_masuk }}</td>
                                    <td class="text-danger">{{ $dist->total_keluar }}</td>
                                    <td>{{ $dist->total_masuk - $dist->total_keluar }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection