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
         Schema::create('pengguna', function (Blueprint $table) {
            $table->unsignedBigInteger('id_aktor');
            $table->unsignedBigInteger('id_role');
            $table->foreign('id_aktor')->references('id_aktor')->on('aktor')->onDelete('cascade');
            $table->foreign('id_role')->references('id_role')->on('role')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
   {
        Schema::dropIfExists('pengguna');
    }
};
