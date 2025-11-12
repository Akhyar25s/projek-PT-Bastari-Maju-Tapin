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
        Schema::table('order', function (Blueprint $table) {
            // Tambahkan kolom id_aktor untuk melacak siapa yang membuat order
            $table->string('id_aktor')->nullable()->after('id_order');
            $table->foreign('id_aktor')->references('id_aktor')->on('aktor')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order', function (Blueprint $table) {
            $table->dropForeign(['id_aktor']);
            $table->dropColumn('id_aktor');
        });
    }
};

