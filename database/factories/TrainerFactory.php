<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TrainerFactory extends Factory
{
    protected $model = \App\Models\Trainer::class;

    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'is_active' => true,
        ];
    }
}
