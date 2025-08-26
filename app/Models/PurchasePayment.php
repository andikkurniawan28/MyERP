<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchasePayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'date',
        'grand_total',
        'currency',
        'contact_id',
        'user_id',
    ];

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
        return $this->hasMany(PurchasePaymentDetail::class);
    }

    // Relasi ke purchases melalui detail (many-to-many)
    public function purchases()
    {
        return $this->belongsToMany(
            Purchase::class,
            'purchase_payment_details',
            'purchase_payment_id',
            'purchase_id'
        )->withPivot('account_id', 'total')->withTimestamps();
    }

    public static function generateCode()
    {
        $prefix = "PRP";
        return $prefix.date('YmdHis');
    }
}
