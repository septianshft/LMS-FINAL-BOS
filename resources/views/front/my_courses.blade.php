<!doctype html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="{{asset('css//output.css')}}" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
    <!-- CSS -->
    <link rel="stylesheet" href="https://unpkg.com/flickity@2/dist/flickity.min.css">
</head>
<body class="text-black font-poppins pt-10 pb-10 bg-gray-100">
    <div class="max-w-[1200px] mx-auto">
        <!-- Navbar -->
        <nav class="flex justify-between items-center py-6 px-[50px] bg-white shadow rounded-xl mb-6">
            <div class="flex items-center gap-3">
                <img src="{{ asset('assets/logo/logo.png') }}" alt="logo" class="w-[50px] h-[50px] object-contain">
                <div class="leading-tight text-black">
                    <p class="font-semibold text-xs">Pusat Unggulan IPTEK Perguruan Tinggi</p>
                    <h1 class="font-bold text-sm">Intelligent Sensing-IoT</h1>
                </div>
            </div>
            <ul class="flex items-center gap-[30px]">
                <li><a href="{{ route('front.index') }}" class="font-semibold">Home</a></li>
                <li><a href="#" class="font-semibold">My Certificate</a></li>
                <li><a href="{{ route('courses.my') }}" class="font-semibold">My Course</a></li>
                <li>
                    <a href="{{ route('cart.index') }}" class="relative flex items-center">
                        <img src="{{ asset('asset/vendor/fontawesome-free/svgs/solid/shopping-cart.svg') }}" class="w-5 h-5" alt="cart">
                        @if($cartCount > 0)
                        <span class="absolute -top-2 -right-2 w-4 h-4 text-xs text-white bg-red-500 rounded-full flex items-center justify-center">{{ $cartCount }}</span>
                        @endif
                    </a>
                </li>
            </ul>
            @auth
            <div class="relative" id="dropdownWrapper">
                <div class="w-[56px] h-[56px] overflow-hidden rounded-full cursor-pointer" id="dropdownAvatar">
                    <img src="{{ Auth::user()->avatar_url }}" class="w-full h-full object-cover" alt="photo">
                </div>
                <div class="absolute right-0 mt-2 bg-white border rounded shadow hidden z-10" id="dropdownMenu">
                    <a href="{{ route('profile.edit') }}" class="block px-4 py-2 hover:bg-gray-100">Profile Settings</a>
                    <a href="{{ route('courses.my') }}" class="block px-4 py-2 hover:bg-gray-100">My Course</a>
                    <a href="{{ route('dashboard') }}" class="block px-4 py-2 hover:bg-gray-100">Dashboard</a>
                </div>
            </div>
            @endauth
            @guest
            <div class="flex gap-[10px] items-center">
                <a href="{{ route('register') }}" class="font-semibold rounded-[30px] p-[16px_32px] ring-1 ring-black transition-all">Register</a>
                <a href="{{ route('login') }}" class="font-semibold rounded-[30px] p-[16px_32px] bg-[#FF6129] text-white">Login In</a>
            </div>
            @endguest
        </nav>

        <!-- Judul -->
        <h1 class="text-2xl font-bold mb-4 px-[50px]">My Courses</h1>

        <!-- Konten -->
        <div class="px-[50px]" id="courseContent">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($courses as $course)
@php $firstVideo = $course->course_videos->first(); @endphp
                <div class="flex flex-col rounded-xl bg-white overflow-hidden transition-all hover:ring-2 hover:ring-[#FF6129]">
                    <a href="{{ $firstVideo ? route('front.learning', ['course' => $course->id, 'courseVideoId' => optional($firstVideo)->id]) : route('front.details', $course->slug) }}" class="thumbnail w-full h-[200px] shrink-0 rounded-[10px] overflow-hidden">
                        <img src="{{ $course->thumbnail_url }}" class="w-full h-full object-cover" alt="thumbnail">
                    </a>
                    <div class="p-4 flex flex-col gap-2">
                        <a href="{{ $firstVideo ? route('front.learning', ['course' => $course->id, 'courseVideoId' => optional($firstVideo)->id]) : route('front.details', $course->slug) }}" class="font-semibold text-lg line-clamp-2 hover:underline">{{ $course->name }}</a>
                        <p class="text-sm text-gray-600">Trainer: {{ $course->trainer?->user?->name ?? 'Unknown' }}</p>
                        <p class="text-sm text-gray-600">{{ $course->mode->name ?? '' }} - {{ $course->level->name ?? '' }}</p>
                        @if($firstVideo)
                        <a href="{{ route('front.learning', ['course' => $course->id, 'courseVideoId' => $firstVideo->id]) }}" class="mt-2 inline-block bg-[#FF6129] text-white text-sm font-semibold px-4 py-2 rounded-lg hover:bg-[#e85520] transition-all">Mulai Belajar</a>
                        @else
                        <a href="{{ route('front.details', $course->slug) }}" class="mt-2 inline-block bg-gray-400 text-white text-sm font-semibold px-4 py-2 rounded-lg cursor-not-allowed opacity-50">Lihat Detail</a>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>

            @if(count($tasksToDo))
            <div class="mt-10">
                <h2 class="text-xl font-semibold mb-4">Pending Tasks</h2>
                <ul class="space-y-2">
                    @foreach($tasksToDo as $task)
                        <li class="bg-white p-4 rounded shadow flex justify-between items-center">
                            <span>{{ $task->module->course->name }} → {{ $task->name }}</span>
                            <a href="{{ route('task.submit.create', $task) }}" class="text-blue-600">Submit</a>
                        </li>
                    @endforeach
                </ul>
            </div>
            @endif
        </div>
    </div>

    <!-- Dropdown Script -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        $('#dropdownAvatar').on('click', function(e){
            e.stopPropagation();
            $('#dropdownMenu').toggleClass('hidden');
        });
        $(document).on('click', function(e){
            if(!$('#dropdownWrapper').is(e.target) && $('#dropdownWrapper').has(e.target).length === 0){
                $('#dropdownMenu').addClass('hidden');
            }
        });
    </script>
</body>
</html>
