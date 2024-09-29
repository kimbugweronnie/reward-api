<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = ['first_name', 'last_name', 'user_type', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customers()
    {
        return $this::all();
    }

    public function customer($id)
    {
        return $this::where('id', $id)->select('id', 'first_name', 'last_name', 'user_type', 'user_id')->first();
    }

    public function customerUser($id)
    {
        return $this::where('user_id', $id)->select('id', 'first_name', 'last_name', 'user_type', 'user_id')->first();
    }

    public function userCustomer($id)
    {
        return User::where('id', $id)->select('id', 'email', 'mobile', 'phone_prefix')->first();
    }

    public function createCustomer($first_name,$last_name,$user_type,$user_id)
    {
        return $this::create(['first_name' => $first_name,'last_name' => $last_name, 'user_type' => $user_type,'user_id' => $user_id]);
    }

    public function updateCustomer($request, $id)
    {
        return $this::where('id', '=', $id)->update($request);
    }
}
