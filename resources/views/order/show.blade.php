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
                    @elseif($order->status === 'final_approved') bg-blue-100 text-blue-800
                    @elseif($order->status === 'rejected') bg-red-100 text-red-800
                    @endif">
                    {{ ucfirst(str_replace('_', ' ', $order->status ?? 'pending')) }}
                </span>
            </dd>
            
            <dt class="font-bold">Tanggal Dibuat:</dt>
            <dd>{{ $order->created_at ? \Carbon\Carbon::parse($order->created_at)->format('d/m/Y H:i') : '-' }}</dd>
            
            <dt class="font-bold">Tanggal Diupdate:</dt>
            <dd>{{ $order->updated_at ? \Carbon\Carbon::parse($order->updated_at)->format('d/m/Y H:i') : '-' }}</dd>
        </dl>

        @php
            $userRole = strtolower(session('role') ?? '');
            $idAktor = session('id_aktor');
            $canViewInvoice = false;
            if (in_array($userRole, ['admin', 'penjaga gudang', 'pejaga gudang'])) {
                $canViewInvoice = true;
            } elseif ($userRole === 'perencanaan' && $order->id_aktor == $idAktor) {
                $canViewInvoice = true;
            }
        @endphp

        @if($canViewInvoice)
        <div style="margin-top: 30px; padding-top: 20px; border-top: 2px solid #e0e0e0;">
            <a href="{{ route('order.invoice', $order->id_order) }}" 
               target="_blank"
               style="padding: 12px 24px; background: var(--btn); color: white; text-decoration: none; border-radius: 6px; font-size: 15px; font-weight: 600; display: inline-block;">
                📄 Lihat Invoice
            </a>
        </div>
        @endif
    </div>
</div>
@endsection

