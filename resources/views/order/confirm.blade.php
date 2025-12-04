@extends('layouts.app')

@section('page_title', 'Konfirmasi Order')

@section('content')
    @if(session('success'))
        <div style="background-color: #d4edda; color: #155724; padding: 12px; border-radius: 4px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div style="background-color: #f8d7da; color: #721c24; padding: 12px; border-radius: 4px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
            {{ session('error') }}
        </div>
    @endif

    <div style="background: white; padding: 30px; border-radius: 8px; border: 2px solid var(--blue);">
        <h2 style="font-size: 24px; font-weight: bold; margin-bottom: 25px; color: #333; padding-bottom: 10px; border-bottom: 3px solid var(--blue);">
            Konfirmasi Order
        </h2>

        <div style="margin-bottom: 30px;">
            <table style="width: 100%; border-collapse: collapse; background: white;">
                <thead>
                    <tr style="background: var(--blue); color: #fff;">
                        <th style="padding: 12px; text-align: left; font-size: 14px; font-weight: bold; border: 1px solid #ddd;">Kode</th>
                        <th style="padding: 12px; text-align: left; font-size: 14px; font-weight: bold; border: 1px solid #ddd;">Nama Barang</th>
                        <th style="padding: 12px; text-align: center; font-size: 14px; font-weight: bold; border: 1px solid #ddd;">Jumlah Orderan</th>
                        @php $userRole = strtolower(session('role') ?? ''); @endphp
                        @if($userRole === 'keuangan')
                            <th style="padding: 12px; text-align: center; font-size: 14px; font-weight: bold; border: 1px solid #ddd;">Harga /unit</th>
                            <th style="padding: 12px; text-align: center; font-size: 14px; font-weight: bold; border: 1px solid #ddd;">Subtotal</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 12px; color: #333; font-size: 14px; border: 1px solid #eee;">{{ $order->kode_barang }}</td>
                        <td style="padding: 12px; color: #333; font-size: 14px; border: 1px solid #eee;">{{ $order->nama_barang }}</td>
                        <td style="padding: 12px; text-align: center; color: #333; font-size: 14px; border: 1px solid #eee;">{{ $order->jumlah }} {{ $order->satuan }}</td>
                        @if($userRole === 'keuangan')
                            <td style="padding: 12px; text-align: center; color: #333; font-size: 14px; border: 1px solid #eee;">{{ isset($order->harga_satuan) ? number_format($order->harga_satuan,2,',','.') : '-' }}</td>
                            <td style="padding: 12px; text-align: center; color: #333; font-size: 14px; border: 1px solid #eee; font-weight: 600;">{{ isset($order->total_harga) ? number_format($order->total_harga,2,',','.') : '-' }}</td>
                        @endif
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" style="text-align: center; padding: 40px; color: #999;">
                            Tidak ada order
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            @php
                // Hitung grand total hanya untuk role keuangan
                $userRole = strtolower(session('role') ?? '');
                $grandTotal = 0;
                if ($userRole === 'keuangan') {
                    $grandTotal = $orders->sum(function($o){ return $o->total_harga ?? 0; });
                }
            @endphp
            @if($userRole === 'keuangan')
                <div style="text-align: right; margin-top: 20px; font-size: 15px; font-weight: 600; color: #333;">
                    Total Keseluruhan: <span style="color: var(--blue);">Rp {{ number_format($grandTotal,2,',','.') }}</span>
                </div>
            @endif
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 15px; margin-top: 30px;">
            <form action="{{ route('order.cancel') }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" 
                        style="padding: 12px 24px; background: #dc3545; color: white; border: none; border-radius: 6px; font-size: 15px; font-weight: 600; cursor: pointer;">
                    Batal
                </button>
            </form>
            <a href="{{ route('order.confirm-form') }}" 
               style="padding: 12px 24px; background: #6c757d; color: white; text-decoration: none; border-radius: 6px; font-size: 15px; font-weight: 600; display: inline-block;">
                Konfirmasi
            </a>
        </div>
    </div>
@endsection

