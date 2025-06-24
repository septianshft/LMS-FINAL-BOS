<?php

namespace Database\Factories;

use App\Models\CourseProgress;
use App\Models\User;
use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

class CourseProgressFactory extends Factory
{
    protected $model = CourseProgress::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'course_id' => Course::factory(),
            'progress' => $this->faker->numberBetween(0, 100),
            'completed_videos' => json_encode([]),
            'quiz_passed' => $this->faker->boolean(70),
        ];
    }
}
