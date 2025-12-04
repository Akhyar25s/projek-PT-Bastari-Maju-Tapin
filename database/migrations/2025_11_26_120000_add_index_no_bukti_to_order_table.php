<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order', function (Blueprint $table) {
            if (!Schema::hasColumn('order', 'no_bukti')) {
                return; // Column must exist from previous migration
            }
            // Add index if not exists (MySQL will error if duplicate name; keep generic try/catch if needed)
            $table->index('no_bukti', 'order_no_bukti_index');
        });
    }

    public function down(): void
    {
        Schema::table('order', function (Blueprint $table) {
            $table->dropIndex('order_no_bukti_index');
        });
    }
};
