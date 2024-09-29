<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $table = 'roles';

    protected $fillable = ['name','guard_name'];

    public function role($role)
    {
        return $this::where('guard_name',$role)->first();  
    }
}
