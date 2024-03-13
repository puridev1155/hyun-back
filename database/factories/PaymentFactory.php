<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [

            'user_id' => User::factory(),//사용자 ID
            'payment_type' => fake()->randomElement(['Post', 'Product']),//결제종류 예) post or product등...
            'title' => $this->faker->sentence(),//결제 종류 제목
            'pay_id' => $this->faker->randomNumber(1),//관련 테이블 ID
            'price' => 0,//가격 00.00
            'tax' => 0,//세금
            'discount' => 0,//할인
            'status' => fake()->randomElement(['paid', 'canceled'])//현황[구매, 취소]
        ];
    }
}
