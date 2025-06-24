<?php

namespace Database\Factories;

use App\Models\TalentRequest;
use App\Models\Talent;
use App\Models\Recruiter;
use Illuminate\Database\Eloquent\Factories\Factory;

class TalentRequestFactory extends Factory
{
    protected $model = TalentRequest::class;

    public function definition()
    {
        return [
            'talent_id' => Talent::factory(),
            'recruiter_id' => Recruiter::factory(),
            'project_id' => null, // Will be populated when integrated with new project system
            'project_title' => $this->faker->sentence(4),
            'project_description' => $this->faker->paragraph(3),
            'required_skills' => json_encode($this->faker->randomElements(['PHP', 'JavaScript', 'Python', 'React', 'Laravel', 'Node.js'], 3)),
            'budget_range' => $this->faker->randomElement(['$500-$1000', '$1000-$2000', '$2000-$5000', '$5000+', 'Negotiable']),
            'project_duration' => $this->faker->randomElement(['1 week', '2 weeks', '1 month', '2-3 months', '6+ months', 'Ongoing']),
            'urgency_level' => $this->faker->randomElement(['low', 'medium', 'high']),
            'status' => $this->faker->randomElement(['pending', 'accepted', 'rejected', 'completed']),
            'talent_accepted' => false,
            'admin_accepted' => false,
            'recruiter_notes' => $this->faker->optional()->paragraph(),
            'talent_notes' => $this->faker->optional()->paragraph(),
            'admin_notes' => $this->faker->optional()->paragraph(),
            'migrated_to_project' => false, // Track migration status
            'created_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
        ];
    }

    public function pending()
    {
        return $this->state([
            'status' => 'pending',
            'talent_accepted' => false,
            'admin_accepted' => false,
        ]);
    }

    public function accepted()
    {
        return $this->state([
            'status' => 'accepted',
            'talent_accepted' => true,
            'admin_accepted' => true,
        ]);
    }

    public function rejected()
    {
        return $this->state([
            'status' => 'rejected',
        ]);
    }
}
