<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCourseMaterialRequest;
use App\Models\{CourseMaterial, Trainer};
use Illuminate\Support\Facades\{DB, Auth, Storage};

class CourseMaterialController extends Controller
{
    /**
     * Store a newly uploaded course material.
     */
    public function store(StoreCourseMaterialRequest $request)
    {
        DB::transaction(function () use ($request) {
            $validated = $request->validated();

            $path = $request->file('file')->store('materials', 'public');
            $validated['file_path'] = $path;
            $validated['file_type'] = $request->file('file')->getClientOriginalExtension();

            CourseMaterial::create($validated);
        });

        return back()->with('success', 'Material uploaded successfully.');
    }

    public function download(CourseMaterial $material)
    {
        $user = Auth::user();
        $course = $material->module->course;

        $allowed = false;

        if ($user->hasRole('admin')) {
            $allowed = true;
        } elseif ($user->hasRole('trainer')) {
            $trainer = Trainer::where('user_id', $user->id)->first();
            if ($trainer && $course->trainer_id === $trainer->id) {
                $allowed = true;
            }
        } elseif ($user->hasRole('trainee')) {
            $allowed = $course->trainees()->where('user_id', $user->id)->exists();
        }

        abort_unless($allowed, 403);

        $downloadName = $material->name;
        // Ensure the downloaded file includes the correct extension
        if ($material->file_type) {
            $extension = '.' . ltrim($material->file_type, '.');
            if (!str_ends_with(strtolower($downloadName), strtolower($extension))) {
                $downloadName .= $extension;
            }
        }

        return Storage::disk('public')->download($material->file_path, $downloadName);
    }
}
