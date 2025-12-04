<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('order', 'harga_satuan')) {
            Schema::table('order', function (Blueprint $table) {
                $table->decimal('harga_satuan', 15, 2)->nullable()->after('jumlah');
                $table->decimal('total_harga', 15, 2)->nullable()->after('harga_satuan');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('order', 'harga_satuan') || Schema::hasColumn('order', 'total_harga')) {
            Schema::table('order', function (Blueprint $table) {
                if (Schema::hasColumn('order', 'total_harga')) {
                    $table->dropColumn('total_harga');
                }
                if (Schema::hasColumn('order', 'harga_satuan')) {
                    $table->dropColumn('harga_satuan');
                }
            });
        }
    }
};
