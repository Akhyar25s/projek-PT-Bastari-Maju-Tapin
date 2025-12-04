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
            Schema::create('rekap_gm', function (Blueprint $table) {
            // id() sudah primary otomatis
            $table->id('gm');
            $table->unsignedbiginteger('id_bulan');
            $table->integer('rantau')->default(0);
            $table->integer('binuang')->default(0);
            $table->integer('tap_sel')->default(0);
            $table->integer('clu')->default(0);
            $table->integer('cls')->default(0);
            $table->integer('tap_tengah')->default(0);
            $table->integer('batu_hapu')->default(0);
            $table->integer('bakarangan')->default(0);
            $table->integer('lokpaikat')->default(0);
            $table->integer('salba')->default(0);
            $table->integer('piani')->default(0);
            $table->integer('jumlah')->default(0);
            $table->foreign('id_bulan')->references('id_bulan')->on('bulan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::dropIfExists('rekap_gm');
    }
};
