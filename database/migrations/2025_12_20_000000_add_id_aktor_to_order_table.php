<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Guard: hanya tambahkan kolom jika belum ada (mencegah duplicate column error)
        if (!Schema::hasColumn('order', 'id_aktor')) {
            Schema::table('order', function (Blueprint $table) {
                // Tambahkan kolom id_aktor untuk melacak siapa yang membuat order
                $table->string('id_aktor')->nullable()->after('id_order');
            });

            // Tambahkan foreign key jika tabel 'aktor' tersedia
            if (Schema::hasTable('aktor')) {
                Schema::table('order', function (Blueprint $table) {
                    $table->foreign('id_aktor')->references('id_aktor')->on('aktor')->onDelete('cascade');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Hanya drop jika kolom ada
        if (Schema::hasColumn('order', 'id_aktor')) {
            Schema::table('order', function (Blueprint $table) {
                // Drop foreign key jika ada
                try {
                    $table->dropForeign(['id_aktor']);
                } catch (\Exception $e) {
                    // ignore if foreign doesn't exist
                }
                $table->dropColumn('id_aktor');
            });
        }
    }
};

