<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class bulanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bulan = [
            ['id_bulan' => '01', 'nama_bulan' => 'Januari'],
            ['id_bulan' => '02', 'nama_bulan' => 'Februari'],
            ['id_bulan' => '03', 'nama_bulan' => 'Maret'],
            ['id_bulan' => '04', 'nama_bulan' => 'April'],
            ['id_bulan' => '05', 'nama_bulan' => 'Mei'],
            ['id_bulan' => '06', 'nama_bulan' => 'Juni'],
            ['id_bulan' => '07', 'nama_bulan' => 'Juli'],
            ['id_bulan' => '08', 'nama_bulan' => 'Agustus'],
            ['id_bulan' => '09', 'nama_bulan' => 'September'],
            ['id_bulan' => '10', 'nama_bulan' => 'Oktober'],
            ['id_bulan' => '11', 'nama_bulan' => 'November'],
            ['id_bulan' => '12', 'nama_bulan' => 'Desember']
        ];

        foreach ($bulan as $bulanItem) {
            DB::table('bulan')->updateOrInsert(
                ['id_bulan' => $bulanItem['id_bulan']],
                $bulanItem
            );
        }
    }
}