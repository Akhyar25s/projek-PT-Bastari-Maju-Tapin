<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Barang::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_barang', 'like', "%{$search}%")
                  ->orWhere('kode_barang', 'like', "%{$search}%");
            });
        }

        $barang = $query->get();

        return view('order.index', compact('barang'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'barang_id' => 'required|array',
            'quantity' => 'required|array',
            'barang_id.*' => 'exists:barang,kode_barang',
            'quantity.*' => 'numeric|min:0',
            'tipe_rekap' => 'required|in:sr,gm'
        ]);

        // Server-side: pastikan jumlah barang_id dan quantity sama
        if (count($request->input('barang_id', [])) !== count($request->input('quantity', []))) {
            return redirect()->route('order.index')->with('error', 'Data barang dan quantity tidak sesuai.');
        }

        // Ambil id_aktor dari session
        $idAktor = session('id_aktor');
        $orderIds = [];
        
        // Generate batch_id unik untuk mengelompokkan order yang dibuat bersamaan
        $batchId = 'BATCH-' . date('YmdHis') . '-' . uniqid();

        // Bungkus pembuatan order dalam transaksi agar konsisten (rollback jika ada error)
        DB::beginTransaction();
        try {
            foreach ($request->barang_id as $index => $kodeBarang) {
                $qty = $request->quantity[$index] ?? 0;
                if ($qty > 0) {
                    // Ambil harga barang saat ini untuk menyimpan historis harga
                    $barangObj = Barang::find($kodeBarang);
                    $hargaSatuan = $barangObj->harga ?? 0;
                    $totalHarga = $hargaSatuan * $qty;

                    $order = Order::create([
                        'id_barang' => $kodeBarang,
                        'jumlah' => $qty,
                        'status' => 'pending',
                        'tipe_rekap' => $request->tipe_rekap,
                        'id_aktor' => $idAktor, // Simpan id_aktor yang membuat order
                        'harga_satuan' => $hargaSatuan,
                        'total_harga' => $totalHarga,
                        'batch_id' => $batchId, // Tambahkan batch_id untuk grouping
                    ]);
                    $orderIds[] = $order->id_order;
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating orders in store(): ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->route('order.index')->with('error', 'Terjadi kesalahan saat membuat order. Silakan coba lagi.');
        }

        if (!empty($orderIds)) {
            // Simpan order IDs dan tipe_rekap ke session untuk konfirmasi
            session(['pending_orders' => $orderIds]);
            session(['pending_tipe_rekap' => $request->tipe_rekap]);
            return redirect()->route('order.confirm')->with('success', 'Order berhasil dibuat. Silakan konfirmasi order.');
        }

        return redirect()->route('order.index')->with('error', 'Tidak ada order yang dibuat');
    }

    public function confirm()
    {
        $orderIds = session('pending_orders', []);
        
        if (empty($orderIds)) {
            return redirect()->route('order.index')->with('error', 'Tidak ada order yang perlu dikonfirmasi');
        }

        $orders = DB::table('order')
            ->join('barang', 'order.id_barang', '=', 'barang.kode_barang')
            ->select('order.*', 'barang.nama_barang', 'barang.kode_barang', 'barang.satuan', 'order.harga_satuan', 'order.total_harga')
            ->whereIn('order.id_order', $orderIds)
            ->where('order.status', 'pending')
            ->get();

        return view('order.confirm', compact('orders'));
    }

    public function confirmForm()
    {
        $orderIds = session('pending_orders', []);
        
        if (empty($orderIds)) {
            return redirect()->route('order.index')->with('error', 'Tidak ada order yang perlu dikonfirmasi');
        }

        // Ambil detail order untuk ditampilkan
        $orders = DB::table('order')
            ->join('barang', 'order.id_barang', '=', 'barang.kode_barang')
            ->select('order.*', 'barang.nama_barang', 'barang.kode_barang', 'barang.satuan', 'order.harga_satuan', 'order.total_harga')
            ->whereIn('order.id_order', $orderIds)
            ->where('order.status', 'pending')
            ->get();

        // List lokasi untuk dropdown
        $lokasi = [
            'PT.BMT',
            'RANTAU',
            'BINUANG',
            'TAP SELATAN',
            'CLU',
            'CLS',
            'TAPIN',
            'TENGAH',
            'BATU HAPU',
            'BAKARANGAN',
            'LOKPAIKAT',
            'SALBA',
            'PIANI'
        ];

        return view('order.confirm-form', compact('lokasi', 'orderIds', 'orders'));
    }

    /**
     * Konfirmasi order dengan alamat dan no BPP
     * Untuk Perencanaan dan Penjaga Gudang
     */
    public function confirmStore(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'no_bukti' => 'required|string|max:255',
            'alamat' => 'required|string|in:PT.BMT,RANTAU,BINUANG,TAP SELATAN,CLU,CLS,TAPIN,TENGAH,BATU HAPU,BAKARANGAN,LOKPAIKAT,SALBA,PIANI',
            'keterangan' => 'nullable|string|max:255'
        ]);

        $orderIds = session('pending_orders', []);
        
        if (empty($orderIds)) {
            return redirect()->route('order.index')->with('error', 'Tidak ada order yang perlu dikonfirmasi');
        }

        // Ambil role user yang sedang login
        $userRole = session('role');
        $idAktor = session('id_aktor');

        DB::beginTransaction();
        try {
            $isPerencanaan = false;
            $isGudang = false;
            
            $rekapJobs = [];
            foreach ($orderIds as $orderId) {
                $order = DB::table('order')->where('id_order', $orderId)->first();
                
                if ($order && $order->status === 'pending') {
                    // Tentukan apakah ini order dari Perencanaan atau Gudang
                    // Ambil role dari aktor yang membuat order
                    $orderCreatorRole = DB::table('pengguna')
                        ->join('role', 'pengguna.id_role', '=', 'role.id_role')
                        ->where('pengguna.id_aktor', $order->id_aktor)
                        ->select('role.nama_role')
                        ->first();

                    if (!$orderCreatorRole) {
                        Log::warning("Role not found for order {$orderId}, id_aktor: {$order->id_aktor}");
                        continue; // Skip order yang tidak valid
                    }

                    $isPerencanaan = in_array(strtolower($orderCreatorRole->nama_role ?? ''), ['perencanaan']);
                    $isGudang = in_array(strtolower($orderCreatorRole->nama_role ?? ''), ['penjaga gudang', 'pejaga gudang']);

                    // Untuk Perencanaan: stok langsung berkurang saat konfirmasi (karena langsung mengambil barang)
                    // Untuk Gudang: stok TIDAK langsung bertambah, baru bertambah setelah di-approve final oleh Keuangan
                    
                    // Simpan alamat dan no_bukti ke order (untuk referensi)
                    DB::table('order')
                        ->where('id_order', $orderId)
                        ->update([
                            'alamat' => $request->alamat,
                            'no_bukti' => $request->no_bukti,
                            'keterangan' => $request->keterangan ?? null
                        ]);

                    // Ambil barang untuk mendapatkan stok terakhir
                    $barang = DB::table('barang')
                        ->where('kode_barang', $order->id_barang)
                        ->first();

                    if ($barang) {
                        if ($isPerencanaan) {
                            // Perencanaan order → stok langsung berkurang (keluar) saat konfirmasi
                        $lastDetail = DB::table('detail_barang')
                            ->where('kode_barang', $order->id_barang)
                            ->orderBy('tanggal', 'desc')
                            ->orderBy('no_bukti', 'desc')
                            ->first();

                            $stokTersedia = $barang->stok ?? 0;
                            $sisaSebelumnya = $lastDetail ? $lastDetail->sisa : $stokTersedia;
                            $sisa = $sisaSebelumnya - $order->jumlah;

                        if ($sisa < 0) {
                                throw new \Exception("Stok tidak cukup untuk barang {$barang->nama_barang}. Stok tersedia: {$sisaSebelumnya}, dibutuhkan: {$order->jumlah}");
                        }

                        // Insert detail_barang sebagai transaksi keluar
                        DB::table('detail_barang')->insert([
                            'kode_barang' => $order->id_barang,
                            'tanggal' => $request->tanggal,
                            'no_bukti' => $request->no_bukti,
                            'masuk' => 0,
                            'keluar' => $order->jumlah,
                            'sisa' => $sisa,
                            'alamat' => $request->alamat,
                                'keterangan' => $request->keterangan ?? 'Order keluar dari Perencanaan'
                        ]);

                        // Update stok di tabel barang (stok berkurang)
                        DB::table('barang')
                            ->where('kode_barang', $order->id_barang)
                            ->update(['stok' => $sisa]);

                            // Catat job rekap agar dijalankan setelah commit
                            $tanggal = \Carbon\Carbon::parse($request->tanggal);
                            $idBulan = $tanggal->month;
                            $tahun = $tanggal->year;
                            $tipeRekap = $order->tipe_rekap ?? 'sr';
                            $rekapJobs[] = ['id_bulan' => $idBulan, 'tipe' => $tipeRekap, 'tahun' => $tahun];
                        }
                        // Untuk Gudang: stok TIDAK diubah di sini, akan diubah setelah final approval
                    }
                }
            }

            DB::commit();
            session()->forget('pending_orders');

            // Jalankan rekap di luar transaksi (synchronous) - bisa dipindah ke job queue jika besar
            foreach ($rekapJobs as $job) {
                try {
                    \App\Jobs\GenerateRekapJob::dispatch($job['id_bulan'], $job['tipe'], $job['tahun']);
                } catch (\Exception $e) {
                    Log::error('Error dispatching GenerateRekapJob after confirmStore(): ' . $e->getMessage(), ['exception' => $e, 'job' => $job]);
                }
            }

            return redirect()->route('order.status')->with('success', 'Order berhasil dikonfirmasi dengan alamat dan no BPP. Menunggu validasi dari Umum.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in confirmStore(): ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->route('order.confirm')->with('error', 'Terjadi kesalahan saat mengonfirmasi order. Silakan coba lagi.');
        }
    }

    /**
     * Validasi order oleh Umum (approve/reject)
     * Hanya validasi order dari Penjaga Gudang (untuk menambah stok gudang)
     */
    public function validateByUmum(Request $request, $id_order)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'keterangan' => 'nullable|string|max:255'
        ]);

        $order = DB::table('order')
            ->where('id_order', $id_order)
            ->where('status', 'pending')
            ->first();

        if (!$order) {
            return redirect()->back()->with('error', 'Order tidak ditemukan atau sudah divalidasi.');
        }

        // Cek apakah order dari Penjaga Gudang (bukan Perencanaan)
        $orderCreatorRole = DB::table('pengguna')
            ->join('role', 'pengguna.id_role', '=', 'role.id_role')
            ->where('pengguna.id_aktor', $order->id_aktor)
            ->select('role.nama_role')
            ->first();

        if (!$orderCreatorRole) {
            return redirect()->back()->with('error', 'Data role pembuat order tidak ditemukan.');
        }

        // Umum hanya validasi order dari Gudang
        if (!in_array(strtolower($orderCreatorRole->nama_role ?? ''), ['penjaga gudang', 'pejaga gudang'])) {
            return redirect()->back()->with('error', 'Order ini bukan dari Penjaga Gudang. Order dari Perencanaan harus divalidasi oleh Gudang.');
        }

        if ($request->action === 'approve') {
            // Update status menjadi approved (menunggu validasi Keuangan)
            DB::table('order')
                ->where('id_order', $id_order)
                ->update([
                    'status' => 'approved',
                    'updated_at' => now()
                ]);
            
            return redirect()->back()->with('success', 'Order berhasil disetujui. Menunggu validasi dari Keuangan.');
        } else {
            // Update status menjadi rejected
            DB::table('order')
                ->where('id_order', $id_order)
                ->update([
                    'status' => 'rejected',
                    'updated_at' => now()
                ]);
            
            return redirect()->back()->with('success', 'Order berhasil ditolak.');
        }
    }

    /**
     * Validasi order oleh Gudang (approve/reject)
     * Hanya validasi order dari Perencanaan (untuk memberikan barang ke teknisi)
     */
    public function validateByGudang(Request $request, $id_order)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'keterangan' => 'nullable|string|max:255'
        ]);

        $order = DB::table('order')
            ->where('id_order', $id_order)
            ->where('status', 'pending')
            ->first();

        if (!$order) {
            return redirect()->back()->with('error', 'Order tidak ditemukan atau sudah divalidasi.');
        }

        // Cek apakah order dari Perencanaan
        $orderCreatorRole = DB::table('pengguna')
            ->join('role', 'pengguna.id_role', '=', 'role.id_role')
            ->where('pengguna.id_aktor', $order->id_aktor)
            ->select('role.nama_role')
            ->first();

        if (!$orderCreatorRole) {
            return redirect()->back()->with('error', 'Data role pembuat order tidak ditemukan.');
        }

        // Gudang hanya validasi order dari Perencanaan
        if (strtolower($orderCreatorRole->nama_role ?? '') !== 'perencanaan') {
            return redirect()->back()->with('error', 'Order ini bukan dari Perencanaan. Order dari Gudang harus divalidasi oleh Umum.');
        }

        if ($request->action === 'approve') {
            // Update status menjadi final_approved (langsung siap diproses, tidak perlu Keuangan)
            DB::table('order')
                ->where('id_order', $id_order)
                ->update([
                    'status' => 'final_approved',
                    'updated_at' => now()
                ]);
            
            return redirect()->back()->with('success', 'Order berhasil disetujui. Order siap diproses.');
        } else {
            // Update status menjadi rejected
            DB::table('order')
                ->where('id_order', $id_order)
                ->update([
                    'status' => 'rejected',
                    'updated_at' => now()
                ]);
            
            return redirect()->back()->with('success', 'Order berhasil ditolak.');
        }
    }

    /**
     * Validasi final order oleh Keuangan (approve/reject)
     */
    public function validateByKeuangan(Request $request, $id_order)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'keterangan' => 'nullable|string|max:255'
        ]);

        $order = DB::table('order')
            ->where('id_order', $id_order)
            ->where('status', 'approved') // Hanya order yang sudah di-approve Umum
            ->first();

        if (!$order) {
            return redirect()->back()->with('error', 'Order tidak ditemukan atau belum divalidasi oleh Umum.');
        }

        if ($request->action === 'approve') {
            // Cek apakah order dari Gudang (untuk menambah stok)
            $orderCreatorRole = DB::table('pengguna')
                ->join('role', 'pengguna.id_role', '=', 'role.id_role')
                ->where('pengguna.id_aktor', $order->id_aktor)
                ->select('role.nama_role')
                ->first();

            if (!$orderCreatorRole) {
                return redirect()->back()->with('error', 'Data role pembuat order tidak ditemukan.');
            }

            $isGudang = in_array(strtolower($orderCreatorRole->nama_role ?? ''), ['penjaga gudang', 'pejaga gudang']);

            DB::beginTransaction();
            try {
                // Update status menjadi final_approved
                DB::table('order')
                    ->where('id_order', $id_order)
                    ->update([
                        'status' => 'final_approved',
                        'updated_at' => now()
                    ]);

                // Jika order dari Gudang, tambahkan stok sekarang (setelah final approval)
                if ($isGudang) {
                    $barang = DB::table('barang')
                        ->where('kode_barang', $order->id_barang)
                        ->first();

                    if ($barang) {
                        // Ambil detail terakhir untuk menghitung sisa
                        $lastDetail = DB::table('detail_barang')
                            ->where('kode_barang', $order->id_barang)
                            ->orderBy('tanggal', 'desc')
                            ->orderBy('no_bukti', 'desc')
                            ->first();

                        $stokTersedia = $barang->stok ?? 0;
                        $sisaSebelumnya = $lastDetail ? $lastDetail->sisa : $stokTersedia;
                        $sisa = $sisaSebelumnya + $order->jumlah;

                        // Ambil alamat dan no_bukti dari order (sudah disimpan saat konfirmasi)
                        $orderData = DB::table('order')
                            ->where('id_order', $id_order)
                            ->first();

                        // Insert detail_barang sebagai transaksi masuk
                        DB::table('detail_barang')->insert([
                            'kode_barang' => $order->id_barang,
                            'tanggal' => $orderData->created_at ? \Carbon\Carbon::parse($orderData->created_at)->format('Y-m-d') : now()->format('Y-m-d'),
                            'no_bukti' => $orderData->no_bukti ?? 'AUTO-' . $id_order,
                            'masuk' => $order->jumlah,
                            'keluar' => 0,
                            'sisa' => $sisa,
                            'alamat' => $orderData->alamat ?? 'PT.BMT',
                            'keterangan' => $orderData->keterangan ?? 'Order masuk dari Gudang (Final Approved)'
                        ]);

                        // Update stok di tabel barang (stok bertambah)
                        DB::table('barang')
                            ->where('kode_barang', $order->id_barang)
                            ->update(['stok' => $sisa]);

                        // Catat job rekap untuk dijalankan setelah commit
                        $tanggal = \Carbon\Carbon::parse($orderData->created_at ?? now());
                        $idBulan = $tanggal->month;
                        $tahun = $tanggal->year;
                        $tipeRekap = $order->tipe_rekap ?? 'sr';
                        $rekapJob = ['id_bulan' => $idBulan, 'tipe' => $tipeRekap, 'tahun' => $tahun];
                    }
                }

                DB::commit();

                // Jalankan rekap setelah commit
                if (isset($rekapJob)) {
                    try {
                        \App\Jobs\GenerateRekapJob::dispatch($rekapJob['id_bulan'], $rekapJob['tipe'], $rekapJob['tahun']);
                    } catch (\Exception $e) {
                        Log::error('Error dispatching GenerateRekapJob after validateByKeuangan(): ' . $e->getMessage(), ['exception' => $e, 'job' => $rekapJob]);
                    }
                }

                return redirect()->back()->with('success', 'Order berhasil disetujui final. ' . ($isGudang ? 'Stok telah ditambahkan.' : 'Order siap diproses.'));
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error in validateByKeuangan(): ' . $e->getMessage(), ['exception' => $e]);
                return redirect()->back()->with('error', 'Terjadi kesalahan saat memproses validasi. Silakan coba lagi.');
            }
        } else {
            // Update status menjadi rejected
            DB::table('order')
                ->where('id_order', $id_order)
                ->update([
                    'status' => 'rejected',
                    'updated_at' => now()
                ]);
            
            return redirect()->back()->with('success', 'Order berhasil ditolak.');
        }
    }

    /**
     * Generate invoice untuk order
     */
    public function invoice($id_order)
    {
        $order = DB::table('order')
            ->join('barang', 'order.id_barang', '=', 'barang.kode_barang')
            ->join('pengguna', 'order.id_aktor', '=', 'pengguna.id_aktor')
            ->join('role', 'pengguna.id_role', '=', 'role.id_role')
            ->join('aktor', 'order.id_aktor', '=', 'aktor.id_aktor')
            ->where('order.id_order', $id_order)
            ->select(
                'order.*',
                'barang.nama_barang',
                'barang.satuan',
                'role.nama_role as role_pemesan',
                'aktor.nama_aktor as nama_pemesan',
                'order.harga_satuan',
                'order.total_harga'
            )
            ->first();

        if (!$order) {
            return redirect()->route('order.status')->with('error', 'Order tidak ditemukan.');
        }

        // Cek apakah user berhak melihat invoice ini
        $userRole = strtolower(session('role') ?? '');
        $idAktor = session('id_aktor');

        $canView = false;
        if (in_array($userRole, ['admin', 'penjaga gudang', 'pejaga gudang'])) {
            $canView = true; // Admin dan Gudang bisa lihat semua
        } elseif ($userRole === 'perencanaan' && $order->id_aktor == $idAktor) {
            $canView = true; // Perencanaan hanya bisa lihat order sendiri
        }

        if (!$canView) {
            return redirect()->route('order.status')->with('error', 'Anda tidak memiliki akses untuk melihat invoice ini.');
        }

        return view('order.invoice', compact('order'));
    }

    public function cancel()
    {
        $orderIds = session('pending_orders', []);
        
        if (!empty($orderIds)) {
            // Hapus order yang pending
            DB::table('order')
                ->whereIn('id_order', $orderIds)
                ->where('status', 'pending')
                ->delete();
        }

        session()->forget('pending_orders');
        return redirect()->route('order.index')->with('info', 'Order dibatalkan');
    }

    public function status()
    {
        // Tampilkan status per surat BPP (no_bukti)
        $bpps = DB::table('order')
            ->whereNotNull('no_bukti')
            ->select(
                'no_bukti',
                DB::raw('MIN(created_at) as created_at'),
                DB::raw('COUNT(*) as item_count'),
                DB::raw("SUM(CASE WHEN status='rejected' THEN 1 ELSE 0 END) as rejected_count"),
                DB::raw("SUM(CASE WHEN status='pending' THEN 1 ELSE 0 END) as pending_count"),
                DB::raw("SUM(CASE WHEN status='approved' THEN 1 ELSE 0 END) as approved_count"),
                DB::raw("SUM(CASE WHEN status='final_approved' THEN 1 ELSE 0 END) as final_count"),
                DB::raw('SUM(COALESCE(total_harga,0)) as grand_total')
            )
            ->groupBy('no_bukti')
            ->orderBy('created_at', 'desc')
            ->get();

        // Tentukan status agregat per BPP
        $bpps = $bpps->map(function($row) {
            $status = 'approved';
            if (($row->final_count ?? 0) == ($row->item_count ?? 0) && ($row->item_count ?? 0) > 0) {
                $status = 'final_approved';
            } elseif (($row->rejected_count ?? 0) > 0) {
                $status = 'rejected';
            } elseif (($row->pending_count ?? 0) > 0) {
                $status = 'pending';
            } else {
                $status = 'approved';
            }
            $row->agg_status = $status;
            return $row;
        });

        return view('order.status', compact('bpps'));
    }

    public function show($id_order)
    {
        $order = DB::table('order')
            ->join('barang', 'order.id_barang', '=', 'barang.kode_barang')
            ->select('order.*', 'barang.nama_barang', 'barang.satuan')
            ->where('order.id_order', $id_order)
            ->first();

        if (!$order) {
            return redirect()->route('order.status')
                ->with('error', 'Order tidak ditemukan');
        }

        return view('order.show', compact('order'));
    }

    /**
     * Validasi batch order oleh Umum (approve/reject semua order dalam batch sekaligus)
     */
    public function validateBatchByUmum(Request $request, $batch_id)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'keterangan' => 'nullable|string|max:255'
        ]);

        // Ambil semua order dalam batch yang masih pending
        $orders = DB::table('order')
            ->where('batch_id', $batch_id)
            ->where('status', 'pending')
            ->get();

        if ($orders->isEmpty()) {
            return redirect()->back()->with('error', 'Batch order tidak ditemukan atau sudah divalidasi.');
        }

        // Cek apakah semua order dari Penjaga Gudang
        $firstOrder = $orders->first();
        $orderCreatorRole = DB::table('pengguna')
            ->join('role', 'pengguna.id_role', '=', 'role.id_role')
            ->where('pengguna.id_aktor', $firstOrder->id_aktor)
            ->select('role.nama_role')
            ->first();

        if (!$orderCreatorRole) {
            return redirect()->back()->with('error', 'Data role pembuat order tidak ditemukan.');
        }

        if (!in_array(strtolower($orderCreatorRole->nama_role ?? ''), ['penjaga gudang', 'pejaga gudang'])) {
            return redirect()->back()->with('error', 'Batch order ini bukan dari Penjaga Gudang.');
        }

        $newStatus = $request->action === 'approve' ? 'approved' : 'rejected';
        
        // Update semua order dalam batch sekaligus
        DB::table('order')
            ->where('batch_id', $batch_id)
            ->where('status', 'pending')
            ->update([
                'status' => $newStatus,
                'updated_at' => now()
            ]);

        $message = $request->action === 'approve' 
            ? 'Batch order berhasil disetujui (' . $orders->count() . ' item). Menunggu validasi dari Keuangan.' 
            : 'Batch order berhasil ditolak (' . $orders->count() . ' item).';

        return redirect()->back()->with('success', $message);
    }

    /**
     * Validasi batch order oleh Gudang (approve/reject semua order dalam batch sekaligus)
     */
    public function validateBatchByGudang(Request $request, $batch_id)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'keterangan' => 'nullable|string|max:255'
        ]);

        $orders = DB::table('order')
            ->where('batch_id', $batch_id)
            ->where('status', 'pending')
            ->get();

        if ($orders->isEmpty()) {
            return redirect()->back()->with('error', 'Batch order tidak ditemukan atau sudah divalidasi.');
        }

        // Cek apakah dari Perencanaan
        $firstOrder = $orders->first();
        $orderCreatorRole = DB::table('pengguna')
            ->join('role', 'pengguna.id_role', '=', 'role.id_role')
            ->where('pengguna.id_aktor', $firstOrder->id_aktor)
            ->select('role.nama_role')
            ->first();

        if (!$orderCreatorRole) {
            return redirect()->back()->with('error', 'Data role pembuat order tidak ditemukan.');
        }

        if (strtolower($orderCreatorRole->nama_role ?? '') !== 'perencanaan') {
            return redirect()->back()->with('error', 'Batch order ini bukan dari Perencanaan.');
        }

        $newStatus = $request->action === 'approve' ? 'final_approved' : 'rejected';
        
        DB::table('order')
            ->where('batch_id', $batch_id)
            ->where('status', 'pending')
            ->update([
                'status' => $newStatus,
                'updated_at' => now()
            ]);

        $message = $request->action === 'approve' 
            ? 'Batch order berhasil disetujui (' . $orders->count() . ' item). Order siap diproses.' 
            : 'Batch order berhasil ditolak (' . $orders->count() . ' item).';

        return redirect()->back()->with('success', $message);
    }

    /**
     * Validasi batch order oleh Keuangan (approve/reject semua order dalam batch sekaligus)
     */
    public function validateBatchByKeuangan(Request $request, $batch_id)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'keterangan' => 'nullable|string|max:255'
        ]);

        $orders = DB::table('order')
            ->where('batch_id', $batch_id)
            ->where('status', 'approved')
            ->get();

        if ($orders->isEmpty()) {
            return redirect()->back()->with('error', 'Batch order tidak ditemukan atau belum divalidasi oleh Umum.');
        }

        if ($request->action === 'approve') {
            // Cek apakah dari Gudang
            $firstOrder = $orders->first();
            $orderCreatorRole = DB::table('pengguna')
                ->join('role', 'pengguna.id_role', '=', 'role.id_role')
                ->where('pengguna.id_aktor', $firstOrder->id_aktor)
                ->select('role.nama_role')
                ->first();

            if (!$orderCreatorRole) {
                return redirect()->back()->with('error', 'Data role pembuat order tidak ditemukan.');
            }

            $isGudang = in_array(strtolower($orderCreatorRole->nama_role ?? ''), ['penjaga gudang', 'pejaga gudang']);

            DB::beginTransaction();
            try {
                // Update status semua order dalam batch
                DB::table('order')
                    ->where('batch_id', $batch_id)
                    ->where('status', 'approved')
                    ->update([
                        'status' => 'final_approved',
                        'updated_at' => now()
                    ]);

                // Jika dari Gudang, proses stok untuk semua item dalam batch
                if ($isGudang) {
                    foreach ($orders as $order) {
                        $barang = DB::table('barang')
                            ->where('kode_barang', $order->id_barang)
                            ->first();

                        if ($barang) {
                            $lastDetail = DB::table('detail_barang')
                                ->where('kode_barang', $order->id_barang)
                                ->orderBy('tanggal', 'desc')
                                ->orderBy('no_bukti', 'desc')
                                ->first();

                            $stokTersedia = $barang->stok ?? 0;
                            $sisaSebelumnya = $lastDetail ? $lastDetail->sisa : $stokTersedia;
                            $sisa = $sisaSebelumnya + $order->jumlah;

                            DB::table('detail_barang')->insert([
                                'kode_barang' => $order->id_barang,
                                'tanggal' => $order->created_at ? \Carbon\Carbon::parse($order->created_at)->format('Y-m-d') : now()->format('Y-m-d'),
                                'no_bukti' => $order->no_bukti ?? 'AUTO-' . $order->id_order,
                                'masuk' => $order->jumlah,
                                'keluar' => 0,
                                'sisa' => $sisa,
                                'alamat' => $order->alamat ?? 'PT.BMT',
                                'keterangan' => $order->keterangan ?? 'Order masuk dari Gudang (Final Approved - Batch)'
                            ]);

                            DB::table('barang')
                                ->where('kode_barang', $order->id_barang)
                                ->update(['stok' => $sisa]);
                        }
                    }
                }

                DB::commit();

                $message = 'Batch order berhasil disetujui final (' . $orders->count() . ' item). ' . 
                          ($isGudang ? 'Stok telah ditambahkan.' : 'Order siap diproses.');
                
                return redirect()->back()->with('success', $message);
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error in validateBatchByKeuangan(): ' . $e->getMessage(), ['exception' => $e]);
                return redirect()->back()->with('error', 'Terjadi kesalahan saat memproses validasi batch.');
            }
        } else {
            DB::table('order')
                ->where('batch_id', $batch_id)
                ->where('status', 'approved')
                ->update([
                    'status' => 'rejected',
                    'updated_at' => now()
                ]);
            
            return redirect()->back()->with('success', 'Batch order berhasil ditolak (' . $orders->count() . ' item).');
        }
    }

    /**
     * Validasi BPP oleh Umum (approve/reject semua item dalam satu no_bukti)
     */
    public function validateBppByUmum(Request $request, $no_bukti)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'keterangan' => 'nullable|string|max:255'
        ]);

        $orders = DB::table('order')
            ->where('no_bukti', $no_bukti)
            ->where('status', 'pending')
            ->get();

        if ($orders->isEmpty()) {
            return redirect()->back()->with('error', 'Surat BPP tidak ditemukan atau sudah divalidasi.');
        }

        // Pastikan pemesan adalah Penjaga Gudang
        $firstOrder = $orders->first();
        $orderCreatorRole = DB::table('pengguna')
            ->join('role', 'pengguna.id_role', '=', 'role.id_role')
            ->where('pengguna.id_aktor', $firstOrder->id_aktor)
            ->select('role.nama_role')
            ->first();

        if (!$orderCreatorRole) {
            return redirect()->back()->with('error', 'Data role pembuat order tidak ditemukan.');
        }

        if (!in_array(strtolower($orderCreatorRole->nama_role ?? ''), ['penjaga gudang', 'pejaga gudang'])) {
            return redirect()->back()->with('error', 'Surat BPP ini bukan dari Penjaga Gudang.');
        }

        $newStatus = $request->action === 'approve' ? 'approved' : 'rejected';

        DB::table('order')
            ->where('no_bukti', $no_bukti)
            ->where('status', 'pending')
            ->update([
                'status' => $newStatus,
                'updated_at' => now()
            ]);

        $message = $request->action === 'approve'
            ? 'Surat BPP berhasil disetujui (' . $orders->count() . ' item). Menunggu validasi dari Keuangan.'
            : 'Surat BPP berhasil ditolak (' . $orders->count() . ' item).';

        return redirect()->back()->with('success', $message);
    }

    /**
     * Validasi BPP oleh Gudang (approve/reject semua item dalam satu no_bukti)
     */
    public function validateBppByGudang(Request $request, $no_bukti)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'keterangan' => 'nullable|string|max:255'
        ]);

        $orders = DB::table('order')
            ->where('no_bukti', $no_bukti)
            ->where('status', 'pending')
            ->get();

        if ($orders->isEmpty()) {
            return redirect()->back()->with('error', 'Surat BPP tidak ditemukan atau sudah divalidasi.');
        }

        // Pastikan pemesan adalah Perencanaan
        $firstOrder = $orders->first();
        $orderCreatorRole = DB::table('pengguna')
            ->join('role', 'pengguna.id_role', '=', 'role.id_role')
            ->where('pengguna.id_aktor', $firstOrder->id_aktor)
            ->select('role.nama_role')
            ->first();

        if (!$orderCreatorRole) {
            return redirect()->back()->with('error', 'Data role pembuat order tidak ditemukan.');
        }

        if (strtolower($orderCreatorRole->nama_role ?? '') !== 'perencanaan') {
            return redirect()->back()->with('error', 'Surat BPP ini bukan dari Perencanaan.');
        }

        $newStatus = $request->action === 'approve' ? 'approved' : 'rejected';

        DB::table('order')
            ->where('no_bukti', $no_bukti)
            ->where('status', 'pending')
            ->update([
                'status' => $newStatus,
                'updated_at' => now()
            ]);

        $message = $request->action === 'approve'
            ? 'Surat BPP berhasil disetujui (' . $orders->count() . ' item).'
            : 'Surat BPP berhasil ditolak (' . $orders->count() . ' item).';

        return redirect()->back()->with('success', $message);
    }

    /**
     * Validasi BPP final oleh Keuangan (final_approved / rejected) + update stok bila sumber Gudang
     */
    public function validateBppByKeuangan(Request $request, $no_bukti)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'keterangan' => 'nullable|string|max:255'
        ]);
        $cleanNoBukti = trim($no_bukti);

        // Normalisasi status otomatis sebelum validasi (auto-fix spasi dan case)
        DB::table('order')
            ->whereRaw('TRIM(no_bukti) = ?', [$cleanNoBukti])
            ->whereRaw("LOWER(TRIM(status)) IN ('pending','approved','final_approved','rejected')")
            ->whereRaw("status != LOWER(TRIM(status))")
            ->update(['status' => DB::raw("LOWER(TRIM(status))")]);

        // Ambil semua item yang masih bisa difinalkan (approved atau pending)
        $orders = DB::table('order')
            ->whereRaw('TRIM(no_bukti) = ?', [$cleanNoBukti])
            ->whereIn('status', ['approved', 'pending'])
            ->get();

        if ($orders->isEmpty()) {
            // Cek apakah ada item dengan status non-standar
            $allItems = DB::table('order')
                ->whereRaw('TRIM(no_bukti) = ?', [$cleanNoBukti])
                ->select('status', DB::raw('COUNT(*) as cnt'))
                ->groupBy('status')
                ->get();
            
            if ($allItems->isNotEmpty()) {
                $statusList = $allItems->map(fn($i) => "'{$i->status}' ({$i->cnt})");
                return redirect()->route('dashboard.keuangan')
                    ->with('error', 'Tidak ada item yang dapat difinalkan. Status saat ini: ' . $statusList->implode(', '));
            }
            
            return redirect()->route('dashboard.keuangan')->with('error', 'Surat BPP tidak ditemukan.');
        }

        $firstOrder = $orders->first();
        $orderCreatorRole = DB::table('pengguna')
            ->join('role', 'pengguna.id_role', '=', 'role.id_role')
            ->where('pengguna.id_aktor', $firstOrder->id_aktor)
            ->select('role.nama_role')
            ->first();

        if (!$orderCreatorRole) {
            return redirect()->route('dashboard.keuangan')->with('error', 'Data role pembuat order tidak ditemukan.');
        }

        $isFromGudang = in_array(strtolower($orderCreatorRole->nama_role ?? ''), ['penjaga gudang', 'pejaga gudang']);

        $newStatus = $request->action === 'approve' ? 'final_approved' : 'rejected';

        // Finalisasi dalam transaksi untuk konsistensi
        \DB::beginTransaction();
        try {
            $affected = DB::table('order')
                ->whereRaw('TRIM(no_bukti) = ?', [$cleanNoBukti])
                ->whereIn('status', ['approved', 'pending'])
                ->update([
                    'status' => $newStatus,
                    'updated_at' => now()
                ]);

            if ($affected === 0) {
                \DB::rollBack();
                return redirect()->route('dashboard.keuangan')->with('error', 'Tidak ada item yang dapat difinalkan (mungkin sudah final atau status berbeda).');
            }

            // Jika dari Gudang dan approve final: kurangi stok
            if ($request->action === 'approve' && $isFromGudang) {
                foreach ($orders as $o) {
                    // Update stok di detail_barang: tambah keluar, kurangi sisa
                    $latestDetail = DB::table('detail_barang')
                        ->where('kode_barang', $o->id_barang)
                        ->orderBy('tanggal', 'desc')
                        ->orderBy('detail_barang', 'desc')
                        ->first();
                    
                    if ($latestDetail) {
                        $newSisa = max(0, $latestDetail->sisa - $o->jumlah);
                        
                        // Insert record keluar baru
                        DB::table('detail_barang')->insert([
                            'kode_barang' => $o->id_barang,
                            'tanggal' => now(),
                            'no_bukti' => $cleanNoBukti,
                            'masuk' => 0,
                            'keluar' => $o->jumlah,
                            'sisa' => $newSisa,
                            'alamat' => $o->alamat ?? 'PT.BMT',
                            'keterangan' => 'Final approved - Order BPP ' . $cleanNoBukti
                        ]);
                    }
                }
            }
            \DB::commit();
        } catch (\Throwable $e) {
            \DB::rollBack();
            return redirect()->route('dashboard.keuangan')->with('error', 'Gagal final approve: ' . $e->getMessage());
        }

        $message = $request->action === 'approve'
            ? 'Surat BPP berhasil final approve (' . $orders->count() . ' item).'
            : 'Surat BPP berhasil ditolak (' . $orders->count() . ' item).';

        // Redirect ke dashboard keuangan agar list ter-refresh dan BPP hilang dari daftar
        return redirect()->route('dashboard.keuangan')->with('success', $message);
    }

    /**
     * Menampilkan detail batch order
     */
    public function batchDetail($batch_id)
    {
        // Ambil semua order dalam batch ini
        $orders = Order::where('batch_id', $batch_id)
            ->with(['barang', 'aktor'])
            ->orderBy('id_order', 'asc')
            ->get();
        
        if ($orders->isEmpty()) {
            return redirect()->back()->with('error', 'Batch order tidak ditemukan.');
        }
        
        // Hitung total harga untuk batch ini
        $totalBatch = $orders->sum('total_harga');
        
        return view('order.batch-detail', compact('orders', 'batch_id', 'totalBatch'));
    }

    /**
     * Menampilkan detail surat BPP (no_bukti) beserta item-itemnya
     */
    public function bppDetail($no_bukti)
    {
        $orders = Order::where('no_bukti', $no_bukti)
            ->with(['barang', 'aktor'])
            ->orderBy('id_order', 'asc')
            ->get();

        if ($orders->isEmpty()) {
            return redirect()->back()->with('error', 'Surat BPP tidak ditemukan.');
        }

        $totalBatch = $orders->sum('total_harga');

        // Hitung status agregat
        $counts = [
            'pending' => 0,
            'approved' => 0,
            'final_approved' => 0,
            'rejected' => 0,
        ];
        foreach ($orders as $o) {
            $s = $o->status ?? 'pending';
            if (isset($counts[$s])) $counts[$s]++;
        }
        $itemCount = $orders->count();
        $aggStatus = 'approved';
        if ($counts['final_approved'] === $itemCount && $itemCount > 0) {
            $aggStatus = 'final_approved';
        } elseif ($counts['rejected'] > 0) {
            $aggStatus = 'rejected';
        } elseif ($counts['pending'] > 0) {
            $aggStatus = 'pending';
        } else {
            $aggStatus = 'approved';
        }

        return view('order.bpp-detail', compact('orders', 'no_bukti', 'totalBatch', 'aggStatus'));
    }

    /**
     * Menampilkan riwayat order berdasarkan status per BPP
     * Khusus untuk role keuangan
     */
    public function orderByStatus($status)
    {
        // Validasi role
        $userRole = strtolower(session('role') ?? '');
        if ($userRole !== 'keuangan') {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        // Validasi status
        $validStatuses = ['pending', 'approved', 'final_approved', 'rejected'];
        if (!in_array($status, $validStatuses)) {
            return redirect()->back()->with('error', 'Status tidak valid.');
        }

        // Ambil BPP yang memiliki status agregat sesuai filter
        // Group per no_bukti dan hitung agregat status
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
                DB::raw('SUM(COALESCE(total_harga,0)) as grand_total')
            )
            ->groupBy(DB::raw('TRIM(no_bukti)'))
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($r) {
                $itemCount = $r->item_count ?? 0;
                $finalCount = $r->final_count ?? 0;
                
                // Gunakan == untuk perbandingan numerik lebih fleksibel (string vs int)
                if ($finalCount == $itemCount && $itemCount > 0) {
                    $r->agg_status = 'final_approved';
                } elseif (($r->rejected_count ?? 0) > 0) {
                    $r->agg_status = 'rejected';
                } elseif (($r->pending_count ?? 0) > 0) {
                    $r->agg_status = 'pending';
                } elseif (($r->approved_count ?? 0) > 0) {
                    $r->agg_status = 'approved';
                } else {
                    $r->agg_status = 'unknown';
                }
                return $r;
            })
            ->filter(function($r) use ($status) {
                return $r->agg_status === $status;
            });

        // Label status untuk tampilan
        $statusLabels = [
            'pending' => 'Surat BPP Pending',
            'approved' => 'Surat BPP Approved (Umum)',
            'final_approved' => 'Surat BPP Final Approved',
            'rejected' => 'Surat BPP Rejected'
        ];

        $statusLabel = $statusLabels[$status] ?? ucfirst($status);
        
        // Hitung total keseluruhan untuk status ini
        $totalKeseluruhan = $bppRows->sum('grand_total');
        $totalBpp = $bppRows->count();

        return view('order.by-status', compact('bppRows', 'status', 'statusLabel', 'totalKeseluruhan', 'totalBpp'));
    }
}
