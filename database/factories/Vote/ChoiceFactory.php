<?php

namespace Database\Factories\Vote;

use App\Models\Vote\Vote;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vote\Choice>
 */
class ChoiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order'=>$this->faker->sentence(),//제목 필수
            'vote_id'=> Vote::factory(), //투표프로젝트
            'name' => $this->faker->sentence(),//명칭 필수
        ];
    }
}
