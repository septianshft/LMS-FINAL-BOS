<?php

namespace App\Http\Controllers;

use App\Models\{ModuleTask, TaskSubmission, CourseProgress, CourseVideo, CourseMaterial, ModuleTask as Task, Certificate};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TaskSubmissionController extends Controller
{
    public function create(ModuleTask $task)
    {
        return view('front.task_submissions.create', compact('task'));
    }

    public function store(Request $request, ModuleTask $task)
    {
        $user = Auth::user();

        $data = $request->validate([
            'file' => 'nullable|file',
            'answer' => 'nullable|string',
        ]);

        if (!$request->hasFile('file') && empty($data['answer'])) {
            return back()->withErrors('File or answer is required.');
        }

        DB::transaction(function () use ($request, $task, $user, $data) {
            $path = null;
            if ($request->hasFile('file')) {
                $path = $request->file('file')->store('task_submissions', 'public');
            }

            TaskSubmission::create([
                'module_task_id' => $task->id,
                'user_id' => $user->id,
                'file_path' => $path,
                'answer' => $data['answer'] ?? null,
            ]);

            $progress = CourseProgress::firstOrCreate([
                'user_id' => $user->id,
                'course_id' => $task->module->course_id,
            ], [
                'completed_videos' => [],
                'completed_materials' => [],
                'completed_tasks' => [],
                'progress' => 0,
            ]);

            $completed = $progress->completed_tasks ?? [];
            if (!in_array($task->id, $completed)) {
                $completed[] = $task->id;
                $progress->completed_tasks = $completed;
            }

            $totalVideos = CourseVideo::where('course_id', $task->module->course_id)->count();
            $totalMaterials = CourseMaterial::whereHas('module', function ($q) use ($task) {
                $q->where('course_id', $task->module->course_id);
            })->count();
            $totalTasks = Task::whereHas('module', function ($q) use ($task) {
                $q->where('course_id', $task->module->course_id);
            })->count();

            $completedCount = count($progress->completed_videos ?? []) + count($progress->completed_materials ?? []) + count($progress->completed_tasks ?? []);
            $totalItems = $totalVideos + $totalMaterials + $totalTasks;
            $progress->progress = $totalItems > 0 ? floor($completedCount / $totalItems * 100) : 0;
            $progress->save();

            if ($progress->progress == 100 && $progress->quiz_passed && !$progress->course->certificates()->where('user_id', $user->id)->exists()) {
                $this->generateCertificate($progress->course, $user);
            }
        });

        return back();
    }

    private function generateCertificate(\App\Models\Course $course, $user): void
    {
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('certificates.certificate', [
            'course' => $course,
            'user' => $user,
            'date' => now()->toDateString(),
        ]);

        $path = 'certificates/' . $user->id . '_' . $course->id . '.pdf';
        Storage::disk('public')->put($path, $pdf->output());

        Certificate::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'path' => $path,
            'generated_at' => now(),
        ]);
    }
}
