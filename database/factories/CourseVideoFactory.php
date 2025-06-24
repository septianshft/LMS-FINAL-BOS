<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CourseVideoFactory extends Factory
{
    protected $model = \App\Models\CourseVideo::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->sentence(2),
            'path_video' => 'video123',
            'course_id' => \App\Models\Course::factory(),
        ];
    }
}
