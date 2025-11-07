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
                    <th class="py-3 px-4 text-left">No Order</th>
                    <th class="py-3 px-4 text-left">Nama Barang</th>
                    <th class="py-3 px-4 text-left">Jumlah</th>
                    <th class="py-3 px-4 text-left">Status</th>
                    <th class="py-3 px-4 text-left">Tanggal</th>
                    <th class="py-3 px-4 text-left">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                <tr class="border-b hover:bg-gray-50">
                    <td class="py-3 px-4">{{ $order->id_order }}</td>
                    <td class="py-3 px-4">{{ $order->nama_barang }}</td>
                    <td class="py-3 px-4">{{ $order->jumlah }}</td>
                    <td class="py-3 px-4">
                        <span class="px-2 py-1 rounded text-sm
                            @if($order->status === 'pending') bg-yellow-100 text-yellow-800
                            @elseif($order->status === 'approved') bg-green-100 text-green-800
                            @elseif($order->status === 'rejected') bg-red-100 text-red-800
                            @endif">
                            {{ ucfirst($order->status) }}
                        </span>
                    </td>
                    <td class="py-3 px-4">{{ $order->created_at ? \Carbon\Carbon::parse($order->created_at)->format('d/m/Y H:i') : '-' }}</td>
                    <td class="py-3 px-4">
                        <a href="{{ route('orders.show', $order->id_order) }}" class="text-blue-500 hover:underline">Detail</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-3 px-4 text-center text-gray-500">Tidak ada order</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection