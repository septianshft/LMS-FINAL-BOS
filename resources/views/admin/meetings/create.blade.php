<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Add Meeting - {{ $course->name }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('admin.courses.meetings.store', $course) }}">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-gray-700">Title</label>
                        <input type="text" name="title" class="w-full border rounded" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700">Start</label>
                        <input type="datetime-local" name="start_datetime" class="w-full border rounded" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700">End</label>
                        <input type="datetime-local" name="end_datetime" class="w-full border rounded" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700">Location</label>
                        <input type="text" name="location" class="w-full border rounded">
                    </div>
                    <button type="submit" class="py-2 px-4 bg-indigo-700 text-white rounded">Save</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
