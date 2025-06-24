<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Add Video to Module') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('admin.curriculum.videos.store', $courseModule) }}">
                    @csrf
                    <input type="hidden" name="course_module_id" value="{{ $courseModule->id }}">
                    <div>
                        <x-input-label for="name" :value="__('Name')" />
                        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" required autofocus />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>
                    <div class="mt-4">
                        <x-input-label for="path_video" :value="__('YouTube Unique URL')" />
                        <x-text-input id="path_video" class="block mt-1 w-full" type="text" name="path_video" required />
                        <x-input-error :messages="$errors->get('path_video')" class="mt-2" />
                    </div>
                    <div class="flex items-center justify-end mt-4">
                        <button type="submit" class="px-4 py-2 bg-indigo-700 text-white rounded">
                            {{ __('Add Video') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
