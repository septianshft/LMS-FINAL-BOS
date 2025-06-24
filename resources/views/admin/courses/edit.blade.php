<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Course') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden p-10 shadow-sm sm:rounded-lg">

                @if($errors->any())
                    @foreach($errors->all() as $error)
                        <div class="py-3 w-full rounded-3xl bg-red-500 text-white">
                            {{ $error }}
                        </div>
                    @endforeach
                @endif

                <form method="POST" action="{{ route('admin.courses.update', $course) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="name" :value="__('Name')" />
                        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="$course->name" required autofocus autocomplete="name" />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    @role('admin')
                    <div class="mt-4">
                        <x-input-label for="trainer_id" :value="__('Trainer')" />
                        <select name="trainer_id" id="trainer_id" class="py-3 rounded-lg pl-3 w-full border border-slate-300">
                            <option value="">Choose trainer</option>
                            @foreach($trainers as $trainer)
                                <option value="{{ $trainer->id }}" {{ $course->trainer_id == $trainer->id ? 'selected' : '' }}>{{ $trainer->user->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('trainer_id')" class="mt-2" />
                    </div>
                    @else
                        <input type="hidden" name="trainer_id" value="{{ Auth::user()->trainer->id ?? '' }}">
                    @endrole

                    <div class="mt-4">
                        <x-input-label for="path_trailer" :value="__('Path Trailer')" />
                        <x-text-input id="path_trailer" class="block mt-1 w-full" type="text" name="path_trailer" :value="$course->path_trailer" required />
                        <x-input-error :messages="$errors->get('path_trailer')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="thumbnail" :value="__('Thumbnail')" />
                        <img src="{{ asset(path: 'storage/' . $course->thumbnail) }}" class="rounded-2xl object-cover w-[120px] h-[90px]" alt="thumbnail">
                       <x-text-input id="thumbnail" class="block mt-1 w-full" type="file" name="thumbnail" autocomplete="thumbnail" />
                        <x-input-error :messages="$errors->get('thumbnail')" class="mt-2" />
                    </div>
                    
                    <div class="mb-4">
                        <label for="price" class="block font-bold">Harga (Rp)</label>
                        <input type="number" name="price" id="price" value="{{ old('price', $course->price ?? 0) }}"
                        class="form-input w-full" min="0">
                    </div>

                    <div class="mt-4">
                        <x-input-label for="category" :value="__('Category')" />
                        <select name="category_id" id="category_id" class="py-3 rounded-lg pl-3 w-full border border-slate-300">
                            <option value="">Choose category</option>
                            @forelse($categories as $category)
                                <option value="{{ $category->id }}" {{ $course->category_id == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @empty
                                <option disabled>No categories available</option>
                            @endforelse
                        </select>
                        <x-input-error :messages="$errors->get('category')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="course_mode_id" :value="__('Mode')" />
                        <select name="course_mode_id" id="course_mode_id" class="py-3 rounded-lg pl-3 w-full border border-slate-300">
                            <option value="">Choose mode</option>
                            @foreach($modes as $mode)
                                <option value="{{ $mode->id }}" {{ $course->course_mode_id == $mode->id ? 'selected' : '' }}>{{ $mode->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('course_mode_id')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="course_level_id" :value="__('Level')" />
                        <select name="course_level_id" id="course_level_id" class="py-3 rounded-lg pl-3 w-full border border-slate-300">
                            <option value="">Choose level</option>
                            @foreach($levels as $level)
                                <option value="{{ $level->id }}" {{ $course->course_level_id == $level->id ? 'selected' : '' }}>{{ $level->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('course_level_id')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="enrollment_start" :value="__('Enrollment Start')" />
                        <x-text-input id="enrollment_start" class="block mt-1 w-full" type="datetime-local" name="enrollment_start" :value="old('enrollment_start', optional($course->enrollment_start)->format('Y-m-d\TH:i'))" />
                        <x-input-error :messages="$errors->get('enrollment_start')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="enrollment_end" :value="__('Enrollment End')" />
                        <x-text-input id="enrollment_end" class="block mt-1 w-full" type="datetime-local" name="enrollment_end" :value="old('enrollment_end', optional($course->enrollment_end)->format('Y-m-d\TH:i'))" />
                        <x-input-error :messages="$errors->get('enrollment_end')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="about" :value="__('About')" />
                        <textarea name="about" id="about" cols="30" rows="5" class="border border-slate-300 rounded-xl w-full">{{ $course->about }}</textarea>
                        <x-input-error :messages="$errors->get('about')" class="mt-2" />
                    </div>

                    <hr class="my-5">

                    <div class="mt-4">
                        <div class="flex flex-col gap-y-5">
                        
                            <x-input-label for="keypoints" :value="__('Keypoints')" />
                            @php
                                $keypoints = $course->course_keypoints->pluck('name')->toArray();
                            @endphp
                            @for ($i = 0; $i < 4; $i++)
                                <input type="text" class="py-3 rounded-lg border-slate-300 border" placeholder="Write your keypoint" name="course_keypoints[]" value="{{ $keypoints[$i] ?? '' }}">
                            @endfor
                        </div>
                        <x-input-error :messages="$errors->get('keypoints')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="materials" :value="__('Course Materials')" />
                        <input id="materials" class="block mt-1 w-full" type="file" name="materials[]" multiple>
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <button type="submit" class="font-bold py-4 px-6 bg-indigo-700 text-white rounded-full">
                            Update Course
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
