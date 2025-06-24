<?php

namespace Tests\Feature;

use App\Models\{User, Course, CourseVideo, CourseMode, CourseMeeting, SubscribeTransaction};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;
use Carbon\Carbon;

class MeetingDisplayTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'trainee']);
    }

    public function test_meeting_times_display_on_learning_page(): void
    {
        $user = User::factory()->create();
        $user->assignRole('trainee');

        $mode = CourseMode::create(['name' => 'onsite']);
        $course = Course::factory()->create(['course_mode_id' => $mode->id]);
        $video = CourseVideo::factory()->create(['course_id' => $course->id]);

        $meeting = CourseMeeting::create([
            'course_id' => $course->id,
            'title' => 'Session 1',
            'start_datetime' => Carbon::now()->addDay(),
            'end_datetime' => Carbon::now()->addDay()->addHour(),
            'location' => 'Lab 1',
        ]);

        SubscribeTransaction::create([
            'total_amount' => 0,
            'is_paid' => true,
            'user_id' => $user->id,
            'course_id' => $course->id,
            'proof' => 'proof.png',
            'subscription_start_date' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('front.learning', [$course->id, $video->id]));

        $response->assertStatus(200);
        $response->assertSee($meeting->title);
        $response->assertSee($meeting->start_datetime->format('d M Y H:i'));
    }
}
