<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class rekapSRSeeder extends Seeder
{
    public function run(): void
    {
        $rekap_sr = [
            [
                'id_bulan' => 1, // Januari
                'rantau' => 250,
                'binuang' => 180,
                'tap sel' => 200,
                'clu' => 150,
                'cls' => 160,
                'tap tengah' => 175,
                'batu hapu' => 190,
                'bakarangan' => 210,
                'lokpaikat' => 170,
                'sel' => 185,
                'jumlah' => 1870
            ],
            [
                'id_bulan' => 2, // Februari
                'rantau' => 240,
                'binuang' => 170,
                'tap sel' => 190,
                'clu' => 145,
                'cls' => 155,
                'tap tengah' => 165,
                'batu hapu' => 180,
                'bakarangan' => 200,
                'lokpaikat' => 160,
                'sel' => 175,
                'jumlah' => 1780
            ],
            [
                'id_bulan' => 3, // Maret
                'rantau' => 260,
                'binuang' => 190,
                'tap sel' => 210,
                'clu' => 155,
                'cls' => 165,
                'tap tengah' => 185,
                'batu hapu' => 200,
                'bakarangan' => 220,
                'lokpaikat' => 180,
                'sel' => 195,
                'jumlah' => 1960
            ]
        ];

        foreach ($rekap_sr as $rekap) {
            DB::table('rekap_sr')->insert($rekap);
        }
    }
}