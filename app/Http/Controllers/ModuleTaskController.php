<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreModuleTaskRequest;
use App\Models\CourseModule;
use App\Models\ModuleTask;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ModuleTaskController extends Controller
{
    public function create(CourseModule $courseModule)
    {
        return view('admin.curriculum.tasks.create', compact('courseModule'));
    }

    public function store(StoreModuleTaskRequest $request, CourseModule $courseModule)
    {
        DB::transaction(function () use ($request, $courseModule) {
            $data = $request->validated();
            $data['course_module_id'] = $courseModule->id;
            $data['order'] = $data['order'] ?? ($courseModule->tasks()->max('order') + 1);
            ModuleTask::create($data);
        });
        return back();
    }

    public function reorder(Request $request, CourseModule $courseModule)
    {
        $request->validate(['tasks' => 'required|array']);
        DB::transaction(function () use ($request) {
            foreach ($request->tasks as $index => $id) {
                ModuleTask::where('id', $id)->update(['order' => $index + 1]);
            }
        });
        return response()->json(['status' => 'ok']);
    }

    public function destroy(ModuleTask $moduleTask)
    {
        $moduleTask->delete();
        return back();
    }
}
