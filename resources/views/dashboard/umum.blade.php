@extends('layouts.app')

@section('page_title', 'Dashboard Umum')

@section('content')
    <div style="background: #d1ecf1; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 2px solid #17a2b8;">
        <strong>Info:</strong> Anda login sebagai Umum. Anda dapat melihat dan memvalidasi (approve/reject) order dari Penjaga Gudang (untuk menambah stok gudang). Order dari Perencanaan harus divalidasi oleh Gudang.
    </div>

    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 30px;">
        <!-- PO Pending -->
        <div style="background: white; padding: 20px; border-radius: 8px; border: 2px solid var(--blue);">
            <div style="font-size: 14px; color: #666; margin-bottom: 8px;">Order Pending :</div>
            <div style="font-size: 32px; font-weight: bold; color: var(--blue);">{{ $poPending }}</div>
        </div>

        <!-- Order Approved -->
        <div style="background: white; padding: 20px; border-radius: 8px; border: 2px solid #28a745;">
            <div style="font-size: 14px; color: #666; margin-bottom: 8px;">Order Approved :</div>
            <div style="font-size: 32px; font-weight: bold; color: #28a745;">{{ $orderApprovedCount }}</div>
        </div>

        <!-- Order Rejected -->
        <div style="background: white; padding: 20px; border-radius: 8px; border: 2px solid #dc3545;">
            <div style="font-size: 14px; color: #666; margin-bottom: 8px;">Order Rejected :</div>
            <div style="font-size: 32px; font-weight: bold; color: #dc3545;">{{ $orderRejectedCount }}</div>
        </div>
    </div>

    <!-- Order yang Perlu Divalidasi -->
    <div style="background: white; padding: 25px; border-radius: 8px; margin-bottom: 30px; border: 2px solid var(--accent);">
        <h3 style="font-size: 20px; font-weight: bold; margin-bottom: 20px; color: #333; padding-bottom: 10px; border-bottom: 2px solid var(--accent);">
            Order yang Perlu Divalidasi
        </h3>
        
        @if(isset($orderPendingBpp) && $orderPendingBpp->count() > 0)
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: var(--accent); color: #333;">
                        <th style="padding: 12px; text-align: left; border: 1px solid #ddd;">Batch ID</th>
                        <th style="padding: 12px; text-align: center; border: 1px solid #ddd;">Jumlah Item</th>
                        <th style="padding: 12px; text-align: center; border: 1px solid #ddd;">Pemesan</th>
                        <th style="padding: 12px; text-align: center; border: 1px solid #ddd;">Tanggal</th>
                        <th style="padding: 12px; text-align: center; border: 1px solid #ddd;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orderPendingBpp as $bpp)
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 12px; border: 1px solid #eee;">
                            <code style="background: #f4f4f4; padding: 4px 8px; border-radius: 4px;">{{ $bpp->no_bukti }}</code>
                        </td>
                        <td style="padding: 12px; text-align: center; border: 1px solid #eee;">
                            <span style="padding: 4px 12px; background: #007bff; color: white; border-radius: 12px; font-weight: bold;">
                                {{ $bpp->item_count }} Item
                            </span>
                        </td>
                        <td style="padding: 12px; text-align: center; border: 1px solid #eee;">
                            <span style="padding: 4px 8px; background: #e7f3ff; color: #0066cc; border-radius: 8px; font-size: 11px;">
                                {{ $bpp->role_pemesan }}
                            </span>
                            <div style="font-size: 12px; color: #666; margin-top: 4px;">
                                {{ $bpp->nama_aktor ?? '-' }}
                            </div>
                        </td>
                        <td style="padding: 12px; text-align: center; border: 1px solid #eee;">
                            {{ \Carbon\Carbon::parse($bpp->created_at)->format('d/m/Y H:i') }}
                        </td>
                        <td style="padding: 12px; text-align: center; border: 1px solid #eee;">
                            <div style="display: flex; gap: 8px; justify-content: center;">
                                <form action="{{ route('order.validate-bpp-umum', $bpp->no_bukti) }}" method="POST" style="display: inline;">
                                    @csrf
                                    <input type="hidden" name="action" value="approve">
                                    <button type="submit" 
                                            onclick="return confirm('Apakah Anda yakin ingin menyetujui surat BPP ini ({{ $bpp->item_count }} item)?')"
                                            style="padding: 6px 12px; background: #28a745; color: white; border: none; border-radius: 4px; font-size: 13px; cursor: pointer;">
                                        Approve
                                    </button>
                                </form>
                                <form action="{{ route('order.validate-bpp-umum', $bpp->no_bukti) }}" method="POST" style="display: inline;">
                                    @csrf
                                    <input type="hidden" name="action" value="reject">
                                    <button type="submit" 
                                            onclick="return confirm('Apakah Anda yakin ingin menolak surat BPP ini ({{ $bpp->item_count }} item)?')"
                                            style="padding: 6px 12px; background: #dc3545; color: white; border: none; border-radius: 4px; font-size: 13px; cursor: pointer;">
                                        Reject
                                    </button>
                                </form>
                                <a href="{{ route('order.bpp-detail', $bpp->no_bukti) }}" 
                                   style="padding: 6px 12px; background: var(--btn); color: white; text-decoration: none; border-radius: 4px; font-size: 13px; display: inline-block;">
                                    Detail Batch
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div style="padding: 40px; text-align: center; color: #999; border: 1px solid #eee; border-radius: 8px;">
            Tidak ada order yang perlu divalidasi
        </div>
        @endif
    </div>
@endsection

