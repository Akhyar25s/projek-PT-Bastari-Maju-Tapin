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
            $table->string('no_bukti')->nullable()->after('jumlah');
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
            ])->nullable()->after('no_bukti');
            $table->text('keterangan')->nullable()->after('alamat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order', function (Blueprint $table) {
            $table->dropColumn(['no_bukti', 'alamat', 'keterangan']);
        });
    }
};
