<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RekapController extends Controller
{
    /**
     * Mapping lokasi dari detail_barang.alamat ke kolom rekap
     */
    private function getLokasiMapping()
    {
        return [
            'RANTAU' => 'rantau',
            'BINUANG' => 'binuang',
            'TAP SELATAN' => 'tap sel',
            'CLU' => 'clu',
            'CLS' => 'cls',
            'TAPIN' => 'tap tengah', // TAPIN masuk ke tap tengah
            'TENGAH' => 'tap tengah',
            'BATU HAPU' => 'batu hapu',
            'BAKARANGAN' => 'bakarangan',
            'LOKPAIKAT' => 'lokpaikat',
            'SALBA' => 'salba',
            'PIANI' => 'piani',
        ];
    }

    /**
     * Hitung rekap dari detail_barang untuk bulan tertentu
     */
    private function hitungRekap($idBulan, $tahun = null)
    {
        if (!$tahun) {
            $tahun = date('Y');
        }

        // Ambil semua bulan
        $bulan = DB::table('bulan')->where('id_bulan', $idBulan)->first();
        if (!$bulan) {
            return null;
        }

        // Hitung total pengeluaran per lokasi dari detail_barang
        // Filter berdasarkan bulan dan tahun
        $query = DB::table('detail_barang')
            ->select('alamat', DB::raw('SUM(keluar) as total_keluar'))
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $idBulan)
            ->where('keluar', '>', 0)
            ->groupBy('alamat');

        $dataPengeluaran = $query->get();

        // Mapping lokasi
        $mapping = $this->getLokasiMapping();
        $rekap = [
            'rantau' => 0,
            'binuang' => 0,
            'tap sel' => 0,
            'clu' => 0,
            'cls' => 0,
            'tap tengah' => 0,
            'batu hapu' => 0,
            'bakarangan' => 0,
            'lokpaikat' => 0,
            'salba' => 0,
            'piani' => 0,
        ];

        foreach ($dataPengeluaran as $item) {
            $kolom = $mapping[$item->alamat] ?? null;
            if ($kolom) {
                $rekap[$kolom] += $item->total_keluar;
            }
        }

        // Hitung jumlah total
        $rekap['jumlah'] = array_sum($rekap);

        return $rekap;
    }

    /**
     * Update atau generate rekap untuk bulan tertentu
     * Method ini bisa dipanggil dari controller lain
     */
    public static function generateRekapStatic($idBulan, $tahun = null)
    {
        $controller = new self();
        return $controller->generateRekap($idBulan, $tahun);
    }

    /**
     * Update atau generate rekap untuk bulan tertentu
     */
    public function generateRekap($idBulan, $tahun = null)
    {
        if (!$tahun) {
            $tahun = date('Y');
        }

        $rekapData = $this->hitungRekap($idBulan, $tahun);
        
        if (!$rekapData) {
            return false;
        }

        // Update atau insert rekap SR
        $rekapSr = DB::table('rekap_sr')
            ->where('id_bulan', $idBulan)
            ->first();

        if ($rekapSr) {
            // Update existing
            DB::table('rekap_sr')
                ->where('id_bulan', $idBulan)
                ->update($rekapData);
        } else {
            // Insert new
            $rekapData['id_bulan'] = $idBulan;
            DB::table('rekap_sr')->insert($rekapData);
        }

        // Update atau insert rekap GM (untuk sementara, GM sama dengan SR)
        // Nanti bisa disesuaikan jika ada logika berbeda
        $rekapGm = DB::table('rekap_gm')
            ->where('id_bulan', $idBulan)
            ->first();

        if ($rekapGm) {
            // Update existing
            DB::table('rekap_gm')
                ->where('id_bulan', $idBulan)
                ->update($rekapData);
        } else {
            // Insert new
            $rekapData['id_bulan'] = $idBulan;
            DB::table('rekap_gm')->insert($rekapData);
        }

        return true;
    }

    /**
     * Display rekap SR and GM in one page
     */
    public function index(Request $request)
    {
        // Jika ada request untuk generate ulang rekap (Hanya Admin)
        if ($request->has('generate')) {
            if (!function_exists('canCreate') || !canCreate()) {
                return redirect()->route('rekap.index')
                    ->with('error', 'Anda tidak memiliki akses untuk generate rekap');
            }
            
            $idBulan = $request->input('bulan');
            $tahun = $request->input('tahun', date('Y'));
            
            if ($idBulan) {
                $this->generateRekap($idBulan, $tahun);
                return redirect()->route('rekap.index')
                    ->with('success', 'Rekap untuk bulan tersebut berhasil di-generate ulang.');
            }
        }

        // Ambil data rekap SR
        $rekapSr = DB::table('rekap_sr')
            ->join('bulan', 'rekap_sr.id_bulan', '=', 'bulan.id_bulan')
            ->select('rekap_sr.*', 'bulan.nama_bulan')
            ->orderBy('rekap_sr.id_bulan')
            ->get();

        // Ambil data rekap GM
        $rekapGm = DB::table('rekap_gm')
            ->join('bulan', 'rekap_gm.id_bulan', '=', 'bulan.id_bulan')
            ->select('rekap_gm.*', 'bulan.nama_bulan')
            ->orderBy('rekap_gm.id_bulan')
            ->get();

        // Ambil daftar bulan untuk dropdown generate
        $bulanList = DB::table('bulan')->orderBy('id_bulan')->get();

        return view('rekap.index', compact('rekapSr', 'rekapGm', 'bulanList'));
    }

    /**
     * Display detail SR
     */
    public function showSr($sr)
    {
        $item = DB::table('rekap_sr')
            ->join('bulan', 'rekap_sr.id_bulan', '=', 'bulan.id_bulan')
            ->select('rekap_sr.*', 'bulan.nama_bulan')
            ->where('rekap_sr.sr', $sr)
            ->first();

        if (!$item) {
            return redirect()->route('rekap.index')->with('error', 'Rekap SR tidak ditemukan');
        }

        return view('rekap.show-sr', compact('item'));
    }

    /**
     * Display detail GM
     */
    public function showGm($gm)
    {
        $item = DB::table('rekap_gm')
            ->join('bulan', 'rekap_gm.id_bulan', '=', 'bulan.id_bulan')
            ->select('rekap_gm.*', 'bulan.nama_bulan')
            ->where('rekap_gm.gm', $gm)
            ->first();

        if (!$item) {
            return redirect()->route('rekap.index')->with('error', 'Rekap GM tidak ditemukan');
        }

        return view('rekap.show-gm', compact('item'));
    }
}
