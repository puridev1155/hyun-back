<?php

namespace Database\Factories\Vote;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vote>
 */
class VoteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title'=>fake()->sentence(),
            'user_id'=> User::factory(), //투표프로젝트 작성자
            'start_at' => $this->faker->dateTime(),//2022-12-31 15:00
            'end_at' => $this->faker->dateTime(), //2022-12-31 15:00
            'status' => 'PENDING'
        ];
    }
}
