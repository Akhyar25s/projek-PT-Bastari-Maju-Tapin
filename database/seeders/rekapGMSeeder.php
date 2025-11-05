<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class rekapGMSeeder extends Seeder
{
    public function run(): void
    {
        $rekap_gm = [
            [
                'id_bulan' => 1, // Januari
                'rantau' => 220,
                'binuang' => 160,
                'tap sel' => 180,
                'clu' => 130,
                'cls' => 140,
                'tap tengah' => 155,
                'batu hapu' => 170,
                'bakarangan' => 190,
                'lokpaikat' => 150,
                'sel' => 165,
                'jumlah' => 1660
            ],
            [
                'id_bulan' => 2, // Februari
                'rantau' => 210,
                'binuang' => 150,
                'tap sel' => 170,
                'clu' => 125,
                'cls' => 135,
                'tap tengah' => 145,
                'batu hapu' => 160,
                'bakarangan' => 180,
                'lokpaikat' => 140,
                'sel' => 155,
                'jumlah' => 1570
            ],
            [
                'id_bulan' => 3, // Maret
                'rantau' => 230,
                'binuang' => 170,
                'tap sel' => 190,
                'clu' => 135,
                'cls' => 145,
                'tap tengah' => 165,
                'batu hapu' => 180,
                'bakarangan' => 200,
                'lokpaikat' => 160,
                'sel' => 175,
                'jumlah' => 1750
            ]
        ];

        foreach ($rekap_gm as $rekap) {
            DB::table('rekap_gm')->insert($rekap);
        }
    }
}