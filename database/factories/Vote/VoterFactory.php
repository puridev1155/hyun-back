<?php

namespace Database\Factories\Vote;

use App\Models\User;
use App\Models\Vote\Vote;
use App\Models\Vote\Choice;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vote\Voter>
 */
class VoterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'vote_title'=>fake()->sentence(),
            'choice_name'=>fake()->sentence(),
            'user_id'=> User::factory(), //투표프로젝트 작성자
            'choice_id'=> Choice::factory(), //투표프로젝트 작성자
        ];
    }
}
