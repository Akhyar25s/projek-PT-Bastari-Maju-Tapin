<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class barangrusakSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $barang_rusak = [
            [
                'kode_barang' => '77',
                'volume' => 5,
                'status' => 'Rusak'
            ],
        ];

        foreach ($barang_rusak as $item) {
            DB::table('barang_rusak')->insert($item);
        }
    }
}