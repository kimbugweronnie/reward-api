<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Merchant;
use App\Models\Program;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;

class ProgramTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_making_an_api_request_return_422_unauthorized(): void
    {
        $user = User::factory()->create([
            'email' => 'melak@gmail.com',
            'mobile' => '077258374',
            'phone_prefix' => '256',
            'password' => 'test',
        ]);

        Sanctum::actingAs($user);
        $response = $this->postJson('api/v2/programs', []);
        $response->assertStatus(403);
    }
    public function test_making_an_api_request_return_422_authorized(): void
    {
        $user = User::factory()->create([
            'email' => 'melak@gmail.com',
            'mobile' => '077258374',
            'phone_prefix' => '256',
            'password' => 'test',
        ]);

        $role = Role::create(['guard_name' => 'merchant', 'name' => 'merchant']);
        $user->assignRole($role);
        Sanctum::actingAs($user);
        $response = $this->postJson('api/v2/programs', []);
        $response->assertStatus(422);
    }

    public function test_unauthorized_creating_program_with_payload(): void
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
        $response = $this->postJson('api/v2/programs', [
            'name' => 'mela',
            'product' => 'mela',
            'percentage' => 12,
            'start_date' => 11 / 11 / 2011,
            'due_date' => 11 / 12 / 2011,
            'status' => true,
            'merchant_id' => $merchant->id,
            'points' => 1000,
            'expired' => false,
        ]);

        $response->assertStatus(403);
    }

    public function test_authorized_creating_program_with_payload(): void
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
        $role = Role::create(['guard_name' => 'merchant', 'name' => 'merchant']);
        $user->assignRole($role);
        $response = $this->postJson('api/v2/programs', [
            'name' => 'mela',
            'product' => 'mela',
            'percentage' => '12',
            'start_date' => '11/11/2011',
            'due_date' => '11/12/2011',
            'status' => true,
            'merchant_id' => $merchant->id,
            'points' => 1000,
            'expired' => false,
        ]);

        $response->assertStatus(201);
    }

    public function test_unauthorized_getting_programs()
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
        $program = Program::factory()->create([
            'name' => 'mela',
            'product' => 'mela',
            'percentage' => '12',
            'start_date' => '11/11/2011',
            'due_date' => '11/12/2011',
            'status' => true,
            'merchant_id' => $merchant->id,
            'points' => 1000,
            'expired' => false,
        ]);

        Sanctum::actingAs($user);
        $response = $this->getJson('/api/v2/programs/' . $program->id);
        $response->assertStatus(403);
    }

    public function test_authorized_getting_programs()
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

        $program = Program::factory()->create([
            'name' => 'mela',
            'product' => 'mela',
            'percentage' => '12',
            'start_date' => '11/11/2011',
            'due_date' => '11/12/2011',
            'status' => true,
            'merchant_id' => $merchant->id,
            'points' => 1000,
            'expired' => false,
        ]);
        $response = $this->getJson('/api/v2/programs/' . $program->id);
        $response->assertStatus(200);
    }
}
