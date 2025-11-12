@extends('layouts.app')

@section('page_title', 'Dashboard Admin')

@section('content')
    @php
        $userRole = strtolower(session('role') ?? '');
    @endphp

    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 30px;">
        <!-- Total Barang -->
        <div style="background: white; padding: 20px; border-radius: 8px; border: 2px solid var(--blue);">
            <div style="font-size: 14px; color: #666; margin-bottom: 8px;">Total Barang :</div>
            <div style="font-size: 32px; font-weight: bold; color: var(--blue);">{{ $totalBarang }}</div>
        </div>

        <!-- Transaksi Bulan Ini -->
        <div style="background: white; padding: 20px; border-radius: 8px; border: 2px solid var(--blue);">
            <div style="font-size: 14px; color: #666; margin-bottom: 8px;">Transaksi Bulan ini :</div>
            <div style="font-size: 32px; font-weight: bold; color: var(--blue);">{{ $transaksiBulanIni }}</div>
        </div>

        <!-- PO Pending -->
        <div style="background: white; padding: 20px; border-radius: 8px; border: 2px solid var(--blue);">
            <div style="font-size: 14px; color: #666; margin-bottom: 8px;">PO Pending :</div>
            <div style="font-size: 32px; font-weight: bold; color: var(--blue);">{{ $poPending }}</div>
        </div>
    </div>

    <!-- Tombol Order untuk Admin -->
    @if(in_array($userRole, ['admin', 'perencanaan']))
    <div style="background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px; border: 2px solid var(--accent); text-align: center;">
        <a href="{{ route('order.index') }}" 
           style="padding: 12px 24px; background: var(--btn); color: white; text-decoration: none; border-radius: 8px; font-size: 16px; font-weight: 600; display: inline-block;">
            + Buat Order Baru
        </a>
    </div>
    @endif

    <!-- Status Pesanan -->
    <div style="background: white; padding: 25px; border-radius: 8px; margin-bottom: 30px; border: 2px solid var(--accent);">
        <h3 style="font-size: 20px; font-weight: bold; margin-bottom: 20px; color: #333; padding-bottom: 10px; border-bottom: 2px solid var(--accent);">
            Status Pesanan
        </h3>
        
        <!-- Statistik Status -->
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin-bottom: 20px;">
            <div style="background: #FFF3CD; padding: 15px; border-radius: 6px; text-align: center; border: 2px solid var(--yellow);">
                <div style="font-size: 12px; color: #666; margin-bottom: 5px;">Pending</div>
                <div style="font-size: 24px; font-weight: bold; color: #856404;">{{ $orderPendingCount }}</div>
            </div>
            <div style="background: #D4EDDA; padding: 15px; border-radius: 6px; text-align: center; border: 2px solid #28a745;">
                <div style="font-size: 12px; color: #666; margin-bottom: 5px;">Approved</div>
                <div style="font-size: 24px; font-weight: bold; color: #155724;">{{ $orderApprovedCount }}</div>
            </div>
            <div style="background: #F8D7DA; padding: 15px; border-radius: 6px; text-align: center; border: 2px solid #dc3545;">
                <div style="font-size: 12px; color: #666; margin-bottom: 5px;">Rejected</div>
                <div style="font-size: 24px; font-weight: bold; color: #721c24;">{{ $orderRejectedCount }}</div>
            </div>
        </div>

        <!-- Tabel Status Pesanan -->
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
                    @forelse($statusPesanan as $order)
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 12px; border: 1px solid #eee;">{{ $order->id_order }}</td>
                        <td style="padding: 12px; border: 1px solid #eee;">{{ $order->nama_barang }}</td>
                        <td style="padding: 12px; text-align: center; border: 1px solid #eee;">{{ $order->jumlah }} {{ $order->satuan }}</td>
                        <td style="padding: 12px; text-align: center; border: 1px solid #eee;">
                            @if($order->status === 'pending')
                                <span style="padding: 4px 12px; background: #FFF3CD; color: #856404; border-radius: 12px; font-size: 12px; font-weight: 600;">Pending</span>
                            @elseif($order->status === 'approved')
                                <span style="padding: 4px 12px; background: #D4EDDA; color: #155724; border-radius: 12px; font-size: 12px; font-weight: 600;">Approved</span>
                            @else
                                <span style="padding: 4px 12px; background: #F8D7DA; color: #721c24; border-radius: 12px; font-size: 12px; font-weight: 600;">Rejected</span>
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
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="padding: 40px; text-align: center; color: #999; border: 1px solid #eee;">
                            Tidak ada pesanan
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Link ke halaman status lengkap -->
        <div style="margin-top: 15px; text-align: right;">
            <a href="{{ route('order.status') }}" 
               style="padding: 8px 16px; background: var(--accent); color: #333; text-decoration: none; border-radius: 6px; font-weight: 600; display: inline-block;">
                Lihat Semua Pesanan →
            </a>
        </div>
    </div>

    <!-- Transaksi Terbaru & Aktivitas -->
    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px;">
        <!-- Transaksi Terbaru -->
        <div style="background: white; padding: 25px; border-radius: 8px; border: 2px solid var(--blue);">
            <h3 style="font-size: 20px; font-weight: bold; margin-bottom: 20px; color: #333; padding-bottom: 10px; border-bottom: 2px solid var(--blue);">
                Transaksi Terbaru
            </h3>
            <div style="max-height: 400px; overflow-y: auto;">
                @forelse($transaksiTerbaru as $transaksi)
                <div style="padding: 12px; border-bottom: 1px solid #eee; margin-bottom: 10px;">
                    <div style="font-weight: 600; color: #333; margin-bottom: 5px;">
                        {{ $transaksi->nama_barang }}
                    </div>
                    <div style="font-size: 13px; color: #666;">
                        @if($transaksi->masuk > 0)
                            <span style="color: #28a745;">Masuk: {{ $transaksi->masuk }}</span>
                        @endif
                        @if($transaksi->keluar > 0)
                            <span style="color: #dc3545;">Keluar: {{ $transaksi->keluar }}</span>
                        @endif
                    </div>
                    <div style="font-size: 12px; color: #999; margin-top: 5px;">
                        {{ \Carbon\Carbon::parse($transaksi->tanggal)->format('d/m/Y') }} - {{ $transaksi->alamat }}
                    </div>
                </div>
                @empty
                <div style="padding: 40px; text-align: center; color: #999;">
                    Tidak ada transaksi
                </div>
                @endforelse
            </div>
        </div>

        <!-- Aktivitas -->
        <div style="background: white; padding: 25px; border-radius: 8px; border: 2px solid var(--accent);">
            <h3 style="font-size: 20px; font-weight: bold; margin-bottom: 20px; color: #333; padding-bottom: 10px; border-bottom: 2px solid var(--accent);">
                Aktivitas
            </h3>
            <div style="max-height: 400px; overflow-y: auto;">
                @forelse($aktivitas as $aktifitas)
                <div style="padding: 12px; border-bottom: 1px solid #eee; margin-bottom: 10px;">
                    <div style="font-weight: 600; color: #333; margin-bottom: 5px;">
                        @if($aktifitas->type === 'order')
                            <span style="background: var(--yellow); color: #333; padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: 600;">ORDER</span>
                        @else
                            <span style="background: var(--blue); color: white; padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: 600;">TRANSAKSI</span>
                        @endif
                        {{ $aktifitas->keterangan }}
                    </div>
                    <div style="font-size: 12px; color: #999; margin-top: 5px;">
                        {{ \Carbon\Carbon::parse($aktifitas->tanggal)->format('d/m/Y H:i') }}
                        @if(isset($aktifitas->status))
                            - <span style="color: 
                                @if($aktifitas->status === 'pending') #856404
                                @elseif($aktifitas->status === 'approved') #155724
                                @else #721c24
                                @endif">
                                {{ ucfirst($aktifitas->status) }}
                            </span>
                        @endif
                        @if(isset($aktifitas->lokasi))
                            - {{ $aktifitas->lokasi }}
                        @endif
                    </div>
                </div>
                @empty
                <div style="padding: 40px; text-align: center; color: #999;">
                    Tidak ada aktivitas
                </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection

