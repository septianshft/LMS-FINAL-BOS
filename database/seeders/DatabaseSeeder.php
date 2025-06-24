<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with clean essential accounts only.
     * LMS system + trainee, recruiter, and talent admin accounts.
     */
    public function run(): void
    {
        $this->command->info('🌱 Starting clean seeding with essential accounts...');

        // Core system setup
        $this->command->info('📋 Creating roles and permissions...');
        $this->call(RolePermissionSeeder::class);

        // LMS system infrastructure (untouched)
        $this->command->info('📚 Setting up LMS infrastructure...');
        $this->call(CourseLevelSeeder::class);
        $this->call(CourseModeSeeder::class);

        // Essential accounts: trainee, recruiter, talent admin only
        $this->command->info('👥 Creating essential accounts...');
        $this->call(SystemUserSeeder::class);

        // Trainee with course completion data
        $this->command->info('🎓 Creating trainee with LMS completion data...');
        $this->call(TraineeSeeder::class);

        $this->call(CourseMeetingSeeder::class);

        $this->command->info('✅ Seeding completed successfully!');
        $this->displaySystemSummary();
    }

    private function displaySystemSummary()
    {
        $this->command->info('');
        $this->command->info('📊 CLEAN SYSTEM SETUP:');
        $this->command->info('═══════════════════════════════════════════════');

        // User statistics
        $userCount = User::count();
        $talentAdminCount = User::whereHas('roles', function($query) {
            $query->where('name', 'talent_admin');
        })->count();
        $traineeCount = User::whereHas('roles', function($query) {
            $query->where('name', 'trainee');
        })->count();
        $recruiterCount = User::whereHas('roles', function($query) {
            $query->where('name', 'recruiter');
        })->count();

        $this->command->info("👥 TOTAL USERS: {$userCount}");
        $this->command->info("🛠️ Talent Admins: {$talentAdminCount}");
        $this->command->info("🎓 Trainees: {$traineeCount}");
        $this->command->info("👔 Recruiters: {$recruiterCount}");

        $this->command->info('');
        $this->command->info('🔑 TEST CREDENTIALS:');
        $this->command->info('═══════════════════════════════════════════════');
        $this->command->info('🛠️ Talent Admin: talent.admin@scout.test / password123');
        $this->command->info('👔 Recruiter: recruiter@scout.test / password123');
        $this->command->info('🎓 Trainee: trainee@test.com / password123');

        $this->command->info('');
        $this->command->info('🌐 SYSTEM ACCESS URLS:');
        $this->command->info('═══════════════════════════════════════════════');
        $this->command->info('🔐 Login: http://127.0.0.1:8000/login');
        $this->command->info('🛠️ Talent Admin: http://127.0.0.1:8000/talent-admin/dashboard');
        $this->command->info('👔 Recruiter: http://127.0.0.1:8000/recruiter/dashboard');

        $this->command->info('');
        $this->command->info('✅ CLEAN SYSTEM READY!');
        $this->command->info('');
    }
}
