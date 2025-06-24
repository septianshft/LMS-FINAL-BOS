<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Course;
use App\Models\CourseTrainee;
use App\Models\SubscribeTransaction;
use App\Models\Trainer;
use App\Models\User; // Pastikan User model di-import jika belum
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Check for cached conversion suggestions and inject into session for persistent display
        $this->loadCachedSuggestions($user);

        $title = 'Dashboard';
        $roles = '';
        $assignedKelas = []; // Penting: Inisialisasi sebagai array kosong
        $trainees = 0;
        $trainers = 0;
        $courses = 0;

        if ($user->roles_id == 1) { // Admin
            $roles = 'Admin';
            $trainees = CourseTrainee::distinct('user_id')->count('user_id');
            $trainers = Trainer::count();
            $courses = Course::count();
        } elseif ($user->roles_id == 2) { // Trainer (Pengajar)
            $roles = 'Trainer';
            $trainerInstance = Trainer::where('user_id', $user->id)->first();
            $trainerCourses = collect();

            if ($trainerInstance) {
                // Ambil kursus yang dimiliki oleh trainer ini
                $trainerCourses = Course::where('trainer_id', $trainerInstance->id)->get();
                if ($trainerCourses->isNotEmpty()) {
                    foreach ($trainerCourses as $course) {
                        $assignedKelas[] = [
                            'mapel' => $course,
                            'kelas' => [$course]
                        ];
                    }
                } // else: $assignedKelas akan tetap kosong jika trainer tidak punya kursus

                $courseIdsForTrainer = $trainerCourses->pluck('id');
                $trainees = CourseTrainee::whereIn('course_id', $courseIdsForTrainer)
                                    ->distinct('user_id')
                                    ->count('user_id');
            } else {
                $trainees = 0;
            }
            $trainers = Trainer::count();
            $courses = $trainerCourses->count();

        } elseif ($user->roles_id == 3 || $user->hasRole('trainee')) { // Trainee (Siswa)
            $roles = 'Trainee';
            // Pastikan relasi 'courses' ada di model User dan berfungsi
            $traineeCourses = $user->courses()->get();

            if ($traineeCourses->isNotEmpty()) {
                foreach ($traineeCourses as $course) {
                    $assignedKelas[] = [
                        'mapel' => $course,
                        'kelas' => [$course]
                    ];
                }
            } // else: $assignedKelas akan tetap kosong jika trainee tidak terdaftar di kursus manapun

            $trainees = CourseTrainee::distinct('user_id')->count('user_id');
            $trainers = Trainer::count();
            $courses = $traineeCourses->count();
        } elseif ($user->hasRole('talent_admin')) { // Talent Admin
            return redirect()->route('talent_admin.dashboard');
        } elseif ($user->hasRole('recruiter')) { // Recruiter
            return redirect()->route('recruiter.dashboard');
        } elseif ($user->hasRole('talent')) { // Talent (only if no trainee role)
            return redirect()->route('talent.dashboard');
        }

        $categories = Category::count();
        $transactions = SubscribeTransaction::count();

        // Rename variables to match dashboard.blade.php expectations
        $students = $trainees;
        $teachers = $trainers;

        return view('dashboard', compact('title', 'roles', 'assignedKelas', 'categories', 'courses', 'transactions', 'students', 'teachers'));
    }

    /**
     * Load cached talent conversion suggestions and inject them into session for persistent display
     * Respects dismissal flags to prevent showing recently dismissed notifications
     */
    private function loadCachedSuggestions($user)
    {
        // Don't show conversion suggestions if user already has talent role
        if ($user->hasRole('talent')) {
            return;
        }

        // Check for cached conversion suggestion
        $conversionSuggestion = Cache::get("conversion_suggestion_{$user->id}");
        $isDismissed = Cache::get("dismissed_smart_talent_suggestion_{$user->id}");

        // If there's a cached suggestion, it's not dismissed, and it's not already in the session, add it
        if ($conversionSuggestion && !$isDismissed && !session()->has('smart_talent_suggestion')) {
            session()->flash('smart_talent_suggestion', $conversionSuggestion);
        }

        // Check for cached certificate-based suggestion
        $certificateSuggestion = Cache::get("certificate_suggestion_{$user->id}");
        $isCertificateDismissed = Cache::get("dismissed_certificate_talent_suggestion_{$user->id}");

        if ($certificateSuggestion && !$isCertificateDismissed && !session()->has('certificate_talent_suggestion')) {
            session()->flash('certificate_talent_suggestion', $certificateSuggestion);
        }
    }

    /**
     * Dismiss a talent conversion suggestion
     * Note: This only clears the session, not the cache. The cache retains its original expiration.
     * The frontend uses localStorage to hide dismissed notifications for 24 hours.
     */
    public function dismissSuggestion(Request $request)
    {
        $user = Auth::user();
        $suggestionType = $request->input('suggestion_type');

        // Handle special debug case to clear all cache
        if ($suggestionType === 'clear_all_cache' && app()->environment('local')) {
            // Clear all notification-related cache and session data
            Cache::forget("conversion_suggestion_{$user->id}");
            Cache::forget("certificate_suggestion_{$user->id}");
            Cache::forget("dismissed_smart_talent_suggestion_{$user->id}");
            Cache::forget("dismissed_certificate_talent_suggestion_{$user->id}");

            // Clear sessions
            session()->forget('smart_talent_suggestion');
            session()->forget('certificate_talent_suggestion');

            return response()->json([
                'success' => true,
                'message' => 'All notification cache and session data cleared (debug mode)',
                'cleared_items' => [
                    'conversion_suggestion_' . $user->id,
                    'certificate_suggestion_' . $user->id,
                    'dismissed_smart_talent_suggestion_' . $user->id,
                    'dismissed_certificate_talent_suggestion_' . $user->id,
                    'session: smart_talent_suggestion',
                    'session: certificate_talent_suggestion'
                ]
            ]);
        }

        // Only clear from session, not cache
        // The cache should retain its original expiration time
        // Frontend localStorage will handle 24-hour hiding
        session()->forget($suggestionType);

        // Optional: Set a temporary "dismissed" flag in cache for server-side tracking
        $dismissedKey = "dismissed_{$suggestionType}_{$user->id}";
        Cache::put($dismissedKey, true, now()->addHours(24));

        return response()->json([
            'success' => true,
            'message' => 'Suggestion dismissed for 24 hours'
        ]);
    }
}
