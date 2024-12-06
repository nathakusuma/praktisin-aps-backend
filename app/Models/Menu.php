<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $table = 'menus';
    protected $primaryKey = 'id';
    protected $keyType = 'int';
    public $timestamps = false;
    public $incrementing = true;

    protected $fillable = [
        'nama',
        'deskripsi',
        'ketersediaan',
        'harga'
    ];

}
