<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_that_true_is_true()
    {
        $this->assertTrue(true);
    }

    // public function a_user_is_a_customer()
    // {
    //     $user = User::factory()->create();
    //     $customer = Customer::factory()->create(['user_id' => $user->id]);
    //     $this->assertInstanceOf(Customer::class, $user->customer);
    //     $this->assertEquals(1, $user->customer->count());
    // }

    // public function a_customer_is_a_user()
    // {
    //     $user = User::factory()->create();
    //     $customer = Customer::factory()->create(['user_id' => $user->id]);
    //     $this->assertInstanceOf(User::class, $customer->user);
    //     $this->assertEquals(1, $customer->user->count());
    // }
}
