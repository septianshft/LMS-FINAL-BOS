<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CourseModeSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('course_modes')->insert([
            ['name' => 'online'],
            ['name' => 'onsite'],
        ]);
    }
}