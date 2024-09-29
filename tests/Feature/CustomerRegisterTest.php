<?php

namespace Tests\Feature;


use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Customer;

class CustomerRegisterTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_making_an_api_request_return_422(): void
    {
        $response = $this->postJson('/api/v2/customer-register', []);

        $response->assertStatus(422);
    }

    public function test_creating_a_customer(): void
    {
        $response = $this->postJson('/api/v2/customer-register', [
            'first_name' => 'jane',
            'last_name' => 'doe',
            'user_type' => 'customer',
            'email' => 'mela@gmail.com',
            'phone_number' => '077258374',
            'phone_prefix' => '256',
            'password' => 'test',
        ]);

        $response->assertStatus(201);
    }

    public function test_creating_a_customer_with_duplicate_email(): void
    {
        User::factory()->create([
            'email' => 'mela@gmail.com',
            'mobile' => '077258374',
            'phone_prefix' => '256',
            'password' => 'test',
        ]);
        $response = $this->postJson('/api/v2/customer-register', [
            'first_name' => 'jane',
            'last_name' => 'doe',
            'user_type' => 'customer',
            'email' => 'mela@gmail.com',
            'phone_number' => '077258374',
            'phone_prefix' => '256',
            'password' => 'test',
        ]);

        $response->assertStatus(403);
    }
}
