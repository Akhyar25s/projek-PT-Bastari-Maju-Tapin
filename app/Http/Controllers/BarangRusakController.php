<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BarangRusakController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $barangRusak = DB::table('barang_rusak')
            ->join('barang', 'barang_rusak.kode_barang', '=', 'barang.kode_barang')
            ->select('barang_rusak.*', 'barang.nama_barang', 'barang.satuan')
            ->orderBy('barang.nama_barang')
            ->get();

        return view('barang-rusak.index', compact('barangRusak'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Cek permission: hanya Admin yang bisa create
        if (!function_exists('canCreate') || !canCreate()) {
            return redirect()->route('barang-rusak.index')
                ->with('error', 'Anda tidak memiliki akses untuk menambah data');
        }

        $barang = DB::table('barang')
            ->orderBy('nama_barang')
            ->get();

        return view('barang-rusak.create', compact('barang'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Cek permission: hanya Admin yang bisa create
        if (!function_exists('canCreate') || !canCreate()) {
            return redirect()->route('barang-rusak.index')
                ->with('error', 'Anda tidak memiliki akses untuk menambah data');
        }

        $request->validate([
            'kode_barang' => 'required|exists:barang,kode_barang',
            'volume' => 'required|integer|min:1',
            'keterangan' => 'nullable|string|max:255'
        ], [
            'kode_barang.required' => 'Barang wajib dipilih.',
            'kode_barang.exists' => 'Barang yang dipilih tidak valid.',
            'volume.required' => 'Volume wajib diisi.',
            'volume.integer' => 'Volume harus berupa angka.',
            'volume.min' => 'Volume minimal 1.',
        ]);

        // Cek apakah barang sudah ada di tabel barang_rusak
        $existing = DB::table('barang_rusak')
            ->where('kode_barang', $request->kode_barang)
            ->first();

        if ($existing) {
            // Update volume jika sudah ada (tambah volume yang baru)
            $newVolume = $existing->volume + $request->volume;
            DB::table('barang_rusak')
                ->where('kode_barang', $request->kode_barang)
                ->update([
                    'volume' => $newVolume,
                    'status' => $request->keterangan ?? 'Rusak'
                ]);
        } else {
            // Insert baru
            DB::table('barang_rusak')->insert([
                'kode_barang' => $request->kode_barang,
                'volume' => $request->volume,
                'status' => $request->keterangan ?? 'Rusak'
            ]);
        }

        return redirect()->route('barang-rusak.index')
            ->with('success', 'Barang rusak berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($kode_barang)
    {
        // Cek permission: hanya Admin yang bisa edit
        if (!function_exists('canEdit') || !canEdit()) {
            return redirect()->route('barang-rusak.index')
                ->with('error', 'Anda tidak memiliki akses untuk mengedit data');
        }

        $barangRusak = DB::table('barang_rusak')
            ->join('barang', 'barang_rusak.kode_barang', '=', 'barang.kode_barang')
            ->select('barang_rusak.*', 'barang.nama_barang', 'barang.satuan')
            ->where('barang_rusak.kode_barang', $kode_barang)
            ->first();

        if (!$barangRusak) {
            return redirect()->route('barang-rusak.index')
                ->with('error', 'Barang rusak tidak ditemukan.');
        }

        $barang = DB::table('barang')
            ->orderBy('nama_barang')
            ->get();

        return view('barang-rusak.edit', compact('barangRusak', 'barang'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $kode_barang)
    {
        // Cek permission: hanya Admin yang bisa update
        if (!function_exists('canEdit') || !canEdit()) {
            return redirect()->route('barang-rusak.index')
                ->with('error', 'Anda tidak memiliki akses untuk mengedit data');
        }

        $request->validate([
            'kode_barang' => 'required|exists:barang,kode_barang',
            'volume' => 'required|integer|min:1',
            'keterangan' => 'nullable|string|max:255'
        ], [
            'kode_barang.required' => 'Barang wajib dipilih.',
            'kode_barang.exists' => 'Barang yang dipilih tidak valid.',
            'volume.required' => 'Volume wajib diisi.',
            'volume.integer' => 'Volume harus berupa angka.',
            'volume.min' => 'Volume minimal 1.',
        ]);

        // Cek apakah barang rusak ada
        $existing = DB::table('barang_rusak')
            ->where('kode_barang', $kode_barang)
            ->first();

        if (!$existing) {
            return redirect()->route('barang-rusak.index')
                ->with('error', 'Barang rusak tidak ditemukan.');
        }

        // Update data
        DB::table('barang_rusak')
            ->where('kode_barang', $kode_barang)
            ->update([
                'kode_barang' => $request->kode_barang,
                'volume' => $request->volume,
                'status' => $request->keterangan ?? 'Rusak'
            ]);

        return redirect()->route('barang-rusak.index')
            ->with('success', 'Barang rusak berhasil diupdate.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($kode_barang)
    {
        // Cek permission: hanya Admin yang bisa delete
        if (!function_exists('canDelete') || !canDelete()) {
            return redirect()->route('barang-rusak.index')
                ->with('error', 'Anda tidak memiliki akses untuk menghapus data');
        }

        $existing = DB::table('barang_rusak')
            ->where('kode_barang', $kode_barang)
            ->first();

        if (!$existing) {
            return redirect()->route('barang-rusak.index')
                ->with('error', 'Barang rusak tidak ditemukan.');
        }

        DB::table('barang_rusak')
            ->where('kode_barang', $kode_barang)
            ->delete();

        return redirect()->route('barang-rusak.index')
            ->with('success', 'Barang rusak berhasil dihapus.');
    }
}

