<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemTransaction extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function warehouse(){
        return $this->belongsTo(Warehouse::class);
    }

    public function purchase(){
        return $this->belongsTo(Purchase::class);
    }

    public function details()
    {
        return $this->hasMany(ItemTransactionDetail::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public static function generateCode()
    {
        return 'ITR'.date('YmdHis');
    }
}
