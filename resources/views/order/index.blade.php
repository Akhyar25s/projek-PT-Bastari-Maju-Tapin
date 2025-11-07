@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <h1 class="text-2xl font-bold">Order Stok</h1>
    </div>

    @if($errors->has('order'))
        <div style="background-color: #f8d7da; color: #721c24; padding: 12px; border-radius: 4px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
            <strong>Error:</strong>
            <ul style="margin: 5px 0 0 20px;">
                @foreach($errors->get('order') as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('error'))
        <div style="background-color: #f8d7da; color: #721c24; padding: 12px; border-radius: 4px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
            {{ session('error') }}
        </div>
    @endif

    <div class="search-bar bg-white p-4 rounded-lg shadow mb-4">
        <div class="flex justify-between items-center">
            <div class="flex-1 mr-4">
                <form action="{{ route('order.index') }}" method="GET" class="flex items-center">
                    <input type="text" name="search" placeholder="Ketik nama/kode..." 
                           class="w-full p-2 border rounded" 
                           value="{{ request('search') }}">
                    <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded ml-2">Cari</button>
                </form>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('order.status') }}" class="bg-gray-500 text-white px-4 py-2 rounded">Status Pesanan</a>
                @if(function_exists('canOrder') && canOrder())
                <button type="submit" form="orderForm" class="bg-blue-500 text-white px-4 py-2 rounded">Order</button>
                @endif
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow">
        <form id="orderForm" action="{{ route('order.store') }}" method="POST">
            @csrf
            <table class="min-w-full">
                <thead class="bg-blue-500 text-white">
                    <tr>
                        <th class="py-3 px-4 text-left">No Kode</th>
                        <th class="py-3 px-4 text-left">Nama Barang</th>
                        <th class="py-3 px-4 text-left">Satuan</th>
                        <th class="py-3 px-4 text-left">Stok</th>
                        <th class="py-3 px-4 text-left">Sisa</th>
                        <th class="py-3 px-4 text-left">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($barang as $item)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="py-3 px-4">{{ $item->kode_barang }}</td>
                        <td class="py-3 px-4">{{ $item->nama_barang }}</td>
                        <td class="py-3 px-4">{{ $item->satuan }}</td>
                        <td class="py-3 px-4">{{ $item->stok }}</td>
                        <td class="py-3 px-4">{{ $item->sisa }}</td>
                        <td class="py-3 px-4">
                            @if(function_exists('canOrder') && canOrder())
                            <div class="flex items-center space-x-2">
                                <input type="hidden" name="barang_id[]" value="{{ $item->kode_barang }}">
                                <button type="button" class="decrease-qty bg-gray-200 px-3 py-1 rounded" onclick="decreaseQuantity(this)">-</button>
                                <input type="number" name="quantity[]" value="0" min="0" max="{{ $item->stok }}" 
                                       class="w-16 text-center border rounded py-1 px-2 quantity-input"
                                       data-stok="{{ $item->stok }}"
                                       onchange="checkStok(this)">
                                <button type="button" class="increase-qty bg-gray-200 px-3 py-1 rounded" onclick="increaseQuantity(this)">+</button>
                                <span class="text-xs text-gray-500" style="margin-left: 5px;">Max: {{ $item->stok }}</span>
                            </div>
                            @else
                            <span class="text-gray-400 text-sm">-</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </form>
    </div>
</div>

@push('scripts')
<script>
function decreaseQuantity(button) {
    const input = button.parentElement.querySelector('.quantity-input');
    if (input.value > 0) {
        input.value = parseInt(input.value) - 1;
        checkStok(input);
    }
}

function increaseQuantity(button) {
    const input = button.parentElement.querySelector('.quantity-input');
    const max = parseInt(input.getAttribute('max'));
    if (parseInt(input.value) < max) {
        input.value = parseInt(input.value) + 1;
        checkStok(input);
    }
}

function checkStok(input) {
    const value = parseInt(input.value) || 0;
    const max = parseInt(input.getAttribute('data-stok'));
    
    if (value > max) {
        alert('Stok tidak mencukupi! Stok tersedia: ' + max);
        input.value = max;
    }
    
    // Update max attribute jika stok berubah
    if (value > 0) {
        input.style.borderColor = value > max ? '#dc3545' : '#28a745';
    } else {
        input.style.borderColor = '';
    }
}
</script>
@endpush
@endsection