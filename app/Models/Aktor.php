<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aktor extends Model
{
    protected $table = 'aktor';
    protected $primaryKey = 'id_aktor';
    public $timestamps = false;

    protected $fillable = [
        'nama_aktor',
        'password',
    ];
}
