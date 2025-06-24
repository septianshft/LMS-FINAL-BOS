<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class QuizOptionFactory extends Factory
{
    protected $model = \App\Models\QuizOption::class;

    public function definition(): array
    {
        return [
            'quiz_question_id' => \App\Models\QuizQuestion::factory(),
            'option_text' => $this->faker->word(),
            'is_correct' => false,
        ];
    }
}
