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
class ProgramFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'product' => $this->faker->name,
            'percentage' => $this->faker->name,
            'start_date' => $this->faker->date,
            'due_date' => $this->faker->date,
            'status' => true,
            'merchant_id' => Merchant::factory(),
            'points' => $this->faker->randomNumber(),
            'expired' => false,
        ];
    }


}
