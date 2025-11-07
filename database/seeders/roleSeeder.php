<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class roleSeeder extends Seeder
{
    public function run(): void
    {
        $role = [
            ['id_role' => '01', 'nama_role' => 'Penjaga Gudang'],
            ['id_role' => '02', 'nama_role' => 'Perencanaan'],
            ['id_role' => '03', 'nama_role' => 'Direktur'],
            ['id_role' => '04', 'nama_role' => 'Umum'],
            ['id_role' => '05', 'nama_role' => 'Keuangan'],
            ['id_role' => '06', 'nama_role' => 'Admin'],
        ];

        foreach ($role as $roleItem) {
            DB::table('role')->updateOrInsert(
                ['id_role' => $roleItem['id_role']],
                $roleItem
            );
        }
    }
}
