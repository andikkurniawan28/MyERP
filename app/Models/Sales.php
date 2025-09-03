<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sales extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function details()
    {
        return $this->hasMany(SalesDetail::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function salesPayments()
    {
        return $this->hasMany(SalesPayment::class);
    }

    public function payments()
    {
        return $this->hasMany(SalesPaymentDetail::class);
    }

    public static function generateCode()
    {
        $prefix = "SLS";
        return $prefix.date('YmdHis');
    }
}
