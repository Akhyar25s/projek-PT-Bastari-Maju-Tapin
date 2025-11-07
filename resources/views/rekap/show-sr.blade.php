@extends('layouts.app')

@section('page_title', 'Detail Rekap SR')

@section('content')
    <div style="margin-bottom: 20px;">
        <a href="{{ route('rekap.index') }}" style="color: var(--blue); text-decoration: none; font-weight: 500;">
            ← Kembali ke Rekap SR/GM
        </a>
    </div>

    <div style="background: white; padding: 30px; border-radius: 8px; overflow-x: hidden;">
        <h2 style="font-size: 24px; font-weight: bold; margin-bottom: 25px; color: #333; padding-bottom: 10px; border-bottom: 3px solid var(--blue); overflow-x: hidden; width: 100%;">
            Detail Rekap SR - {{ $item->nama_bulan }}
        </h2>

        <div class="detail-table-wrapper">
            <table class="detail-table">
                <thead>
                    <tr>
                        <th class="sticky-column-detail">Lokasi</th>
                        <th>Rantau</th>
                        <th>Binuang</th>
                        <th>Tap Sel</th>
                        <th>CLU</th>
                        <th>CLS</th>
                        <th>Tap Tengah</th>
                        <th>Batu Hapu</th>
                        <th>Bakarangan</th>
                        <th>Lokpaikat</th>
                        <th>Salba</th>
                        <th>Piani</th>
                        <th>Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="sticky-column-detail"><strong>Nilai</strong></td>
                        <td>{{ $item->rantau ?? 0 }}</td>
                        <td>{{ $item->binuang ?? 0 }}</td>
                        <td>{{ $item->{'tap sel'} ?? 0 }}</td>
                        <td>{{ $item->clu ?? 0 }}</td>
                        <td>{{ $item->cls ?? 0 }}</td>
                        <td>{{ $item->{'tap tengah'} ?? 0 }}</td>
                        <td>{{ $item->{'batu hapu'} ?? 0 }}</td>
                        <td>{{ $item->bakarangan ?? 0 }}</td>
                        <td>{{ $item->lokpaikat ?? 0 }}</td>
                        <td>{{ $item->salba ?? 0 }}</td>
                        <td>{{ $item->piani ?? 0 }}</td>
                        <td style="font-weight: bold; background: var(--blue); color: #fff;">{{ $item->jumlah ?? 0 }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <style>
        /* Container untuk detail - header tidak scroll */
        .detail-table-wrapper {
            display: block;
            width: 100%;
            overflow-x: auto;
            overflow-y: hidden;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: thin;
            scrollbar-color: var(--blue) #f1f1f1;
            position: relative;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        .detail-table-wrapper::-webkit-scrollbar {
            height: 10px;
        }

        .detail-table-wrapper::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 5px;
        }

        .detail-table-wrapper::-webkit-scrollbar-thumb {
            background: var(--blue);
            border-radius: 5px;
        }

        .detail-table-wrapper::-webkit-scrollbar-thumb:hover {
            background: var(--dark-blue);
        }

        /* Styling tabel detail */
        .detail-table {
            width: 100%;
            min-width: 1200px;
            border-collapse: collapse;
            margin: 0;
            display: table;
        }

        .detail-table thead th {
            background: var(--blue) !important;
            color: #fff !important;
            padding: 12px;
            text-align: center;
            font-weight: bold;
            white-space: nowrap;
            position: relative;
            border: 1px solid #ddd;
        }

        .detail-table tbody td {
            padding: 15px;
            text-align: center;
            color: #333;
            border: 1px solid #eee;
            white-space: nowrap;
            font-size: 16px;
        }

        /* Kolom Lokasi yang sticky/fixed */
        .detail-table thead th.sticky-column-detail {
            position: sticky !important;
            left: 0 !important;
            z-index: 20 !important;
            background: var(--blue) !important;
            color: #fff !important;
            min-width: 120px;
            max-width: 120px;
            box-shadow: 2px 0 5px rgba(0,0,0,0.2);
        }

        .detail-table tbody td.sticky-column-detail {
            position: sticky !important;
            left: 0 !important;
            z-index: 15 !important;
            background: #fff !important;
            color: #333 !important;
            min-width: 120px;
            max-width: 120px;
            border-right: 2px solid #ddd;
            text-align: left;
            font-weight: 500;
        }

        .detail-table tbody tr:hover td.sticky-column-detail {
            background: #f9f9f9 !important;
        }

        /* Hover effect */
        .detail-table tbody tr:hover {
            background-color: #f9f9f9;
        }
    </style>
@endsection

