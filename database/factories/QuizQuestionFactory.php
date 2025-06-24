<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class QuizQuestionFactory extends Factory
{
    protected $model = \App\Models\QuizQuestion::class;

    public function definition(): array
    {
        return [
            'final_quiz_id' => \App\Models\FinalQuiz::factory(),
            'question' => $this->faker->sentence(6),
        ];
    }
}
