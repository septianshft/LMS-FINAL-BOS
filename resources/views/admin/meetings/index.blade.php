<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Manage Meetings') }} - {{ $course->name }}
            </h2>
            <a href="{{ route('admin.courses.meetings.create', $course) }}" class="font-bold py-2 px-4 bg-indigo-700 text-white rounded-full">Add Meeting</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                @forelse($course->meetings as $meeting)
                    <div class="border-b py-4 flex justify-between items-center">
                        <div>
                            <h3 class="font-semibold">{{ $meeting->title }}</h3>
                            <p class="text-sm text-gray-600">{{ $meeting->start_datetime }} - {{ $meeting->end_datetime }}</p>
                            @if($meeting->location)
                                <p class="text-sm text-gray-600">{{ $meeting->location }}</p>
                            @endif
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.courses.meetings.edit', [$course, $meeting]) }}" class="py-1 px-3 bg-yellow-500 text-white rounded">Edit</a>
                            <form action="{{ route('admin.courses.meetings.destroy', [$course, $meeting]) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="py-1 px-3 bg-red-600 text-white rounded">Delete</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p>No meetings scheduled.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
