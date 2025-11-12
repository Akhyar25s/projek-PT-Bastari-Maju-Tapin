<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        // Ambil id_aktor dari session
        $idAktor = session('id_aktor');
        
        $orderIds = [];
        foreach ($request->barang_id as $index => $kodeBarang) {
            if ($request->quantity[$index] > 0) {
                $order = Order::create([
                    'id_barang' => $kodeBarang,
                    'jumlah' => $request->quantity[$index],
                    'status' => 'pending',
                    'tipe_rekap' => $request->tipe_rekap,
                    'id_aktor' => $idAktor, // Simpan id_aktor yang membuat order
                ]);
                $orderIds[] = $order->id_order;
            }
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
            ->select('order.*', 'barang.nama_barang', 'barang.kode_barang', 'barang.satuan')
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
            ->select('order.*', 'barang.nama_barang', 'barang.kode_barang', 'barang.satuan')
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

                            // Auto-update rekap untuk Perencanaan
                        $tanggal = \Carbon\Carbon::parse($request->tanggal);
                        $idBulan = $tanggal->month;
                        $tahun = $tanggal->year;
                            $tipeRekap = $order->tipe_rekap ?? 'sr';
                        \App\Http\Controllers\RekapController::generateRekapByTipe($idBulan, $tipeRekap, $tahun);
                        }
                        // Untuk Gudang: stok TIDAK diubah di sini, akan diubah setelah final approval
                    }
                }
            }

            DB::commit();
            session()->forget('pending_orders');
            
            return redirect()->route('order.status')->with('success', 'Order berhasil dikonfirmasi dengan alamat dan no BPP. Menunggu validasi dari Umum.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('order.confirm')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
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

                        // Auto-update rekap
                        $tanggal = \Carbon\Carbon::parse($orderData->created_at ?? now());
                        $idBulan = $tanggal->month;
                        $tahun = $tanggal->year;
                        $tipeRekap = $order->tipe_rekap ?? 'sr';
                        \App\Http\Controllers\RekapController::generateRekapByTipe($idBulan, $tipeRekap, $tahun);
                    }
                }

                DB::commit();
                return redirect()->back()->with('success', 'Order berhasil disetujui final. ' . ($isGudang ? 'Stok telah ditambahkan.' : 'Order siap diproses.'));
            } catch (\Exception $e) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
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
                'aktor.nama_aktor as nama_pemesan'
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
        $orders = DB::table('order')
            ->join('barang', 'order.id_barang', '=', 'barang.kode_barang')
            ->select('order.*', 'barang.nama_barang')
            ->orderBy('order.id_order', 'desc')
            ->get();

        return view('order.status', compact('orders'));
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
}
