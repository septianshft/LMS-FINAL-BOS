<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use App\Models\Category;
use App\Models\Course;
use App\Models\Trainer;
use App\Models\CourseMode;
use App\Models\CourseLevel;
use App\Models\CourseKeypoint;
use App\Models\CourseMaterial;
use App\Models\CourseVideo;
use App\Models\CourseModule;
use App\Models\SubscribeTransaction;
use App\Models\CourseProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;



class CourseController extends Controller
{
    // ✅ Untuk halaman frontend (landing page)
    public function frontIndex(Request $request)
    {
        $categories = Category::all();
        $modes = CourseMode::all();
        $levels = CourseLevel::all();

        $courses = Course::with(['category', 'trainer.user', 'trainees', 'mode', 'level'])
            ->when($request->category_id, fn($q) => $q->where('category_id', $request->category_id))
            ->when($request->course_mode_id, fn($q) => $q->where('course_mode_id', $request->course_mode_id))
            ->when($request->course_level_id, fn($q) => $q->where('course_level_id', $request->course_level_id))
            ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->latest()
            ->get();

        if ($request->ajax()) {
            return view('partials.course-list', compact('courses'))->render();
        }

        return view('front.index', compact('courses', 'categories', 'modes', 'levels'));
    }

    /**
     * Page to explore all courses with filtering and search
     */
    public function explore(Request $request)
    {
        $categories = Category::all();
        $modes = CourseMode::all();
        $levels = CourseLevel::all();

        $courses = Course::with(['category', 'trainer.user', 'trainees', 'mode', 'level'])
            ->when($request->category_id, fn($q) => $q->where('category_id', $request->category_id))
            ->when($request->course_mode_id, fn($q) => $q->where('course_mode_id', $request->course_mode_id))
            ->when($request->course_level_id, fn($q) => $q->where('course_level_id', $request->course_level_id))
            ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->latest()
            ->get();

        if ($request->ajax()) {
            return view('partials.course-list', compact('courses'))->render();
        }

        return view('front.explore', compact('courses', 'categories', 'modes', 'levels'));
    }

    /**
     * Display courses owned by authenticated user
     */
    public function myCourses()
    {
        $user = Auth::user();

        $courses = Course::whereHas('subscribeTransactions', function ($q) use ($user) {
            $q->where('user_id', $user->id)->where('is_paid', true);
        })->with(['category', 'trainer.user', 'mode', 'level', 'modules.tasks'])->get();

        $tasksToDo = [];
        foreach ($courses as $course) {
            $progress = CourseProgress::firstOrCreate([
                'user_id' => $user->id,
                'course_id' => $course->id,
            ], [
                'completed_videos' => [],
                'completed_materials' => [],
                'completed_tasks' => [],
                'progress' => 0,
            ]);

            $completed = $progress->completed_tasks ?? [];
            foreach ($course->modules as $module) {
                foreach ($module->tasks as $task) {
                    if (!in_array($task->id, $completed)) {
                        $tasksToDo[] = $task;
                    }
                }
            }
        }

        return view('front.my_courses', compact('courses', 'tasksToDo'));
    }

    /**
     * Join a free course and add it to the trainee's course list
     */
    public function join(Course $course)
    {
        $user = Auth::user();

        if ($course->price > 0) {
            return redirect()->route('front.pricing', $course->slug);
        }

        if ($course->mode && $course->mode->name === 'onsite') {
            $today = now()->toDateString();
            if (($course->enrollment_start && $today < $course->enrollment_start) ||
                ($course->enrollment_end && $today > $course->enrollment_end)) {
                return redirect()->back()->with('error', 'Enrollment period is closed.');
            }
        }

        $existing = SubscribeTransaction::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->where('is_paid', true)
            ->first();

        if (!$existing) {
            SubscribeTransaction::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'total_amount' => 0,
                'is_paid' => true,
                'proof' => 'free',
                'subscription_start_date' => now(),
            ]);
        }

        $user->courses()->syncWithoutDetaching($course->id);

        return redirect()->route('courses.my')->with('success', 'Course added to your list.');
    }

    // ✅ Untuk halaman admin (manage course)
    public function index()
    {
        $user = Auth::user();
        $query = Course::with(['category', 'trainer.user', 'trainees', 'course_videos', 'mode', 'level'])->orderByDesc('id');

        if ($user->hasRole('trainer')) {
            $query->whereHas('trainer', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            });
        }

        $courses = $query->paginate(10);

        return view('admin.courses.index', compact('courses'));
    }

    public function create()
    {
        $categories = Category::all();
        $modes = CourseMode::all();
        $levels = CourseLevel::all();
        $trainers = [];
        if (Auth::user()->hasRole('admin')) {
            $trainers = Trainer::with('user')->get();
        }

        return view('admin.courses.create', compact('categories', 'modes', 'levels', 'trainers'));
    }

