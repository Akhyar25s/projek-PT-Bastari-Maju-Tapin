@extends('layouts.app')

@section('page_title', 'Rekap SR/GM')

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

    <!-- Form Generate Rekap -->
    <div style="background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px; border: 2px solid var(--blue);">
        <h3 style="font-size: 18px; font-weight: bold; margin-bottom: 15px; color: #333;">
            Generate Rekap Otomatis dari Pengeluaran Barang
        </h3>
        <p style="color: #666; margin-bottom: 15px; font-size: 14px;">
            Rekap akan dihitung otomatis dari data pengeluaran barang (detail_barang) berdasarkan bulan dan lokasi.
            <br><strong>Catatan:</strong> Setiap kali ada transaksi pengeluaran baru, rekap akan otomatis terupdate untuk bulan tersebut.
        </p>
        <div style="background: #e3f2fd; padding: 12px; border-radius: 6px; margin-bottom: 15px; font-size: 13px; color: #1976d2;">
            <strong>Mapping Lokasi:</strong> RANTAU→rantau, BINUANG→binuang, TAP SELATAN→tap sel, CLU→clu, CLS→cls, 
            TENGAH→tap tengah, TAPIN→tap tengah, BATU HAPU→batu hapu, BAKARANGAN→bakarangan, LOKPAIKAT→lokpaikat, 
            SALBA→salba, PIANI→piani
        </div>
        <form method="GET" action="{{ route('rekap.index') }}" style="display: flex; gap: 10px; align-items: flex-end;">
            <input type="hidden" name="generate" value="1">
            <div style="flex: 1;">
                <label for="bulan" style="display: block; margin-bottom: 5px; font-weight: 500; color: #333;">Pilih Bulan:</label>
                <select name="bulan" id="bulan" required style="width: 100%; padding: 10px; border: 2px solid #e0e0e0; border-radius: 6px; font-size: 15px;">
                    <option value="">-- Pilih Bulan --</option>
                    @foreach($bulanList as $bulan)
                        <option value="{{ $bulan->id_bulan }}">{{ $bulan->nama_bulan }}</option>
                    @endforeach
                </select>
            </div>
            <div style="flex: 1;">
                <label for="tahun" style="display: block; margin-bottom: 5px; font-weight: 500; color: #333;">Tahun:</label>
                <input type="number" name="tahun" id="tahun" value="{{ date('Y') }}" min="2020" max="2100" required style="width: 100%; padding: 10px; border: 2px solid #e0e0e0; border-radius: 6px; font-size: 15px;">
            </div>
            <div>
                <button type="submit" style="padding: 10px 24px; background: var(--blue); color: white; border: none; border-radius: 6px; font-size: 15px; font-weight: 600; cursor: pointer;">
                    Generate Rekap
                </button>
            </div>
        </form>
    </div>

    <!-- Rekap SR Section -->
    <div style="margin-bottom: 40px;">
        <h2 style="font-size: 22px; font-weight: bold; margin-bottom: 20px; color: #333; padding-bottom: 10px; border-bottom: 3px solid var(--blue);">
            Rekap SR
        </h2>
        <div>
            <table style="width: 100%; border-collapse: collapse; background: white; font-size: 12px;">
                <thead>
                    <tr>
                        <th style="padding: 8px 6px; background: var(--blue); color: #fff; text-align: left; font-size: 11px; font-weight: bold; border: 1px solid #ddd; white-space: nowrap;">Bulan</th>
                        <th style="padding: 8px 6px; background: var(--blue); color: #fff; text-align: center; font-size: 11px; font-weight: bold; border: 1px solid #ddd;">Rantau</th>
                        <th style="padding: 8px 6px; background: var(--blue); color: #fff; text-align: center; font-size: 11px; font-weight: bold; border: 1px solid #ddd;">Binuang</th>
                        <th style="padding: 8px 6px; background: var(--blue); color: #fff; text-align: center; font-size: 11px; font-weight: bold; border: 1px solid #ddd;">Tap Sel</th>
                        <th style="padding: 8px 6px; background: var(--blue); color: #fff; text-align: center; font-size: 11px; font-weight: bold; border: 1px solid #ddd;">CLU</th>
                        <th style="padding: 8px 6px; background: var(--blue); color: #fff; text-align: center; font-size: 11px; font-weight: bold; border: 1px solid #ddd;">CLS</th>
                        <th style="padding: 8px 6px; background: var(--blue); color: #fff; text-align: center; font-size: 11px; font-weight: bold; border: 1px solid #ddd;">Tap Tengah</th>
                        <th style="padding: 8px 6px; background: var(--blue); color: #fff; text-align: center; font-size: 11px; font-weight: bold; border: 1px solid #ddd;">Batu Hapu</th>
                        <th style="padding: 8px 6px; background: var(--blue); color: #fff; text-align: center; font-size: 11px; font-weight: bold; border: 1px solid #ddd;">Bakarangan</th>
                        <th style="padding: 8px 6px; background: var(--blue); color: #fff; text-align: center; font-size: 11px; font-weight: bold; border: 1px solid #ddd;">Lokpaikat</th>
                        <th style="padding: 8px 6px; background: var(--blue); color: #fff; text-align: center; font-size: 11px; font-weight: bold; border: 1px solid #ddd;">Salba</th>
                        <th style="padding: 8px 6px; background: var(--blue); color: #fff; text-align: center; font-size: 11px; font-weight: bold; border: 1px solid #ddd;">Piani</th>
                        <th style="padding: 8px 6px; background: var(--blue); color: #fff; text-align: center; font-size: 11px; font-weight: bold; border: 1px solid #ddd;">Jumlah</th>
                        <th style="padding: 8px 6px; background: var(--blue); color: #fff; text-align: center; font-size: 11px; font-weight: bold; border: 1px solid #ddd;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rekapSr as $item)
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 8px 6px; color: #333; font-size: 11px; border: 1px solid #eee;">{{ $item->nama_bulan }}</td>
                        <td style="padding: 8px 6px; text-align: center; color: #333; font-size: 11px; border: 1px solid #eee;">{{ $item->rantau ?? 0 }}</td>
                        <td style="padding: 8px 6px; text-align: center; color: #333; font-size: 11px; border: 1px solid #eee;">{{ $item->binuang ?? 0 }}</td>
                        <td style="padding: 8px 6px; text-align: center; color: #333; font-size: 11px; border: 1px solid #eee;">{{ $item->{'tap sel'} ?? 0 }}</td>
                        <td style="padding: 8px 6px; text-align: center; color: #333; font-size: 11px; border: 1px solid #eee;">{{ $item->clu ?? 0 }}</td>
                        <td style="padding: 8px 6px; text-align: center; color: #333; font-size: 11px; border: 1px solid #eee;">{{ $item->cls ?? 0 }}</td>
                        <td style="padding: 8px 6px; text-align: center; color: #333; font-size: 11px; border: 1px solid #eee;">{{ $item->{'tap tengah'} ?? 0 }}</td>
                        <td style="padding: 8px 6px; text-align: center; color: #333; font-size: 11px; border: 1px solid #eee;">{{ $item->{'batu hapu'} ?? 0 }}</td>
                        <td style="padding: 8px 6px; text-align: center; color: #333; font-size: 11px; border: 1px solid #eee;">{{ $item->bakarangan ?? 0 }}</td>
                        <td style="padding: 8px 6px; text-align: center; color: #333; font-size: 11px; border: 1px solid #eee;">{{ $item->lokpaikat ?? 0 }}</td>
                        <td style="padding: 8px 6px; text-align: center; color: #333; font-size: 11px; border: 1px solid #eee;">{{ $item->salba ?? 0 }}</td>
                        <td style="padding: 8px 6px; text-align: center; color: #333; font-size: 11px; border: 1px solid #eee;">{{ $item->piani ?? 0 }}</td>
                        <td style="padding: 8px 6px; text-align: center; font-weight: bold; color: #333; font-size: 11px; border: 1px solid #eee;">{{ $item->jumlah ?? 0 }}</td>
                        <td style="padding: 8px 6px; text-align: center; border: 1px solid #eee;">
                            <a href="{{ route('rekap.show-sr', $item->sr) }}" 
                               style="padding: 4px 8px; background: var(--btn); color: white; text-decoration: none; border-radius: 4px; font-size: 11px; display: inline-block;">
                                Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="14" style="text-align: center; padding: 40px; color: #999;">
                            Tidak ada data rekap SR
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Rekap GM Section -->
    <div>
        <h2 style="font-size: 22px; font-weight: bold; margin-bottom: 20px; color: #333; padding-bottom: 10px; border-bottom: 3px solid var(--accent);">
            Rekap GM
        </h2>
        <div>
            <table style="width: 100%; border-collapse: collapse; background: white; font-size: 12px;">
                <thead>
                    <tr>
                        <th style="padding: 8px 6px; background: var(--accent); color: #333; text-align: left; font-size: 11px; font-weight: bold; border: 1px solid #ddd; white-space: nowrap;">Bulan</th>
                        <th style="padding: 8px 6px; background: var(--accent); color: #333; text-align: center; font-size: 11px; font-weight: bold; border: 1px solid #ddd;">Rantau</th>
                        <th style="padding: 8px 6px; background: var(--accent); color: #333; text-align: center; font-size: 11px; font-weight: bold; border: 1px solid #ddd;">Binuang</th>
                        <th style="padding: 8px 6px; background: var(--accent); color: #333; text-align: center; font-size: 11px; font-weight: bold; border: 1px solid #ddd;">Tap Sel</th>
                        <th style="padding: 8px 6px; background: var(--accent); color: #333; text-align: center; font-size: 11px; font-weight: bold; border: 1px solid #ddd;">CLU</th>
                        <th style="padding: 8px 6px; background: var(--accent); color: #333; text-align: center; font-size: 11px; font-weight: bold; border: 1px solid #ddd;">CLS</th>
                        <th style="padding: 8px 6px; background: var(--accent); color: #333; text-align: center; font-size: 11px; font-weight: bold; border: 1px solid #ddd;">Tap Tengah</th>
                        <th style="padding: 8px 6px; background: var(--accent); color: #333; text-align: center; font-size: 11px; font-weight: bold; border: 1px solid #ddd;">Batu Hapu</th>
                        <th style="padding: 8px 6px; background: var(--accent); color: #333; text-align: center; font-size: 11px; font-weight: bold; border: 1px solid #ddd;">Bakarangan</th>
                        <th style="padding: 8px 6px; background: var(--accent); color: #333; text-align: center; font-size: 11px; font-weight: bold; border: 1px solid #ddd;">Lokpaikat</th>
                        <th style="padding: 8px 6px; background: var(--accent); color: #333; text-align: center; font-size: 11px; font-weight: bold; border: 1px solid #ddd;">Salba</th>
                        <th style="padding: 8px 6px; background: var(--accent); color: #333; text-align: center; font-size: 11px; font-weight: bold; border: 1px solid #ddd;">Piani</th>
                        <th style="padding: 8px 6px; background: var(--accent); color: #333; text-align: center; font-size: 11px; font-weight: bold; border: 1px solid #ddd;">Jumlah</th>
                        <th style="padding: 8px 6px; background: var(--accent); color: #333; text-align: center; font-size: 11px; font-weight: bold; border: 1px solid #ddd;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rekapGm as $item)
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 8px 6px; color: #333; font-size: 11px; border: 1px solid #eee;">{{ $item->nama_bulan }}</td>
                        <td style="padding: 8px 6px; text-align: center; color: #333; font-size: 11px; border: 1px solid #eee;">{{ $item->rantau ?? 0 }}</td>
                        <td style="padding: 8px 6px; text-align: center; color: #333; font-size: 11px; border: 1px solid #eee;">{{ $item->binuang ?? 0 }}</td>
                        <td style="padding: 8px 6px; text-align: center; color: #333; font-size: 11px; border: 1px solid #eee;">{{ $item->{'tap sel'} ?? 0 }}</td>
                        <td style="padding: 8px 6px; text-align: center; color: #333; font-size: 11px; border: 1px solid #eee;">{{ $item->clu ?? 0 }}</td>
                        <td style="padding: 8px 6px; text-align: center; color: #333; font-size: 11px; border: 1px solid #eee;">{{ $item->cls ?? 0 }}</td>
                        <td style="padding: 8px 6px; text-align: center; color: #333; font-size: 11px; border: 1px solid #eee;">{{ $item->{'tap tengah'} ?? 0 }}</td>
                        <td style="padding: 8px 6px; text-align: center; color: #333; font-size: 11px; border: 1px solid #eee;">{{ $item->{'batu hapu'} ?? 0 }}</td>
                        <td style="padding: 8px 6px; text-align: center; color: #333; font-size: 11px; border: 1px solid #eee;">{{ $item->bakarangan ?? 0 }}</td>
                        <td style="padding: 8px 6px; text-align: center; color: #333; font-size: 11px; border: 1px solid #eee;">{{ $item->lokpaikat ?? 0 }}</td>
                        <td style="padding: 8px 6px; text-align: center; color: #333; font-size: 11px; border: 1px solid #eee;">{{ $item->salba ?? 0 }}</td>
                        <td style="padding: 8px 6px; text-align: center; color: #333; font-size: 11px; border: 1px solid #eee;">{{ $item->piani ?? 0 }}</td>
                        <td style="padding: 8px 6px; text-align: center; font-weight: bold; color: #333; font-size: 11px; border: 1px solid #eee;">{{ $item->jumlah ?? 0 }}</td>
                        <td style="padding: 8px 6px; text-align: center; border: 1px solid #eee;">
                            <a href="{{ route('rekap.show-gm', $item->gm) }}" 
                               style="padding: 4px 8px; background: var(--btn); color: white; text-decoration: none; border-radius: 4px; font-size: 11px; display: inline-block;">
                                Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="14" style="text-align: center; padding: 40px; color: #999;">
                            Tidak ada data rekap GM
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <style>
        /* Styling tabel rekap - muat di layar laptop */
        table {
            font-size: 12px;
        }

        /* Hover effect untuk baris tabel */
        tbody tr:hover {
            background-color: #f9f9f9;
        }
    </style>
@endsection

