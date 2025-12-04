@extends('layouts.app')

@section('title', 'Detail Surat BPP')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-0"><i class="fas fa-file-invoice me-2"></i>Detail Surat BPP</h3>
                        <small>No BPP: <strong>{{ $no_bukti }}</strong></small>
                    </div>
                    <div>
                        @php
                            $badgeClass = match($aggStatus) {
                                'pending' => 'bg-warning text-dark',
                                'approved' => 'bg-info',
                                'final_approved' => 'bg-success',
                                'rejected' => 'bg-danger',
                                default => 'bg-secondary'
                            };
                            $statusText = ucfirst(str_replace('_',' ', $aggStatus));
                        @endphp
                        <span class="badge {{ $badgeClass }}">Status: {{ $statusText }}</span>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="mb-3">
                        <strong>Jumlah Item:</strong> {{ $orders->count() }}
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>No</th>
                                    <th>Kode Barang</th>
                                    <th>Nama Barang</th>
                                    <th class="text-center">Jumlah</th>
                                    @php $userRole = strtolower(session('role') ?? ''); @endphp
                                    @if($userRole === 'keuangan')
                                        <th class="text-end">Harga Satuan</th>
                                        <th class="text-end">Total Harga</th>
                                    @endif
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $index => $order)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><code>{{ $order->id_barang }}</code></td>
                                    <td>{{ $order->barang->nama_barang ?? '-' }}</td>
                                    <td class="text-center">{{ number_format($order->jumlah,0,',','.') }}</td>
                                    @if($userRole === 'keuangan')
                                        <td class="text-end">{{ $order->harga_satuan ? 'Rp '.number_format($order->harga_satuan,0,',','.') : '-' }}</td>
                                        <td class="text-end">{{ $order->total_harga ? 'Rp '.number_format($order->total_harga,0,',','.') : '-' }}</td>
                                    @endif
                                    <td class="text-center">
                                        <span class="badge {{
                                            $order->status==='pending' ? 'bg-warning text-dark' : (
                                            $order->status==='approved' ? 'bg-info' : (
                                            $order->status==='final_approved' ? 'bg-success' : (
                                            $order->status==='rejected' ? 'bg-danger' : 'bg-secondary'))) }}">
                                            {{ ucfirst(str_replace('_',' ',$order->status ?? 'pending')) }}
                                        </span>
                                        @if(session('role') === 'Admin' || session('role') === 'admin')
                                            <div style="font-size:9px; color:#888; margin-top:2px;">
                                                Raw: <code style="font-size:9px;">{{ $order->status }}</code> (len:{{ strlen($order->status ?? '') }})
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            @if($userRole === 'keuangan' && ($totalBatch ?? 0) > 0)
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="5" class="text-end"><strong>Grand Total:</strong></td>
                                    <td class="text-end"><strong class="text-primary">Rp {{ number_format($totalBatch,0,',','.') }}</strong></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>

                    <div class="mt-3">
                        <a href="javascript:history.back()" class="btn btn-secondary"><i class="fas fa-arrow-left me-2"></i>Kembali</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
