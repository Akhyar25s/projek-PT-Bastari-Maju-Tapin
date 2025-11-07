@extends('layouts.app')

@section('page_title', 'Detail Rekap SR')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <h1 class="text-2xl font-bold">Detail Rekap SR</h1>
        <a href="{{ route('rekap.sr.index') }}" class="text-blue-500 hover:underline">← Kembali</a>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <dl class="grid grid-cols-2 gap-4">
            <dt class="font-bold">Rantau:</dt>
            <dd>{{ $item->rantau }}</dd>
            
            <dt class="font-bold">Binuang:</dt>
            <dd>{{ $item->binuang }}</dd>
            
            <dt class="font-bold">Tap Sel:</dt>
            <dd>{{ $item->{'tap sel'} }}</dd>
            
            <dt class="font-bold">CLU:</dt>
            <dd>{{ $item->clu }}</dd>
            
            <dt class="font-bold">CLS:</dt>
            <dd>{{ $item->cls }}</dd>
            
            <dt class="font-bold">Tap Tengah:</dt>
            <dd>{{ $item->{'tap tengah'} }}</dd>
            
            <dt class="font-bold">Batu Hapu:</dt>
            <dd>{{ $item->{'batu hapu'} }}</dd>
            
            <dt class="font-bold">Bakarangan:</dt>
            <dd>{{ $item->bakarangan }}</dd>
            
            <dt class="font-bold">Lokpaikat:</dt>
            <dd>{{ $item->lokpaikat }}</dd>
            
            <dt class="font-bold">Sel:</dt>
            <dd>{{ $item->sel }}</dd>
            
            <dt class="font-bold">Jumlah:</dt>
            <dd class="text-xl font-bold text-blue-600">{{ $item->jumlah }}</dd>
        </dl>
    </div>
</div>
@endsection

