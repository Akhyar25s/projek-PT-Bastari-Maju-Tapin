@extends('layouts.app')

@section('page_title', $statusLabel)

@section('content')
<div class="content-wrapper">
    <div class="content-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <div>
            <h1 class="text-2xl font-bold">{{ $statusLabel }}</h1>
            <p class="text-gray-600 mt-2">Riwayat surat BPP dengan status: <strong>{{ ucfirst(str_replace('_', ' ', $status)) }}</strong></p>
        </div>
        <a href="{{ route('dashboard.keuangan') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
            ← Kembali ke Dashboard
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow">
        @if($bppRows->count() > 0)
        <div style="padding: 20px; border-bottom: 2px solid #f0f0f0;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <span style="font-weight: 600; color: #333;">Total Surat BPP:</span> 
                    <span style="color: #2563eb; font-weight: bold; font-size: 18px;">{{ $totalBpp }}</span>
                </div>
                <div>
                    <span style="font-weight: 600; color: #333;">Total Keseluruhan:</span> 
                    <span style="color: #16a34a; font-weight: bold; font-size: 18px;">Rp {{ number_format($totalKeseluruhan, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <div style="overflow-x: auto;">
            <table class="min-w-full">
                <thead class="bg-blue-500 text-white">
                    <tr>
                        <th class="py-3 px-4 text-left">No BPP</th>
                        <th class="py-3 px-4 text-center">Jumlah Item</th>
                        <th class="py-3 px-4 text-right">Grand Total</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-center">Tanggal</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bppRows as $bpp)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="py-3 px-4">
                            <code style="background:#f4f4f4; padding:4px 8px; border-radius:4px; font-weight:600;">{{ $bpp->no_bukti }}</code>
                        </td>
                        <td class="py-3 px-4 text-center">
                            <span style="background:#007bff; color:#fff; padding:4px 10px; border-radius:12px; font-size:12px; font-weight:bold;">
                                {{ $bpp->item_count }} Item
                            </span>
                        </td>
                        <td class="py-3 px-4 text-right font-semibold" style="color:#16a34a;">
                            Rp {{ number_format($bpp->grand_total, 0, ',', '.') }}
                        </td>
                        <td class="py-3 px-4 text-center">
                            <span class="px-3 py-1 rounded text-sm font-semibold
                                @if($bpp->agg_status === 'pending') bg-yellow-100 text-yellow-800
                                @elseif($bpp->agg_status === 'approved') bg-blue-100 text-blue-800
                                @elseif($bpp->agg_status === 'final_approved') bg-green-100 text-green-800
                                @elseif($bpp->agg_status === 'rejected') bg-red-100 text-red-800
                                @endif">
                                {{ ucfirst(str_replace('_', ' ', $bpp->agg_status)) }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-center">
                            {{ \Carbon\Carbon::parse($bpp->created_at)->format('d/m/Y H:i') }}
                        </td>
                        <td class="py-3 px-4 text-center">
                            <a href="{{ route('order.bpp-detail', $bpp->no_bukti) }}" 
                               class="bg-blue-500 text-white px-3 py-1 rounded text-sm hover:bg-blue-600">
                                Detail
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50 font-bold">
                    <tr>
                        <td colspan="2" class="py-3 px-4 text-right">Total Keseluruhan:</td>
                        <td class="py-3 px-4 text-right text-green-600">Rp {{ number_format($totalKeseluruhan, 0, ',', '.') }}</td>
                        <td colspan="3"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @else
        <div class="py-20 text-center text-gray-500">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
            </svg>
            <p class="mt-4 text-lg">Tidak ada surat BPP dengan status <strong>{{ ucfirst(str_replace('_', ' ', $status)) }}</strong></p>
        </div>
        @endif
    </div>
</div>
@endsection
