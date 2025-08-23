<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function details()
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public static function generateCode($type)
    {
        $prefixes = [
            'purchase_quotation' => 'PQC',
            'sales_quotation'    => 'SQC',
            'purchase_order'     => 'PO',
            'sales_order'        => 'SO',
            'delivery_order'     => 'DO',
        ];

        $prefix = $prefixes[$type] ?? 'ORD';

        return $prefix . '-' . date('Ymd') . '-' . date('His');
    }
}
