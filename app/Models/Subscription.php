<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'status',
        'card_number',
        'merchant_id',
        'program_id',
        'user_id'
    ];
    
    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function createSubscription($request,$cardnumber,$merchant_id)
    {
        return $this::create($request + ['card_number' => $cardnumber,'merchant_id' => $merchant_id,'status' => true]);
    }

    public function subscription($id,$user_id)
    {
        return $this::where('id',$id)->where('user_id',$user_id)->first();  
    }

    public function userSubscriptions($user_id)
    {
        return $this::where('user_id',$user_id)->select('program_id','merchant_id')->get();  
    }

    public function userSubscription($user_id)
    {
        return $this::where('user_id',$user_id)->where('status',1)->get();  
    }
    
    public function checkStatus($id)
    {
        return $this::where('id',$id)->select('status')->first();  
    }

    public function uniqueSubscription($user_id,$program_id)
    {
        return $this::where('user_id',$user_id)->where('program_id',$program_id)->first();  
    }
    
    public function subscribers($merchant_id)
    {
        return $this::where('merchant_id',$merchant_id)->select('user_id')->get();  
    }
    
    public function updateSubscription($request,$id)
    {
        return $this::where('id','=',$id)->update($request);
    } 

    public function unSubscibe($id)
    {
        return $this::where('id','=',$id)->update(['status' => false]);
    } 
    
    public function updateCardNumber($cardnumber,$id)
    {
        return $this::where('id','=',$id)->update(['card_number' => $cardnumber]);
    } 
}


