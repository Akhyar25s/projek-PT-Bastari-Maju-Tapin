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
        'status'
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang', 'kode_barang');
    }
}