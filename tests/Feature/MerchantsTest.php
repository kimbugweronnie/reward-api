<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Merchant;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;

class MerchantsTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_merchant_getting_number_of_customers_without_authentication()
    {
        $user = User::factory()->create([
            'email' => 'melak@gmail.com',
            'mobile' => '077258374',
            'phone_prefix' => '256',
            'password' => 'test',
        ]);

        $merchant = Merchant::factory()->create([
            'merchant_name' => 'mela',
            'merchant_description' => 'mela',
            'user_type' => 'merchant',
            'location' => 'test',
            'user_id' => $user->id,
        ]);
        $response = $this->getJson('/api/v2/' . $merchant->id . '/customers');
        $response->assertStatus(401);
    }

    public function test_unauthorized_user_getting_number_of_customers()
    {
        $this->withExceptionHandling();

        $user = User::factory()->create([
            'email' => 'melak@gmail.com',
            'mobile' => '077258374',
            'phone_prefix' => '256',
            'password' => 'test',
        ]);

        $merchant = Merchant::factory()->create([
            'merchant_name' => 'mela',
            'merchant_description' => 'mela',
            'user_type' => 'merchant',
            'location' => 'test',
            'user_id' => $user->id,
        ]);
        
        Sanctum::actingAs($user);
        $response = $this->getJson('/api/v2/' . $merchant->id . '/customers');
        $response->assertStatus(403);
    }

    public function test_authorized_user_getting_number_of_customers()
    {
        $this->withExceptionHandling();

        $user = User::factory()->create([
            'email' => 'melak@gmail.com',
            'mobile' => '077258374',
            'phone_prefix' => '256',
            'password' => 'test',
        ]);

        $merchant = Merchant::factory()->create([
            'merchant_name' => 'mela',
            'merchant_description' => 'mela',
            'user_type' => 'merchant',
            'location' => 'test',
            'user_id' => $user->id,
        ]);

        $role = Role::create(['guard_name' => 'merchant', 'name' => 'merchant']);
        $user->assignRole($role);
        Sanctum::actingAs($user);
        $response = $this->getJson('/api/v2/' . $merchant->id . '/customers');
        $response->assertStatus(200);
    }

    public function test_unauthorized_user_getting_number_of_programs()
    {
        $user = User::factory()->create([
            'email' => 'melak@gmail.com',
            'mobile' => '077258374',
            'phone_prefix' => '256',
            'password' => 'test',
        ]);

        $merchant = Merchant::factory()->create([
            'merchant_name' => 'mela',
            'merchant_description' => 'mela',
            'user_type' => 'merchant',
            'location' => 'test',
            'user_id' => $user->id,
        ]);
        Sanctum::actingAs($user);
        $response = $this->getJson('/api/v2/' . $merchant->id . '/programs');
        $response->assertStatus(403);
    }

    public function test_authorized_user_getting_number_of_programs()
    {
        $user = User::factory()->create([
            'email' => 'melak@gmail.com',
            'mobile' => '077258374',
            'phone_prefix' => '256',
            'password' => 'test',
        ]);

        $merchant = Merchant::factory()->create([
            'merchant_name' => 'mela',
            'merchant_description' => 'mela',
            'user_type' => 'merchant',
            'location' => 'test',
            'user_id' => $user->id,
        ]);

        $role = Role::create(['guard_name' => 'merchant', 'name' => 'merchant']);
        $user->assignRole($role);
        Sanctum::actingAs($user);
        $response = $this->getJson('/api/v2/' . $merchant->id . '/programs');
        $response->assertStatus(200);
    }

    public function test_unauthorized_user_getting_number_of_active_programs()
    {
        $user = User::factory()->create([
            'email' => 'melak@gmail.com',
            'mobile' => '077258374',
            'phone_prefix' => '256',
            'password' => 'test',
        ]);

        $merchant = Merchant::factory()->create([
            'merchant_name' => 'mela',
            'merchant_description' => 'mela',
            'user_type' => 'merchant',
            'location' => 'test',
            'user_id' => $user->id,
        ]);
        Sanctum::actingAs($user);
        $response = $this->getJson('/api/v2/' . $merchant->id . '/programs/active');
        $response->assertStatus(403);
    }

    public function test_authorized_user_getting_number_of_active_programs()
    {
        $user = User::factory()->create([
            'email' => 'melak@gmail.com',
            'mobile' => '077258374',
            'phone_prefix' => '256',
            'password' => 'test',
        ]);

        $merchant = Merchant::factory()->create([
            'merchant_name' => 'mela',
            'merchant_description' => 'mela',
            'user_type' => 'merchant',
            'location' => 'test',
            'user_id' => $user->id,
        ]);

        $role = Role::create(['guard_name' => 'merchant', 'name' => 'merchant']);
        $user->assignRole($role);
        Sanctum::actingAs($user);
        $response = $this->getJson('/api/v2/' . $merchant->id . '/programs/active');
        $response->assertStatus(200);
    }

    public function test_unauthorized_user_getting_number_of_inactive_programs()
    {
        $user = User::factory()->create([
            'email' => 'melak@gmail.com',
            'mobile' => '077258374',
            'phone_prefix' => '256',
            'password' => 'test',
        ]);

        $merchant = Merchant::factory()->create([
            'merchant_name' => 'mela',
            'merchant_description' => 'mela',
            'user_type' => 'merchant',
            'location' => 'test',
            'user_id' => $user->id,
        ]);
        Sanctum::actingAs($user);
        $response = $this->getJson('/api/v2/' . $merchant->id . '/programs/inactive');
        $response->assertStatus(403);
    }

    public function test_authorized_user_getting_number_of_inactive_programs()
    {
        $user = User::factory()->create([
            'email' => 'melak@gmail.com',
            'mobile' => '077258374',
            'phone_prefix' => '256',
            'password' => 'test',
        ]);

        $merchant = Merchant::factory()->create([
            'merchant_name' => 'mela',
            'merchant_description' => 'mela',
            'user_type' => 'merchant',
            'location' => 'test',
            'user_id' => $user->id,
        ]);

        $role = Role::create(['guard_name' => 'merchant', 'name' => 'merchant']);
        $user->assignRole($role);
        Sanctum::actingAs($user);
        $response = $this->getJson('/api/v2/' . $merchant->id . '/programs/inactive');
        $response->assertStatus(200);
    }

    public function test_unauthorized_user_getting_points()
    {
        $user = User::factory()->create([
            'email' => 'melak@gmail.com',
            'mobile' => '077258374',
            'phone_prefix' => '256',
            'password' => 'test',
        ]);

        $merchant = Merchant::factory()->create([
            'merchant_name' => 'mela',
            'merchant_description' => 'mela',
            'user_type' => 'merchant',
            'location' => 'test',
            'user_id' => $user->id,
        ]);

        Sanctum::actingAs($user);
        $response = $this->getJson('/api/v2/points/' . $merchant->id . '/get-points');
        $response->assertStatus(403);
    }

    public function test_authorized_user_getting_points()
    {
        $user = User::factory()->create([
            'email' => 'melak@gmail.com',
            'mobile' => '077258374',
            'phone_prefix' => '256',
            'password' => 'test',
        ]);

        $merchant = Merchant::factory()->create([
            'merchant_name' => 'mela',
            'merchant_description' => 'mela',
            'user_type' => 'merchant',
            'location' => 'test',
            'user_id' => $user->id,
        ]);

        $role = Role::create(['guard_name' => 'merchant', 'name' => 'merchant']);
        $user->assignRole($role);
        Sanctum::actingAs($user);
        $response = $this->getJson('/api/v2/points/' . $merchant->id . '/get-points');
        $response->assertStatus(200);
    }

    public function test_unauthorized_user_getting_redeemed_points()
    {
        $user = User::factory()->create([
            'email' => 'melak@gmail.com',
            'mobile' => '077258374',
            'phone_prefix' => '256',
            'password' => 'test',
        ]);

        $merchant = Merchant::factory()->create([
            'merchant_name' => 'mela',
            'merchant_description' => 'mela',
            'user_type' => 'merchant',
            'location' => 'test',
            'user_id' => $user->id,
        ]);

        Sanctum::actingAs($user);
        $response = $this->getJson('/api/v2/points/' . $merchant->id . '/redeemed');
        $response->assertStatus(403);
    }
    
    public function test_authorized_user_getting_redeemed_points()
    {
        $user = User::factory()->create([
            'email' => 'melak@gmail.com',
            'mobile' => '077258374',
            'phone_prefix' => '256',
            'password' => 'test',
        ]);

        $merchant = Merchant::factory()->create([
            'merchant_name' => 'mela',
            'merchant_description' => 'mela',
            'user_type' => 'merchant',
            'location' => 'test',
            'user_id' => $user->id,
        ]);

        $role = Role::create(['guard_name' => 'merchant', 'name' => 'merchant']);
        $user->assignRole($role);
        Sanctum::actingAs($user);
        $response = $this->getJson('/api/v2/points/' . $merchant->id . '/redeemed');
        $response->assertStatus(200);
    }

    public function test_unauthorized_user_getting_unredeemed_points()
    {
        $user = User::factory()->create([
            'email' => 'melak@gmail.com',
            'mobile' => '077258374',
            'phone_prefix' => '256',
            'password' => 'test',
        ]);

        $merchant = Merchant::factory()->create([
            'merchant_name' => 'mela',
            'merchant_description' => 'mela',
            'user_type' => 'merchant',
            'location' => 'test',
            'user_id' => $user->id,
        ]);

        Sanctum::actingAs($user);
        $response = $this->getJson('/api/v2/points/' . $merchant->id . '/unredeemed');
        $response->assertStatus(403);
    }
    
    public function test_authorized_user_getting_unredeemed_points()
    {
        $user = User::factory()->create([
            'email' => 'melak@gmail.com',
            'mobile' => '077258374',
            'phone_prefix' => '256',
            'password' => 'test',
        ]);

        $merchant = Merchant::factory()->create([
            'merchant_name' => 'mela',
            'merchant_description' => 'mela',
            'user_type' => 'merchant',
            'location' => 'test',
            'user_id' => $user->id,
        ]);

        $role = Role::create(['guard_name' => 'merchant', 'name' => 'merchant']);
        $user->assignRole($role);
        Sanctum::actingAs($user);
        $response = $this->getJson('/api/v2/points/' . $merchant->id . '/unredeemed');
        $response->assertStatus(200);
    }

    public function test_unauthorized_user_getting_expired_points()
    {
        $user = User::factory()->create([
            'email' => 'melak@gmail.com',
            'mobile' => '077258374',
            'phone_prefix' => '256',
            'password' => 'test',
        ]);

        $merchant = Merchant::factory()->create([
            'merchant_name' => 'mela',
            'merchant_description' => 'mela',
            'user_type' => 'merchant',
            'location' => 'test',
            'user_id' => $user->id,
        ]);

        Sanctum::actingAs($user);
        $response = $this->getJson('/api/v2/points/' . $merchant->id . '/expired');
        $response->assertStatus(403);
    }
    
    public function test_authorized_user_getting_expired_points()
    {
        $user = User::factory()->create([
            'email' => 'melak@gmail.com',
            'mobile' => '077258374',
            'phone_prefix' => '256',
            'password' => 'test',
        ]);

        $merchant = Merchant::factory()->create([
            'merchant_name' => 'mela',
            'merchant_description' => 'mela',
            'user_type' => 'merchant',
            'location' => 'test',
            'user_id' => $user->id,
        ]);

        $role = Role::create(['guard_name' => 'merchant', 'name' => 'merchant']);
        $user->assignRole($role);
        Sanctum::actingAs($user);
        $response = $this->getJson('/api/v2/points/' . $merchant->id . '/expired');
        $response->assertStatus(200);
    }

    public function test_unauthorized_getting_user()
    {
        $user = User::factory()->create([
            'email' => 'melak@gmail.com',
            'mobile' => '077258374',
            'phone_prefix' => '256',
            'password' => 'test',
        ]);

        $merchant = Merchant::factory()->create([
            'merchant_name' => 'mela',
            'merchant_description' => 'mela',
            'user_type' => 'merchant',
            'location' => 'test',
            'user_id' => $user->id,
        ]);

        Sanctum::actingAs($user);
        $response = $this->getJson('/api/v2/merchants/' . $merchant->id);
        $response->assertStatus(403);
    }
    
    public function test_authorized_getting_user()
    {
        $user = User::factory()->create([
            'email' => 'melak@gmail.com',
            'mobile' => '077258374',
            'phone_prefix' => '256',
            'password' => 'test',
        ]);

        $merchant = Merchant::factory()->create([
            'merchant_name' => 'mela',
            'merchant_description' => 'mela',
            'user_type' => 'merchant',
            'location' => 'test',
            'user_id' => $user->id,
        ]);

        $role = Role::create(['guard_name' => 'merchant', 'name' => 'merchant']);
        $user->assignRole($role);
        Sanctum::actingAs($user);
        $response = $this->getJson('/api/v2/merchants/' . $merchant->id);
        $response->assertStatus(200);
    }

    public function test_unauthorized_update_of_merchant()
    {
        $user = User::factory()->create([
            'email' => 'melak@gmail.com',
            'mobile' => '077258374',
            'phone_prefix' => '256',
            'password' => 'test',
        ]);

        $merchant = Merchant::factory()->create([
            'merchant_name' => 'mela',
            'merchant_description' => 'mela',
            'user_type' => 'merchant',
            'location' => 'test',
            'user_id' => $user->id,
        ]);

        Sanctum::actingAs($user);
        $response = $this->putJson('/api/v2/merchants/'.$merchant->id .'/update',[
            'merchant_name' => 'mela',
            'merchant_description' => 'mela',
            'user_type' => 'merchant',
            'location' => 'test',
        ]);
        $response->assertStatus(403);
    }

    public function test_authorized_update_of_merchant()
    {
        $user = User::factory()->create([
            'email' => 'melak@gmail.com',
            'mobile' => '077258374',
            'phone_prefix' => '256',
            'password' => 'test',
        ]);

        $merchant = Merchant::factory()->create([
            'merchant_name' => 'mela',
            'merchant_description' => 'mela',
            'user_type' => 'merchant',
            'location' => 'test',
            'user_id' => $user->id,
        ]);

        $role = Role::create(['guard_name' => 'merchant', 'name' => 'merchant']);
        $user->assignRole($role);
        Sanctum::actingAs($user);
        $response = $this->putJson('/api/v2/merchants/'.$merchant->id .'/update',[
            'merchant_name' => 'mela',
            'merchant_description' => 'mela',
            'user_type' => 'merchant',
            'location' => 'test',
        ]);
        $response->assertStatus(201);
    }

    public function test_unauthorized_delete_of_merchant()
    {
        $user = User::factory()->create([
            'email' => 'melak@gmail.com',
            'mobile' => '077258374',
            'phone_prefix' => '256',
            'password' => 'test',
        ]);

        $merchant = Merchant::factory()->create([
            'merchant_name' => 'mela',
            'merchant_description' => 'mela',
            'user_type' => 'merchant',
            'location' => 'test',
            'user_id' => $user->id,
        ]);

        Sanctum::actingAs($user);
        $response = $this->deleteJson('/api/v2/merchants/'.$merchant->id .'/delete');
        $response->assertStatus(403);
    }

    public function test_authorized_delete_of_merchant()
    {
        $user = User::factory()->create([
            'email' => 'melak@gmail.com',
            'mobile' => '077258374',
            'phone_prefix' => '256',
            'password' => 'test',
        ]);

        $merchant = Merchant::factory()->create([
            'merchant_name' => 'mela',
            'merchant_description' => 'mela',
            'user_type' => 'merchant',
            'location' => 'test',
            'user_id' => $user->id,
        ]);

        $role = Role::create(['guard_name' => 'merchant', 'name' => 'merchant']);
        $user->assignRole($role);
        Sanctum::actingAs($user);
        $response = $this->deleteJson('/api/v2/merchants/'.$merchant->id .'/delete');
        $response->assertStatus(200);
    }

    public function test_unauthorized_getting_merchants()
    {
        $user = User::factory()->create([
            'email' => 'melak@gmail.com',
            'mobile' => '077258374',
            'phone_prefix' => '256',
            'password' => 'test',
        ]);

        $merchant = Merchant::factory()->create([
            'merchant_name' => 'mela',
            'merchant_description' => 'mela',
            'user_type' => 'merchant',
            'location' => 'test',
            'user_id' => $user->id,
        ]);

        Sanctum::actingAs($user);
        $response = $this->getJson('/api/v2/merchants');
        $response->assertStatus(403);
    }

    public function test_authorized_getting_merchants()
    {
        $user = User::factory()->create([
            'email' => 'melak@gmail.com',
            'mobile' => '077258374',
            'phone_prefix' => '256',
            'password' => 'test',
        ]);

        $merchant = Merchant::factory()->create([
            'merchant_name' => 'mela',
            'merchant_description' => 'mela',
            'user_type' => 'merchant',
            'location' => 'test',
            'user_id' => $user->id,
        ]);

        $role = Role::create(['guard_name' => 'merchant', 'name' => 'merchant']);
        $user->assignRole($role);
        Sanctum::actingAs($user);
        $response = $this->getJson('/api/v2/merchants');
        $response->assertStatus(200);
    }

    
}
