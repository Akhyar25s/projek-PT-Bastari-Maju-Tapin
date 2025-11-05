<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order', function (Blueprint $table) {
            $table->id('id_order')->primary();
            $table->unsignedbiginteger('id_barang');
            $table->string('satuan');
            $table->integer('volume');
            $table->foreign('id_barang')->references('kode_barang')->on('barang')->onDelete('cascade');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order');
    }
};
