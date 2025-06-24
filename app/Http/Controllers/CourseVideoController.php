<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCourseVideoRequest;
use App\Models\Course;
use App\Models\{CourseVideo, CourseModule};
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class CourseVideoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Course $course)
    {
        $modules = CourseModule::where('course_id', $course->id)->get();

        return view('admin.course_videos.create', compact('course', 'modules'));

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCourseVideoRequest $request, Course $course)
    {
        //
        DB::transaction(function () use ($request, $course) {
            $validated = $request->validated();

            $validated['course_id'] = $course->id;

            $courseVideo = CourseVideo::create($validated);

            
        });
        
        return redirect()->route('admin.courses.show', $course->id);

    }

    /**
     * Display the specified resource.
     */
    public function show(CourseVideo $courseVideo)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CourseVideo $courseVideo)
    {
        $modules = CourseModule::where('course_id', $courseVideo->course_id)->get();

        return view('admin.course_videos.edit', compact('courseVideo', 'modules'));

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreCourseVideoRequest $request, CourseVideo $courseVideo)
    {
        //
        DB::transaction(function () use ($request, $courseVideo) {
            $validated = $request->validated();
            $courseVideo->update($validated);
        });

        return redirect()->route('admin.courses.show', $courseVideo->course_id);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CourseVideo $courseVideo)
    {
        //
        DB::beginTransaction();

        try {
            $courseVideo->delete();
            DB::commit();

            return redirect()->route('admin.courses.show', $courseVideo->course_id);
        } catch(\Exception $e){
            DB::rollBack();
            return redirect()->route('admin.courses.show', $courseVideo->course_id)->with('error', 'terjadinya sebuah error');
    }
    }
}
