<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class orderSeeder extends Seeder
{
    public function run(): void
    {
        $orders = [
            [
                'id_barang' => '77',
                'jumlah' => 50,
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id_barang' => '19',
                'jumlah' => 20,
                'status' => 'approved',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id_barang' => '82',
                'jumlah' => 15,
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id_barang' => '4',
                'jumlah' => 30,
                'status' => 'rejected',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        DB::table('order')->insert($orders);
    }
}