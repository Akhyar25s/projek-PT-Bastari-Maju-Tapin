<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class penggunaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pengguna = [
            // Gudang1 sebagai role Gudang
            ['id_aktor' => '01', 'id_role' => '01'],
            // Rencana sebagai role Rencana
            ['id_aktor' => '02', 'id_role' => '02'],
            // Direktur sebagai role Direktur
            ['id_aktor' => '03', 'id_role' => '03'],
            // Keuangan sebagai role Keuangan
            ['id_aktor' => '04', 'id_role' => '04'],
            // Admin sebagai role Admin
            ['id_aktor' => '05', 'id_role' => '05'],
        ];

        foreach ($pengguna as $penggunaItem) {
            DB::table('pengguna')->updateOrInsert(
                [
                    'id_aktor' => $penggunaItem['id_aktor'],
                    'id_role' => $penggunaItem['id_role']
                ],
                $penggunaItem
            );
        }
    }
}
