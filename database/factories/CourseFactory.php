<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CourseFactory extends Factory
{
    protected $model = \App\Models\Course::class;

    public function definition(): array
    {
        $start = $this->faker->dateTimeBetween('-1 month', '+1 month');
        $end = (clone $start)->modify('+'.$this->faker->numberBetween(5, 30).' days');

        return [
            'name' => $this->faker->sentence(3),
            'slug' => $this->faker->unique()->slug(),
            'about' => $this->faker->paragraph(),
            'path_trailer' => 'abcd',
            'thumbnail' => 'thumb.png',
            'trainer_id' => \App\Models\Trainer::factory(),
            'category_id' => \App\Models\Category::factory(),
            'price' => 0,
            'course_mode_id' => null,
            'course_level_id' => null,
            'enrollment_start' => $start,
            'enrollment_end' => $end,
        ];
    }
}
