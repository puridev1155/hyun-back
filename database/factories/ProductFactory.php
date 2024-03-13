<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),//사용자ID
            'category_id' => $this->faker->randomNumber(1),//카테고리
            'brand' => $this->faker->text(5), //브랜드명
            'product_name' => $this->faker->text(5), //제품명
            'in_stock' => $this->faker->randomNumber(1),//수량
            'out_stock' => $this->faker->randomNumber(1),//판매 수량
            'description' => $this->faker->text(5), //짧은 설명
            'info' => $this->faker->sentence(), //상품정보
            'shipping' => $this->faker->text(5) //배송정보
        ];
    }
}
