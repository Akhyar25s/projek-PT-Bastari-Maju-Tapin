<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RekapGmController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $rekap = DB::table('rekap_gm')
            ->join('bulan', 'rekap_gm.id_bulan', '=', 'bulan.id_bulan')
            ->select('rekap_gm.*', 'bulan.nama_bulan')
            ->orderBy('rekap_gm.id_bulan')
            ->get();

        return view('rekap.gm.index', compact('rekap'));
    }

    /**
     * Display the specified resource.
     */
    public function show($gm)
    {
        $item = DB::table('rekap_gm')->where('gm', $gm)->first();

        if (! $item) {
            return redirect()->route('rekap.gm.index')->with('error', 'Rekap GM tidak ditemukan');
        }

        return view('rekap.gm.show', compact('item'));
    }
}
