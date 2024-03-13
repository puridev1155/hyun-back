<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Eprice;
use App\Models\Country;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => 22,//사용자ID
            'country_id' => 1, //Country::inRandomOrder()->value('id'),//국가ID
            'category_id'=> 1, //Category::inRandomOrder()->value('id'),//카테고리ID
            // 'eprice_id'=> Eprice::factory(),//이벤트가격ID
            'title' => $this->faker->sentence(),//제목
            'order' => 0,//순서
            // 'board' => ;//카테고리와 같은 역할
            'public' => fake()->boolean,//공개 비공개 여부
            'content' => $this->faker->sentence(),//내용

            'like_count' => 0,//좋아요 수
            'view_count' => 0,//조회수
            'comment_count' => 0,//댓글수

            // 'participant_type' => nullable();// 참여 여부
            'age_limit' => 99,//가능 나이
            // 'start_date' => nullable(); // Post 시작일
            // 'end_date' => nullable(); // Post 종료일

            // 'memo_info' => nullable();// 메모
            // 'memo_price' => nullable();// 가격종류 추가
            'ticket_count' => 0, // 티켓수
            // 'address' => nullable(); // 주소
            // 'address_detail' => nullable(); // 상세주소
            'sns_agree' => fake()->boolean, //소셜 필수 사항
            'price_agree' => 0, //가격 필수 사항 (0:옵션, 1:필수, 3:무료)

            // 'x' => nullable(), // x 좌표
            // 'y' => nullable(), // y 좌표
        ];
    }
}
