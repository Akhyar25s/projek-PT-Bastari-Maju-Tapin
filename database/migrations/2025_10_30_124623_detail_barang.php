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
            Schema::create('detail_barang', function (Blueprint $table) {
            $table->id('detail_barang')->primary();
            // kode_barang is alphanumeric in some items; store as string
            $table->string('kode_barang');
            $table->date('tanggal');
            $table->string('no_bukti');
            $table->integer('masuk');
            $table->integer('keluar');
            $table->integer('sisa');
            $table->enum('alamat', [
                'PT.BMT',
                'RANTAU',
                'BINUANG',
                'TAP SELATAN',
                'CLU',
                'CLS',
                'TAPIN',
                'TENGAH',
                'BATU HAPU',
                'BAKARANGAN',
                'LOKPAIKAT',
                'SALBA',
                'PIANI',
            ]);
            $table->string('keterangan');
            $table->foreign('kode_barang')->references('kode_barang')->on('barang')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_barang');
    }
};
