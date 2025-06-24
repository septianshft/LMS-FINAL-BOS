<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Task Submissions for ') }}{{ $course->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-3 py-2 text-left">Trainee</th>
                            <th class="px-3 py-2 text-left">Task</th>
                            <th class="px-3 py-2 text-left">Answer</th>
                            <th class="px-3 py-2 text-left">File</th>
                            <th class="px-3 py-2 text-left">Grade</th>
                            <th class="px-3 py-2"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($submissions as $submission)
                            <tr>
                                <td class="px-3 py-2">{{ $submission->user->name }}</td>
                                <td class="px-3 py-2">{{ $submission->task->name }}</td>
                                <td class="px-3 py-2">{{ $submission->answer }}</td>
                                <td class="px-3 py-2">
                                    @if($submission->file_path)
                                        <a href="{{ route('admin.task_submissions.download', $submission) }}" class="text-blue-500">Download</a>
                                    @endif
                                </td>
                                <td class="px-3 py-2">
                                    <form action="{{ route('admin.task_submissions.update', $submission) }}" method="POST" class="flex items-center gap-2">
                                        @csrf
                                        @method('PATCH')
                                        <input type="number" name="grade" value="{{ $submission->grade }}" class="border rounded p-1 w-20">
                                        <button type="submit" class="px-2 py-1 bg-indigo-600 text-white rounded">Save</button>
                                    </form>
                                </td>
                                <td class="px-3 py-2"></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
