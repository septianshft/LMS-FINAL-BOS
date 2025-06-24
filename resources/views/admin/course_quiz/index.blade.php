<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-row justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Manage Quizzes by Course') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-10 flex flex-col gap-y-5">

                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                        <strong class="font-bold">Success!</strong>
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif
                @if(session('info'))
                    <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative" role="alert">
                        <strong class="font-bold">Info:</strong>
                        <span class="block sm:inline">{{ session('info') }}</span>
                    </div>
                @endif


                @forelse($courses as $course)
                    <div class="item-card flex flex-col md:flex-row gap-y-10 justify-between md:items-center">
                        <div class="flex flex-row items-center gap-x-3">
                        <img src="{{ asset(path: 'storage/' . $course->thumbnail) }}" class="rounded-2xl object-cover w-[120px] h-[90px]" alt="thumbnail">
                            <div class="flex flex-col">
                                <h3 class="text-indigo-950 text-xl font-bold">{{ $course->name }}</h3>
                                <p class="text-slate-500 text-sm">{{ $course->category->name }}</p>
                            </div>
                        </div>
                        
                        {{-- Display Quiz Status --}}
                        <div class="flex flex-col">
                            @if($course->finalQuizzes->count() > 0)
                                @php $quiz = $course->finalQuizzes->first(); @endphp
                                <p class="text-slate-500 text-sm">
                                    Status: 
                                    @if($quiz->is_hidden_from_trainee)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            Hidden
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Visible
                                        </span>
                                    @endif
                                </p>
                            @else
                                <p class="text-slate-500 text-sm">No quiz created yet.</p>
                            @endif
                        </div>

                        <div class="hidden md:flex flex-row items-center gap-x-3">
                            @if($course->finalQuizzes->count() > 0)
                                <a href="{{ route('admin.course_quiz.edit', $course) }}" class="font-bold py-4 px-6 bg-yellow-500 hover:bg-yellow-600 text-white rounded-full">
                                    Edit Quiz
                                </a>
                            @else
                                <a href="{{ route('admin.course_quiz.create', $course) }}" class="font-bold py-4 px-6 bg-indigo-700 hover:bg-indigo-800 text-white rounded-full">
                                    Create Quiz
                                </a>
                            @endif
                        </div>
                    </div>
                @empty
                    <p>Tidak ada course yang ditemukan.</p>
                @endforelse

            </div>
            <div class="mt-4">
                {{ $courses->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
