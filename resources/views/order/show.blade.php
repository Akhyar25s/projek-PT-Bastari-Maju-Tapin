@extends('layouts.app')

@section('page_title', 'Detail Order')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <h1 class="text-2xl font-bold">Detail Order</h1>
        <a href="{{ route('order.status') }}" class="text-blue-500 hover:underline">← Kembali</a>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <dl class="grid grid-cols-2 gap-4">
            <dt class="font-bold">ID Order:</dt>
            <dd>{{ $order->id_order }}</dd>
            
            <dt class="font-bold">Nama Barang:</dt>
            <dd>{{ $order->nama_barang }}</dd>
            
            <dt class="font-bold">Satuan:</dt>
            <dd>{{ $order->satuan }}</dd>
            
            <dt class="font-bold">Jumlah:</dt>
            <dd>{{ $order->jumlah }}</dd>
            
            <dt class="font-bold">Status:</dt>
            <dd>
                <span class="px-2 py-1 rounded text-sm
                    @if($order->status === 'pending') bg-yellow-100 text-yellow-800
                    @elseif($order->status === 'approved') bg-green-100 text-green-800
                    @elseif($order->status === 'rejected') bg-red-100 text-red-800
                    @endif">
                    {{ ucfirst($order->status) }}
                </span>
            </dd>
            
            <dt class="font-bold">Tanggal Dibuat:</dt>
            <dd>{{ $order->created_at ? \Carbon\Carbon::parse($order->created_at)->format('d/m/Y H:i') : '-' }}</dd>
            
            <dt class="font-bold">Tanggal Diupdate:</dt>
            <dd>{{ $order->updated_at ? \Carbon\Carbon::parse($order->updated_at)->format('d/m/Y H:i') : '-' }}</dd>
        </dl>
    </div>
</div>
@endsection

