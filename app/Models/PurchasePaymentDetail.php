<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchasePaymentDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_payment_id',
        'purchase_id',
        'account_id',
        'total',
    ];

    // Relasi ke header payment
    public function purchasePayment()
    {
        return $this->belongsTo(PurchasePayment::class);
    }

    // Relasi ke purchase
    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    // Relasi ke akun kas/bank
    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}
