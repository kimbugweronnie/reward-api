<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guard_name = ['customer','merchant'];

    protected $fillable = [
        'email',
        'mobile',
        'phone_prefix',
        'password',   
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    public function setPasswordAttribute($password) 
    { 
        return $this->attributes['password'] = bcrypt($password); 
    }

    public function merchant()
    {
        return $this->hasOne(Merchant::class);
    }

    public function userMerchant($id)
    {
        return Merchant::where('user_id','=',$id)->select('user_type')->first();
    }

    public function userCustomer($id)
    {
        return Customer::where('user_id','=',$id)->select('user_type')->first();
    }

    public function customer()
    {
        return $this->hasOne(Customer::class);
    }
    
    public function createUser($email,$phone_number,$phone_prefix,$password)
    {
        return $this::create(['email' => $email,'mobile' => $phone_number,'phone_prefix' => $phone_prefix,'password'=>$password]);
    }

    public function userByUsername($username)
    {
        return $this::where('username',$username)->first();  
    }

    public function customers($role)
    {
        return $this::where('role',$role)->select('id','email')->get();  
    }

    public function user($id)
    {
        return $this::where('id',$id)->first();  
    }

    public function userByEmail($request)
    {
        return $this::where('email',$request->email)->orwhere('email',$request->email)->first();  
    }

    public function updatePassword($email,$password)
    {
        return $this::where('email','=',$email)->update(['password' =>$password]);
    }

    public function updateCustomer($request,$id)
    {
        return $this::where('id','=',$id)->update($request);
    }

    public function updateMerchant($request,$id)
    {
        return $this::where('id','=',$id)->update($request);
    }

    public function updateCustomerStatus($id)
    {
        return $this::where('id',$id)->update(['is_verified' => 1]);  
    }

    public function updatePoints($id,$balance)
    {
        return $this::where('id',$id)->update(['points' => $balance]);  
    }
    

}
