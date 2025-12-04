@extends('layouts.app')

@section('page_title', 'Dashboard Keuangan')

@section('content')
    @if(session('success'))
        <div style="background:#d4edda; color:#155724; padding:12px 16px; border:2px solid #28a745; border-radius:6px; margin-bottom:18px; font-weight:600;">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div style="background:#f8d7da; color:#721c24; padding:12px 16px; border:2px solid #dc3545; border-radius:6px; margin-bottom:18px; font-weight:600;">
            {{ session('error') }}
        </div>
    @endif
    <div style="background: #fff3cd; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 2px solid var(--yellow);">
        <strong>Info:</strong> Anda login sebagai Keuangan. Anda dapat melihat dan memvalidasi laporan orderan yang sudah di-approve oleh Umum (birokrasi akhir).
    </div>

    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 30px;">
        <!-- Order Pending -->
        <a href="{{ route('order.by-status', 'pending') }}" style="text-decoration: none; color: inherit; transition: transform 0.2s;">
            <div style="background: white; padding: 20px; border-radius: 8px; border: 2px solid var(--yellow); cursor: pointer; transition: all 0.3s;" 
                 onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 6px 20px rgba(0,0,0,0.15)';" 
                 onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                <div style="font-size: 14px; color: #666; margin-bottom: 8px;">Order Pending :</div>
                <div style="font-size: 32px; font-weight: bold; color: #856404;">{{ $orderPendingCount }}</div>
                <div style="font-size: 12px; color: #999; margin-top: 8px;">Klik untuk lihat detail →</div>
            </div>
        </a>

        <!-- Order Approved (by Umum) -->
        <a href="{{ route('order.by-status', 'approved') }}" style="text-decoration: none; color: inherit; transition: transform 0.2s;">
            <div style="background: white; padding: 20px; border-radius: 8px; border: 2px solid #17a2b8; cursor: pointer; transition: all 0.3s;" 
                 onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 6px 20px rgba(0,0,0,0.15)';" 
                 onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                <div style="font-size: 14px; color: #666; margin-bottom: 8px;">Approved (Umum) :</div>
                <div style="font-size: 32px; font-weight: bold; color: #17a2b8;">{{ $orderApprovedCount }}</div>
                <div style="font-size: 12px; color: #999; margin-top: 8px;">Klik untuk lihat detail →</div>
            </div>
        </a>

        <!-- Order Final Approved -->
        <a href="{{ route('order.by-status', 'final_approved') }}" style="text-decoration: none; color: inherit; transition: transform 0.2s;">
            <div style="background: white; padding: 20px; border-radius: 8px; border: 2px solid #28a745; cursor: pointer; transition: all 0.3s;" 
                 onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 6px 20px rgba(0,0,0,0.15)';" 
                 onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                <div style="font-size: 14px; color: #666; margin-bottom: 8px;">Final Approved :</div>
                <div style="font-size: 32px; font-weight: bold; color: #28a745;">{{ $orderFinalApprovedCount }}</div>
                <div style="font-size: 12px; color: #999; margin-top: 8px;">Klik untuk lihat detail →</div>
            </div>
        </a>

        <!-- Order Rejected -->
        <a href="{{ route('order.by-status', 'rejected') }}" style="text-decoration: none; color: inherit; transition: transform 0.2s;">
            <div style="background: white; padding: 20px; border-radius: 8px; border: 2px solid #dc3545; cursor: pointer; transition: all 0.3s;" 
                 onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 6px 20px rgba(0,0,0,0.15)';" 
                 onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                <div style="font-size: 14px; color: #666; margin-bottom: 8px;">Order Rejected :</div>
                <div style="font-size: 32px; font-weight: bold; color: #dc3545;">{{ $orderRejectedCount }}</div>
                <div style="font-size: 12px; color: #999; margin-top: 8px;">Klik untuk lihat detail →</div>
            </div>
        </a>
    </div>
    @if(isset($orderUnknownCount) && $orderUnknownCount > 0)
        <div style="background:#ffeeba; color:#856404; padding:10px 14px; border:2px dashed #ffc107; border-radius:6px; margin-bottom:22px; font-size:13px;">
            Peringatan: Terdapat {{ $orderUnknownCount }} surat BPP dengan status item tidak dikenali (mungkin ada spasi / huruf besar berbeda). Silakan normalisasi status.
        </div>
    @endif

    <!-- Surat BPP yang Perlu Final Validasi -->
    <div style="background: white; padding: 25px; border-radius: 8px; margin-bottom: 30px; border: 2px solid var(--accent);">
        <h3 style="font-size: 20px; font-weight: bold; margin-bottom: 20px; color: #333; padding-bottom: 10px; border-bottom: 2px solid var(--accent);">
            Surat BPP Perlu Final Validasi
        </h3>
        @if($bppForFinal->count() > 0)
        <div style="overflow-x:auto;">
            <table style="width:100%; border-collapse:collapse;">
                <thead>
                    <tr style="background: var(--accent); color:#333;">
                        <th style="padding:12px; border:1px solid #ddd; text-align:left;">No BPP</th>
                        <th style="padding:12px; border:1px solid #ddd; text-align:center;">Jumlah Item</th>
                        <th style="padding:12px; border:1px solid #ddd; text-align:right;">Grand Total</th>
                        <th style="padding:12px; border:1px solid #ddd; text-align:center;">Status</th>
                        <th style="padding:12px; border:1px solid #ddd; text-align:center;">Tanggal</th>
                        <th style="padding:12px; border:1px solid #ddd; text-align:center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bppForFinal as $bpp)
                    <tr style="border-bottom:1px solid #eee;">
                        <td style="padding:12px; border:1px solid #eee;"><code style="background:#f4f4f4; padding:4px 8px; border-radius:4px;">{{ $bpp->no_bukti }}</code></td>
                        <td style="padding:12px; border:1px solid #eee; text-align:center;">
                            <span style="background:#007bff; color:#fff; padding:4px 10px; border-radius:12px; font-size:12px; font-weight:bold;">{{ $bpp->item_count }} Item</span>
                        </td>
                        <td style="padding:12px; border:1px solid #eee; text-align:right; font-weight:bold; color:#006400;">
                            Rp {{ number_format($bpp->grand_total,0,',','.') }}
                        </td>
                        <td style="padding:12px; border:1px solid #eee; text-align:center;">
                            @php
                                $status = $bpp->agg_status;
                                $badgeColor = match($status) {
                                    'pending' => 'background:#fff3cd; color:#856404;',
                                    'approved' => 'background:#d1ecf1; color:#0c5460;',
                                    'final_approved' => 'background:#d4edda; color:#155724;',
                                    'rejected' => 'background:#f8d7da; color:#721c24;',
                                    default => 'background:#e2e3e5; color:#383d41;'
                                };
                            @endphp
                            <span style="padding:4px 10px; border-radius:12px; font-size:12px; {{ $badgeColor }}">{{ ucfirst(str_replace('_',' ',$status)) }}</span>
                        </td>
                        <td style="padding:12px; border:1px solid #eee; text-align:center;">
                            {{ \Carbon\Carbon::parse($bpp->created_at)->format('d/m/Y H:i') }}
                        </td>
                        <td style="padding:12px; border:1px solid #eee; text-align:center;">
                            <div style="display:flex; gap:6px; justify-content:center; flex-wrap:wrap;">
                                <a href="{{ route('order.bpp-detail', $bpp->no_bukti) }}" style="padding:6px 12px; background:var(--btn); color:#fff; text-decoration:none; border-radius:4px; font-size:12px;">Detail</a>
                                <form action="{{ route('order.validate-bpp-keuangan', $bpp->no_bukti) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <input type="hidden" name="action" value="approve">
                                    <button type="submit" onclick="return confirm('Final approve seluruh item dalam BPP ini? Stok akan dikurangi jika asal Gudang.')" style="padding:6px 12px; background:#28a745; color:#fff; border:none; border-radius:4px; font-size:12px; cursor:pointer;">Final Approve</button>
                                </form>
                                <form action="{{ route('order.validate-bpp-keuangan', $bpp->no_bukti) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <input type="hidden" name="action" value="reject">
                                    <button type="submit" onclick="return confirm('Tolak seluruh item dalam BPP ini?')" style="padding:6px 12px; background:#dc3545; color:#fff; border:none; border-radius:4px; font-size:12px; cursor:pointer;">Reject</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div style="padding:40px; text-align:center; color:#999; border:1px solid #eee; border-radius:8px;">Tidak ada surat BPP yang perlu final validasi</div>
        @endif
    </div>
@endsection

