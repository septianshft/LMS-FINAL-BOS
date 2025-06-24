<?php

namespace Database\Factories;

use App\Models\QuizAttempt;
use App\Models\User;
use App\Models\FinalQuiz;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuizAttemptFactory extends Factory
{
    protected $model = QuizAttempt::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'final_quiz_id' => FinalQuiz::factory(),
            'score' => $this->faker->numberBetween(0, 100),
            'is_passed' => $this->faker->boolean(70),
        ];
    }
}
