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
        // Jalankan perubahan enum hanya untuk MySQL; SQLite tidak mendukung MODIFY COLUMN enum
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `order` MODIFY COLUMN `status` ENUM('pending', 'approved', 'rejected', 'final_approved') DEFAULT 'pending'");
        } else {
            // Pada SQLite kita biarkan apa adanya; aplikasi masih dapat menggunakan nilai 'final_approved'
            // karena SQLite memperlakukan enum sebagai TEXT dengan constraint.
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            // Kembalikan ke enum sebelumnya (tanpa final_approved)
            DB::statement("ALTER TABLE `order` MODIFY COLUMN `status` ENUM('pending', 'approved', 'rejected') DEFAULT 'pending'");
        }
        // SQLite: no action needed
    }
};
