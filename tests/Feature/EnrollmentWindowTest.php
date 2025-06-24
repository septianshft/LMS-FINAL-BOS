<?php

namespace Tests\Feature;

use App\Models\{Course, CourseMode, CourseLevel, User};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class EnrollmentWindowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'trainee']);
    }

    public function test_join_outside_enrollment_dates_is_blocked(): void
    {
        Carbon::setTestNow('2025-01-01');

        $mode = CourseMode::create(['name' => 'onsite']);
        $level = CourseLevel::create(['name' => 'beginner']);

        $course = Course::factory()->create([
            'price' => 0,
            'course_mode_id' => $mode->id,
            'course_level_id' => $level->id,
            'enrollment_start' => '2025-02-01',
            'enrollment_end' => '2025-02-10',
        ]);

        $user = User::factory()->create();
        $user->assignRole('trainee');

        $response = $this->actingAs($user)->post(route('courses.join', $course->slug));
        $response->assertSessionHas('error');

        $this->assertDatabaseMissing('course_trainees', [
            'user_id' => $user->id,
            'course_id' => $course->id,
        ]);
    }

    public function test_checkout_outside_enrollment_dates_is_blocked(): void
    {
        Storage::fake('public');
        Carbon::setTestNow('2025-01-01');

        $mode = CourseMode::create(['name' => 'onsite']);
        $level = CourseLevel::create(['name' => 'beginner']);

        $course = Course::factory()->create([
            'price' => 100,
            'course_mode_id' => $mode->id,
            'course_level_id' => $level->id,
            'enrollment_start' => '2025-02-01',
            'enrollment_end' => '2025-02-10',
        ]);

        $user = User::factory()->create();
        $user->assignRole('trainee');

        $response = $this->actingAs($user)->post(route('front.checkout.store', $course->slug), [
            'course_id' => $course->id,
            'proof' => UploadedFile::fake()->image('proof.jpg'),
        ]);

        $response->assertSessionHas('error');

        $this->assertDatabaseMissing('subscribe_transactions', [
            'user_id' => $user->id,
            'course_id' => $course->id,
        ]);
    }
}
