<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function itemTransactionDetails()
    {
        return $this->hasMany(ItemTransactionDetail::class);
    }

    public function saldo()
    {
        $in = $this->itemTransactionDetails()->sum('in');
        $out = $this->itemTransactionDetails()->sum('out');
        return $in - $out;
    }

    // Relasi ke kategori barang
    public function category()
    {
        return $this->belongsTo(ItemCategory::class, 'item_category_id');
    }

    // Relasi ke satuan utama
    public function mainUnit()
    {
        return $this->belongsTo(Unit::class, 'main_unit_id');
    }

    // Relasi ke satuan sekunder
    public function secondaryUnit()
    {
        return $this->belongsTo(Unit::class, 'secondary_unit_id');
    }

    public function sales()
    {
        return $this->hasMany(SalesDetail::class);
    }

    public function purchase()
    {
        return $this->hasMany(PurchaseDetail::class);
    }
}
