<?php

namespace Database\Factories;

use App\Models\Talent;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TalentFactory extends Factory
{
    protected $model = Talent::class;

    public function definition()
    {
        return [
            'user_id' => User::factory()->state([
                'talent_skills' => [
                    [
                        'name' => 'PHP',
                        'level' => $this->faker->randomElement(['beginner', 'intermediate', 'advanced', 'expert']),
                        'experience_years' => $this->faker->numberBetween(1, 10),
                        'category' => 'Programming Language'
                    ],
                    [
                        'name' => 'Laravel',
                        'level' => $this->faker->randomElement(['beginner', 'intermediate', 'advanced', 'expert']),
                        'experience_years' => $this->faker->numberBetween(1, 8),
                        'category' => 'Framework'
                    ],
                    [
                        'name' => 'JavaScript',
                        'level' => $this->faker->randomElement(['beginner', 'intermediate', 'advanced', 'expert']),
                        'experience_years' => $this->faker->numberBetween(1, 7),
                        'category' => 'Programming Language'
                    ]
                ],
                'pekerjaan' => $this->faker->randomElement(['Full-stack Developer', 'Backend Developer', 'Frontend Developer', 'Web Developer']),
                'talent_bio' => $this->faker->paragraph(),
                'portfolio_url' => $this->faker->url(),
                'location' => $this->faker->city(),
                'phone' => $this->faker->phoneNumber(),
                'available_for_scouting' => $this->faker->boolean(80), // 80% chance of being available
                'is_active_talent' => $this->faker->boolean(90), // 90% chance of being active
            ]),
            'is_active' => true,
        ];
    }
}
