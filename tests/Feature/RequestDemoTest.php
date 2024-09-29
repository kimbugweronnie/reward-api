<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RequestDemoTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_create_request_demo_return_422()
    {
        $response = $this->postJson('/api/v2/request-demo', []);
        $response->assertStatus(422);
    }

    public function test_create_request_demo()
    {
        $response = $this->postJson('/api/v2/request-demo', [
            'company_name' => 'test limited',
            'company_email' => 'test@gmail.com',
            'phone_prefix' => '245',
            'phone_number' => '256773553311',
        ]);
        $response->assertStatus(201);
    }
}
