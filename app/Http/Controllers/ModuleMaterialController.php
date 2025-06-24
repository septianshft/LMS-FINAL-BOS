<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCourseMaterialRequest;
use App\Models\CourseModule;
use App\Models\CourseMaterial;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ModuleMaterialController extends Controller
{
    public function create(CourseModule $courseModule)
    {
        return view('admin.curriculum.materials.create', compact('courseModule'));
    }

    public function store(StoreCourseMaterialRequest $request, CourseModule $courseModule)
    {
        DB::transaction(function () use ($request, $courseModule) {
            $validated = $request->validated();
            $path = $request->file('file')->store('materials', 'public');
            $validated['file_path'] = $path;
            $validated['file_type'] = $request->file('file')->getClientOriginalExtension();
            $validated['course_module_id'] = $courseModule->id;
            $validated['order'] = $validated['order'] ?? ($courseModule->materials()->max('order') + 1);
            CourseMaterial::create($validated);
        });
        return back()->with('success', 'Material uploaded successfully.');
    }

    public function reorder(Request $request, CourseModule $courseModule)
    {
        $request->validate(['materials' => 'required|array']);
        DB::transaction(function () use ($request) {
            foreach ($request->materials as $index => $id) {
                CourseMaterial::where('id', $id)->update(['order' => $index + 1]);
            }
        });
        return response()->json(['status' => 'ok']);
    }

    public function destroy(CourseMaterial $courseMaterial)
    {
        $courseMaterial->delete();
        return back();
    }
}
