<nav class="flex justify-between items-center pt-6 px-[50px]">
    <div class="flex items-center gap-3">
        <img src="{{ asset('assets/logo/logo.png') }}" alt="logo" class="w-[50px] h-[50px] object-contain">
        <div class="leading-tight text-white">
            <p class="font-semibold text-xs text-white">Pusat Unggulan IPTEK Perguruan Tinggi</p>
            <h1 class="font-bold text-sm text-white">Intelligent Sensing-IoT</h1>
        </div>
    </div>
    <ul class="flex items-center gap-[30px] text-white">
        <li><a href="{{ route('front.index') }}" class="font-semibold">Home</a></li>
        <li><a href="" class="font-semibold">My Certificate</a></li>
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
        <div class="flex flex-col items-end justify-center">
            <p class="font-semibold text-white">Hi, {{ Auth::user()->name }}</p>
            @if(Auth::user()->hasActiveSubscription())
            <p class="p-[2px_10px] rounded-full bg-[#FF6129] font-semibold text-xs text-white text-center">PRO</p>
            @endif
        </div>
        <div class="w-[56px] h-[56px] overflow-hidden rounded-full flex shrink-0 cursor-pointer" id="dropdownAvatar">
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
        <a href="{{ route('register') }}" class="text-white font-semibold rounded-[30px] p-[16px_32px] ring-1 ring-white transition-all duration-300 hover:ring-2 hover:ring-[#FF6129]">Register</a>
        <a href="{{ route('login') }}" class="text-white font-semibold rounded-[30px] p-[16px_32px] bg-[#FF6129] transition-all duration-300 hover:shadow-[0_10px_20px_0_#FF612980]">Login In</a>
    </div>
    @endguest
</nav>
