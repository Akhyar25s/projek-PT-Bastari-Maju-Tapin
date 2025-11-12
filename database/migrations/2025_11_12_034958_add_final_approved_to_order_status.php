<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menambahkan status 'final_approved' ke enum status order
     * untuk mendukung alur birokrasi: Umum approve → Keuangan approve (final)
     */
    public function up(): void
    {
        // Ubah enum status untuk menambahkan 'final_approved'
        DB::statement("ALTER TABLE `order` MODIFY COLUMN `status` ENUM('pending', 'approved', 'rejected', 'final_approved') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembalikan ke enum sebelumnya (tanpa final_approved)
        // Hati-hati: jika ada data dengan status final_approved, akan error
        DB::statement("ALTER TABLE `order` MODIFY COLUMN `status` ENUM('pending', 'approved', 'rejected') DEFAULT 'pending'");
    }
};
