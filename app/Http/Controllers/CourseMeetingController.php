<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseMeeting;
use Illuminate\Http\Request;

class CourseMeetingController extends Controller
{
    public function index(Course $course)
    {
        $course->load('meetings');
        return view('admin.meetings.index', compact('course'));
    }

    public function create(Course $course)
    {
        return view('admin.meetings.create', compact('course'));
    }

    public function store(Request $request, Course $course)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'start_datetime' => 'required|date',
            'end_datetime' => 'required|date|after:start_datetime',
            'location' => 'nullable|string|max:255',
        ]);
        $course->meetings()->create($data);
        return redirect()->route('admin.courses.meetings.index', $course);
    }

    public function edit(Course $course, CourseMeeting $meeting)
    {
        return view('admin.meetings.edit', compact('course', 'meeting'));
    }

    public function update(Request $request, Course $course, CourseMeeting $meeting)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'start_datetime' => 'required|date',
            'end_datetime' => 'required|date|after:start_datetime',
            'location' => 'nullable|string|max:255',
        ]);
        $meeting->update($data);
        return redirect()->route('admin.courses.meetings.index', $course);
    }

    public function destroy(Course $course, CourseMeeting $meeting)
    {
        $meeting->delete();
        return redirect()->route('admin.courses.meetings.index', $course);
    }
}
