<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_amount',
        'payment_mode',
        'points_awarded',
        'points_used',
        'subscription_id',
        'user_id',
        'receipt_number',
        'program_id',
        'merchant_id'
    ];
    
    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function earnPoints($request)
    {
        return $this::create($request);
    }

    

   
}


