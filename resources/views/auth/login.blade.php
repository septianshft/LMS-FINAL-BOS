<x-guest-layout>
    <div class="min-h-screen flex">
        <!-- Left Side - Welcome Section -->
        <div class="hidden lg:flex lg:w-1/2 gradient-bg relative overflow-hidden">
            <div class="floating-shapes"></div>
            <div class="relative z-10 flex flex-col justify-center items-center text-white p-12 slide-in-left">
                <div class="max-w-md text-center">
                    <!-- Logo/Icon -->
                    <div class="mb-8">
                        <div class="w-20 h-20 mx-auto bg-white bg-opacity-20 rounded-full flex items-center justify-center backdrop-blur-sm">
                            <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                            </svg>
                        </div>
                    </div>

                    <!-- Dynamic Content Based on Platform -->
                    <div id="lmsContent">
                        <h1 class="text-4xl font-bold mb-4">Selamat Datang Kembali!</h1>
                        <p class="text-lg text-white text-opacity-90 mb-8">
                            Masuk untuk melanjutkan perjalanan belajar Anda dan mengakses dashboard personal dengan kursus, pelacakan kemajuan, dan peluang karir.
                        </p>

                        <!-- LMS Features -->
                        <div class="space-y-4 text-left">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <span>Akses kursus Anda</span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <span>Lacak kemajuan Anda</span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <span>Terhubung dengan peluang</span>
                            </div>
                        </div>
                    </div>

                    <div id="talentContent" class="hidden">
                        <h1 class="text-4xl font-bold mb-4">Talent Platform</h1>
                        <p class="text-lg text-white text-opacity-90 mb-8">
                            Access the talent scouting platform to discover opportunities or find the perfect talent for your projects.
                        </p>

                        <!-- Talent Features -->
                        <div class="space-y-4 text-left">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <span>Discover talent opportunities</span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <span>Connect with recruiters</span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <span>Manage talent requests</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side - Login Form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-8 bg-gray-50 slide-in-right relative">
            <!-- Home Button -->
            <div class="absolute top-6 right-6">
                <a href="{{ url('/') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-xl shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Beranda
                </a>
            </div>

            <div class="w-full max-w-md">
                <!-- Platform Toggle -->
                <div class="mb-6">
                    <div class="bg-white rounded-xl p-1 shadow-sm border border-gray-200">
                        <div class="flex">
                            <button type="button" id="lmsToggle" onclick="switchPlatform('lms')"
                                    class="flex-1 py-2 px-4 text-sm font-medium rounded-lg transition-all duration-200 bg-purple-100 text-purple-700">
                                <i class="fas fa-graduation-cap mr-2"></i>
                                Learning Platform
                            </button>
                            <button type="button" id="talentToggle" onclick="switchPlatform('talent')"
                                    class="flex-1 py-2 px-4 text-sm font-medium rounded-lg transition-all duration-200 text-gray-500 hover:text-gray-700">
                                <i class="fas fa-users-cog mr-2"></i>
                                Talent Platform
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Mobile Logo (visible on small screens) -->
                <div class="lg:hidden text-center mb-8">
                    <div class="w-16 h-16 mx-auto bg-gradient-to-r from-purple-600 to-blue-600 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                    </div>
                </div>

                <!-- Header -->
                <div class="text-center mb-8">
                    <h2 class="text-3xl font-bold text-gray-900 mb-2">Masuk</h2>
                    <p id="platformDescription" class="text-gray-600">Selamat datang kembali di platform kami</p>
                </div>

                <!-- Login Form -->
                <form method="POST" action="{{ route('login') }}" id="loginForm" class="space-y-6">
                    @csrf
                    <input type="hidden" name="platform" id="platformInput" value="lms">

                    <!-- Email -->
                    <div class="space-y-2">
                        <x-input-label for="email" :value="__('Alamat Email')" class="text-sm font-semibold text-gray-700" />
                        <x-text-input id="email"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition duration-200 bg-white"
                            type="email"
                            name="email"
                            :value="old('email')"
                            placeholder="email.anda@contoh.com"
                            required autofocus autocomplete="username" />
                        <x-input-error :messages="$errors->get('email')" class="mt-1" />
                    </div>

                    <!-- Password -->
                    <div class="space-y-2">
                        <x-input-label for="password" :value="__('Kata Sandi')" class="text-sm font-semibold text-gray-700" />
                        <div class="relative">
                            <x-text-input id="password"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition duration-200 bg-white pr-12"
                                type="password"
                                name="password"
                                placeholder="Masukkan kata sandi Anda"
                                required autocomplete="current-password" />
                            <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 flex items-center px-4 text-gray-400 hover:text-gray-600 transition duration-200">
                                <svg id="eyeIcon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-1" />
                    </div>

                    <!-- Remember Me & Forgot Password -->
                    <div class="flex items-center justify-between">
                        <label class="flex items-center text-sm">
                            <input type="checkbox" name="remember" class="rounded border-gray-300 text-purple-600 shadow-sm focus:ring-purple-500">
                            <span class="ml-2 text-sm text-gray-600">{{ __('Ingat saya') }}</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a class="text-sm text-purple-600 hover:text-purple-500 transition duration-200" href="{{ route('password.request') }}">
                                {{ __('Lupa kata sandi?') }}
                            </a>
                        @endif
                    </div>

                    <!-- Submit Button -->
                    <div>
                        <button type="submit" id="submitButton" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-semibold text-white bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition duration-200 transform hover:scale-[1.02]">
                            <i class="fas fa-graduation-cap mr-2"></i>
                            {{ __('Masuk ke Platform Pembelajaran') }}
                        </button>
                    </div>

                    <!-- Divider -->
                    <div class="relative my-6">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-4 bg-gray-50 text-gray-500">Belum punya akun?</span>
                        </div>
                    </div>

                    <!-- Register Link -->
                    <div class="text-center">
                        <a href="{{ route('register') }}" class="w-full inline-flex justify-center py-3 px-4 border border-purple-300 rounded-xl shadow-sm text-sm font-semibold text-purple-600 bg-white hover:bg-purple-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition duration-200 transform hover:scale-[1.02]">
                            {{ __('Buat Akun') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Platform switching functionality
        function switchPlatform(platform) {
            const lmsToggle = document.getElementById('lmsToggle');
            const talentToggle = document.getElementById('talentToggle');
            const lmsContent = document.getElementById('lmsContent');
            const talentContent = document.getElementById('talentContent');
            const platformInput = document.getElementById('platformInput');
            const platformDescription = document.getElementById('platformDescription');
            const submitButton = document.getElementById('submitButton');

            if (platform === 'lms') {
                // Update toggle buttons
                lmsToggle.className = 'flex-1 py-2 px-4 text-sm font-medium rounded-lg transition-all duration-200 bg-purple-100 text-purple-700';
                talentToggle.className = 'flex-1 py-2 px-4 text-sm font-medium rounded-lg transition-all duration-200 text-gray-500 hover:text-gray-700';

                // Update content
                lmsContent.classList.remove('hidden');
                talentContent.classList.add('hidden');

                // Update form
                platformInput.value = 'lms';
                platformDescription.textContent = 'Masukkan kredensial Anda untuk mengakses akun pembelajaran';
                submitButton.innerHTML = '<i class="fas fa-graduation-cap mr-2"></i>Masuk ke Platform Pembelajaran';

            } else {
                // Update toggle buttons
                talentToggle.className = 'flex-1 py-2 px-4 text-sm font-medium rounded-lg transition-all duration-200 bg-blue-100 text-blue-700';
                lmsToggle.className = 'flex-1 py-2 px-4 text-sm font-medium rounded-lg transition-all duration-200 text-gray-500 hover:text-gray-700';

                // Update content
                talentContent.classList.remove('hidden');
                lmsContent.classList.add('hidden');

                // Update form
                platformInput.value = 'talent';
                platformDescription.textContent = 'Masukkan kredensial Anda untuk mengakses platform talenta';
                submitButton.innerHTML = '<i class="fas fa-users-cog mr-2"></i>Masuk ke Platform Talenta';
            }
        }

        // Password toggle functionality
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordField = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');

            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                eyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L12 12m0 0l3.878 3.878M12 12l-3.878-3.878"></path>
                `;
            } else {
                passwordField.type = 'password';
                eyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                `;
            }
        });
    </script>
</x-guest-layout>
