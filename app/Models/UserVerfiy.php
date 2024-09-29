<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class UserVerfiy extends Model
{
    use HasFactory;
    protected $table = 'users_verify';
   
    protected $fillable = ['user_id','token'];

    public function create_record($user_id)
    {
        $token = Str::random(64);
        return $this::create(['user_id' => $user_id,'token' => $token]);
    }

    public function record_by_user($user)
    {
        return $this::where('user',$user)->first();  
    }

    public function delete_by_user($email)
    {
        return $this::where('email',$email)->delete();  
    }
}
