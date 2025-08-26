<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Journal extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function details()
    {
        return $this->hasMany(JournalDetail::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public static function generateCode()
    {
        return 'JRN'.date('YmdHis').strtoupper(substr(uniqid(), -4));
    }
}
