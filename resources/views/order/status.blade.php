@extends('layouts.app')

@section('page_title', 'Status Pesanan')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <h1 class="text-2xl font-bold">Status Pesanan</h1>
    </div>

    <div class="bg-white rounded-lg shadow">
        <table class="min-w-full">
            <thead class="bg-blue-500 text-white">
                <tr>
                    <th class="py-3 px-4 text-left">No BPP</th>
                    <th class="py-3 px-4 text-left">Jumlah Item</th>
                    @php $userRole = strtolower(session('role') ?? ''); @endphp
                    @if($userRole === 'keuangan')
                        <th class="py-3 px-4 text-left">Grand Total</th>
                    @endif
                    <th class="py-3 px-4 text-left">Status</th>
                    <th class="py-3 px-4 text-left">Tanggal</th>
                    <th class="py-3 px-4 text-left">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bpps as $row)
                <tr class="border-b hover:bg-gray-50">
                    <td class="py-3 px-4"><code>{{ $row->no_bukti }}</code></td>
                    <td class="py-3 px-4">{{ $row->item_count }}</td>
                    @if($userRole === 'keuangan')
                        <td class="py-3 px-4">{{ isset($row->grand_total) ? number_format($row->grand_total,2,',','.') : '-' }}</td>
                    @endif
                    <td class="py-3 px-4">
                        <span class="px-2 py-1 rounded text-sm
                            @if(($row->agg_status ?? 'pending') === 'pending') bg-yellow-100 text-yellow-800
                            @elseif(($row->agg_status ?? '') === 'approved') bg-green-100 text-green-800
                            @elseif(($row->agg_status ?? '') === 'final_approved') bg-blue-100 text-blue-800
                            @elseif(($row->agg_status ?? '') === 'rejected') bg-red-100 text-red-800
                            @endif">
                            {{ ucfirst(str_replace('_', ' ', $row->agg_status ?? 'pending')) }}
                        </span>
                    </td>
                    <td class="py-3 px-4">{{ $row->created_at ? \Carbon\Carbon::parse($row->created_at)->format('d/m/Y H:i') : '-' }}</td>
                    <td class="py-3 px-4">
                        <div style="display: flex; gap: 10px;">
                            <a href="{{ route('order.bpp-detail', $row->no_bukti) }}" class="text-blue-500 hover:underline">Detail</a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-3 px-4 text-center text-gray-500">Tidak ada surat BPP</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection