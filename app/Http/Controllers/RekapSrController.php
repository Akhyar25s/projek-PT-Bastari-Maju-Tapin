<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RekapSrController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $rekap = DB::table('rekap_sr')
            ->join('bulan', 'rekap_sr.id_bulan', '=', 'bulan.id_bulan')
            ->select('rekap_sr.*', 'bulan.nama_bulan')
            ->orderBy('rekap_sr.id_bulan')
            ->get();

        return view('rekap.sr.index', compact('rekap'));
    }

    /**
     * Display the specified resource.
     */
    public function show($sr)
    {
        $item = DB::table('rekap_sr')->where('sr', $sr)->first();

        if (! $item) {
            return redirect()->route('rekap.sr.index')->with('error', 'Rekap SR tidak ditemukan');
        }

        return view('rekap.sr.show', compact('item'));
    }
}
