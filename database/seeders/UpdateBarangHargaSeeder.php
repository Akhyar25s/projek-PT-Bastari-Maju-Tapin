<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateBarangHargaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Update harga untuk barang yang belum punya harga
     */
    public function run(): void
    {
        // Ambil semua barang yang belum punya harga
        $barangTanpaHarga = DB::table('barang')
            ->whereNull('harga')
            ->orWhere('harga', 0)
            ->get();

        if ($barangTanpaHarga->count() === 0) {
            $this->command->info('Semua barang sudah memiliki harga.');
            return;
        }

        $this->command->info("Mengupdate harga untuk {$barangTanpaHarga->count()} barang...");

        // Update harga dengan nilai default berdasarkan kategori/jenis barang
        // Anda bisa sesuaikan harga ini sesuai kebutuhan
        foreach ($barangTanpaHarga as $barang) {
            // Generate harga default (sesuaikan dengan kebutuhan bisnis Anda)
            // Contoh: harga berdasarkan panjang nama atau random untuk demo
            $hargaDefault = $this->generateDefaultPrice($barang->nama_barang);
            
            DB::table('barang')
                ->where('kode_barang', $barang->kode_barang)
                ->update(['harga' => $hargaDefault]);
                
            $this->command->info("✓ {$barang->kode_barang} - {$barang->nama_barang}: Rp " . number_format($hargaDefault, 2, ',', '.'));
        }

        $this->command->info('Selesai mengupdate harga barang.');
    }

    /**
     * Generate default price based on item name
     * Sesuaikan logika ini dengan kebutuhan bisnis Anda
     */
    private function generateDefaultPrice($namaBarang): float
    {
        // Contoh sederhana: harga berdasarkan kategori nama
        $namaLower = strtolower($namaBarang);
        
        // Kategori harga berdasarkan jenis barang (sesuaikan dengan bisnis Anda)
        if (str_contains($namaLower, 'kertas') || str_contains($namaLower, 'paper')) {
            return rand(10000, 50000);
        } elseif (str_contains($namaLower, 'pulpen') || str_contains($namaLower, 'pen')) {
            return rand(2000, 15000);
        } elseif (str_contains($namaLower, 'kabel') || str_contains($namaLower, 'cable')) {
            return rand(50000, 200000);
        } elseif (str_contains($namaLower, 'komputer') || str_contains($namaLower, 'computer')) {
            return rand(500000, 5000000);
        } elseif (str_contains($namaLower, 'alat') || str_contains($namaLower, 'tool')) {
            return rand(25000, 150000);
        } else {
            // Default harga umum
            return rand(5000, 100000);
        }
    }
}
