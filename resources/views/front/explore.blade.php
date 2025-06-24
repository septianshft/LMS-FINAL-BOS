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
        <nav class="flex justify-between items-center py-6 px-[50px]">
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
                <div class="absolute right-0 mt-2 bg-white border rounded shadow hidden" id="dropdownMenu">
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

        <!-- Filter + Konten -->
        <div class="flex gap-6 mt-6">
            <!-- Sidebar Filter -->
            <aside class="w-1/4">
                <form id="filterForm" method="GET" class="flex flex-col gap-4">
                    <input type="text" name="search" placeholder="Search" value="{{ request('search') }}" class="border rounded p-2">
                    <select name="category_id" class="border rounded p-2">
                        <option value="">All Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id')==$category->id?'selected':'' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    <select name="course_mode_id" class="border rounded p-2">
                        <option value="">All Modes</option>
                        @foreach($modes as $mode)
                            <option value="{{ $mode->id }}" {{ request('course_mode_id')==$mode->id?'selected':'' }}>{{ $mode->name }}</option>
                        @endforeach
                    </select>
                    <select name="course_level_id" class="border rounded p-2">
                        <option value="">All Levels</option>
                        @foreach($levels as $level)
                            <option value="{{ $level->id }}" {{ request('course_level_id')==$level->id?'selected':'' }}>{{ $level->name }}</option>
                        @endforeach
                    </select>
                    <button class="px-4 py-2 bg-[#FF6129] text-white rounded">Filter</button>
                </form>
            </aside>

            <!-- Course List -->
            <div class="flex-1">
                <div id="courseContent" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                    @foreach($courses as $course)
                    <div class="flex flex-col rounded-xl bg-white overflow-hidden transition-all hover:ring-2 hover:ring-[#FF6129]">
                        <a href="{{ route('front.details', $course->slug) }}" class="thumbnail w-full h-[200px] shrink-0 rounded-[10px] overflow-hidden">
                        <img src="{{ asset(path: 'storage/' . $course->thumbnail) }}" alt="thumbnail" class="w-full h-full object-cover">                        </a>
                        <div class="p-4 flex flex-col gap-2">
                            <a href="{{ route('front.details', $course->slug) }}" class="font-semibold text-lg line-clamp-2 hover:underline">{{ $course->name }}</a>
                            <p class="text-sm text-gray-600">Trainer: {{ $course->trainer?->user?->name ?? 'Unknown' }}</p>
                            <p class="text-sm text-gray-600">{{ $course->mode->name ?? '' }} - {{ $course->level->name ?? '' }}</p>
                            @if($course->enrollment_start || $course->enrollment_end)
                                <p class="text-xs text-gray-500">
                                    Enrollment:
                                    {{ $course->enrollment_start ? $course->enrollment_start->format('d M Y') : '-' }}
                                    -
                                    {{ $course->enrollment_end ? $course->enrollment_end->format('d M Y') : '-' }}
                                </p>
                            @endif
                            <p class="font-semibold">{{ $course->price > 0 ? 'Rp ' . number_format($course->price, 0, ',', '.') : 'FREE' }}</p>
                            <a href="{{ route('front.details', $course->slug) }}" class="mt-2 inline-block bg-[#FF6129] text-white text-sm font-semibold px-4 py-2 rounded-lg hover:bg-[#e85520] transition-all">Lihat Detail</a>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Script -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        $(document).ready(function(){
            $('#filterForm').on('submit', function(e){
                e.preventDefault();
                $.ajax({
                    url: '{{ route('courses.index') }}',
                    type: 'GET',
                    data: $(this).serialize(),
                    success: function(data){
                        const html = $(data).find('#courseContent').html();
                        $('#courseContent').html(html);
                    }
                });
            });

            $('#dropdownAvatar').on('click', function(e){
                e.stopPropagation();
                $('#dropdownMenu').toggleClass('hidden');
            });
            $(document).on('click', function(e){
                if(!$('#dropdownWrapper').is(e.target) && $('#dropdownWrapper').has(e.target).length === 0){
                    $('#dropdownMenu').addC
