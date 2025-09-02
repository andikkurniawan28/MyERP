<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $guarded = [];

    // Relasi ke satuan utama
    public function mainUnit()
    {
        return $this->belongsTo(Unit::class, 'main_unit_id');
    }
}
