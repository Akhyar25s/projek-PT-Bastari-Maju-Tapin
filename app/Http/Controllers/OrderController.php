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

        return view('orders.index', compact('barang'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'barang_id' => 'required|array',
            'quantity' => 'required|array',
            'barang_id.*' => 'exists:barang,id',
            'quantity.*' => 'numeric|min:0'
        ]);

        foreach ($request->barang_id as $index => $barangId) {
            if ($request->quantity[$index] > 0) {
                Order::create([
                    'id_barang' => $barangId,
                    'jumlah' => $request->quantity[$index],
                    'status' => 'pending',
                ]);
            }
        }

        return redirect()->route('order.index')->with('success', 'Order berhasil dibuat');
    }

    public function status()
    {
        $orders = DB::table('order')
            ->join('barang', 'order.id_barang', '=', 'barang.kode_barang')
            ->select('order.*', 'barang.nama_barang')
            ->orderBy('order.id_order', 'desc')
            ->get();

        return view('orders.status', compact('orders'));
    }
}
