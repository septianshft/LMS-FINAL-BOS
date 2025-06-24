<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\CourseMeeting;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CourseMeetingSeeder extends Seeder
{
    public function run(): void
    {
        echo "🏢 Creating course meetings...\n";

        $courses = Course::take(2)->get();

        if ($courses->isEmpty()) {
            echo "⚠️ No courses found. Please run course seeders first.\n";
            return;
        }

        foreach ($courses as $course) {
            // Kickoff Meeting
            $kickoffMeeting = CourseMeeting::firstOrCreate([
                'course_id' => $course->id,
                'title' => 'Kickoff Meeting',
            ], [
                'start_datetime' => Carbon::now()->addDays(1)->setTime(10, 0),
                'end_datetime' => Carbon::now()->addDays(1)->setTime(12, 0),
                'location' => 'Room 101',
            ]);

            // Mid-course Session
            $secondMeeting = CourseMeeting::firstOrCreate([
                'course_id' => $course->id,
                'title' => 'Mid-Course Review Session',
            ], [
                'start_datetime' => Carbon::now()->addDays(7)->setTime(14, 0),
                'end_datetime' => Carbon::now()->addDays(7)->setTime(16, 0),
                'location' => 'Room 102',
            ]);

            // Final Session
            $finalMeeting = CourseMeeting::firstOrCreate([
                'course_id' => $course->id,
                'title' => 'Final Presentation & Graduation',
            ], [
                'start_datetime' => Carbon::now()->addDays(14)->setTime(13, 0),
                'end_datetime' => Carbon::now()->addDays(14)->setTime(17, 0),
                'location' => 'Auditorium A',
            ]);

            echo "   ✓ Created 3 meetings for course: {$course->name}\n";
        }

        echo "✅ Course meetings created successfully!\n";
    }
}
