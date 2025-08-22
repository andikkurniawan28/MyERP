<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function inventoryAccount()
    {
        return $this->belongsTo(Account::class, 'inventory_account_id');
    }

    public function stockInAccount()
    {
        return $this->belongsTo(Account::class, 'stock_in_account_id');
    }

    public function stockOutAccount()
    {
        return $this->belongsTo(Account::class, 'stock_out_account_id');
    }
}
