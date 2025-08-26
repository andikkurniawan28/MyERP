<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function sales()
    {
        return $this->hasMany(Sales::class);
    }

    public function payable()
    {
        return $this->purchases()->where('status', '!=', 'Lunas')->sum('remaining');
    }

    public function receivable()
    {
        return $this->sales()->where('status', '!=', 'Lunas')->sum('remaining');
    }

    public static function purchaseNotPaid($id)
    {
        return Purchase::where('contact_id', $id)
            ->where('status', '!=', 'Lunas')
            ->get();
    }

    public static function salesNotPaid($id)
    {
        return Sales::where('contact_id', $id)
            ->where('status', '!=', 'Lunas')
            ->get();
    }
}
