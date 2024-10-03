<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;


class UserLoginTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_user_wrong_credentials_login()
    {
        User::factory()->create([
            'email' => 'melanin@gmail.com',
            'mobile' => '077258374',
            'phone_prefix' => '256',
            'password' => 'test',
        ]);

        $response = $this->postJson('/api/v2/login', [
            'email' => 'asdfghjkl;zxcvba@gmail.com',
            'password' => 'test',
        ]);

        $response->assertStatus(401);
    }

    public function test_user_wrong_password_login()
    {
        User::factory()->create([
            'email' => 'mela@gmail.com',
            'mobile' => '077258374',
            'phone_prefix' => '256',
            'password' => 'test',
        ]);

        $response = $this->postJson('/api/v2/login', [
            'email' => 'mela@gmail.com',
            'password' => 'asdfghjkl;zxcvba',
        ]);

        $response->assertStatus(401);
    }

    public function test_user_failure_login()
    {
        User::factory()->create([
            'email' => 'mela@gmail.com',
            'mobile' => '077258374',
            'phone_prefix' => '256',
            'password' => 'test',
        ]);

        $response = $this->postJson('/api/v2/login', [
            'email' => 'mela@gmail.com',
            'password' => 'test',
        ]);

        $response->assertStatus(200);
    }

    public function test_customer_success_login()
    {
        $this->postJson('/api/v2/customer-register', [
            'first_name' => 'jane',
            'last_name' => 'doe',
            'user_type' => 'customer',
            'email' => 'mela@gmail.com',
            'phone_number' => '077258374',
            'phone_prefix' => '256',
            'password' => 'test',
        ]);

        $response = $this->postJson('/api/v2/login', [
            'email' => 'mela@gmail.com',
            'password' => 'test',
        ]);

        $response->assertStatus(200);
    }
    public function test_merchant_success_login()
    {
        $this->postJson('/api/v2/merchant-register', [
            'email' => 'luxorp@gmail.com',
            'password' => 'rk12345',
            'merchant_name' => 'weclome',
            'merchant_description' => 'merchant of venice',
            'phone_prefix' => '245',
            'phone_number' => '25677321133',
            'location' => 'location',
            'user_type' => 'merchant',
        ]);

        $response = $this->postJson('/api/v2/login', [
            'email' => 'luxorp@gmail.com',
            'password' => 'rk12345',
        ]);

        $response->assertStatus(200);
    }
}
