<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesPaymentDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'sales_payment_id',
        'sales_id',
        // 'account_id',
        'total',
    ];

    // Relasi ke header payment
    public function salesPayment()
    {
        return $this->belongsTo(SalesPayment::class);
    }

    // Relasi ke sales
    public function sales()
    {
        return $this->belongsTo(Sales::class);
    }

    // Relasi ke akun kas/bank
    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}
