@extends('layouts.app')

@section('page_title', 'Rekap SR')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <h1 class="text-2xl font-bold">Rekap SR</h1>
    </div>

    <div class="bg-white rounded-lg shadow">
        <table class="min-w-full">
            <thead class="bg-blue-500 text-white">
                <tr>
                    <th class="py-3 px-4 text-left">Bulan</th>
                    <th class="py-3 px-4 text-left">Rantau</th>
                    <th class="py-3 px-4 text-left">Binuang</th>
                    <th class="py-3 px-4 text-left">Tap Sel</th>
                    <th class="py-3 px-4 text-left">CLU</th>
                    <th class="py-3 px-4 text-left">CLS</th>
                    <th class="py-3 px-4 text-left">Tap Tengah</th>
                    <th class="py-3 px-4 text-left">Batu Hapu</th>
                    <th class="py-3 px-4 text-left">Bakarangan</th>
                    <th class="py-3 px-4 text-left">Lokpaikat</th>
                    <th class="py-3 px-4 text-left">Sel</th>
                    <th class="py-3 px-4 text-left">Jumlah</th>
                    <th class="py-3 px-4 text-left">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rekap as $item)
                <tr class="border-b hover:bg-gray-50">
                    <td class="py-3 px-4">{{ $item->nama_bulan }}</td>
                    <td class="py-3 px-4">{{ $item->rantau }}</td>
                    <td class="py-3 px-4">{{ $item->binuang }}</td>
                    <td class="py-3 px-4">{{ $item->{'tap sel'} }}</td>
                    <td class="py-3 px-4">{{ $item->clu }}</td>
                    <td class="py-3 px-4">{{ $item->cls }}</td>
                    <td class="py-3 px-4">{{ $item->{'tap tengah'} }}</td>
                    <td class="py-3 px-4">{{ $item->{'batu hapu'} }}</td>
                    <td class="py-3 px-4">{{ $item->bakarangan }}</td>
                    <td class="py-3 px-4">{{ $item->lokpaikat }}</td>
                    <td class="py-3 px-4">{{ $item->sel }}</td>
                    <td class="py-3 px-4 font-bold">{{ $item->jumlah }}</td>
                    <td class="py-3 px-4">
                        <a href="{{ route('rekap.sr.show', $item->sr) }}" class="text-blue-500 hover:underline">Detail</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="13" class="py-3 px-4 text-center text-gray-500">Tidak ada data rekap SR</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

