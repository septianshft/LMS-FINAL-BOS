<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCourseVideoRequest;
use App\Models\CourseModule;
use App\Models\CourseVideo;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ModuleVideoController extends Controller
{
    public function create(CourseModule $courseModule)
    {
        return view('admin.curriculum.videos.create', compact('courseModule'));
    }

    public function store(StoreCourseVideoRequest $request, CourseModule $courseModule)
    {
        DB::transaction(function () use ($request, $courseModule) {
            $data = $request->validated();
            $data['course_id'] = $courseModule->course_id;
            $data['course_module_id'] = $courseModule->id;
            $data['order'] = $data['order'] ?? ($courseModule->videos()->max('order') + 1);
            CourseVideo::create($data);
        });
        return back();
    }

    public function reorder(Request $request, CourseModule $courseModule)
    {
        $request->validate(['videos' => 'required|array']);
        DB::transaction(function () use ($request) {
            foreach ($request->videos as $index => $id) {
                CourseVideo::where('id', $id)->update(['order' => $index + 1]);
            }
        });
        return response()->json(['status' => 'ok']);
    }

    public function destroy(CourseVideo $courseVideo)
    {
        $courseVideo->delete();
        return back();
    }
}