public function store(StoreCourseRequest $request)
{
    $data = $request->validated();

    $data['enrollment_start'] = $request->input('enrollment_start');
    $data['enrollment_end'] = $request->input('enrollment_end');

    // Upload thumbnail
    if ($request->hasFile('thumbnail')) {
        $path = $request->file('thumbnail')->store('thumbnails', 'public');
        $data['thumbnail'] = $path;
    }

    // Tambahkan slug
    $data['slug'] = \Str::slug($data['name']) . '-' . uniqid();

    // Simpan Course
    $course = Course::create($data);

    // ✅ Buat Modul Default
    $module = CourseModule::create([
        'course_id' => $course->id,
        'name' => 'Modul 1',
        'description' => 'Modul default',
        'order' => 1,
    ]);

    // ✅ Simpan Keypoints
    if ($request->has('course_keypoints')) {
        foreach ($request->course_keypoints as $keypoint) {
            if (!empty($keypoint)) {
                CourseKeypoint::create([
                    'course_id' => $course->id,
                    'name' => $keypoint,
                ]);
            }
        }
    }

    // ✅ Simpan Materials ke modul default
    if ($request->hasFile('materials')) {
        foreach ($request->file('materials') as $file) {
            $materialPath = $file->store('materials', 'public');
            CourseMaterial::create([
                'course_id' => $course->id,
                'course_module_id' => $module->id, // ⬅ penting
                'path' => $materialPath,
                'name' => $file->getClientOriginalName(),
            ]);
        }
    }

    return redirect()->route('admin.courses.index')->with('success', 'Course created successfully with default module.');
}


    public function show(Course $course)
{
    $course->load([
        'category',
        'trainer.user',
        'trainees',
        'course_videos',
        'course_keypoints',
        'modules',
        'modules.videos',
        'modules.materials',
        'modules.tasks',
        'meetings',
    ]);

    return view('admin.courses.show', compact('course'));
}


    public function edit(Course $course)
    {
        $course->load(['category', 'trainer.user', 'trainees', 'course_videos', 'course_keypoints', 'modules']);

        $user = Auth::user();
        if ($user->hasRole('trainer') && $course->trainer->user_id !== $user->id) {
            abort(403);
        }

        $categories = Category::all();
        $modes = CourseMode::all();
        $levels = CourseLevel::all();
        $trainers = [];
        if ($user->hasRole('admin')) {
            $trainers = Trainer::with('user')->get();
        }
        return view('admin.courses.edit', compact('course', 'categories', 'modes', 'levels', 'trainers'));
    }

    public function update(UpdateCourseRequest $request, Course $course)
    {
        DB::transaction(function () use ($request, $course) {
            $validated = $request->validated();

            $validated['enrollment_start'] = $request->input('enrollment_start');
            $validated['enrollment_end'] = $request->input('enrollment_end');

            if (Auth::user()->hasRole('admin') && $request->filled('trainer_id')) {
                $course->trainer_id = $request->input('trainer_id');
            } elseif (Auth::user()->hasRole('trainer')) {
                $course->trainer_id = Auth::user()->trainer->id ?? $course->trainer_id;
            }

            if ($request->hasFile('thumbnail')) {
                $validated['thumbnail'] = $request->file('thumbnail')->store('thumbnails', 'public');
            }

            $slug = Str::slug($validated['name']);
            $count = Course::where('slug', 'like', "{$slug}%")->where('id', '!=', $course->id)->count();
            $slug = $count ? "{$slug}-{$count}" : $slug;

            $validatedData = array_merge($validated, [
                'slug' => $slug,
                'trainer_id' => $course->trainer_id,
            ]);

            $course->update($validatedData);

            if (!empty($validated['course_keypoints'])) {
                $course->course_keypoints()->delete();
                foreach ($validated['course_keypoints'] as $keypointText) {
                    if (!empty($keypointText)) {
                        $course->course_keypoints()->create(['name' => $keypointText]);
                    }
                }
            }
        });

        return redirect()->route('admin.courses.show', $course);
    }

    public function destroy(Course $course)
    {
        $user = Auth::user();
        if ($user->hasRole('trainer') && $course->trainer->user_id !== $user->id) {
            abort(403);
        }

        DB::beginTransaction();
        try {
            $course->delete();
            DB::commit();
            return redirect()->route('admin.courses.index')->with('success', 'Course deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.courses.index')->with('error', 'An error occurred while deleting the course.');
        }
    }
}
