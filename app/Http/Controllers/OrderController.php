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
        // Cek permission: hanya Admin yang bisa order
        if (!function_exists('canOrder') || !canOrder()) {
            return redirect()->route('order.index')
                ->with('error', 'Anda tidak memiliki akses untuk membuat order');
        }

        $request->validate([
            'barang_id' => 'required|array',
            'quantity' => 'required|array',
            'barang_id.*' => 'exists:barang,kode_barang',
            'quantity.*' => 'numeric|min:0'
        ]);

        $orderIds = [];
        $errors = [];

        foreach ($request->barang_id as $index => $kodeBarang) {
            $quantity = (int) $request->quantity[$index];
            
            if ($quantity > 0) {
                // Cek stok barang
                $barang = DB::table('barang')
                    ->where('kode_barang', $kodeBarang)
                    ->first();

                if (!$barang) {
                    $errors[] = "Barang dengan kode {$kodeBarang} tidak ditemukan";
                    continue;
                }

                $currentStok = $barang->stok ?? 0;
                if ($currentStok < $quantity) {
                    $errors[] = "Stok {$barang->nama_barang} tidak mencukupi. Stok tersedia: {$currentStok}, dibutuhkan: {$quantity}";
                    continue;
                }

                $order = Order::create([
                    'id_barang' => $kodeBarang,
                    'jumlah' => $quantity,
                    'status' => 'pending',
                ]);
                $orderIds[] = $order->id_order;
            }
        }

        if (!empty($errors)) {
            return redirect()->route('order.index')
                ->withErrors(['order' => $errors])
                ->withInput();
        }

        if (!empty($orderIds)) {
            // Simpan order IDs ke session untuk konfirmasi
            session(['pending_orders' => $orderIds]);
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

    public function confirmStore(Request $request)
    {
        // Cek permission: hanya Admin yang bisa konfirmasi order
        if (!function_exists('canOrder') || !canOrder()) {
            return redirect()->route('order.index')
                ->with('error', 'Anda tidak memiliki akses untuk mengonfirmasi order');
        }

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

        DB::beginTransaction();
        try {
            foreach ($orderIds as $orderId) {
                $order = DB::table('order')->where('id_order', $orderId)->first();
                
                if ($order && $order->status === 'pending') {
                    // Update order status menjadi approved
                    DB::table('order')
                        ->where('id_order', $orderId)
                        ->update(['status' => 'approved']);

                    // Ambil barang untuk mendapatkan stok terakhir
                    $barang = DB::table('barang')
                        ->where('kode_barang', $order->id_barang)
                        ->first();

                    if ($barang) {
                        // Cek apakah stok cukup
                        $currentStok = $barang->stok ?? 0;
                        if ($currentStok < $order->jumlah) {
                            throw new \Exception("Stok barang {$barang->nama_barang} tidak mencukupi. Stok tersedia: {$currentStok}, dibutuhkan: {$order->jumlah}");
                        }

                        // Hitung sisa berdasarkan stok terakhir
                        $lastDetail = DB::table('detail_barang')
                            ->where('kode_barang', $order->id_barang)
                            ->orderBy('tanggal', 'desc')
                            ->orderBy('no_bukti', 'desc')
                            ->first();

                        $sisa = ($lastDetail ? $lastDetail->sisa : $currentStok) - $order->jumlah;

                        // Insert detail_barang sebagai transaksi keluar (order = pengeluaran stok)
                        DB::table('detail_barang')->insert([
                            'kode_barang' => $order->id_barang,
                            'tanggal' => $request->tanggal,
                            'no_bukti' => $request->no_bukti,
                            'masuk' => 0,
                            'keluar' => $order->jumlah,
                            'sisa' => $sisa,
                            'alamat' => $request->alamat,
                            'keterangan' => $request->keterangan ?? 'Order keluar'
                        ]);

                        // Update stok di tabel barang (stok berkurang)
                        DB::table('barang')
                            ->where('kode_barang', $order->id_barang)
                            ->update(['stok' => $sisa]);

                        // Auto-update rekap karena ada pengeluaran
                        $tanggal = \Carbon\Carbon::parse($request->tanggal);
                        $idBulan = $tanggal->month;
                        $tahun = $tanggal->year;
                        
                        // Panggil generate rekap otomatis
                        \App\Http\Controllers\RekapController::generateRekapStatic($idBulan, $tahun);
                    }
                }
            }

            DB::commit();
            session()->forget('pending_orders');
            
            return redirect()->route('order.status')->with('success', 'Order berhasil dikonfirmasi dan stok telah diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('order.confirm')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
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
