<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class orderSeeder extends Seeder
{
    public function run(): void
    {
        $orders = [
            [
                'id_barang' => '77', // Seal Tape/TBA
                'satuan' => 'Buah',
                'volume' => 50
            ],
            [
                'id_barang' => '19', // Lem Pipa (Tube)
                'satuan' => 'Kaleng',
                'volume' => 20
            ],
            [
                'id_barang' => '82', // Clamp Saddle HDPE Ø 2" X 1 1/4"
                'satuan' => 'Buah',
                'volume' => 15
            ],
            [
                'id_barang' => '4', // Tap Kran Ø 1/2"
                'satuan' => 'Buah',
                'volume' => 30
            ],
            [
                'id_barang' => '5', // Stop Kran Ø 1/2"
                'satuan' => 'Buah',
                'volume' => 25
            ]
        ];

        foreach ($orders as $order) {
            DB::table('order')->insert($order);
        }
    }
}