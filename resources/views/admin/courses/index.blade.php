<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-row justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Manage Courses') }}
            </h2>
            <a href="{{ route('admin.courses.create') }}" class="font-bold py-4 px-6 bg-indigo-700 text-white rounded-full">
                Add New
            </a>
        </div>
    </x-slot>
    
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-10 flex flex-col gap-y-5">

            @forelse($courses as $course)
                <div class="item-card flex flex-col md:flex-row gap-y-10 justify-between md:items-center">
                    <div class="flex flex-row items-center gap-x-3">
                    <img src="{{ asset(path: 'storage/' . $course->thumbnail) }}" class="rounded-2xl object-cover w-[120px] h-[90px]" alt="thumbnail">
                    
                    <div class="flex flex-col">
                            <h3 class="text-indigo-950 text-xl font-bold">{{ $course->name }}</h3>
                            <p class="text-slate-500 text-sm">{{$course->category->name}}</p>
                        </div>
                    </div>
                    <div class="hidden md:flex flex-col">
                        <p class="text-slate-500 text-sm">Trainee</p>
                        <h3 class="text-indigo-950 text-xl font-bold">{{ $course->trainees->count() }}</h3>
                    </div>
                    <div class="hidden md:flex flex-col">
                        <p class="text-slate-500 text-sm">Videos</p>
                        <h3 class="text-indigo-950 text-xl font-bold">{{ $course->course_videos->count() }}</h3>
                    </div>
                    <div class="hidden md:flex flex-col">
                        <p class="text-slate-500 text-sm">Trainer</p>
                        <h3 class="text-indigo-950 text-xl font-bold">{{ optional($course->trainer?->user)->name ?? '-' }}</h3>
                        </div>
                    <div class="hidden md:flex flex-row items-center gap-x-3">
                        <a href="{{ route('admin.courses.show', $course)}}" class="font-bold py-4 px-6 bg-indigo-700 text-white rounded-full">
                            Manage
                        </a>
                        <form action="{{ route('admin.courses.destroy', $course) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="font-bold py-4 px-6 bg-red-700 text-white rounded-full">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
                @empty
                <p>
                    Belum ada kelas yang ditambahkan
                </p>
                @endforelse
                
            </div>
        </div>
    </div>
</x-app-layout>