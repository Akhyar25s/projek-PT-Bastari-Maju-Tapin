<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Total Barang
        $totalBarang = DB::table('barang')->count();

        // Transaksi Bulan Ini (dari detail_barang)
        $transaksiBulanIni = DB::table('detail_barang')
            ->whereYear('tanggal', Carbon::now()->year)
            ->whereMonth('tanggal', Carbon::now()->month)
            ->count();

        // PO Pending (order dengan status pending)
        $poPending = DB::table('order')
            ->where('status', 'pending')
            ->count();

        // Status Pesanan (untuk tabel)
        $statusPesanan = DB::table('order')
            ->join('barang', 'order.id_barang', '=', 'barang.kode_barang')
            ->select('order.*', 'barang.nama_barang', 'barang.satuan')
            ->orderBy('order.created_at', 'desc')
            ->limit(10)
            ->get();

        // Statistik Status Order
        $orderStats = DB::table('order')
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get()
            ->pluck('total', 'status')
            ->toArray();

        $orderPendingCount = $orderStats['pending'] ?? 0;
        $orderApprovedCount = $orderStats['approved'] ?? 0;
        $orderRejectedCount = $orderStats['rejected'] ?? 0;

        // Transaksi Terbaru (dari detail_barang)
        $transaksiTerbaru = DB::table('detail_barang')
            ->join('barang', 'detail_barang.kode_barang', '=', 'barang.kode_barang')
            ->select('detail_barang.*', 'barang.nama_barang')
            ->orderBy('detail_barang.tanggal', 'desc')
            ->orderBy('detail_barang.detail_barang', 'desc')
            ->limit(5)
            ->get();

        // Aktivitas Terbaru (gabungan dari order dan detail_barang)
        $aktivitas = collect();
        
        // Ambil order terbaru
        $orders = DB::table('order')
            ->join('barang', 'order.id_barang', '=', 'barang.kode_barang')
            ->select('order.*', 'barang.nama_barang')
            ->orderBy('order.created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function($item) {
                return (object) [
                    'type' => 'order',
                    'tanggal' => Carbon::parse($item->created_at)->format('Y-m-d H:i:s'),
                    'keterangan' => "Order {$item->nama_barang} - {$item->jumlah} unit",
                    'status' => $item->status
                ];
            });

        // Ambil transaksi terbaru
        $transactions = DB::table('detail_barang')
            ->join('barang', 'detail_barang.kode_barang', '=', 'barang.kode_barang')
            ->select('detail_barang.*', 'barang.nama_barang')
            ->orderBy('detail_barang.tanggal', 'desc')
            ->orderBy('detail_barang.detail_barang', 'desc')
            ->limit(5)
            ->get()
            ->map(function($item) {
                $type = $item->masuk > 0 ? 'masuk' : 'keluar';
                return (object) [
                    'type' => 'transaksi',
                    'tanggal' => Carbon::parse($item->tanggal)->format('Y-m-d H:i:s'),
                    'keterangan' => "Transaksi {$item->nama_barang} - {$type} " . ($item->masuk > 0 ? $item->masuk : $item->keluar) . " unit",
                    'lokasi' => $item->alamat
                ];
            });

        // Gabungkan dan urutkan
        $aktivitas = $orders->merge($transactions)
            ->sortByDesc('tanggal')
            ->take(10)
            ->values();

        return view('dashboard.index', compact(
            'totalBarang',
            'transaksiBulanIni',
            'poPending',
            'statusPesanan',
            'orderPendingCount',
            'orderApprovedCount',
            'orderRejectedCount',
            'transaksiTerbaru',
            'aktivitas'
        ));
    }
}
