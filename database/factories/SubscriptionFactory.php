<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Merchant;
use App\Models\Program;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class MerchantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
  
    public function definition()
    {
        return [
            'status' => false,
            'card_number' => $this->faker->number,
            'merchant_id' => Merchant::factory(),
            'program_id' => Program::factory(),
            'user_id' => User::factory(),
        ];
    }

   
}
