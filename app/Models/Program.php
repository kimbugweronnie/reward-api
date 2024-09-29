<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'product',
        'percentage',
        'start_date',
        'due_date',
        'status',
        'merchant_id',
        'points',
        'expired'
    ];
    
    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }

    public function createprogram($request)
    {
        return $this::create($request);
    }

    public function program($id)
    {
        return $this::where('id',$id)->first();  
    }

    public function merchantPrograms($merchant_id)
    {
        return $this::where('merchant_id',$merchant_id)->get();  
    }

    public function activePrograms($merchant_id)
    {
        return $this::where('merchant_id',$merchant_id)->where('status','=',1)->get();  
    }

    public function subscribers($id)
    {
        return Subscription::where('program_id',$id)->select('user_id')->get();  
    }

    public function programSubscribers($id)
    {
        $users = [];
        $subscribers = $this->subscribers($id);
        foreach ($subscribers as $subscriber) {
            $user = $this->user($subscriber['user_id']);
            array_push($users,$user);
        }
        return $users;
    }

    public function user($id)
    {
        return User::where('id',$id)->select('id','email','points')->first();  
    }


    public function inactivePrograms($merchant_id)
    {
        return $this::where('merchant_id',$merchant_id)->where('status','=',0)->get();  
    }

    public function getPoints($merchant_id)
    {
        return $this::where('merchant_id',$merchant_id)->sum('points');  
    }

    public function expiredPoints($merchant_id)
    {
        return $this::where('merchant_id',$merchant_id)->where('status','=',0)->sum('points');  
    }


    public function programs()
    {
        return $this::all();  
    }

    public function getMerchant($id)
    {
        return $this::where('id','=',$id)->select('merchant_id')->first()['merchant_id'];
    }

    public function updateProgram($request,$id)
    {
        return $this::where('id','=',$id)->update($request);
    }

    public function deactivateProgram($id)
    {
        return $this::where('id','=',$id)->update(['status' => 'false']);
    }

    public function expireProgram($id)
    {
        return $this::where('id','=',$id)->update(['expired' => 'true']);
    }

    
}
