<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    
    public function run()
    {
        $role1 = Role::create(['guard_name' => 'customer', 'name' => 'customer']);
        $role2 = Role::create(['guard_name' => 'merchant', 'name' => 'merchant']);
    }

}
