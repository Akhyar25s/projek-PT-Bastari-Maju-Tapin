@extends('layouts.app')

@section('page_title', 'Detail Barang')

@section('content')
<div class="content-wrapper">
    <!-- Header dengan tombol kembali dan tambah transaksi -->
    <div class="content-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <div>
            <a href="{{ route('barang.index') }}" class="btn-cta" style="text-decoration: none; display: inline-block; margin-bottom: 10px;">← Kembali ke Daftar Barang</a>
            <h1 class="text-2xl font-bold" style="margin: 0;">Detail Barang</h1>
        </div>
        @php
            $userRole = strtolower(session('role') ?? '');
            $canEdit = in_array($userRole, ['admin', 'pejaga gudang']);
        @endphp
        @if($canEdit)
        <a href="{{ route('barang.detail.create', $barang->kode_barang) }}" class="btn-cta" style="text-decoration: none; background-color: #49c2d3; padding: 10px 20px;">
            + Tambah Transaksi
        </a>
        @endif
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="background-color: #d4edda; color: #155724; padding: 12px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-error" style="background-color: #f8d7da; color: #721c24; padding: 12px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
            {{ session('error') }}
        </div>
    @endif

    <!-- Informasi Barang -->
    <div class="bg-white rounded-lg shadow p-6" style="background: #fff; padding: 20px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #ddd;">
        <h2 class="text-lg font-semibold" style="font-size: 18px; font-weight: 600; margin-bottom: 15px; color: #333;">Informasi Barang</h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
            <div>
                <strong style="color: #666;">Kode Barang:</strong>
                <p style="margin: 5px 0; color: #333;">{{ $barang->kode_barang }}</p>
            </div>
            <div>
                <strong style="color: #666;">Nama Barang:</strong>
                <p style="margin: 5px 0; color: #333;">{{ $barang->nama_barang }}</p>
            </div>
            <div>
                <strong style="color: #666;">Satuan:</strong>
                <p style="margin: 5px 0; color: #333;">{{ $barang->satuan }}</p>
            </div>
            <div>
                <strong style="color: #666;">Stok Saat Ini:</strong>
                <p style="margin: 5px 0; color: #333; font-weight: bold; color: #49c2d3;">{{ number_format($barang->stok, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>

    <!-- Ringkasan Statistik -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px; margin-bottom: 20px;">
        <div class="bg-white rounded-lg shadow p-6" style="background: #fff; padding: 20px; border-radius: 8px; border: 1px solid #ddd;">
            <h3 style="font-size: 14px; color: #666; margin-bottom: 10px;">Total Masuk</h3>
            <p style="font-size: 24px; font-weight: bold; color: #28a745; margin: 0;">{{ number_format($totalMasuk, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6" style="background: #fff; padding: 20px; border-radius: 8px; border: 1px solid #ddd;">
            <h3 style="font-size: 14px; color: #666; margin-bottom: 10px;">Total Keluar</h3>
            <p style="font-size: 24px; font-weight: bold; color: #dc3545; margin: 0;">{{ number_format($totalKeluar, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6" style="background: #fff; padding: 20px; border-radius: 8px; border: 1px solid #ddd;">
            <h3 style="font-size: 14px; color: #666; margin-bottom: 10px;">Saldo Akhir</h3>
            <p style="font-size: 24px; font-weight: bold; color: #49c2d3; margin: 0;">{{ number_format($barang->stok, 0, ',', '.') }}</p>
        </div>
    </div>

    <!-- Distribusi Per Lokasi -->
    @if($distribusiLokasi->count() > 0)
    <div class="bg-white rounded-lg shadow p-6" style="background: #fff; padding: 20px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #ddd;">
        <h2 class="text-lg font-semibold" style="font-size: 18px; font-weight: 600; margin-bottom: 15px; color: #333;">Distribusi Per Lokasi</h2>
        <div class="table-wrapper" style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background-color: #f8f9fa;">
                        <th style="padding: 10px; text-align: left; border: 1px solid #ddd;">Lokasi</th>
                        <th style="padding: 10px; text-align: right; border: 1px solid #ddd;">Total Masuk</th>
                        <th style="padding: 10px; text-align: right; border: 1px solid #ddd;">Total Keluar</th>
                        <th style="padding: 10px; text-align: right; border: 1px solid #ddd;">Saldo</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($distribusiLokasi as $dist)
                    @php
                        $saldo = $dist->total_masuk - $dist->total_keluar;
                    @endphp
                    <tr>
                        <td style="padding: 10px; border: 1px solid #ddd;">{{ $dist->alamat }}</td>
                        <td style="padding: 10px; text-align: right; border: 1px solid #ddd; color: #28a745;">{{ number_format($dist->total_masuk, 0, ',', '.') }}</td>
                        <td style="padding: 10px; text-align: right; border: 1px solid #ddd; color: #dc3545;">{{ number_format($dist->total_keluar, 0, ',', '.') }}</td>
                        <td style="padding: 10px; text-align: right; border: 1px solid #ddd; font-weight: bold;">{{ number_format($saldo, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Riwayat Transaksi -->
    <div class="bg-white rounded-lg shadow p-6" style="background: #fff; padding: 20px; border-radius: 8px; border: 1px solid #ddd;">
        <h2 class="text-lg font-semibold" style="font-size: 18px; font-weight: 600; margin-bottom: 15px; color: #333;">Riwayat Transaksi</h2>
        
        @if($details->count() > 0)
        <div class="table-wrapper" style="overflow-x: auto; max-height: 500px; overflow-y: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead style="position: sticky; top: 0; background-color: #f8f9fa; z-index: 10;">
                    <tr>
                        <th style="padding: 10px; text-align: center; border: 1px solid #ddd; background-color: #f8f9fa;">Tanggal</th>
                        <th style="padding: 10px; text-align: center; border: 1px solid #ddd; background-color: #f8f9fa;">No. Bukti</th>
                        <th style="padding: 10px; text-align: right; border: 1px solid #ddd; background-color: #f8f9fa;">Masuk</th>
                        <th style="padding: 10px; text-align: right; border: 1px solid #ddd; background-color: #f8f9fa;">Keluar</th>
                        <th style="padding: 10px; text-align: right; border: 1px solid #ddd; background-color: #f8f9fa;">Sisa</th>
                        <th style="padding: 10px; text-align: center; border: 1px solid #ddd; background-color: #f8f9fa;">Lokasi</th>
                        <th style="padding: 10px; text-align: left; border: 1px solid #ddd; background-color: #f8f9fa;">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($details as $detail)
                    <tr style="border-bottom: 1px solid #ddd;">
                        <td style="padding: 10px; text-align: center; border: 1px solid #ddd;">
                            {{ \Carbon\Carbon::parse($detail->tanggal)->format('d/m/Y') }}
                        </td>
                        <td style="padding: 10px; text-align: center; border: 1px solid #ddd;">{{ $detail->no_bukti }}</td>
                        <td style="padding: 10px; text-align: right; border: 1px solid #ddd; color: #28a745;">
                            @if($detail->masuk > 0)
                                {{ number_format($detail->masuk, 0, ',', '.') }}
                            @else
                                -
                            @endif
                        </td>
                        <td style="padding: 10px; text-align: right; border: 1px solid #ddd; color: #dc3545;">
                            @if($detail->keluar > 0)
                                {{ number_format($detail->keluar, 0, ',', '.') }}
                            @else
                                -
                            @endif
                        </td>
                        <td style="padding: 10px; text-align: right; border: 1px solid #ddd; font-weight: bold;">
                            {{ number_format($detail->sisa, 0, ',', '.') }}
                        </td>
                        <td style="padding: 10px; text-align: center; border: 1px solid #ddd;">{{ $detail->alamat }}</td>
                        <td style="padding: 10px; text-align: left; border: 1px solid #ddd;">{{ $detail->keterangan }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div style="text-align: center; padding: 40px; color: #999;">
            <p>Belum ada riwayat transaksi untuk barang ini.</p>
            @if($canEdit)
            <a href="{{ route('barang.detail.create', $barang->kode_barang) }}" class="btn-cta" style="text-decoration: none; display: inline-block; margin-top: 15px;">
                Tambah Transaksi Pertama
            </a>
            @endif
        </div>
        @endif
    </div>
</div>
@endsection

@section('style')
<style>
    .content-wrapper {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .content-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        flex-wrap: wrap;
        gap: 15px;
    }

    .btn-cta {
        background-color: #f0ad4e;
        color: #fff;
        padding: 8px 16px;
        border-radius: 5px;
        text-decoration: none;
        display: inline-block;
        transition: background-color 0.3s;
        border: none;
        cursor: pointer;
        font-size: 14px;
    }

    .btn-cta:hover {
        background-color: #ec971f;
    }

    .table-wrapper {
        overflow-x: auto;
    }

    .table-wrapper table {
        width: 100%;
        border-collapse: collapse;
        background-color: #fff;
    }

    .table-wrapper table th,
    .table-wrapper table td {
        border: 1px solid #ddd;
        padding: 10px;
    }

    .table-wrapper table th {
        background-color: #f8f9fa;
        font-weight: 600;
        text-align: center;
    }

    .table-wrapper table tbody tr:hover {
        background-color: #f1f1f1;
    }

    .alert {
        padding: 12px;
        border-radius: 5px;
        margin-bottom: 20px;
    }

    .alert-success {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .alert-error {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    @media (max-width: 768px) {
        .content-header {
            flex-direction: column;
        }

        .table-wrapper {
            font-size: 12px;
        }

        .table-wrapper table th,
        .table-wrapper table td {
            padding: 6px;
        }
    }
</style>
@endsection

