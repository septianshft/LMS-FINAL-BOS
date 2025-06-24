<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCourseModuleRequest;
use App\Http\Requests\UpdateCourseModuleRequest;
use App\Models\Course;
use App\Models\CourseModule;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class CourseModuleController extends Controller
{
    public function index(Course $course)
    {
        $modules = $course->modules()->with(['videos','materials','tasks'])->orderBy('order')->get();
        return view('admin.curriculum.index', compact('course','modules'));
    }

    public function store(StoreCourseModuleRequest $request, Course $course)
    {
        DB::transaction(function () use ($request, $course) {
            $data = $request->validated();
            $data['course_id'] = $course->id;
            $data['order'] = $data['order'] ?? ($course->modules()->max('order') + 1);
            CourseModule::create($data);
        });
        return redirect()->route('admin.curriculum.index', $course);
    }

    public function update(UpdateCourseModuleRequest $request, CourseModule $courseModule)
    {
        DB::transaction(function () use ($request, $courseModule) {
            $courseModule->update($request->validated());
        });
        return back();
    }

    public function destroy(CourseModule $courseModule)
    {
        $course = $courseModule->course;
        $courseModule->delete();
        return redirect()->route('admin.curriculum.index', $course);
    }

    public function reorder(Request $request, Course $course)
    {
        $request->validate(['modules' => 'required|array']);
        DB::transaction(function () use ($request) {
            foreach ($request->modules as $index => $id) {
                CourseModule::where('id', $id)->update(['order' => $index + 1]);
            }
        });
        return response()->json(['status' => 'ok']);
    }
}
