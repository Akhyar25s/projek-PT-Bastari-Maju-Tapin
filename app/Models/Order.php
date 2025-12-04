<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'order';
    protected $primaryKey = 'id_order';
    
    protected $fillable = [
        'id_barang',
        'jumlah',
        'status',
        'tipe_rekap',
        'id_aktor',
        'no_bukti',
        'alamat',
        'keterangan',
        'harga_satuan',
        'total_harga',
        'batch_id'
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang', 'kode_barang');
    }

    public function aktor()
    {
        return $this->belongsTo(Aktor::class, 'id_aktor', 'id_aktor');
    }
}