@extends('layouts.app')

@section('page_title', 'Barang Rusak')

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

    <div class="table-wrapper" style="border: 8px solid var(--blue); border-radius: 0; padding: 0; position: relative; background: white;">
        <div style="border: 4px solid var(--yellow); margin: 4px; position: relative; background: white;">
            <div style="border: 4px solid var(--accent); margin: 4px; background: white;">
                <div style="padding: 8px;">
                    <table style="width: 100%; border-collapse: collapse; background: white;">
                        <thead>
                            <tr>
                                <th style="width:60px; background: var(--blue); color: #fff; padding: 12px; text-align: left; font-weight: 600;">No</th>
                                <th style="background: var(--blue); color: #fff; padding: 12px; text-align: left; font-weight: 600;">Nama Barang</th>
                                <th style="width:120px; background: var(--blue); color: #fff; padding: 12px; text-align: left; font-weight: 600;">Volume</th>
                                <th style="background: var(--blue); color: #fff; padding: 12px; text-align: left; font-weight: 600;">Keterangan</th>
                                <th style="width:150px; background: var(--blue); color: #fff; padding: 12px; text-align: left; font-weight: 600;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($barangRusak as $index => $item)
                            <tr style="border-bottom: none;">
                                <td style="padding: 12px; text-align: center; color: #333;">{{ $index + 1 }}</td>
                                <td style="padding: 12px; color: #333;">{{ $item->nama_barang }}</td>
                                <td style="padding: 12px; text-align: center; color: #333;">{{ $item->volume }}</td>
                                <td style="padding: 12px; color: #333;">{{ $item->status ?? 'Rusak' }}</td>
                                <td style="padding: 12px; text-align: center;">
                                    <a href="{{ route('barang-rusak.edit', $item->kode_barang) }}" 
                                       class="action-btn" 
                                       style="padding: 6px 12px; margin-right: 5px; font-size: 13px; background: var(--btn); color: white; text-decoration: none; border-radius: 4px; display: inline-block;">
                                        Edit
                                    </a>
                                    <form action="{{ route('barang-rusak.destroy', $item->kode_barang) }}" 
                                          method="POST" 
                                          style="display: inline-block;"
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus barang rusak ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                style="padding: 6px 12px; background-color: #dc3545; color: white; font-size: 13px; border: none; cursor: pointer; border-radius: 4px;">
                                            Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 40px; color: #999;">
                                    Tidak ada data barang rusak
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div style="margin-top: 20px; display: flex; justify-content: flex-end;">
        <a href="{{ route('barang-rusak.create') }}" 
           style="background: #4b4b4b; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; font-weight: 500; display: inline-block;">
            Tambah
        </a>
    </div>

    <style>
        .table-wrapper table tbody tr:hover {
            background-color: #f9f9f9;
        }

        .action-btn:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }
    </style>
@endsection

