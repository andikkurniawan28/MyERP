<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_id',
        'code',
        'date',
        'grand_total',
        'currency',
        'contact_id',
        'user_id',
    ];

    // Relasi ke account (account)
    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    // Relasi ke contact (supplier)
    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    // Relasi ke user (pembuat payment)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke detail payment (many)
    public function details()
    {
        return $this->hasMany(SalesPaymentDetail::class);
    }

    // Relasi ke saless melalui detail (many-to-many)
    public function saless()
    {
        return $this->belongsToMany(
            Sales::class,
            'sales_payment_details',
            'sales_payment_id',
            'sales_id'
        )->withPivot('account_id', 'total')->withTimestamps();
    }

    public static function generateCode()
    {
        $prefix = "SLP";
        return $prefix.date('YmdHis');
    }
}
