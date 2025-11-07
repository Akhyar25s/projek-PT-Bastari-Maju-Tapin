<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    protected $table = 'barang';
    protected $primaryKey = 'kode_barang';
    protected $keyType = 'string';
    public $incrementing = false;
    
    protected $fillable = [
        'kode_barang',
        'nama_barang',
        'satuan',
        'stok',
        'sisa'
    ];

    public function orders()
    {
        return $this->hasMany(Order::class, 'id_barang', 'kode_barang');
    }

    public function getSisaAttribute()
    {
        return $this->stok - ($this->orders()->where('status', '!=', 'rejected')->sum('jumlah') ?? 0);
    }
}