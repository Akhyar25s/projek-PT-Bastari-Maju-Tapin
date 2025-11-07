@extends('layouts.app')

@section('page_title', 'Detail Barang')

@section('content')
    @if(session('success'))
        <div style="background-color: #d4edda; color: #155724; padding: 12px; border-radius: 4px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div style="background-color: #f8d7da; color: #721c24; padding: 12px; border-radius: 4px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
            {{ session('error') }}
        </div>
    @endif

    <div style="margin-bottom: 20px;">
        <a href="{{ route('barang.index') }}" style="color: var(--blue); text-decoration: none; font-weight: 500;">
            ← Kembali ke Daftar Barang
        </a>
    </div>

    <!-- Header dengan Tombol Tambah Transaksi -->
    <div style="background: white; padding: 25px; border-radius: 8px; margin-bottom: 25px; border: 2px solid var(--blue);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <div>
                <h1 style="font-size: 28px; font-weight: bold; color: #333; margin: 0 0 5px 0;">
                    {{ $barang->nama_barang }}
                </h1>
                <p style="color: #666; font-size: 16px; margin: 0;">Kode: {{ $barang->kode_barang }}</p>
            </div>
            <a href="{{ route('barang.detail.create', $barang->kode_barang) }}" 
               style="padding: 12px 24px; background: var(--btn); color: white; text-decoration: none; border-radius: 6px; font-size: 15px; font-weight: 600; display: inline-block;">
                + Tambah Transaksi
            </a>
        </div>

        <!-- Info Barang -->
        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; padding-top: 20px; border-top: 2px solid #eee;">
            <div>
                <div style="font-size: 13px; color: #666; margin-bottom: 5px;">Kode Barang</div>
                <div style="font-size: 18px; font-weight: 600; color: #333;">{{ $barang->kode_barang }}</div>
            </div>
            <div>
                <div style="font-size: 13px; color: #666; margin-bottom: 5px;">Nama Barang</div>
                <div style="font-size: 18px; font-weight: 600; color: #333;">{{ $barang->nama_barang }}</div>
            </div>
            <div>
                <div style="font-size: 13px; color: #666; margin-bottom: 5px;">Satuan</div>
                <div style="font-size: 18px; font-weight: 600; color: #333;">{{ $barang->satuan }}</div>
            </div>
            <div>
                <div style="font-size: 13px; color: #666; margin-bottom: 5px;">Stok Saat Ini</div>
                <div style="font-size: 24px; font-weight: bold; color: var(--blue);">{{ $barang->stok ?? 0 }}</div>
            </div>
        </div>
    </div>

    <!-- Statistik Cards -->
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 30px;">
        <div style="background: white; padding: 25px; border-radius: 8px; border: 2px solid #28a745;">
            <div style="font-size: 14px; color: #666; margin-bottom: 10px; font-weight: 600;">Total Masuk</div>
            <div style="font-size: 36px; font-weight: bold; color: #28a745;">{{ number_format($totalMasuk, 0, ',', '.') }}</div>
        </div>
        <div style="background: white; padding: 25px; border-radius: 8px; border: 2px solid #dc3545;">
            <div style="font-size: 14px; color: #666; margin-bottom: 10px; font-weight: 600;">Total Keluar</div>
            <div style="font-size: 36px; font-weight: bold; color: #dc3545;">{{ number_format($totalKeluar, 0, ',', '.') }}</div>
        </div>
        <div style="background: white; padding: 25px; border-radius: 8px; border: 2px solid var(--blue);">
            <div style="font-size: 14px; color: #666; margin-bottom: 10px; font-weight: 600;">Sisa Stok</div>
            <div style="font-size: 36px; font-weight: bold; color: var(--blue);">{{ number_format($barang->stok ?? 0, 0, ',', '.') }}</div>
        </div>
    </div>

    <!-- Tabel Riwayat Transaksi -->
    <div style="background: white; padding: 25px; border-radius: 8px; margin-bottom: 30px; border: 2px solid var(--blue);">
        <h2 style="font-size: 22px; font-weight: bold; margin-bottom: 20px; color: #333; padding-bottom: 10px; border-bottom: 3px solid var(--blue);">
            Riwayat Transaksi
        </h2>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                <thead>
                    <tr>
                        <th style="padding: 12px; background: var(--blue); color: #fff; text-align: left; font-weight: bold; border: 1px solid #ddd;">Tanggal</th>
                        <th style="padding: 12px; background: var(--blue); color: #fff; text-align: left; font-weight: bold; border: 1px solid #ddd;">No Bukti</th>
                        <th style="padding: 12px; background: var(--blue); color: #fff; text-align: center; font-weight: bold; border: 1px solid #ddd;">Masuk</th>
                        <th style="padding: 12px; background: var(--blue); color: #fff; text-align: center; font-weight: bold; border: 1px solid #ddd;">Keluar</th>
                        <th style="padding: 12px; background: var(--blue); color: #fff; text-align: center; font-weight: bold; border: 1px solid #ddd;">Sisa</th>
                        <th style="padding: 12px; background: var(--blue); color: #fff; text-align: left; font-weight: bold; border: 1px solid #ddd;">Lokasi</th>
                        <th style="padding: 12px; background: var(--blue); color: #fff; text-align: left; font-weight: bold; border: 1px solid #ddd;">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($details as $detail)
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 12px; color: #333; border: 1px solid #eee;">{{ \Carbon\Carbon::parse($detail->tanggal)->format('d/m/Y') }}</td>
                        <td style="padding: 12px; color: #333; border: 1px solid #eee;">{{ $detail->no_bukti }}</td>
                        <td style="padding: 12px; text-align: center; color: #28a745; font-weight: 600; border: 1px solid #eee;">
                            @if($detail->masuk > 0)
                                +{{ number_format($detail->masuk, 0, ',', '.') }}
                            @else
                                -
                            @endif
                        </td>
                        <td style="padding: 12px; text-align: center; color: #dc3545; font-weight: 600; border: 1px solid #eee;">
                            @if($detail->keluar > 0)
                                -{{ number_format($detail->keluar, 0, ',', '.') }}
                            @else
                                -
                            @endif
                        </td>
                        <td style="padding: 12px; text-align: center; font-weight: 600; color: #333; border: 1px solid #eee;">{{ number_format($detail->sisa, 0, ',', '.') }}</td>
                        <td style="padding: 12px; color: #333; border: 1px solid #eee;">{{ $detail->alamat }}</td>
                        <td style="padding: 12px; color: #333; border: 1px solid #eee;">{{ $detail->keterangan }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 40px; color: #999;">
                            Belum ada transaksi
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Distribusi per Lokasi -->
    <div style="background: white; padding: 25px; border-radius: 8px; border: 2px solid var(--accent);">
        <h2 style="font-size: 22px; font-weight: bold; margin-bottom: 20px; color: #333; padding-bottom: 10px; border-bottom: 3px solid var(--accent);">
            Distribusi per Lokasi
        </h2>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                <thead>
                    <tr>
                        <th style="padding: 12px; background: var(--accent); color: #333; text-align: left; font-weight: bold; border: 1px solid #ddd;">Lokasi</th>
                        <th style="padding: 12px; background: var(--accent); color: #333; text-align: center; font-weight: bold; border: 1px solid #ddd;">Total Masuk</th>
                        <th style="padding: 12px; background: var(--accent); color: #333; text-align: center; font-weight: bold; border: 1px solid #ddd;">Total Keluar</th>
                        <th style="padding: 12px; background: var(--accent); color: #333; text-align: center; font-weight: bold; border: 1px solid #ddd;">Selisih</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($distribusiLokasi as $dist)
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 12px; color: #333; border: 1px solid #eee; font-weight: 500;">{{ $dist->alamat }}</td>
                        <td style="padding: 12px; text-align: center; color: #28a745; font-weight: 600; border: 1px solid #eee;">
                            {{ number_format($dist->total_masuk ?? 0, 0, ',', '.') }}
                        </td>
                        <td style="padding: 12px; text-align: center; color: #dc3545; font-weight: 600; border: 1px solid #eee;">
                            {{ number_format($dist->total_keluar ?? 0, 0, ',', '.') }}
                        </td>
                        <td style="padding: 12px; text-align: center; font-weight: 600; color: #333; border: 1px solid #eee;">
                            {{ number_format(($dist->total_masuk ?? 0) - ($dist->total_keluar ?? 0), 0, ',', '.') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 40px; color: #999;">
                            Belum ada distribusi
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <style>
        tbody tr:hover {
            background-color: #f9f9f9;
        }
    </style>
@endsection
