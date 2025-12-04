<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    protected $table = 'barang';
    protected $primaryKey = 'kode_barang';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false; // tabel barang tidak memiliki kolom timestamps
    
    protected $fillable = [
        'kode_barang',
        'nama_barang',
        'satuan',
        'stok',
        'sisa',
        'harga'
    ];

    public function orders()
    {
        return $this->hasMany(Order::class, 'id_barang', 'kode_barang');
    }

    public function getSisaAttribute()
    {
        // Hanya kurangi order yang sudah benar-benar approved/final_approved
        $used = $this->orders()
            ->whereIn('status', ['approved', 'final_approved'])
            ->sum('jumlah') ?? 0;
        return max(0, $this->stok - $used);
    }
}