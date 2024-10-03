<?php
use Tests\TestCase;
use App\Models\User;
use Spatie\Permission\Models\Role;
use App\Models\Customer;
use App\Models\Subscription;
use App\Models\Program;
use App\Models\Merchant;
use App\Models\UserVerify;
use App\Services\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserServiceTest extends TestCase
{
    use RefreshDatabase, WithFaker;
    
    protected function setUp(): void
    {
        parent::setUp();
        // // Create roles and other necessary records if needed
        // Role::create(['name' => 'customer']);
    }

    public function test_registration_success()
    {
        $user = new User();
        $role = new Role(); 
        $userVerify = new UserVerify();
        $customer = new Customer();
        $subscription = new Subscription();
        $program = new Program();
        $merchant = new Merchant();
        $userService = new UserService($user, $role, $userVerify, $customer, $subscription, $program, $merchant);
        $request = [
            'email' => 'kk@gmail.com',
            'phone_number' => '0773221123',
            'phone_prefix' => '256',
            'password' => 'test',
            'user_type' => 'customer'
        ];
        $response = $userService->createUser($request);

        // Assert: Check that the response email is the same as request email
        $this->assertEquals($request['email'], $response->email);
    }

    public function test_fetch_role()
    {
        $user = new User();
        $role = new Role(); 
        $userVerify = new UserVerify();
        $customer = new Customer();
        $subscription = new Subscription();
        $program = new Program();
        $merchant = new Merchant();
        $userService = new UserService($user, $role, $userVerify, $customer, $subscription, $program, $merchant);

        Role::create(['name' => 'merchant']);
        $request = [
            'user_type' => 'merchant'
        ];
        $response = $userService->fetchRole($request['user_type']);
       // Assert: Check that the response is not null
        $this->assertNotNull($response);
    }

}