<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;

class BarangController extends Controller
{
    // Form edit harga (role keuangan saja)
    public function editHarga($kode_barang)
    {
        if (strtolower(session('role') ?? '') !== 'keuangan') {
            abort(403, 'Akses ditolak');
        }
        $barang = Barang::findOrFail($kode_barang);
        return view('barang.edit-harga', compact('barang'));
    }

    // Update harga
    public function updateHarga(Request $request, $kode_barang)
    {
        if (strtolower(session('role') ?? '') !== 'keuangan') {
            abort(403, 'Akses ditolak');
        }

        $request->validate([
            'harga' => 'required|numeric|min:0'
        ], [
            'harga.required' => 'Harga wajib diisi.',
            'harga.numeric' => 'Harga harus berupa angka.',
            'harga.min' => 'Harga tidak boleh negatif.'
        ]);

        $barang = Barang::findOrFail($kode_barang);
        $barang->harga = $request->harga;
        $barang->save();

        return redirect()->route('barang.index')->with('success', 'Harga barang berhasil diperbarui.');
    }
}
