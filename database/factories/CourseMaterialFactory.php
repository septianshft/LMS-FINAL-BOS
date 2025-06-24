<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\CourseModule;

class CourseMaterialFactory extends Factory
{
    protected $model = \App\Models\CourseMaterial::class;

    public function definition(): array
    {
        return [
            'course_module_id' => CourseModule::factory(),
            'name' => $this->faker->sentence(2),
            'file_path' => 'materials/sample.pdf',
            'file_type' => 'pdf',
            'order' => 0,
        ];
    }
}
