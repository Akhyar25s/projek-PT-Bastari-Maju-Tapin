<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('order', 'batch_id')) {
            Schema::table('order', function (Blueprint $table) {
                $table->string('batch_id', 50)->nullable()->after('id_order');
                $table->index('batch_id'); // Index untuk performa query
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('order', 'batch_id')) {
            Schema::table('order', function (Blueprint $table) {
                $table->dropIndex(['batch_id']);
                $table->dropColumn('batch_id');
            });
        }
    }
};
