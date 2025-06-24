<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\CourseModule;

class ModuleTaskFactory extends Factory
{
    protected $model = \App\Models\ModuleTask::class;

    public function definition(): array
    {
        return [
            'course_module_id' => CourseModule::factory(),
            'name' => $this->faker->sentence(2),
            'description' => $this->faker->sentence(6),
            'order' => 0,
        ];
    }
}
