<?php

namespace Tests\Feature;

use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;
use App\Models\User;


class ExampleTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_the_application_returns_a_successful_response()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    } 

    public function test_the_homepage_contains_tests()
    {
        $response = $this->get('/');
        $response->assertSee(value: 'tests');
        $response->assertStatus(200);
    }

    public function a_user_is_a_customer()
    {
        $user = User::factory()->create();
        $customer = Customer::factory()->create(['user_id' => $user->id]);
        $this->assertInstanceOf(Customer::class, $user->customer);
        $this->assertEquals(1, $user->customer->count());
    }

    public function a_customer_is_a_user()
    {
        $user = User::factory()->create();
        $customer = Customer::factory()->create(['user_id' => $user->id]);
        $this->assertInstanceOf(User::class, $customer->user);
        $this->assertEquals(1, $customer->user->count());
    }

    

   
    
    
}
