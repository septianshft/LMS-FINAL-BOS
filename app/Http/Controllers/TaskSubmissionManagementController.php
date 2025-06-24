<?php

namespace App\Http\Controllers;

use App\Models\{Course, TaskSubmission, Trainer};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, Storage};

class TaskSubmissionManagementController extends Controller
{
    public function index(Course $course)
    {
        $submissions = TaskSubmission::with(['task.module.course', 'user'])
            ->whereHas('task.module', function ($q) use ($course) {
                $q->where('course_id', $course->id);
            })
            ->get();

        return view('admin.task_submissions.index', compact('course', 'submissions'));
    }

    public function update(Request $request, TaskSubmission $submission)
    {
        $data = $request->validate(['grade' => 'nullable|integer']);
        $submission->update($data);
        return back();
    }

    public function download(TaskSubmission $submission)
    {
        $user = Auth::user();
        $course = $submission->task->module->course;

        $allowed = false;

        if ($user->hasRole('admin')) {
            $allowed = true;
        } elseif ($user->hasRole('trainer')) {
            $trainer = Trainer::where('user_id', $user->id)->first();
            if ($trainer && $course->trainer_id === $trainer->id) {
                $allowed = true;
            }
        }

        abort_unless($allowed, 403);

        abort_if(!$submission->file_path, 404);

        return Storage::disk('public')->download($submission->file_path, basename($submission->file_path));
    }
}
