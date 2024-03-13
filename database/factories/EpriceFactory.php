<?php

namespace Database\Factories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Eprice>
 */
class EpriceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(),//가격 제목
            'post_id' => Post::factory(),//이벤트 ID
            'price' => 0,//가격 00.00
            'discount' => 0,//할인
            'price_type' => fake()->randomElement(['won', 'dollar', 'yen']),//won, dollar, yen
        ];
    }
}
