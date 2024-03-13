<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Notice>
 */
class NoticeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [

            'public' => fake()->boolean(),//
            'category_id' => Category::factory(),//
            'title' => $this->faker->text(5),
            'info' => $this->faker->text(20),
            'user_type' => $this->faker->text(5),

        ];
    }
}
