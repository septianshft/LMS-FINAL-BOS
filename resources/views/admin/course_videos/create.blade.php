<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Add Video to Course') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden p-10 shadow-sm sm:rounded-lg">

                @if($errors->any())
                    @foreach($errors->all() as $error)
                        <div class="py-3 w-full rounded-3xl bg-red-500 text-white">
                            {{$error}}
                        </div>
                    @endforeach
                @endif

                <div class="item-card flex flex-row gap-y-10 justify-between items-center">
                    <div class="flex flex-row items-center gap-x-3">
                    <img src="{{ asset(path: 'storage/' . $course->thumbnail) }}" class="rounded-2xl object-cover w-[120px] h-[90px]" alt="thumbnail">
                    <div class="flex flex-col">
                            <h3 class="text-indigo-950 text-xl font-bold">{{ $course->name }}</h3>
                            <p class="text-slate-500 text-sm">{{ $course->category->name }}</p>
                        </div>
                    </div>
                    <div>
                        <p class="text-slate-500 text-sm">Trainer</p>
                        <h3 class="text-indigo-950 text-xl font-bold">{{ $course->trainer->user->name }}</h3>
                    </div>
                </div>

                <hr class="my-5">
                
                <form method="POST" action="{{ route('admin.course.add_video.save', $course->id) }}" enctype="multipart/form-data">
                    @csrf

                    <div>
                        <x-input-label for="name" :value="__('Name')" />
                        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="path_video" :value="__('path_video')" />
                        <x-text-input id="path_video" class="block mt-1 w-full" type="text" name="path_video" :value="old('path_video')" required autofocus autocomplete="path_video" />
                        <x-input-error :messages="$errors->get('path_video')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="course_module_id" :value="__('Module')" />
                        <select id="course_module_id" name="course_module_id" class="block mt-1 w-full">
                            @foreach($modules as $module)
                                <option value="{{ $module->id }}" {{ old('course_module_id') == $module->id ? 'selected' : '' }}>{{ $module->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('course_module_id')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="order" :value="__('Order')" />
                        <x-text-input id="order" class="block mt-1 w-full" type="number" name="order" :value="old('order', 0)" />
                        <x-input-error :messages="$errors->get('order')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end mt-4">
            
                        <button type="submit" class="font-bold py-4 px-6 bg-indigo-700 text-white rounded-full">
                            Add New Video
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
