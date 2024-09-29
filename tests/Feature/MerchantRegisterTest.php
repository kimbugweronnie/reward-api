<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class MerchantRegisterTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_making_an_api_request_return_422(): void
    {
        $response = $this->postJson('/api/v2/merchant-register', []);
        $response->assertStatus(422);
    }

    public function test_creating_merchant(): void
    {
        $response = $this->postJson('/api/v2/merchant-register', [
            'email' => 'luxorp@gmail.com',
            'password' => 'rk12345',
            'merchant_name' => 'weclome',
            'merchant_description' => 'merchant of venice',
            'phone_prefix' => '245',
            'phone_number' => '25677321133',
            'location' => 'location',
            'user_type' => 'merchant',
        ]);

        $response->assertStatus(201);
    }

    public function test_creating_a_merchant_with_duplicate_email(): void
    {
        User::factory()->create([
            'email' => 'melak@gmail.com',
            'mobile' => '077258374',
            'phone_prefix' => '256',
            'password' => 'test',
        ]);
        $response = $this->postJson('/api/v2/merchant-register', [
            'merchant_name' => 'jane',
            'merchant_description' => 'doe',
            'user_type' => 'customer',
            'location' => 'customer',
            'email' => 'melak@gmail.com',
            'phone_number' => '077258374',
            'phone_prefix' => '256',
            'password' => 'test',
        ]);

        $response->assertStatus(403);
    }
}
