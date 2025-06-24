<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Course;

class CourseModuleFactory extends Factory
{
    protected $model = \App\Models\CourseModule::class;

    public function definition(): array
    {
        return [
            'course_id' => Course::factory(),
            'name' => $this->faker->sentence(3),
            'order' => 0,
        ];
    }
}
