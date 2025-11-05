<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class barangSeeder extends Seeder
{
   public function run(): void
    {
        $barang = [
            ['kode_barang' => '77', 'nama_barang' => 'Seal Tape/TBA', 'satuan' => 'Buah', 'stok' => 100, 'sisa' => 90],
        ];

        foreach ($barang as $barangItem) {
            DB::table('barang')->updateOrInsert(
                ['kode_barang' => $barangItem['kode_barang']],
                $barangItem
            );
        }
    }
}
