<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Manage Curriculum') }}</h2>
            <a href="{{ route('admin.courses.show', $course) }}" class="font-bold py-2 px-4 bg-gray-700 text-white rounded-full">Back</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('admin.curriculum.store', $course) }}" class="mb-4 flex gap-2">
                    @csrf
                    <input type="text" name="name" class="border rounded w-full" placeholder="Module name">
                    <button class="px-4 py-2 bg-indigo-700 text-white rounded">Add Module</button>
                </form>
                <div id="modules-list">
                @foreach($modules as $module)
                    <div class="border p-4 rounded mb-4 module-item" data-id="{{ $module->id }}">
                        <span class="handle cursor-move">&#9776;</span>
                        <div class="flex justify-between items-center mb-2">
                            <form method="POST" action="{{ route('admin.curriculum.update', $module) }}" class="flex flex-1 gap-2">
                                @csrf
                                @method('PUT')
                                <input type="text" name="name" value="{{ $module->name }}" class="border rounded w-full">
                                <button class="px-3 py-1 bg-indigo-700 text-white rounded">Save</button>
                            </form>
                            <form method="POST" action="{{ route('admin.curriculum.destroy', $module) }}" onsubmit="return confirm('Are you sure?')">
                                @csrf
                                @method('DELETE')
                                <button class="px-3 py-1 bg-red-700 text-white rounded">Delete</button>
                            </form>
                        </div>

                        <div class="ml-4">
                            <div class="flex gap-2 mb-2">
                                <a href="{{ route('admin.curriculum.videos.create', $module) }}" class="px-3 py-1 bg-green-700 text-white rounded">Add Video</a>
                                <a href="{{ route('admin.curriculum.materials.create', $module) }}" class="px-3 py-1 bg-green-700 text-white rounded">Add Material</a>
                                <a href="{{ route('admin.curriculum.tasks.create', $module) }}" class="px-3 py-1 bg-green-700 text-white rounded">Add Task</a>
                            </div>

                            <div class="mt-2">
                                <h4 class="font-semibold">Videos</h4>
                                <ul class="list-disc list-inside sortable-videos" data-module="{{ $module->id }}">
                                    @foreach($module->videos as $v)
                                        <li class="video-item" data-id="{{ $v->id }}">
                                            <div class="flex justify-between items-center">
                                                <span>{{ $v->name }}</span>
                                                <form action="{{ route('admin.curriculum.videos.destroy', $v) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600">Delete</button>
                                                </form>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                                <h4 class="font-semibold mt-2">Materials</h4>
                                <ul class="list-disc list-inside sortable-materials" data-module="{{ $module->id }}">
                                    @foreach($module->materials as $m)
                                        <li class="material-item" data-id="{{ $m->id }}">
                                            <div class="flex justify-between items-center">
                                                <span>{{ $m->name }}</span>
                                                <form action="{{ route('admin.curriculum.materials.destroy', $m) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600">Delete</button>
                                                </form>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                                <h4 class="font-semibold mt-2">Tasks</h4>
                                <ul class="list-disc list-inside sortable-tasks" data-module="{{ $module->id }}">
                                    @foreach($module->tasks as $t)
                                        <li class="task-item" data-id="{{ $t->id }}">
                                            <div class="flex justify-between items-center">
                                                <span>{{ $t->name }}</span>
                                                <form action="{{ route('admin.curriculum.tasks.destroy', $t) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600">Delete</button>
                                                </form>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endforeach
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            new Sortable(document.getElementById('modules-list'), {
                handle: '.handle',
                animation: 150,
                onEnd: () => {
                    const ids = Array.from(document.querySelectorAll('.module-item')).map(el => el.dataset.id);
                    fetch('{{ route('admin.curriculum.modules.reorder', $course) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({modules: ids})
                    });
                }
            });

            document.querySelectorAll('.sortable-videos').forEach(list => {
                new Sortable(list, {
                    animation: 150,
                    onEnd: () => {
                        const moduleId = list.dataset.module;
                        const ids = Array.from(list.querySelectorAll('.video-item')).map(el => el.dataset.id);
                        fetch(`/admin/curriculum/module/${moduleId}/videos/reorder`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({videos: ids})
                        });
                    }
                });
            });

            document.querySelectorAll('.sortable-materials').forEach(list => {
                new Sortable(list, {
                    animation: 150,
                    onEnd: () => {
                        const moduleId = list.dataset.module;
                        const ids = Array.from(list.querySelectorAll('.material-item')).map(el => el.dataset.id);
                        fetch(`/admin/curriculum/module/${moduleId}/materials/reorder`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({materials: ids})
                        });
                    }
                });
            });

            document.querySelectorAll('.sortable-tasks').forEach(list => {
                new Sortable(list, {
                    animation: 150,
                    onEnd: () => {
                        const moduleId = list.dataset.module;
                        const ids = Array.from(list.querySelectorAll('.task-item')).map(el => el.dataset.id);
                        fetch(`/admin/curriculum/module/${moduleId}/tasks/reorder`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({tasks: ids})
                        });
                    }
                });
            });
        });
    </script>
</x-app-layout>
