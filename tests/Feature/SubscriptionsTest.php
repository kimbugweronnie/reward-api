<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Subscription;
use App\Models\User;
use App\Models\Merchant;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;

class SubscriptionTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_getting_subscriptions()
    {
        $user = User::factory()->create([
            'email' => 'melak@gmail.com',
            'mobile' => '077258374',
            'phone_prefix' => '256',
            'password' => 'test',
        ]);

        $role = Role::create(['guard_name' => 'customer', 'name' => 'customer']);
        $user->assignRole($role);
        Sanctum::actingAs($user);
        $response = $this->getJson('/api/v2/subscriptions/'. $user->id);
        $response->assertStatus(200);
    }

    
}
