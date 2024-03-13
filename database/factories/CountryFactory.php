<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Country>
 */
class CountryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['korea', 'us', 'japan']),
            'lang' => fake()->randomElement(['korean', 'english', 'japanese']),
            'currency' => fake()->randomElement(['won', 'dollar', 'yen']),
        ];
    }
}
