@extends('layouts.app')

@section('page_title', 'Invoice Order')

@section('content')
<div class="content-wrapper">
    <div class="content-header" style="margin-bottom: 30px;">
        <h1 class="text-2xl font-bold">Invoice Order #{{ $order->id_order }}</h1>
        <div style="margin-top: 15px;">
            <a href="{{ route('order.status') }}" class="text-blue-500 hover:underline">← Kembali ke Status Order</a>
            <button onclick="window.print()" 
                    style="margin-left: 15px; padding: 8px 16px; background: var(--btn); color: white; border: none; border-radius: 4px; cursor: pointer;">
                🖨️ Cetak Invoice
            </button>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-8" style="max-width: 800px; margin: 0 auto;">
        <!-- Header Invoice -->
        <div style="text-align: center; margin-bottom: 40px; padding-bottom: 20px; border-bottom: 3px solid var(--blue);">
            <h2 style="font-size: 28px; font-weight: bold; color: #333; margin-bottom: 10px;">PT. Bastari Maju</h2>
            <p style="color: #666; font-size: 14px;">Tapin (Perseroda)</p>
            <h3 style="font-size: 20px; font-weight: 600; color: var(--blue); margin-top: 15px;">INVOICE ORDER</h3>
        </div>

        <!-- Info Order -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 30px;">
            <div>
                <h4 style="font-size: 16px; font-weight: bold; color: #333; margin-bottom: 15px; padding-bottom: 8px; border-bottom: 2px solid var(--accent);">
                    Informasi Order
                </h4>
                <table style="width: 100%;">
                    <tr>
                        <td style="padding: 8px 0; color: #666; font-size: 14px; width: 40%;">No. Order:</td>
                        <td style="padding: 8px 0; color: #333; font-size: 14px; font-weight: 600;">#{{ $order->id_order }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; color: #666; font-size: 14px;">Tanggal:</td>
                        <td style="padding: 8px 0; color: #333; font-size: 14px;">
                            {{ \Carbon\Carbon::parse($order->created_at)->format('d F Y') }}
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; color: #666; font-size: 14px;">Status:</td>
                        <td style="padding: 8px 0;">
                            @if($order->status === 'pending')
                                <span style="padding: 4px 12px; background: #FFF3CD; color: #856404; border-radius: 12px; font-size: 12px; font-weight: 600;">Pending</span>
                            @elseif($order->status === 'approved')
                                <span style="padding: 4px 12px; background: #D4EDDA; color: #155724; border-radius: 12px; font-size: 12px; font-weight: 600;">Approved</span>
                            @elseif($order->status === 'final_approved')
                                <span style="padding: 4px 12px; background: #D1ECF1; color: #0c5460; border-radius: 12px; font-size: 12px; font-weight: 600;">Final Approved</span>
                            @else
                                <span style="padding: 4px 12px; background: #F8D7DA; color: #721c24; border-radius: 12px; font-size: 12px; font-weight: 600;">Rejected</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>

            <div>
                <h4 style="font-size: 16px; font-weight: bold; color: #333; margin-bottom: 15px; padding-bottom: 8px; border-bottom: 2px solid var(--accent);">
                    Informasi Pemesan
                </h4>
                <table style="width: 100%;">
                    <tr>
                        <td style="padding: 8px 0; color: #666; font-size: 14px; width: 40%;">Nama:</td>
                        <td style="padding: 8px 0; color: #333; font-size: 14px; font-weight: 600;">{{ $order->nama_pemesan }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; color: #666; font-size: 14px;">Role:</td>
                        <td style="padding: 8px 0; color: #333; font-size: 14px;">
                            <span style="padding: 4px 8px; background: #e7f3ff; color: #0066cc; border-radius: 8px; font-size: 12px;">
                                {{ $order->role_pemesan }}
                            </span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Detail Barang -->
        <div style="margin-bottom: 30px;">
            <h4 style="font-size: 16px; font-weight: bold; color: #333; margin-bottom: 15px; padding-bottom: 8px; border-bottom: 2px solid var(--accent);">
                Detail Barang
            </h4>
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: var(--blue); color: white;">
                        <th style="padding: 12px; text-align: left; border: 1px solid #ddd;">Nama Barang</th>
                        <th style="padding: 12px; text-align: center; border: 1px solid #ddd;">Jumlah</th>
                        <th style="padding: 12px; text-align: center; border: 1px solid #ddd;">Satuan</th>
                        @php $userRole = strtolower(session('role') ?? ''); @endphp
                        @if($userRole === 'keuangan')
                            <th style="padding: 12px; text-align: center; border: 1px solid #ddd;">Harga /unit</th>
                            <th style="padding: 12px; text-align: center; border: 1px solid #ddd;">Subtotal</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="padding: 12px; border: 1px solid #eee; color: #333;">{{ $order->nama_barang }}</td>
                        <td style="padding: 12px; text-align: center; border: 1px solid #eee; color: #333; font-weight: 600;">{{ $order->jumlah }}</td>
                        <td style="padding: 12px; text-align: center; border: 1px solid #eee; color: #333;">{{ $order->satuan }}</td>
                        @if($userRole === 'keuangan')
                            <td style="padding: 12px; text-align: center; border: 1px solid #eee; color: #333;">{{ isset($order->harga_satuan) ? number_format($order->harga_satuan,2,',','.') : '-' }}</td>
                            <td style="padding: 12px; text-align: center; border: 1px solid #eee; color: #333; font-weight: 700;">{{ isset($order->total_harga) ? number_format($order->total_harga,2,',','.') : '-' }}</td>
                        @endif
                    </tr>
                </tbody>
            </table>
            @php $userRole = strtolower(session('role') ?? ''); @endphp
            @if($userRole === 'keuangan')
                <div style="text-align: right; margin-top: 18px; font-size: 15px; font-weight: 600; color: #333;">
                    Total: <span style="color: var(--blue);">Rp {{ isset($order->total_harga) ? number_format($order->total_harga,2,',','.') : number_format(($order->harga_satuan ?? 0) * ($order->jumlah ?? 0),2,',','.') }}</span>
                </div>
            @endif
        </div>

        <!-- Footer -->
        <div style="margin-top: 40px; padding-top: 20px; border-top: 2px solid var(--accent); text-align: center; color: #666; font-size: 12px;">
            <p>Invoice ini dibuat secara otomatis oleh sistem.</p>
            <p style="margin-top: 5px;">Tanggal cetak: {{ \Carbon\Carbon::now()->format('d F Y H:i:s') }}</p>
        </div>
    </div>
</div>

<style>
    @media print {
        .content-header,
        .content-wrapper > *:not(.bg-white) {
            display: none;
        }
        .bg-white {
            box-shadow: none;
            border: none;
        }
    }
</style>
@endsection

