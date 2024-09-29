<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Point extends Model
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function earnPoints($request)
    {
        return $this::create($request);
    }

    public function points($id)
    {
        return $this::where('user_id',$id)->select('points')->first()['points'];
    }

    public function point($id)
    {
        return $this::where('user_id',$id)->first();
    }

    public function getPoint($id)
    {
        return $this::where('id',$id)->first();  
    }

    public function pointsUpdate($id,$balance)
    {
        return $this::where('id',$id)->update(['balance_points' => $balance]);
    }

    public function pointsEarned($id)
    {
        return $this::where('user_id','=',$id)->where('points_awarded','>',0)->sum('points_awarded');
    }

    public function pointsMerchants($id,$merchant_id)
    {
        return $this::where('user_id','=',$id)->where('points_awarded','>',0)->where('merchant_id','=',$merchant_id)->sum('points_awarded');
    }
    
    public function pointsSpentMerchant($id,$merchant_id)
    {
        return $this::where('user_id','=',$id)->where('merchant_id','=',$merchant_id)->sum('points_used');
    }

    public function pointsRedeemed($merchant_id)
    {
        return $this::where('merchant_id','=',$merchant_id)->where('points_awarded','>',0)->sum('points_awarded');
    }
    
    public function SpentMerchant($merchant_id)
    {
        return $this::where('merchant_id','=',$merchant_id)->sum('points_used');
    }

    public function pointsSpent($id)
    {
        return $this::where('user_id','=',$id)->where('points_used','>',0)->sum('points_used'); 
    }
    
    public function pointsUsed($id)
    {
        return $this::where('id',$id)->first();  
    }



}
