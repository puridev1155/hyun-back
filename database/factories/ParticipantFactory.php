<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Participant>
 */
class ParticipantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'payment_id' => Payment::factory(), //결제 내역 ID
            'user_id' => User::factory(),//사용자 ID
            'post_id'=> Post::factory(),//이벤트 ID
            'audition' => 0,//오디션값 (필요할 듯 싶음)
        ];
    }
}
