<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('barang', 'harga')) {
            Schema::table('barang', function (Blueprint $table) {
                $table->decimal('harga', 15, 2)->nullable()->after('sisa');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('barang', 'harga')) {
            Schema::table('barang', function (Blueprint $table) {
                $table->dropColumn('harga');
            });
        }
    }
};
