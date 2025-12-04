<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class aktorSeeder extends Seeder
{
    public function run(): void
    {
        // Gunakan Hash::make agar password tidak tersimpan sebagai plain text
        $aktorRaw = [
            ['id_aktor' => '01', 'nama_aktor' => 'Gudang1', 'password' => 'gudang123'],
            ['id_aktor' => '02', 'nama_aktor' => 'Rencana', 'password' => 'rencana123'],
            ['id_aktor' => '03', 'nama_aktor' => 'Direktur', 'password' => 'direktur123'],
            ['id_aktor' => '04', 'nama_aktor' => 'Keuangan', 'password' => 'keuangan123'],
            ['id_aktor' => '05', 'nama_aktor' => 'Admin', 'password' => 'admin123'],
            ['id_aktor' => '06', 'nama_aktor' => 'Umum', 'password' => 'umum123'],
        ];

        foreach ($aktorRaw as $aktorItem) {
            $data = [
                'id_aktor' => $aktorItem['id_aktor'],
                'nama_aktor' => $aktorItem['nama_aktor'],
                'password' => Hash::make($aktorItem['password']),
            ];

            DB::table('aktor')->updateOrInsert([
                'id_aktor' => $data['id_aktor']
            ], $data);
        }
    }
}
