<?php

namespace Database\Factories;

use App\Models\Certificate;
use App\Models\User;
use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

class CertificateFactory extends Factory
{
    protected $model = Certificate::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'course_id' => Course::factory(),
            'path' => 'certificates/' . $this->faker->uuid() . '.pdf',
            'generated_at' => $this->faker->dateTimeThisYear(),
        ];
    }
}
