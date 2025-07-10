{{-- Educational Minimal Header with Tailwind CSS --}}
<header class="bg-gradient-to-r from-blue-50 to-white border-b border-blue-100 sticky top-0 z-40 shadow-sm backdrop-blur-sm print:hidden">
    <div class="flex items-center justify-between px-6 py-4 min-h-[70px]">

        {{-- Left Section: Educational Brand --}}
        <div class="flex items-center">
            <div class="flex items-center space-x-3">
                {{-- Brand Icon --}}
                <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl shadow-lg transform hover:scale-105 transition-transform duration-200">
                    <i class="fas fa-graduation-cap text-white text-xl"></i>
                </div>

                {{-- Brand Content --}}
                <div class="flex flex-col">
                    @if(Auth::user()->hasRole(['talent_admin', 'talent', 'recruiter']))
                        <h1 class="text-xl font-bold text-gray-800 tracking-tight">Web Scouting</h1>
                        <div class="flex items-center space-x-1 text-sm text-blue-600">
                            <i class="fas fa-search text-xs"></i>
                            <span class="font-medium">Talent Platform</span>
                        </div>
                    @else
                        <h1 class="text-xl font-bold text-gray-800 tracking-tight">WebPelatihan</h1>
                        <div class="flex items-center space-x-1 text-sm text-blue-600">
                            <i class="fas fa-book-open text-xs"></i>
                            <span class="font-medium">Learning Platform</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Right Section: Educational Actions & Profile --}}
        <div class="flex items-center space-x-4">

            {{-- Educational Quick Actions (Desktop) --}}
            <div class="hidden lg:flex items-center space-x-2">
                {{-- Visit Learning Portal --}}
                <a href="{{ route('front.index') }}"
                   target="_blank"
                   class="flex items-center space-x-2 px-3 py-2 text-sm font-medium text-blue-600 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition-all duration-200 group"
                   title="Visit Learning Portal">
                    <i class="fas fa-external-link-alt text-xs group-hover:scale-110 transition-transform"></i>
                    <span class="hidden xl:inline">Learning Portal</span>
                </a>

                {{-- Quick Nav Separator --}}
                <div class="w-px h-6 bg-gray-300"></div>

                {{-- Dashboard Quick Access --}}
                @if(Auth::user()->hasRole(['talent_admin', 'talent', 'recruiter']))
                    <a href="{{ route('dashboard') }}"
                       class="flex items-center space-x-2 px-3 py-2 text-sm font-medium text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all duration-200 group"
                       title="My Dashboard">
                        <i class="fas fa-tachometer-alt text-xs group-hover:scale-110 transition-transform"></i>
                        <span class="hidden xl:inline">Dashboard</span>
                    </a>
                @endif
            </div>

            {{-- Educational User Profile Dropdown --}}
            <div class="relative" id="userProfileDropdown">
                <button class="flex items-center space-x-3 p-2 hover:bg-blue-50 rounded-xl transition-all duration-200 group"
                        type="button"
                        id="userDropdown"
                        onclick="toggleUserDropdown()"
                        aria-expanded="false"
                        title="My Account">

                    {{-- User Avatar with Status --}}
                    <div class="relative">
                        <img src="{{ Auth::user()->avatar_url }}"
                             alt="{{ Auth::user()->name }}"
                             class="w-10 h-10 rounded-full object-cover border-2 border-white shadow-md">
                        <div class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-green-400 border-2 border-white rounded-full" title="Online"></div>
                    </div>

                    {{-- User Information (Desktop) --}}
                    <div class="hidden md:block text-left">
                        <div class="text-sm font-semibold text-gray-800 leading-tight">{{ Str::limit(Auth::user()->name, 20) }}</div>
                        <div class="text-xs">
                            @php
                                $user = Auth::user();
                                $userRole = [
                                    'name' => 'Learner',
                                    'color' => 'gray',
                                    'icon' => 'fas fa-user'
                                ];

                                if ($user->roles_id == 1) {
                                    $userRole = ['name' => 'Administrator', 'color' => 'purple', 'icon' => 'fas fa-crown'];
                                } elseif ($user->roles_id == 2) {
                                    $userRole = ['name' => 'Instructor', 'color' => 'blue', 'icon' => 'fas fa-chalkboard-teacher'];
                                } elseif ($user->roles_id == 3) {
                                    $userRole = ['name' => 'Student', 'color' => 'green', 'icon' => 'fas fa-user-graduate'];
                                } elseif ($user->hasRole('talent_admin')) {
                                    $userRole = ['name' => 'Talent Admin', 'color' => 'yellow', 'icon' => 'fas fa-users-cog'];
                                } elseif ($user->hasRole('talent')) {
                                    $userRole = ['name' => 'Talent', 'color' => 'green', 'icon' => 'fas fa-star'];
                                } elseif ($user->hasRole('recruiter')) {
                                    $userRole = ['name' => 'Recruiter', 'color' => 'blue', 'icon' => 'fas fa-handshake'];
                                }
                            @endphp
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-{{ $userRole['color'] }}-100 text-{{ $userRole['color'] }}-800">
                                <i class="{{ $userRole['icon'] }} mr-1 text-xs"></i>
                                {{ $userRole['name'] }}
                            </span>
                        </div>
                    </div>

                    {{-- Dropdown Indicator --}}
                    <i class="fas fa-chevron-down text-xs text-gray-400 group-hover:text-blue-600 transition-colors hidden md:inline" id="dropdownChevron"></i>
                </button>

                {{-- Educational Dropdown Menu --}}
                <div class="absolute right-0 mt-2 w-80 bg-white rounded-2xl shadow-xl border border-gray-100 p-0 overflow-hidden z-50 hidden" id="userDropdownMenu">
                    {{-- Educational User Header --}}
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-4 text-white">
                        <div class="flex items-center space-x-3">
                            <div class="relative">
                                <img src="{{ Auth::user()->avatar_url }}"
                                     alt="{{ Auth::user()->name }}"
                                     class="w-12 h-12 rounded-full object-cover border-2 border-white shadow-md">
                                <div class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-green-400 border-2 border-white rounded-full"></div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="font-semibold text-white truncate">{{ Auth::user()->name }}</div>
                                <div class="text-blue-100 text-sm truncate">{{ Auth::user()->email }}</div>
                                <span class="inline-flex items-center px-2 py-1 mt-1 rounded-full text-xs font-medium bg-white/20 text-white">
                                    <i class="{{ $userRole['icon'] }} mr-1 text-xs"></i>
                                    {{ $userRole['name'] }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Educational Menu Items --}}
                    <div class="p-2">
                        {{-- My Learning Profile --}}
                        @php
                            $profileRoute = '#';
                            $profileTitle = 'My Profile';
                            $profileDesc = 'View and edit profile';

                            if (Auth::user()->roles_id == 1 || Auth::user()->roles_id == 2) {
                                $profileRoute = route('viewProfilePengajar', ['token' => encrypt(Auth::user()->id)]);
                                $profileTitle = 'Educator Profile';
                                $profileDesc = 'Manage teaching profile';
                            } elseif (Auth::user()->roles_id == 3) {
                                $profileRoute = route('viewProfileSiswa', ['token' => encrypt(Auth::user()->id)]);
                                $profileTitle = 'Student Profile';
                                $profileDesc = 'View learning progress';
                            } elseif (Auth::user()->hasRole(['talent_admin', 'talent', 'recruiter'])) {
                                $profileRoute = route('profile.edit');
                                $profileTitle = 'Professional Profile';
                                $profileDesc = 'Update career information';
                            }
                        @endphp

                        <a class="flex items-center p-3 rounded-xl hover:bg-blue-50 transition-colors group no-underline" href="{{ $profileRoute }}">
                            <div class="flex items-center justify-center w-10 h-10 bg-blue-100 rounded-lg group-hover:bg-blue-200 transition-colors">
                                <i class="fas fa-user-circle text-blue-600"></i>
                            </div>
                            <div class="ml-3 flex-1">
                                <div class="text-sm font-medium text-gray-800">{{ $profileTitle }}</div>
                                <div class="text-xs text-gray-500">{{ $profileDesc }}</div>
                            </div>
                            <i class="fas fa-chevron-right text-xs text-gray-400 group-hover:text-blue-600 transition-colors"></i>
                        </a>

                        {{-- Learning Progress (for Students) --}}
                        @if(Auth::user()->roles_id == 3)
                            <a class="flex items-center p-3 rounded-xl hover:bg-green-50 transition-colors group" href="#">
                                <div class="flex items-center justify-center w-10 h-10 bg-green-100 rounded-lg group-hover:bg-green-200 transition-colors">
                                    <i class="fas fa-chart-line text-green-600"></i>
                                </div>
                                <div class="ml-3 flex-1">
                                    <div class="text-sm font-medium text-gray-800">Learning Progress</div>
                                    <div class="text-xs text-gray-500">Track your achievements</div>
                                </div>
                                <i class="fas fa-chevron-right text-xs text-gray-400 group-hover:text-green-600 transition-colors"></i>
                            </a>
                        @endif

                        {{-- Quick Access (Mobile) --}}
                        <a class="flex items-center p-3 rounded-xl hover:bg-purple-50 transition-colors group lg:hidden" href="{{ route('front.index') }}" target="_blank">
                            <div class="flex items-center justify-center w-10 h-10 bg-purple-100 rounded-lg group-hover:bg-purple-200 transition-colors">
                                <i class="fas fa-globe text-purple-600"></i>
                            </div>
                            <div class="ml-3 flex-1">
                                <div class="text-sm font-medium text-gray-800">Learning Portal</div>
                                <div class="text-xs text-gray-500">Visit public website</div>
                            </div>
                            <i class="fas fa-external-link-alt text-xs text-gray-400 group-hover:text-purple-600 transition-colors"></i>
                        </a>
                    </div>

                    {{-- Educational Logout --}}
                    <div class="border-t border-gray-100 p-2">
                        <a class="flex items-center p-3 rounded-xl hover:bg-red-50 transition-colors group no-underline"
                           href="#"
                           data-bs-toggle="modal"
                           data-bs-target="#logoutModal">
                            <div class="flex items-center justify-center w-10 h-10 bg-red-100 rounded-lg group-hover:bg-red-200 transition-colors">
                                <i class="fas fa-sign-out-alt text-red-600"></i>
                            </div>
                            <div class="ml-3 flex-1">
                                <div class="text-sm font-medium text-gray-800">Sign Out</div>
                                <div class="text-xs text-gray-500">End your learning session</div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<script>
function toggleUserDropdown() {
    const dropdownMenu = document.getElementById('userDropdownMenu');
    const chevron = document.getElementById('dropdownChevron');

    if (dropdownMenu.classList.contains('hidden')) {
        dropdownMenu.classList.remove('hidden');
        if (chevron) chevron.classList.add('rotate-180');
    } else {
        dropdownMenu.classList.add('hidden');
        if (chevron) chevron.classList.remove('rotate-180');
    }
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    const dropdown = document.getElementById('userProfileDropdown');
    const dropdownMenu = document.getElementById('userDropdownMenu');

    if (!dropdown.contains(event.target)) {
        dropdownMenu.classList.add('hidden');
        const chevron = document.getElementById('dropdownChevron');
        if (chevron) chevron.classList.remove('rotate-180');
    }
});

// Prevent dropdown from closing when clicking inside it
document.getElementById('userDropdownMenu').addEventListener('click', function(event) {
    event.stopPropagation();
});
</script>

<style>
.rotate-180 {
    transform: rotate(180deg);
}
</style>
