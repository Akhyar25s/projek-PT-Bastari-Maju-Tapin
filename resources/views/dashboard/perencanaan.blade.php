@extends('layouts.app')

@section('page_title', 'Dashboard Perencanaan')

@section('content')
    @php
        $userRole = strtolower(session('role') ?? '');
    @endphp

    <div style="background: #d4edda; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 2px solid #28a745;">
        <strong>Info:</strong> Anda login sebagai Perencanaan. Anda hanya dapat mengorder barang.
    </div>

    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 30px;">
        <!-- Order Pending -->
        <div style="background: white; padding: 20px; border-radius: 8px; border: 2px solid var(--yellow);">
            <div style="font-size: 14px; color: #666; margin-bottom: 8px;">Order Pending :</div>
            <div style="font-size: 32px; font-weight: bold; color: #856404;">{{ $myOrderPending }}</div>
        </div>

        <!-- Order Approved -->
        <div style="background: white; padding: 20px; border-radius: 8px; border: 2px solid #28a745;">
            <div style="font-size: 14px; color: #666; margin-bottom: 8px;">Order Approved :</div>
            <div style="font-size: 32px; font-weight: bold; color: #28a745;">{{ $myOrderApproved }}</div>
        </div>

        <!-- Order Final Approved -->
        <div style="background: white; padding: 20px; border-radius: 8px; border: 2px solid #17a2b8;">
            <div style="font-size: 14px; color: #666; margin-bottom: 8px;">Final Approved :</div>
            <div style="font-size: 32px; font-weight: bold; color: #17a2b8;">{{ $myOrderFinalApproved }}</div>
        </div>

        <!-- Order Rejected -->
        <div style="background: white; padding: 20px; border-radius: 8px; border: 2px solid #dc3545;">
            <div style="font-size: 14px; color: #666; margin-bottom: 8px;">Order Rejected :</div>
            <div style="font-size: 32px; font-weight: bold; color: #dc3545;">{{ $myOrderRejected }}</div>
        </div>
    </div>

    <!-- Tombol Order Barang -->
    @if(in_array($userRole, ['admin', 'perencanaan']))
    <div style="background: white; padding: 25px; border-radius: 8px; margin-bottom: 30px; border: 2px solid var(--accent); text-align: center;">
        <a href="{{ route('order.index') }}" 
           style="padding: 12px 24px; background: var(--btn); color: white; text-decoration: none; border-radius: 8px; font-size: 16px; font-weight: 600; display: inline-block;">
            + Buat Order Baru
        </a>
    </div>
    @endif

    <!-- Daftar Order Saya -->
    <div style="background: white; padding: 25px; border-radius: 8px; margin-bottom: 30px; border: 2px solid var(--accent);">
        <h3 style="font-size: 20px; font-weight: bold; margin-bottom: 20px; color: #333; padding-bottom: 10px; border-bottom: 2px solid var(--accent);">
            Daftar Order Saya
        </h3>
        
        @if($myOrders->count() > 0)
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: var(--accent); color: #333;">
                        <th style="padding: 12px; text-align: left; border: 1px solid #ddd;">No Order</th>
                        <th style="padding: 12px; text-align: left; border: 1px solid #ddd;">Nama Barang</th>
                        <th style="padding: 12px; text-align: center; border: 1px solid #ddd;">Jumlah</th>
                        <th style="padding: 12px; text-align: center; border: 1px solid #ddd;">Status</th>
                        <th style="padding: 12px; text-align: center; border: 1px solid #ddd;">Tanggal</th>
                        <th style="padding: 12px; text-align: center; border: 1px solid #ddd;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($myOrders as $order)
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 12px; border: 1px solid #eee;">{{ $order->id_order }}</td>
                        <td style="padding: 12px; border: 1px solid #eee;">{{ $order->nama_barang }}</td>
                        <td style="padding: 12px; text-align: center; border: 1px solid #eee;">{{ $order->jumlah }} {{ $order->satuan }}</td>
                        <td style="padding: 12px; text-align: center; border: 1px solid #eee;">
                            @if($order->status === 'pending')
                                <span style="padding: 4px 12px; background: #FFF3CD; color: #856404; border-radius: 12px; font-size: 12px; font-weight: 600;">Pending</span>
                            @elseif($order->status === 'approved')
                                <span style="padding: 4px 12px; background: #D4EDDA; color: #155724; border-radius: 12px; font-size: 12px; font-weight: 600;">Approved</span>
                            @elseif($order->status === 'final_approved')
                                <span style="padding: 4px 12px; background: #D1ECF1; color: #0c5460; border-radius: 12px; font-size: 12px; font-weight: 600;">Final Approved</span>
                            @elseif($order->status === 'rejected')
                                <span style="padding: 4px 12px; background: #F8D7DA; color: #721c24; border-radius: 12px; font-size: 12px; font-weight: 600;">Rejected</span>
                            @else
                                <span style="padding: 4px 12px; background: #E2E3E5; color: #383d41; border-radius: 12px; font-size: 12px; font-weight: 600;">{{ ucfirst(str_replace('_', ' ', $order->status ?? 'unknown')) }}</span>
                            @endif
                        </td>
                        <td style="padding: 12px; text-align: center; border: 1px solid #eee;">
                            {{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y H:i') }}
                        </td>
                        <td style="padding: 12px; text-align: center; border: 1px solid #eee;">
                            <a href="{{ route('orders.show', $order->id_order) }}" 
                               style="padding: 6px 12px; background: var(--btn); color: white; text-decoration: none; border-radius: 4px; font-size: 13px; display: inline-block;">
                                Detail
                            </a>
                            @if($order->status === 'pending')
                            <form action="{{ route('order.cancel') }}" method="POST" style="display: inline; margin-left: 8px;">
                                @csrf
                                <input type="hidden" name="id_order" value="{{ $order->id_order }}">
                                <button type="submit" 
                                        onclick="return confirm('Apakah Anda yakin ingin membatalkan order ini?')"
                                        style="padding: 6px 12px; background: #dc3545; color: white; border: none; border-radius: 4px; font-size: 13px; cursor: pointer;">
                                    Batal
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div style="padding: 40px; text-align: center; color: #999; border: 1px solid #eee; border-radius: 8px;">
            Belum ada order. <a href="{{ route('order.index') }}" style="color: var(--btn);">Buat order baru</a>
        </div>
        @endif
    </div>
@endsection

