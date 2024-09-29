<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Otp extends Model
{
    use HasFactory;
    protected $table = 'otp_verify';

    protected $fillable = [
        'user_id',
        'otp',
        'expirytime'
    ];

    public function createotp($id,$otp,$expiry)
    { 
        return $this::create(['user_id' => $id,'otp' => $otp,'expirytime' => $expiry]);
    }

    public function otp($id)
    {
        return $this::where('user_id',$id)->first();  
    }

    public function verifyotp($id,$otp)
    {
        return $this::where('user_id',$id)->where('otp',$otp)->first();  
    }

    public function updateotp($id,$otp,$expiry)
    {
        return $this::where('user_id',$id)->update(['otp' => $otp,'expirytime' => $expiry]);  
    }

}
