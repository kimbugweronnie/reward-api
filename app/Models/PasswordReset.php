<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

use Carbon\Carbon; 
use Mail; 
use Hash;

class PasswordReset extends Model
{
    use HasFactory;
    protected $table = 'password_resets';
    public $timestamps = false;

    protected $fillable = ['email','token','created_at'];

    public function create_record($request)
    {
        $token = Str::random(64);
        $created_at = Carbon::now();
        return $this::create($request + ['token' => $token,'created_at' => $created_at]);
    }

    public function record_by_email($email)
    {
        return $this::where('email',$email)->first();  
    }

    public function passwordreset($email)
    {
        return $this::destroy($email); 
    }

    public function delete_by_email($email)
    {
        return $this::where('email',$email)->delete();  
    }
}
