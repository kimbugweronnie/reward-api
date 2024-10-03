<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class Merchant extends Model
{
    use HasRoles, HasFactory;
    protected $guard_name = ['merchant'];
    protected $fillable = ['merchant_name', 'merchant_description', 'user_type', 'location', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function userMerchant($user_id)
    {
        return User::where('id', $user_id)->select('id','email', 'mobile', 'phone_prefix')->first();
    }

    public function merchantUser($id)
    {
        return $this::where('user_id', $id)->select('id','merchant_name', 'user_type')->first();
    }

    public function createMerchant($merchant_name, $merchant_description, $user_type, $location, $user_id)
    {
        return $this::create(['merchant_name' => $merchant_name, 'merchant_description' => $merchant_description, 'user_type' => $user_type, 'location' => $location, 'user_id' => $user_id]);
    }

    public function createCustomer($first_name, $last_name, $user_type, $user_id)
    {
        return $this::create(['first_name' => $first_name, 'last_name' => $last_name, 'user_type' => $user_type, 'user_id' => $user_id]);
    }

    public function merchant($id)
    {
        return $this::where('id', $id)->first();
    }

    public function updateMerchant($request, $id)
    {
        return $this::where('id', '=', $id)->update($request);
    }
}
