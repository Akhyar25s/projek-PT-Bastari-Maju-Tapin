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
            Schema::create('rekap_sr', function (Blueprint $table) {
            $table->id('sr')->primary();
            $table->unsignedbiginteger('id_bulan');
            $table->integer('rantau');
            $table->integer('binuang');
            $table->integer('tap sel');
            $table->integer('clu');
            $table->integer('cls');
            $table->integer('tap tengah');
            $table->integer('batu hapu');
            $table->integer('bakarangan');
            $table->integer('lokpaikat');
            $table->integer('salba');
            $table->integer('piani');
            $table->integer('jumlah');
            $table->foreign('id_bulan')->references('id_bulan')->on('bulan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::dropIfExists('rekap_sr');
    }
};
