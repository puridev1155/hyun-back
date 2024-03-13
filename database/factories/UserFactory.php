<?php

namespace Database\Factories;

use App\Models\Country;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
            'birth' => 20010310, //$this->faker->randomNumber(8), //생년월일
            'gender' => fake()->randomElement(['male', 'female']),//성별 [1,2]
            'country_id' => Country::factory(),//국가
            'role' => 10,//admin:1, manager:5, (default)member: 10
            'is_agree' => fake()->boolean(),//약관 동의
            'membership' => $this->faker->randomNumber(1),//멤버쉽
            'block_notice' => fake()->boolean()//공지 동의
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
