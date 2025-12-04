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
        // Data dasar
        $data = $this->getDashboardData();

        // Group per BPP (no_bukti) yang pending dari Perencanaan untuk divalidasi oleh Gudang
        $bppPendingPerencanaan = DB::table('order')
            ->join('pengguna', 'order.id_aktor', '=', 'pengguna.id_aktor')
            ->join('role', 'pengguna.id_role', '=', 'role.id_role')
            ->where('order.status', 'pending')
            ->whereNotNull('order.no_bukti')
            ->where('role.nama_role', 'Perencanaan')
            ->select(
                'order.no_bukti',
                DB::raw('MIN(order.created_at) as created_at'),
                DB::raw('COUNT(*) as item_count')
            )
            ->groupBy('order.no_bukti')
            ->orderBy('created_at', 'desc')
            ->get();

        $data['bppPendingPerencanaan'] = $bppPendingPerencanaan;
        $data['bppPendingPerencanaanCount'] = $bppPendingPerencanaan->count();

        // Status per BPP agregat (distinct no_bukti) untuk statistik
        $bppAgg = DB::table('order')
            ->whereNotNull('no_bukti')
            ->select(
                'no_bukti',
                DB::raw("SUM(CASE WHEN status='pending' THEN 1 ELSE 0 END) as pending_count"),
                DB::raw("SUM(CASE WHEN status='approved' THEN 1 ELSE 0 END) as approved_count"),
                DB::raw("SUM(CASE WHEN status='final_approved' THEN 1 ELSE 0 END) as final_count"),
                DB::raw("SUM(CASE WHEN status='rejected' THEN 1 ELSE 0 END) as rejected_count"),
                DB::raw('COUNT(*) as item_total')
            )
            ->groupBy('no_bukti')
            ->get()
            ->map(function($r){
                $itemCount = $r->item_total;
                if ($r->final_count == $itemCount && $itemCount > 0) $r->agg_status = 'final_approved';
                elseif ($r->rejected_count > 0) $r->agg_status = 'rejected';
                elseif ($r->pending_count > 0) $r->agg_status = 'pending';
                else $r->agg_status = 'approved';
                return $r;
            });

        $data['orderPendingCount'] = $bppAgg->where('agg_status','pending')->count();
        $data['orderApprovedCount'] = $bppAgg->where('agg_status','approved')->count();
        $data['orderFinalApprovedCount'] = $bppAgg->where('agg_status','final_approved')->count();
        $data['orderRejectedCount'] = $bppAgg->where('agg_status','rejected')->count();

        // Ambil daftar BPP terbaru (limit 10) untuk tabel status (tanpa harga)
        $data['statusPesananBpp'] = $bppAgg->sortByDesc('agg_status')->take(10); // bisa diganti urutan waktu jika perlu

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
        // Group by batch_id untuk menampilkan batch order
        // Grouping berdasarkan no_bukti (surat BPP) bukan batch_id
        $orderPendingBpp = DB::table('order')
            ->join('pengguna', 'order.id_aktor', '=', 'pengguna.id_aktor')
            ->join('aktor', 'order.id_aktor', '=', 'aktor.id_aktor')
            ->join('role', 'pengguna.id_role', '=', 'role.id_role')
            ->where('order.status', 'pending')
            ->whereNotNull('order.id_aktor')
            ->whereIn('role.nama_role', ['Penjaga Gudang', 'penjaga gudang', 'pejaga gudang'])
            ->whereNotNull('order.no_bukti')
            ->select(
                'order.no_bukti',
                DB::raw('MIN(order.created_at) as created_at'),
                DB::raw('COUNT(*) as item_count'),
                'aktor.nama_aktor as nama_aktor',
                'role.nama_role as role_pemesan'
            )
            ->groupBy('order.no_bukti', 'aktor.nama_aktor', 'role.nama_role')
            ->orderBy('created_at', 'desc')
            ->get();

        // Hitung total pending surat BPP
        $poPending = $orderPendingBpp->count();

        // Statistik Order - Hanya dari Gudang (per batch)
        $orderStats = DB::table('order')
            ->join('pengguna', 'order.id_aktor', '=', 'pengguna.id_aktor')
            ->join('role', 'pengguna.id_role', '=', 'role.id_role')
            ->whereNotNull('order.id_aktor')
            ->whereNotNull('order.no_bukti')
            ->whereIn('role.nama_role', ['Penjaga Gudang', 'penjaga gudang', 'pejaga gudang'])
            ->select('order.status', DB::raw('COUNT(DISTINCT order.no_bukti) as total'))
            ->groupBy('order.status')
            ->get()
            ->pluck('total', 'status')
            ->toArray();

        $orderPendingCount = $orderStats['pending'] ?? 0;
        $orderApprovedCount = $orderStats['approved'] ?? 0;
        $orderRejectedCount = $orderStats['rejected'] ?? 0;

        return view('dashboard.umum', compact(
            'poPending',
            'orderPendingBpp',
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
        // Group order saya per surat BPP
        $myBpp = DB::table('order')
            ->where('id_aktor', session('id_aktor'))
            ->whereNotNull('no_bukti')
            ->select(
                'no_bukti',
                DB::raw('MIN(created_at) as created_at'),
                DB::raw('COUNT(*) as item_count'),
                DB::raw("SUM(CASE WHEN status='pending' THEN 1 ELSE 0 END) as pending_count"),
                DB::raw("SUM(CASE WHEN status='approved' THEN 1 ELSE 0 END) as approved_count"),
                DB::raw("SUM(CASE WHEN status='final_approved' THEN 1 ELSE 0 END) as final_count"),
                DB::raw("SUM(CASE WHEN status='rejected' THEN 1 ELSE 0 END) as rejected_count")
            )
            ->groupBy('no_bukti')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($r){
                $totalItems = $r->item_count;
                if ($r->final_count == $totalItems && $totalItems > 0) $r->agg_status = 'final_approved';
                elseif ($r->rejected_count > 0) $r->agg_status = 'rejected';
                elseif ($r->pending_count > 0) $r->agg_status = 'pending';
                else $r->agg_status = 'approved';
                return $r;
            });

        // Statistik per BPP (distinct BPP per status)
        $myOrderPending = $myBpp->where('agg_status','pending')->count();
        $myOrderApproved = $myBpp->where('agg_status','approved')->count();
        $myOrderFinalApproved = $myBpp->where('agg_status','final_approved')->count();
        $myOrderRejected = $myBpp->where('agg_status','rejected')->count();

        return view('dashboard.perencanaan', compact(
            'myBpp',
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
        // Kelompokkan per surat BPP (no_bukti) untuk tampilan keuangan
        $bppRows = DB::table('order')
            ->whereNotNull('no_bukti')
            ->select(
                DB::raw('TRIM(no_bukti) as no_bukti'),
                DB::raw('MIN(created_at) as created_at'),
                DB::raw('COUNT(*) as item_count'),
                DB::raw("SUM(CASE WHEN LOWER(TRIM(status))='pending' THEN 1 ELSE 0 END) as pending_count"),
                DB::raw("SUM(CASE WHEN LOWER(TRIM(status))='approved' THEN 1 ELSE 0 END) as approved_count"),
                DB::raw("SUM(CASE WHEN LOWER(TRIM(status))='final_approved' THEN 1 ELSE 0 END) as final_count"),
                DB::raw("SUM(CASE WHEN LOWER(TRIM(status))='rejected' THEN 1 ELSE 0 END) as rejected_count"),
                DB::raw('SUM(COALESCE(total_harga,0)) as grand_total'),
                DB::raw("SUM(CASE WHEN LOWER(TRIM(status)) NOT IN ('pending','approved','final_approved','rejected') THEN 1 ELSE 0 END) as unknown_count")
            )
            ->groupBy(DB::raw('TRIM(no_bukti)'))
            ->orderBy('created_at', 'desc')
            ->get();

        // Hitung status agregat & siapkan list BPP yang perlu final approve
        $bppRows = $bppRows->map(function($r){
            $itemCount = $r->item_count ?? 0;
            $status = 'approved';
            if (($r->final_count ?? 0) === $itemCount && $itemCount > 0) {
                $status = 'final_approved';
            } elseif (($r->rejected_count ?? 0) > 0) {
                $status = 'rejected';
            } elseif (($r->pending_count ?? 0) > 0) {
                $status = 'pending';
            } elseif (($r->approved_count ?? 0) > 0) {
                $status = 'approved';
            } elseif (($r->unknown_count ?? 0) > 0) {
                $status = 'unknown';
            }
            $r->agg_status = $status;
            return $r;
        });

        // BPP yang siap divalidasi final:
        // - Harus ada minimal 1 item approved/pending (belum semua final/reject)
        // - Exclude BPP yang sudah semua item final_approved atau semua rejected
        $bppForFinal = $bppRows->filter(function($r){
            $hasApprovedOrPending = ($r->approved_count ?? 0) > 0 || ($r->pending_count ?? 0) > 0;
            $notAllFinalized = ($r->final_count ?? 0) < ($r->item_count ?? 0);
            return $hasApprovedOrPending && $notAllFinalized;
        });

        // Statistik agregat per BPP (distinct surat BPP per status)
        $orderPendingCount = $bppRows->where('agg_status','pending')->count();
        $orderApprovedCount = $bppRows->where('agg_status','approved')->count();
        $orderRejectedCount = $bppRows->where('agg_status','rejected')->count();
        $orderFinalApprovedCount = $bppRows->where('agg_status','final_approved')->count();
        $orderUnknownCount = $bppRows->where('agg_status','unknown')->count();

        return view('dashboard.keuangan', compact(
            'bppForFinal',
            'orderPendingCount',
            'orderApprovedCount',
            'orderRejectedCount',
            'orderFinalApprovedCount',
            'orderUnknownCount'
        ));
    }
}
