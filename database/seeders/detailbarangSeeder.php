<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class detailbarangSeeder extends Seeder
{
    public function run(): void
    {
        $details = [
            // Riwayat untuk TAWAS (K1)
            [
                'kode_barang' => 'K1',
                'tanggal' => '2025-11-01',
                'no_bukti' => 'TW-001',
                'masuk' => 100,
                'keluar' => 0,
                'sisa' => 100,
                'alamat' => 'PT.BMT',
                'keterangan' => 'Stok awal TAWAS'
            ],
            [
                'kode_barang' => 'K1',
                'tanggal' => '2025-11-02',
                'no_bukti' => 'TW-002',
                'masuk' => 0,
                'keluar' => 30,
                'sisa' => 70,
                'alamat' => 'RANTAU',
                'keterangan' => 'Pengiriman ke RANTAU'
            ],

            // Riwayat untuk Seal Tape (kode 77)
            [
                'kode_barang' => '77',
                'tanggal' => '2025-11-01',
                'no_bukti' => 'ST-001',
                'masuk' => 200,
                'keluar' => 0,
                'sisa' => 200,
                'alamat' => 'PT.BMT',
                'keterangan' => 'Stok awal Seal Tape'
            ],
            [
                'kode_barang' => '77',
                'tanggal' => '2025-11-03',
                'no_bukti' => 'ST-002',
                'masuk' => 0,
                'keluar' => 50,
                'sisa' => 150,
                'alamat' => 'BINUANG',
                'keterangan' => 'Pengiriman ke BINUANG'
            ],

            // Riwayat untuk KAPORIT (K4)
            [
                'kode_barang' => 'K4',
                'tanggal' => '2025-11-01',
                'no_bukti' => 'KP-001',
                'masuk' => 150,
                'keluar' => 0,
                'sisa' => 150,
                'alamat' => 'PT.BMT',
                'keterangan' => 'Stok awal KAPORIT'
            ],
            [
                'kode_barang' => 'K4',
                'tanggal' => '2025-11-04',
                'no_bukti' => 'KP-002',
                'masuk' => 0,
                'keluar' => 40,
                'sisa' => 110,
                'alamat' => 'TAPIN',
                'keterangan' => 'Pengiriman ke TAPIN'
            ]
        ];

        foreach ($details as $detail) {
            DB::table('detail_barang')->insert($detail);
        }
    }
}
