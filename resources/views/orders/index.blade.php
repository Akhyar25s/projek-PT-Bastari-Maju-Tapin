@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <h1 class="text-2xl font-bold">Order Stok</h1>
    </div>

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
                <button type="submit" form="orderForm" class="bg-blue-500 text-white px-4 py-2 rounded">Order</button>
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
                            <div class="flex items-center space-x-2">
                                <input type="hidden" name="barang_id[]" value="{{ $item->id }}">
                                <button type="button" class="decrease-qty bg-gray-200 px-3 py-1 rounded" onclick="decreaseQuantity(this)">-</button>
                                <input type="number" name="quantity[]" value="0" min="0" max="{{ $item->sisa }}" 
                                       class="w-16 text-center border rounded py-1 px-2 quantity-input">
                                <button type="button" class="increase-qty bg-gray-200 px-3 py-1 rounded" onclick="increaseQuantity(this)">+</button>
                            </div>
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
    }
}

function increaseQuantity(button) {
    const input = button.parentElement.querySelector('.quantity-input');
    const max = parseInt(input.getAttribute('max'));
    if (parseInt(input.value) < max) {
        input.value = parseInt(input.value) + 1;
    }
}
</script>
@endpush
@endsection