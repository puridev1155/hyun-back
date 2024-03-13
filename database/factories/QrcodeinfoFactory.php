<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Qrcodeinfo>
 */
class QrcodeinfoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'model_type' => $this->faker->text(5),
            'model_id' => $this->faker->randomNumber(1),//
            'user_id' => User::factory(),//
            'usage' => fake()->boolean(),//
        ];
    }
}
