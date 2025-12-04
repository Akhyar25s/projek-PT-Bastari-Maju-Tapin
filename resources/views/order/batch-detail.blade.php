@extends('layouts.app')

@section('title', 'Detail Batch Order')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0">
                        <i class="fas fa-box-open me-2"></i>Detail Batch Order
                    </h3>
                    <small>Batch ID: <strong>{{ $batch_id }}</strong></small>
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

                    <!-- Info Batch -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="info-card bg-light p-3 rounded">
                                <h6 class="text-muted mb-2"><i class="fas fa-cubes me-2"></i>Total Item</h6>
                                <h4 class="mb-0">{{ $orders->count() }} Item</h4>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-card bg-light p-3 rounded">
                                <h6 class="text-muted mb-2"><i class="fas fa-user me-2"></i>Dibuat Oleh</h6>
                                <h5 class="mb-0">{{ $orders->first()->aktor->nama_aktor ?? '-' }}</h5>
                                <small class="text-muted">{{ $orders->first()->aktor->role ?? '-' }}</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-card bg-light p-3 rounded">
                                <h6 class="text-muted mb-2"><i class="fas fa-info-circle me-2"></i>Status</h6>
                                @php
                                    $status = $orders->first()->status_order;
                                    $badgeClass = match($status) {
                                        'pending' => 'bg-warning text-dark',
                                        'approved' => 'bg-info',
                                        'final_approved' => 'bg-success',
                                        'rejected' => 'bg-danger',
                                        default => 'bg-secondary'
                                    };
                                    $statusText = match($status) {
                                        'pending' => 'Menunggu',
                                        'approved' => 'Disetujui',
                                        'final_approved' => 'Final Disetujui',
                                        'rejected' => 'Ditolak',
                                        default => $status
                                    };
                                @endphp
                                <h5 class="mb-0">
                                    <span class="badge {{ $badgeClass }}">{{ $statusText }}</span>
                                </h5>
                            </div>
                        </div>
                    </div>

                    <!-- Tabel Item -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="12%">Kode Barang</th>
                                    <th width="25%">Nama Barang</th>
                                    <th width="8%" class="text-center">Jumlah</th>
                                    @php
                                        $userRole = strtolower(session('role') ?? '');
                                    @endphp
                                    @if($userRole === 'keuangan')
                                        <th width="12%" class="text-end">Harga Satuan</th>
                                        <th width="12%" class="text-end">Total Harga</th>
                                    @endif
                                    <th width="12%" class="text-center">Status</th>
                                    <th width="8%" class="text-center">Tipe</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $index => $order)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><code>{{ $order->id_barang }}</code></td>
                                    <td>{{ $order->barang->nama_barang ?? '-' }}</td>
                                    <td class="text-center"><strong>{{ number_format($order->jumlah, 0, ',', '.') }}</strong></td>
                                    @if($userRole === 'keuangan')
                                        <td class="text-end">
                                            @if($order->harga_satuan)
                                                Rp {{ number_format($order->harga_satuan, 0, ',', '.') }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            @if($order->total_harga)
                                                <strong>Rp {{ number_format($order->total_harga, 0, ',', '.') }}</strong>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    @endif
                                    <td class="text-center">
                                        @php
                                            $itemStatus = strtolower($order->status ?? '-');
                                            $itemBadgeClass = match($itemStatus) {
                                                'pending' => 'bg-warning text-dark',
                                                'approved' => 'bg-info',
                                                'final_approved' => 'bg-success',
                                                'rejected' => 'bg-danger',
                                                default => 'bg-secondary'
                                            };
                                            $itemStatusText = match($itemStatus) {
                                                'pending' => 'Pending',
                                                'approved' => 'Approved',
                                                'final_approved' => 'Final Approved',
                                                'rejected' => 'Rejected',
                                                default => ucfirst($itemStatus)
                                            };
                                        @endphp
                                        <span class="badge {{ $itemBadgeClass }}">{{ $itemStatusText }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge {{ $order->tipe_rekap == 'sr' ? 'bg-success' : 'bg-warning text-dark' }}">
                                            {{ strtoupper($order->tipe_rekap) }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            @if($userRole === 'keuangan' && $totalBatch > 0)
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="5" class="text-end"><strong>Grand Total:</strong></td>
                                    <td class="text-end">
                                        <strong class="text-primary fs-5">Rp {{ number_format($totalBatch, 0, ',', '.') }}</strong>
                                    </td>
                                    <td colspan="2"></td>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>

                    <!-- Tombol Aksi Batch -->
                    @php
                        $currentStatus = $orders->first()->status_order;
                        $creatorRole = strtolower($orders->first()->aktor->role ?? '');
                    @endphp

                    @if($currentStatus === 'pending')
                        <!-- Umum bisa approve/reject order dari Gudang -->
                        @if($userRole === 'umum' && $creatorRole === 'penjaga gudang')
                            <div class="d-flex gap-2 mt-4">
                                <form action="{{ route('order.validate-batch-umum', $batch_id) }}" method="POST" class="flex-fill">
                                    @csrf
                                    <input type="hidden" name="action" value="approve">
                                    <button type="submit" class="btn btn-success w-100" onclick="return confirm('Apakah Anda yakin ingin menyetujui seluruh batch ini ({{ $orders->count() }} item)?')">
                                        <i class="fas fa-check-circle me-2"></i>Setujui Batch
                                    </button>
                                </form>
                                <form action="{{ route('order.validate-batch-umum', $batch_id) }}" method="POST" class="flex-fill">
                                    @csrf
                                    <input type="hidden" name="action" value="reject">
                                    <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Apakah Anda yakin ingin menolak seluruh batch ini ({{ $orders->count() }} item)?')">
                                        <i class="fas fa-times-circle me-2"></i>Tolak Batch
                                    </button>
                                </form>
                            </div>
                        @endif

                        <!-- Gudang bisa approve/reject order dari Perencanaan -->
                        @if($userRole === 'penjaga gudang' && $creatorRole === 'perencanaan')
                            <div class="d-flex gap-2 mt-4">
                                <form action="{{ route('order.validate-batch-gudang', $batch_id) }}" method="POST" class="flex-fill">
                                    @csrf
                                    <input type="hidden" name="action" value="approve">
                                    <button type="submit" class="btn btn-success w-100" onclick="return confirm('Apakah Anda yakin ingin menyetujui seluruh batch ini ({{ $orders->count() }} item)?')">
                                        <i class="fas fa-check-circle me-2"></i>Setujui Batch
                                    </button>
                                </form>
                                <form action="{{ route('order.validate-batch-gudang', $batch_id) }}" method="POST" class="flex-fill">
                                    @csrf
                                    <input type="hidden" name="action" value="reject">
                                    <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Apakah Anda yakin ingin menolak seluruh batch ini ({{ $orders->count() }} item)?')">
                                        <i class="fas fa-times-circle me-2"></i>Tolak Batch
                                    </button>
                                </form>
                            </div>
                        @endif
                    @endif

                    @if($currentStatus === 'approved')
                        <!-- Keuangan bisa final approve/reject order dari Gudang -->
                        @if($userRole === 'keuangan' && $creatorRole === 'penjaga gudang')
                            <div class="d-flex gap-2 mt-4">
                                <form action="{{ route('order.validate-batch-keuangan', $batch_id) }}" method="POST" class="flex-fill">
                                    @csrf
                                    <input type="hidden" name="action" value="approve">
                                    <button type="submit" class="btn btn-success w-100" onclick="return confirm('Apakah Anda yakin ingin menyetujui final seluruh batch ini ({{ $orders->count() }} item)? Stok akan dikurangi.')">
                                        <i class="fas fa-check-double me-2"></i>Final Approve Batch
                                    </button>
                                </form>
                                <form action="{{ route('order.validate-batch-keuangan', $batch_id) }}" method="POST" class="flex-fill">
                                    @csrf
                                    <input type="hidden" name="action" value="reject">
                                    <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Apakah Anda yakin ingin menolak seluruh batch ini ({{ $orders->count() }} item)?')">
                                        <i class="fas fa-times-circle me-2"></i>Tolak Batch
                                    </button>
                                </form>
                            </div>
                        @endif
                    @endif

                    <!-- Tombol Kembali -->
                    <div class="mt-4">
                        <a href="javascript:history.back()" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.info-card {
    transition: transform 0.2s;
}
.info-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
</style>
@endsection
