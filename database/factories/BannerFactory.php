<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Banner>
 */
class BannerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'country_id' => 1, //Country::inRandomOrder()->value('id'),//국가ID
            'category_id'=> 1, //Category::inRandomOrder()->value('id'),//카테고리ID
            'order' => 0,//순서
            'location' => $this->faker->sentence(),
            'banner_title' => $this->faker->sentence(),//제목
        ];
    }
}
