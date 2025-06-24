<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CourseLevelSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('course_levels')->insert([
            ['name' => 'beginner'],
            ['name' => 'intermediate'],
            ['name' => 'advanced'],
        ]);
    }
}
