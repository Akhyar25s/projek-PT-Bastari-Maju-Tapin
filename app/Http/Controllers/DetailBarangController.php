<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DetailBarangController extends Controller
{
    public function index()
    {
        // Ambil semua barang untuk ditampilkan di halaman utama
        // Jika request ?sort=popular maka urutkan berdasarkan total 'masuk' (paling sering ditambahkan)
        $sort = request()->get('sort', 'popular');
        $q = trim(request()->get('q', ''));

        if ($sort === 'popular') {
            // Left join ke detail_barang lalu hitung total masuk per barang
            $query = DB::table('barang')
                ->leftJoin('detail_barang', 'barang.kode_barang', '=', 'detail_barang.kode_barang')
                ->select('barang.kode_barang', 'barang.nama_barang', 'barang.satuan', 'barang.stok', DB::raw('COALESCE(SUM(detail_barang.masuk),0) as total_masuk'))
                ->groupBy('barang.kode_barang', 'barang.nama_barang', 'barang.satuan', 'barang.stok');

            if ($q !== '') {
                $query->where(function($wr) use ($q) {
                    $wr->where('barang.nama_barang', 'like', "%{$q}%")
                       ->orWhere('barang.kode_barang', 'like', "%{$q}%");
                });
            }

            $barang = $query->orderByDesc('total_masuk')->get();
        } else {
            // Default: alfabet
            $query = DB::table('barang')
                ->select('kode_barang', 'nama_barang', 'satuan', 'stok')
                ->orderBy('nama_barang');

            if ($q !== '') {
                $query->where(function($wr) use ($q) {
                    $wr->where('nama_barang', 'like', "%{$q}%")
                       ->orWhere('kode_barang', 'like', "%{$q}%");
                });
            }

            $barang = $query->get();
        }

        return view('barang.index', compact('barang'));
    }

    public function show($kode_barang)
    {
        // Ambil info barang
        $barang = DB::table('barang')
            ->where('kode_barang', $kode_barang)
            ->first();

        if (!$barang) {
            return redirect()->route('barang.index')
                ->with('error', 'Barang tidak ditemukan');
        }

        // Ambil riwayat detail barang
        $details = DB::table('detail_barang')
            ->where('kode_barang', $kode_barang)
            ->orderBy('tanggal', 'desc')
            ->orderBy('no_bukti', 'desc')
            ->get();

        // Hitung total masuk/keluar
        $totalMasuk = $details->sum('masuk');
        $totalKeluar = $details->sum('keluar');
        
        // Ambil data distribusi per lokasi
        $distribusiLokasi = DB::table('detail_barang')
            ->select('alamat', DB::raw('SUM(masuk) as total_masuk'), DB::raw('SUM(keluar) as total_keluar'))
            ->where('kode_barang', $kode_barang)
            ->groupBy('alamat')
            ->get();

        return view('barang.detail', compact(
            'barang',
            'details',
            'totalMasuk',
            'totalKeluar',
            'distribusiLokasi'
        ));
    }

    public function create($kode_barang)
    {
        // Ambil info barang untuk form tambah transaksi
        $barang = DB::table('barang')
            ->where('kode_barang', $kode_barang)
            ->first();

        if (!$barang) {
            return redirect()->route('barang.index')
                ->with('error', 'Barang tidak ditemukan');
        }

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

        return view('barang.create-detail', compact('barang', 'lokasi'));
    }

    public function store(Request $request, $kode_barang)
    {
        // Validasi input
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'no_bukti' => 'required|string|max:255',
            'masuk' => 'required|integer|min:0',
            'keluar' => 'required|integer|min:0',
            'alamat' => 'required|string|in:PT.BMT,RANTAU,BINUANG,TAP SELATAN,CLU,CLS,TAPIN,TENGAH,BATU HAPU,BAKARANGAN,LOKPAIKAT,SALBA,PIANI',
            'keterangan' => 'required|string|max:255'
        ]);

        // Hitung sisa berdasarkan stok terakhir
        $lastDetail = DB::table('detail_barang')
            ->where('kode_barang', $kode_barang)
            ->orderBy('tanggal', 'desc')
            ->orderBy('no_bukti', 'desc')
            ->first();

        $sisa = ($lastDetail ? $lastDetail->sisa : 0) + $validated['masuk'] - $validated['keluar'];

        // Insert detail baru
        DB::table('detail_barang')->insert([
            'kode_barang' => $kode_barang,
            'tanggal' => $validated['tanggal'],
            'no_bukti' => $validated['no_bukti'],
            'masuk' => $validated['masuk'],
            'keluar' => $validated['keluar'],
            'sisa' => $sisa,
            'alamat' => $validated['alamat'],
            'keterangan' => $validated['keterangan']
        ]);

        // Update stok di tabel barang
        DB::table('barang')
            ->where('kode_barang', $kode_barang)
            ->update(['stok' => $sisa]);

        return redirect()->route('barang.detail', $kode_barang)
            ->with('success', 'Detail transaksi berhasil ditambahkan');
    }
}