<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class FinalQuizFactory extends Factory
{
    protected $model = \App\Models\FinalQuiz::class;

    public function definition(): array
    {
        return [
            'course_id' => \App\Models\Course::factory(),
            'title' => $this->faker->sentence(3),
            'passing_score' => 50,
            'is_hidden_from_trainee' => false,
        ];
    }
}
