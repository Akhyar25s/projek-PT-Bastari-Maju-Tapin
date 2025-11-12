<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Dashboard index - Redirect ke dashboard sesuai role
     * Method ini digunakan sebagai fallback jika user mengakses /dashboard
     */
    public function index()
    {
        // Ambil role dari session
        $userRole = session('role');
        
        // Jika role tidak ada, coba ambil dari database
        if (!$userRole && session('id_aktor')) {
            $roleData = DB::table('pengguna')
                ->join('role', 'pengguna.id_role', '=', 'role.id_role')
                ->select('role.nama_role')
                ->where('pengguna.id_aktor', session('id_aktor'))
                ->first();
            
            if ($roleData) {
                $userRole = $roleData->nama_role;
                session(['role' => $userRole]);
            }
        }
        
        // Redirect ke dashboard sesuai role
        $redirectRoute = $this->getDashboardRouteByRole($userRole);
        
        if ($redirectRoute) {
            return redirect()->route($redirectRoute);
        }
        
        // Fallback: gunakan data dashboard default jika role tidak dikenali
        $data = $this->getDashboardData();
        return view('dashboard.index', $data);
    }

    /**
     * Helper method untuk mendapatkan route dashboard berdasarkan role
     * 
     * @param string|null $role
     * @return string|null
     */
    private function getDashboardRouteByRole($role)
    {
        if (!$role) {
            return null;
        }

        // Menggunakan if-elseif eksplisit untuk konsistensi dengan LoginController
        // Urutan penting: Admin dulu, lalu yang lain
        if ($role === 'Admin') {
            return 'dashboard.admin';
        } elseif ($role === 'Penjaga Gudang' || $role === 'Penjaga gudang') {
            return 'dashboard.gudang';
        } elseif ($role === 'Direktur') {
            return 'dashboard.direktur';
        } elseif ($role === 'Keuangan') {
            return 'dashboard.keuangan';
        } elseif ($role === 'Umum') {
            return 'dashboard.umum';
        } elseif ($role === 'Perencanaan') {
            return 'dashboard.perencanaan';
        }

        return null;
    }

    /**
     * Dashboard untuk Admin - Akses Penuh
     */
    public function admin()
    {
        // Admin memiliki akses penuh, gunakan data yang sama dengan index
        $data = $this->getDashboardData();
        return view('dashboard.admin', $data);
    }

    /**
     * Dashboard untuk Pejaga Gudang - Akses Penuh + Validasi Order dari Perencanaan
     */
    public function gudang()
    {
        // Pejaga gudang memiliki akses penuh, gunakan data yang sama dengan index
        $data = $this->getDashboardData();
        
        // Tambahkan order dari Perencanaan yang perlu divalidasi
        $orderFromPerencanaan = DB::table('order')
            ->join('barang', 'order.id_barang', '=', 'barang.kode_barang')
            ->join('pengguna', 'order.id_aktor', '=', 'pengguna.id_aktor')
            ->join('role', 'pengguna.id_role', '=', 'role.id_role')
            ->where('order.status', 'pending')
            ->whereNotNull('order.id_aktor')
            ->where('role.nama_role', 'Perencanaan')
            ->select('order.*', 'barang.nama_barang', 'barang.satuan', 'role.nama_role as role_pemesan')
            ->orderBy('order.created_at', 'desc')
            ->get();
        
        $data['orderFromPerencanaan'] = $orderFromPerencanaan;
        $data['orderFromPerencanaanCount'] = $orderFromPerencanaan->count();
        
        return view('dashboard.gudang', $data);
    }
    
    /**
     * Helper method untuk mendapatkan data dashboard
     */
    private function getDashboardData()
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
        $orderFinalApprovedCount = $orderStats['final_approved'] ?? 0;
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

        return compact(
            'totalBarang',
            'transaksiBulanIni',
            'poPending',
            'statusPesanan',
            'orderPendingCount',
            'orderApprovedCount',
            'orderRejectedCount',
            'transaksiTerbaru',
            'aktivitas'
        );
    }

    /**
     * Dashboard untuk Direktur - Read Only
     */
    public function direktur()
    {
        // Direktur hanya bisa melihat data (read-only)
        $totalBarang = DB::table('barang')->count();

        $transaksiBulanIni = DB::table('detail_barang')
            ->whereYear('tanggal', Carbon::now()->year)
            ->whereMonth('tanggal', Carbon::now()->month)
            ->count();

        $poPending = DB::table('order')
            ->where('status', 'pending')
            ->count();

        // Status Pesanan (untuk tabel) - read only
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
        $orderFinalApprovedCount = $orderStats['final_approved'] ?? 0;
        $orderRejectedCount = $orderStats['rejected'] ?? 0;

        // Transaksi Terbaru
        $transaksiTerbaru = DB::table('detail_barang')
            ->join('barang', 'detail_barang.kode_barang', '=', 'barang.kode_barang')
            ->select('detail_barang.*', 'barang.nama_barang')
            ->orderBy('detail_barang.tanggal', 'desc')
            ->orderBy('detail_barang.detail_barang', 'desc')
            ->limit(5)
            ->get();

        // Aktivitas Terbaru
        $aktivitas = collect();
        
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

        $aktivitas = $orders->merge($transactions)
            ->sortByDesc('tanggal')
            ->take(10)
            ->values();

        return view('dashboard.direktur', compact(
            'totalBarang',
            'transaksiBulanIni',
            'poPending',
            'statusPesanan',
            'orderPendingCount',
            'orderApprovedCount',
            'orderRejectedCount',
            'transaksiTerbaru',
            'aktivitas',
            'orderFinalApprovedCount'
        ));
    }

    /**
     * Dashboard untuk Umum - Hanya melihat dan memvalidasi order dari Gudang
     */
    public function umum()
    {
        // Umum hanya bisa melihat dan memvalidasi order dari Penjaga Gudang (bukan Perencanaan)
        $poPending = DB::table('order')
            ->join('pengguna', 'order.id_aktor', '=', 'pengguna.id_aktor')
            ->join('role', 'pengguna.id_role', '=', 'role.id_role')
            ->where('order.status', 'pending')
            ->whereIn('role.nama_role', ['Penjaga Gudang', 'penjaga gudang', 'pejaga gudang'])
            ->count();

        // Order yang perlu divalidasi (pending) - Hanya dari Gudang
        $orderPending = DB::table('order')
            ->join('barang', 'order.id_barang', '=', 'barang.kode_barang')
            ->join('pengguna', 'order.id_aktor', '=', 'pengguna.id_aktor')
            ->join('role', 'pengguna.id_role', '=', 'role.id_role')
            ->where('order.status', 'pending')
            ->whereNotNull('order.id_aktor') // Pastikan order memiliki id_aktor
            ->whereIn('role.nama_role', ['Penjaga Gudang', 'penjaga gudang', 'pejaga gudang']) // Hanya order dari Gudang
            ->select('order.*', 'barang.nama_barang', 'barang.satuan', 'role.nama_role as role_pemesan')
            ->orderBy('order.created_at', 'desc')
            ->get();

        // Statistik Order - Hanya dari Gudang
        $orderStats = DB::table('order')
            ->join('pengguna', 'order.id_aktor', '=', 'pengguna.id_aktor')
            ->join('role', 'pengguna.id_role', '=', 'role.id_role')
            ->whereNotNull('order.id_aktor') // Pastikan order memiliki id_aktor
            ->whereIn('role.nama_role', ['Penjaga Gudang', 'penjaga gudang', 'pejaga gudang']) // Hanya order dari Gudang
            ->select('order.status', DB::raw('count(*) as total'))
            ->groupBy('order.status')
            ->get()
            ->pluck('total', 'status')
            ->toArray();

        $orderPendingCount = $orderStats['pending'] ?? 0;
        $orderApprovedCount = $orderStats['approved'] ?? 0;
        $orderRejectedCount = $orderStats['rejected'] ?? 0;

        return view('dashboard.umum', compact(
            'poPending',
            'orderPending',
            'orderPendingCount',
            'orderApprovedCount',
            'orderRejectedCount'
        ));
    }

    /**
     * Dashboard untuk Perencanaan - Hanya bisa mengorder barang
     */
    public function perencanaan()
    {
        // Perencanaan hanya bisa mengorder barang
        $myOrders = DB::table('order')
            ->join('barang', 'order.id_barang', '=', 'barang.kode_barang')
            ->where('order.id_aktor', session('id_aktor'))
            ->whereNotNull('order.id_aktor') // Pastikan order memiliki id_aktor
            ->select('order.*', 'barang.nama_barang', 'barang.satuan')
            ->orderBy('order.created_at', 'desc')
            ->get();

        // Statistik order saya - Include final_approved
        $myOrderStats = DB::table('order')
            ->where('id_aktor', session('id_aktor'))
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get()
            ->pluck('total', 'status')
            ->toArray();

        $myOrderPending = $myOrderStats['pending'] ?? 0;
        $myOrderApproved = $myOrderStats['approved'] ?? 0;
        $myOrderFinalApproved = $myOrderStats['final_approved'] ?? 0;
        $myOrderRejected = $myOrderStats['rejected'] ?? 0;

        return view('dashboard.perencanaan', compact(
            'myOrders',
            'myOrderPending',
            'myOrderApproved',
            'myOrderFinalApproved',
            'myOrderRejected'
        ));
    }

    /**
     * Dashboard untuk Keuangan - Bisa melihat dan memvalidasi laporan orderan
     */
    public function keuangan()
    {
        // Keuangan bisa melihat dan memvalidasi laporan orderan dari umum (birokrasi akhir)
        // Order yang sudah di-approve oleh umum dan perlu validasi akhir dari keuangan
        $orderApprovedByUmum = DB::table('order')
            ->join('barang', 'order.id_barang', '=', 'barang.kode_barang')
            ->join('pengguna', 'order.id_aktor', '=', 'pengguna.id_aktor')
            ->join('role', 'pengguna.id_role', '=', 'role.id_role')
            ->where('order.status', 'approved')
            ->whereNotNull('order.id_aktor') // Pastikan order memiliki id_aktor
            ->whereIn('role.nama_role', ['Penjaga Gudang', 'Perencanaan'])
            ->select('order.*', 'barang.nama_barang', 'barang.satuan', 'role.nama_role as role_pemesan')
            ->orderBy('order.created_at', 'desc')
            ->get();

        // Statistik Order
        $orderStats = DB::table('order')
            ->join('pengguna', 'order.id_aktor', '=', 'pengguna.id_aktor')
            ->join('role', 'pengguna.id_role', '=', 'role.id_role')
            ->whereNotNull('order.id_aktor') // Pastikan order memiliki id_aktor
            ->whereIn('role.nama_role', ['Penjaga Gudang', 'Perencanaan'])
            ->select('order.status', DB::raw('count(*) as total'))
            ->groupBy('order.status')
            ->get()
            ->pluck('total', 'status')
            ->toArray();

        $orderPendingCount = $orderStats['pending'] ?? 0;
        $orderApprovedCount = $orderStats['approved'] ?? 0;
        $orderRejectedCount = $orderStats['rejected'] ?? 0;
        $orderFinalApprovedCount = $orderStats['final_approved'] ?? 0;

        return view('dashboard.keuangan', compact(
            'orderApprovedByUmum',
            'orderPendingCount',
            'orderApprovedCount',
            'orderRejectedCount',
            'orderFinalApprovedCount'
        ));
    }
}
