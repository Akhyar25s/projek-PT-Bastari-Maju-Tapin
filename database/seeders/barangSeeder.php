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
            ['kode_barang' => '19', 'nama_barang' => 'Lem Pipa ( Tube )', 'satuan' => 'Kaleng', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => '18', 'nama_barang' => 'Lem Pipa ( Kaleng )', 'satuan' => 'Kaleng', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => '83', 'nama_barang' => 'Pipa GI Ø 1/2"', 'satuan' => 'Meter', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => '72', 'nama_barang' => 'Pipa HDPE Ø 1/2 "', 'satuan' => 'Meter', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => '82', 'nama_barang' => 'Clamp Saddle HDPE Ø 2" X 1 1/4"', 'satuan' => 'Buah', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => '81', 'nama_barang' => 'Clamp Saddle HDPE Ø 3" X 1 1/4"', 'satuan' => 'Buah', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => '80', 'nama_barang' => 'Clamp Saddle HDPE Ø 4" X 1 1/4"', 'satuan' => 'Buah', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => '10', 'nama_barang' => 'Clamp Saddle Ø 6" X 2"', 'satuan' => 'Buah', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => '24', 'nama_barang' => 'Clamp Saddle Ø 8"x 3/4"', 'satuan' => 'Buah', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => '35', 'nama_barang' => 'Clamp Saddle Ø 8" X 2"', 'satuan' => 'Buah', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => '120', 'nama_barang' => 'Gibolt Joint Ø 10"', 'satuan' => 'Buah', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => '49', 'nama_barang' => 'Gibolt Joint Ø 2"', 'satuan' => 'Buah', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => '50', 'nama_barang' => 'Gibolt Joint Ø 3"', 'satuan' => 'Buah', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => '51', 'nama_barang' => 'Gibolt Joint Ø 4"', 'satuan' => 'Buah', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => '52', 'nama_barang' => 'Gibolt Joint Ø 6"', 'satuan' => 'Buah', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => '124', 'nama_barang' => 'Gibolt Joint Ø 8"', 'satuan' => 'Buah', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => '14', 'nama_barang' => 'Knee PVC Drat Dalam Ø 1/2"', 'satuan' => 'Buah', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => '15', 'nama_barang' => 'Knee PVC Drat Dalam Ø 3/4"', 'satuan' => 'Buah', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => '17', 'nama_barang' => 'Knee PVC Ø 1/2"', 'satuan' => 'Buah', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => '60', 'nama_barang' => 'Knee PVC Ø 2"', 'satuan' => 'Buah', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => '16', 'nama_barang' => 'Knee PVC Ø 3/4"', 'satuan' => 'Buah', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => '125', 'nama_barang' => 'Knee Ø 1"', 'satuan' => 'Buah', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => '13', 'nama_barang' => 'Knee GI Ø 3/4"', 'satuan' => 'Buah', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => '89', 'nama_barang' => 'Reducer Ø 4" X 2"', 'satuan' => 'Buah', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => '104', 'nama_barang' => 'Reducer Ø 4" X 3"', 'satuan' => 'Buah', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => '68', 'nama_barang' => 'Reducer PVC Ø 250 x 110 MM', 'satuan' => 'Buah', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => '63', 'nama_barang' => 'Reducing Tee PVC SNI Ø 110 X 63 MM', 'satuan' => 'Buah', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => '118', 'nama_barang' => 'Reducing Tee PVC SNI Ø 250 X 110 MM', 'satuan' => 'Buah', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => '9', 'nama_barang' => 'Double Nipple Ø 1/2"', 'satuan' => 'Buah', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => '69', 'nama_barang' => 'Male Thread Elbow Ø 1/2"', 'satuan' => 'Buah', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => '4', 'nama_barang' => 'Tap Kran Ø 1/2"', 'satuan' => 'Buah', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => '5', 'nama_barang' => 'Stop Kran Ø 1/2"', 'satuan' => 'Buah', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => '78', 'nama_barang' => 'Check Valve Ø 1/2"', 'satuan' => 'Buah', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => '107', 'nama_barang' => 'Flange Steel Ø 2"', 'satuan' => 'Buah', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => '108', 'nama_barang' => 'Flange Steel Ø 4"', 'satuan' => 'Buah', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => '61', 'nama_barang' => 'Flange Steel Ø 10"', 'satuan' => 'Buah', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => '66', 'nama_barang' => 'Flange GI Ø 4"', 'satuan' => 'Buah', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => '2', 'nama_barang' => 'Flange Socket PVC Ø 2"', 'satuan' => 'Buah', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => '100', 'nama_barang' => 'Flange Socket PVC Ø 3"', 'satuan' => 'Buah', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => '99', 'nama_barang' => 'Flange Socket PVC Ø 6"', 'satuan' => 'Buah', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => '117', 'nama_barang' => 'Flange Socket PVC Ø 8"', 'satuan' => 'Buah', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => '115', 'nama_barang' => 'Flange Socket PVC Ø 10"', 'satuan' => 'Buah', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => '96', 'nama_barang' => 'Flange Spigot Ø 2"', 'satuan' => 'Buah', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => '94', 'nama_barang' => 'Flange Spigot Ø 4"', 'satuan' => 'Buah', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => '47', 'nama_barang' => 'Flange Spigot PVC Ø 6"', 'satuan' => 'Buah', 'stok' => '', 'sisa'=>''],
            ['kode_barang' => '70', 'nama_barang' => 'Stub End Ø 12"', 'satuan' => 'Buah', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => '111', 'nama_barang' => 'Stub Flange HDPE Ø 2"', 'satuan' => 'Buah', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => '112', 'nama_barang' => 'Stub Flange HDPE Ø 4"', 'satuan' => 'Buah', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => '62', 'nama_barang' => 'Stub Flange HDPE Ø 8"', 'satuan' => 'Buah', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => '46', 'nama_barang' => 'Stub End HDPE Ø 250 MM + Bacing Ring', 'satuan' => 'Buah', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => '42', 'nama_barang' => 'Water Moer Ø 1/2"', 'satuan' => 'Buah', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => '43', 'nama_barang' => 'Water Moer Ø 3/4"', 'satuan' => 'Buah', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => '34', 'nama_barang' => 'Water Moer Ø 2"', 'satuan' => 'Buah', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => '73', 'nama_barang' => 'Feruller Tee HDPE 1 1/4" x 1/2"', 'satuan' => 'Buah', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => '8', 'nama_barang' => 'Double Nipple Ø 3/4', 'satuan' => 'Buah', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => '114', 'nama_barang' => 'Baut Ø 1" X 3"', 'satuan' => 'Buah', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => '113', 'nama_barang' => 'Baut Ø 3/4" X 3"', 'satuan' => 'Buah', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => '22', 'nama_barang' => 'Clamp Saddle Ø 2" X 1/2"', 'satuan' => 'Buah', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => '21', 'nama_barang' => 'Clamp Saddle Ø 3" X 1/2"', 'satuan' => 'Buah', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => '20', 'nama_barang' => 'Clamp Saddle Ø 4" X 1/2"', 'satuan' => 'Buah', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => '12', 'nama_barang' => 'Knee GI Ø 1/2"', 'satuan' => 'Buah', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => '37', 'nama_barang' => 'Tee GI Ø 1/2"', 'satuan' => 'Buah', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => '86', 'nama_barang' => 'Dop PVC Ø 1/2"', 'satuan' => 'Buah', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => '32', 'nama_barang' => 'Dop GI Ø 20 MM', 'satuan' => 'Buah', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => '71', 'nama_barang' => 'Dop PVC Ø 3" (90 MM)', 'satuan' => 'Buah', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => '67', 'nama_barang' => 'Dop PVC Ø 250 MM', 'satuan' => 'Buah', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => '39', 'nama_barang' => 'L. Boch GI Ø 13 MM', 'satuan' => 'Buah', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => '38', 'nama_barang' => 'L. Boch GI Ø 20 MM', 'satuan' => 'Buah', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => '106', 'nama_barang' => 'L. Boch GI Ø 4"', 'satuan' => 'Buah', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => '65', 'nama_barang' => 'L. Boch PVC Ø 250 MM 45', 'satuan' => 'Buah', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => '64', 'nama_barang' => 'L. Boch PVC Ø 250 MM 90', 'satuan' => 'Buah', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => '95', 'nama_barang' => 'Elbow PVC Ø 100 MM X 90°', 'satuan' => 'Buah', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => '6', 'nama_barang' => 'Stop Kran GI Ø 3/4"', 'satuan' => 'Buah', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => '101', 'nama_barang' => 'Stop Kran PVC Ø 2"', 'satuan' => 'Buah', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => '11', 'nama_barang' => 'Stop Kran Kuningan ONDA Ø 3/4 "', 'satuan' => 'Buah', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => '79', 'nama_barang' => 'Plug Kran GI Ø 1/2"', 'satuan' => 'Buah', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => '58', 'nama_barang' => 'Tee PVC Ø 3" X 2"', 'satuan' => 'Buah', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => '59', 'nama_barang' => 'Tee PVC Ø 3" X 3"', 'satuan' => 'Buah', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => '126', 'nama_barang' => 'Tee PVC Ø 4" X 3"', 'satuan' => 'Buah', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => '97', 'nama_barang' => 'Tee PVC Ø 6" X 3"', 'satuan' => 'Buah', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => '93', 'nama_barang' => 'Tee PVC Ø 6" X 4"', 'satuan' => 'Buah', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => '103', 'nama_barang' => 'Tee Sock GI Ø 3/4"', 'satuan' => 'Buah', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => '33', 'nama_barang' => 'Tee GI Ø 1" X 1/2" (25 X 13 MM)', 'satuan' => 'Buah', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => '7', 'nama_barang' => 'Butterfly Valve ECONOSTO 4"', 'satuan' => 'Buah', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => '92', 'nama_barang' => 'Valve Ø 6"', 'satuan' => 'Buah', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => '45', 'nama_barang' => 'Valve Socket Ø 2"', 'satuan' => 'Buah', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => '53', 'nama_barang' => 'Ball Valve PVC Ø 2"', 'satuan' => 'Buah', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => '31', 'nama_barang' => 'Brass Ball Valve Ø 2"', 'satuan' => 'Buah', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => '91', 'nama_barang' => 'Ball Valve PVC Ø 6"', 'satuan' => 'Buah', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => '121', 'nama_barang' => 'Ball Valve PVC Ø 3"', 'satuan' => 'Buah', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => '98', 'nama_barang' => 'Gate Valve Ø 8"', 'satuan' => 'Buah', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => '116', 'nama_barang' => 'Gate Valve Ø 10"', 'satuan' => 'Buah', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => '127', 'nama_barang' => 'STUB END DHPE 500 +FLANGE', 'satuan' => 'Buah', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => '128', 'nama_barang' => 'NOZZLE STRAINER WTP 3/4', 'satuan' => 'Buah', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => '129', 'nama_barang' => 'CLAMP SEDLE 6X1/2', 'satuan' => 'Buah', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => '130', 'nama_barang' => 'GATE VALVE 3 "', 'satuan' => 'Buah', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => '131', 'nama_barang' => 'Packing Karet Ф 3"', 'satuan' => 'Buah', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => '132', 'nama_barang' => 'Strainer Dia 2" (Saringan Y)', 'satuan' => 'Buah', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => '133', 'nama_barang' => 'Air Relase Valve Dia 2"', 'satuan' => 'Buah', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => '134', 'nama_barang' => 'Pipa Hdpe 12 "', 'satuan' => 'Meter', 'stok' => '', 'sisa'=>''],
            ['kode_barang' => 'K1', 'nama_barang' => 'TAWAS', 'satuan' => 'Kg', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => 'K2', 'nama_barang' => 'PAC LIQUID', 'satuan' => 'Kg', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => 'K3', 'nama_barang' => 'PAC POWDER', 'satuan' => 'Kg', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => 'K4', 'nama_barang' => 'KAPORIT', 'satuan' => 'Kg', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => 'K5', 'nama_barang' => 'SODA ASH', 'satuan' => 'Kg', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => 'K6', 'nama_barang' => 'GAS CHLOR', 'satuan' => 'Kg', 'stok' => '', 'sisa' => ''],
            ['kode_barang' => '105', 'nama_barang' => 'Air Valve Ø 3/4"', 'satuan' => 'Buah', 'stok' => '', 'sisa'=>'']
        ];

        foreach ($barang as $barangItem) {
            // ensure integer fields are valid (convert empty strings to 0)
            $barangItem['stok'] = isset($barangItem['stok']) && $barangItem['stok'] !== '' ? (int) $barangItem['stok'] : 0;
            $barangItem['sisa'] = isset($barangItem['sisa']) && $barangItem['sisa'] !== '' ? (int) $barangItem['sisa'] : 0;

            DB::table('barang')->updateOrInsert(
                ['kode_barang' => $barangItem['kode_barang']],
                $barangItem
            );
        }
    }
}
