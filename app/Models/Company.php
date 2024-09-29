<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_name',
        'company_description',
        'company_email',
        'phone_prefix',
        'phone_number',
        'company_location',
        'status'
    ];

    public function createCompany($request)
    {
        return $this::create($request);
    }
    
}
