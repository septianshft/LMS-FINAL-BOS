<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create essential roles only
        $traineeRole = Role::firstOrCreate([
            'name' => 'trainee'
        ]);

        $talentAdminRole = Role::firstOrCreate([
            'name' => 'talent_admin'
        ]);

        $talentRole = Role::firstOrCreate([
            'name' => 'talent'
        ]);

        $recruiterRole = Role::firstOrCreate([
            'name' => 'recruiter'
        ]);

        // LMS roles (keep for LMS system)
        $adminRole = Role::firstOrCreate([
            'name' => 'admin'
        ]);

        $trainerRole = Role::firstOrCreate([
            'name' => 'trainer'
        ]);
    }
}
