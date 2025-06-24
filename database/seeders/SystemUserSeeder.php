<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\TalentAdmin;
use App\Models\Talent;
use App\Models\Recruiter;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class SystemUserSeeder extends Seeder
{
    /**
     * Seed essential system users: trainee, recruiter, and talent admin only.
     */
    public function run(): void
    {
        $this->command->info('👥 Creating essential system users...');

        // ===============================================
        // ESSENTIAL ACCOUNTS ONLY
        // ===============================================

        // Talent Admin - Talent system management
        $talentAdminUser = User::firstOrCreate([
            'email' => 'talent.admin@scout.test'
        ], [
            'name' => 'Emma Talent Admin',
            'pekerjaan' => 'Talent System Administrator',
            'avatar' => null,
            'password' => bcrypt('password123'),
        ]);
        $talentAdminUser->assignRole('talent_admin');

        // Create talent admin profile
        TalentAdmin::firstOrCreate([
            'user_id' => $talentAdminUser->id
        ], [
            'is_active' => true
        ]);
        $this->command->info('   ✓ Talent Admin created: talent.admin@scout.test');

        // Recruiter - Talent discovery and project management
        $recruiterUser = User::firstOrCreate([
            'email' => 'recruiter@scout.test'
        ], [
            'name' => 'Michael Recruiter',
            'pekerjaan' => 'Senior Talent Recruiter',
            'avatar' => null,
            'password' => bcrypt('password123'),
        ]);
        $recruiterUser->assignRole('recruiter');

        // Create recruiter profile
        Recruiter::firstOrCreate([
            'user_id' => $recruiterUser->id
        ], [
            'company_name' => 'Tech Solutions Inc.',
            'industry' => 'Technology',
            'company_size' => '50-100',
            'website' => 'https://techsolutions.com',
            'company_description' => 'Leading technology consulting firm',
            'phone' => '+1-555-0123',
            'address' => '123 Tech Street, Silicon Valley',
            'is_active' => true
        ]);
        $this->command->info('   ✓ Recruiter created: recruiter@scout.test');

        $this->command->info('✅ Essential system users created successfully!');
    }
}
