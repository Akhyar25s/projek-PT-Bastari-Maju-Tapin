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
        Schema::create('bulan', function (Blueprint $table) {
            // id() sudah otomatis menjadi primary key, tidak perlu ->primary() lagi
            $table->id('id_bulan');
            $table->string('nama_bulan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::dropIfExists('bulan');
    }
};
