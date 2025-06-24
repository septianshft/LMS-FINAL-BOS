<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSubscribeTransactionRequest;
use App\Models\Category;
use App\Models\Course;
use App\Models\CourseMode;
use App\Models\CourseLevel;
use App\Models\CourseVideo;
use App\Models\CourseMaterial;
use App\Models\ModuleTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SubscribeTransaction;
use App\Models\CourseProgress;
use App\Models\Certificate;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class FrontController extends Controller
{
    /**
     * Halaman depan menampilkan semua course
     */
    public function index(Request $request)
    {
        $query = Course::with(['category', 'trainer', 'trainees', 'mode', 'level'])->orderByDesc('id');

        if ($request->filled('course_mode_id')) {
            $query->where('course_mode_id', $request->course_mode_id);
        }

        if ($request->filled('course_level_id')) {
            $query->where('course_level_id', $request->course_level_id);
        }

        $courses = $query->get();
        $categories = Category::all();
        $modes = CourseMode::all();
        $levels = CourseLevel::all();

        return view('front.index', compact('courses', 'categories', 'modes', 'levels'));
    }

    /**
     * Detail course
     */
    public function details(Course $course)
    {
        $course->load(['category', 'trainer.user', 'trainees', 'course_videos', 'course_keypoints', 'modules']);

        return view('front.details', compact('course'));
    }

    public function category(Request $request, Category $category)
    {
        $query = $category->courses()->with(['mode', 'level']);

        if ($request->filled('course_mode_id')) {
            $query->where('course_mode_id', $request->course_mode_id);
        }

        if ($request->filled('course_level_id')) {
            $query->where('course_level_id', $request->course_level_id);
        }

        $courses = $query->get();

        $otherCategories = Category::where('id', '!=', $category->id)->get();
        $modes = CourseMode::all();
        $levels = CourseLevel::all();

        return view('front.category', compact('courses', 'category', 'otherCategories', 'modes', 'levels'));
    }

    /**
     * Halaman pembelajaran untuk course tertentu
     */
    public function learning(Course $course, $courseVideoId)
    {
        $course->load([
            "category", "trainer.user", "trainees",
            "course_videos", "course_keypoints",
            "modules.videos", "modules.materials", "modules.tasks",
            "finalQuiz",
            "meetings",
        ]);

        $user = Auth::user();

        // Cek apakah user punya akses ke course ini
        $hasAccess = SubscribeTransaction::where('user_id', $user->id)
            ->where('is_paid', true)
            ->where(function ($query) use ($course) {
                $query->whereNull('course_id') // akses ke semua kelas
                      ->orWhere('course_id', $course->id); // atau hanya kelas ini
            })
            ->exists();

        // Jika tidak punya akses dan course berbayar, redirect
        if (!$hasAccess && $course->price > 0) {
            return redirect()->route('front.pricing', compact('course'))
                             ->with('error', 'Kamu belum membeli akses ke kelas ini.');
        }

        // Ambil video dari relasi
        $video = $course->course_videos->firstWhere('id', $courseVideoId);

        // Jika video tidak ditemukan, lempar 404
        if (!$video) {
            abort(404, 'Video tidak ditemukan.');
        }

        // Tambahkan course ke relasi user jika belum ada (untuk pelacakan/history)
        $user->courses()->syncWithoutDetaching($course->id);

        // Ensure progress record exists for this user and course
        $progress = CourseProgress::firstOrCreate([
            'user_id' => $user->id,
            'course_id' => $course->id,
        ], [
            'completed_videos' => [],
            'completed_materials' => [],
            'completed_tasks' => [],
            'progress' => 0,
        ]);

        // Generate certificate if eligible
        if ($progress->progress == 100 && $progress->quiz_passed && !$progress->course->certificates()->where('user_id', $user->id)->exists()) {
            $this->generateCertificate($course, $user);
        }

        $certificate = Certificate::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        $quizAttempt = null;
        if ($course->finalQuiz) {
            $quizAttempt = $course->finalQuiz->attempts()
                ->where('user_id', $user->id)
                ->latest()
                ->first();
        }

        return view('front.learning', compact('course', 'video', 'certificate', 'progress', 'quizAttempt'));
    }

    /**
     * Halaman pricing sebelum checkout
     */
    public function pricing(Course $course)
    {
        return view('front.pricing', compact('course'));
    }

    /**
     * Halaman checkout
     */
    public function checkout(Course $course)
    {
        return view('front.checkout', compact('course'));
    }

    public function markItemComplete(Request $request, Course $course, $itemId)
    {
        $request->validate([
            'type' => 'required|in:video,material,task',
        ]);

        $user = Auth::user();

        $progress = CourseProgress::firstOrCreate([
            'user_id' => $user->id,
            'course_id' => $course->id,
        ], [
            'completed_videos' => [],
            'completed_materials' => [],
            'completed_tasks' => [],
            'progress' => 0,
        ]);

        switch ($request->type) {
            case 'video':
                $item = CourseVideo::where('course_id', $course->id)->findOrFail($itemId);
                $completed = $progress->completed_videos ?? [];
                if (!in_array($item->id, $completed)) {
                    $completed[] = $item->id;
                    $progress->completed_videos = $completed;
                }
                break;
            case 'material':
                $item = CourseMaterial::whereHas('module', function ($q) use ($course) {
                    $q->where('course_id', $course->id);
                })->findOrFail($itemId);
                $completed = $progress->completed_materials ?? [];
                if (!in_array($item->id, $completed)) {
                    $completed[] = $item->id;
                    $progress->completed_materials = $completed;
                }
                break;
            case 'task':
                $item = ModuleTask::whereHas('module', function ($q) use ($course) {
                    $q->where('course_id', $course->id);
                })->findOrFail($itemId);
                $completed = $progress->completed_tasks ?? [];
                if (!in_array($item->id, $completed)) {
                    $completed[] = $item->id;
                    $progress->completed_tasks = $completed;
                }
                break;
        }

        $totalVideos = CourseVideo::where('course_id', $course->id)->count();
        $totalMaterials = CourseMaterial::whereHas('module', function ($q) use ($course) {
            $q->where('course_id', $course->id);
        })->count();
        $totalTasks = ModuleTask::whereHas('module', function ($q) use ($course) {
            $q->where('course_id', $course->id);
        })->count();

        $completedCount = count($progress->completed_videos ?? []) + count($progress->completed_materials ?? []) + count($progress->completed_tasks ?? []);
        $totalItems = $totalVideos + $totalMaterials + $totalTasks;
        $progress->progress = $totalItems > 0 ? floor($completedCount / $totalItems * 100) : 0;
        $progress->save();

        if ($progress->progress == 100 && $progress->quiz_passed && !$progress->course->certificates()->where('user_id', $user->id)->exists()) {
            $this->generateCertificate($course, $user);
        }

        return back();
    }

    private function generateCertificate(Course $course, $user): void
    {
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('certificates.certificate', [
            'course' => $course,
            'user' => $user,
            'date' => now()->toDateString(),
        ]);

        $path = 'certificates/' . $user->id . '_' . $course->id . '.pdf';
        \Illuminate\Support\Facades\Storage::disk('public')->put($path, $pdf->output());

        Certificate::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'path' => $path,
            'generated_at' => now(),
        ]);
    }

    /**
     * Proses penyimpanan data saat checkout
     */

     public function checkout_store(StoreSubscribeTransactionRequest $request, Course $course)
     {
         $user = Auth::user();

         if ($course->mode && $course->mode->name === 'onsite') {
             $today = now()->toDateString();
             if (($course->enrollment_start && $today < $course->enrollment_start) ||
                 ($course->enrollment_end && $today > $course->enrollment_end)) {
                 return redirect()->back()->with('error', 'Enrollment period is closed.');
             }
         }
     
         // Check if the user is already actively subscribed to THIS specific course
         if ($user->hasActiveSubscription($course)) {
             return redirect()->route('front.details', $course->slug)->with('info', 'You are already subscribed to this course.');
         }

         // Check if the user already has a PENDING transaction for THIS specific course
         $existingPendingTransaction = SubscribeTransaction::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->where('is_paid', false)
            ->exists();

         if ($existingPendingTransaction) {
            return redirect()->route('dashboard')->with('info', 'You already have a pending transaction for this course. Please wait for admin approval.');
         }
     
         DB::transaction(function () use ($request, $user, $course) {
             $validated = $request->validated();
     
             if ($request->hasFile('proof')) {
                 $proofPath = $request->file('proof')->store('proofs', 'public');
                 $validated['proof'] = $proofPath;
             }
     
             $validated['user_id'] = $user->id;
             $validated['course_id'] = $course->id;
             $validated['total_amount'] = $course->price; // Ensures correct price for the specific course
             $validated['is_paid'] = false;
             // subscription_start_date will be null until admin approves
     
             SubscribeTransaction::create($validated);
         });
     
         return redirect()->route('dashboard')->with('success', 'Your transaction is being processed. Please wait for admin approval.');
     }
     
}
