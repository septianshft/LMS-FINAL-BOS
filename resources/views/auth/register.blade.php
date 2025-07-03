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
                                <path d="M12 14l9-5-9-5-9 5 9 5z"/>
                                <path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                            </svg>
                        </div>
                    </div>

                    <!-- Welcome Content -->
                    <h1 class="text-4xl font-bold mb-4">Bergabunglah dengan Komunitas Kami!</h1>
                    <p class="text-lg text-white text-opacity-90 mb-8">
                        Buat akun Anda dan mulai perjalanan dengan platform pelatihan komprehensif kami. Terhubung, belajar, dan berkembang bersama profesional di seluruh dunia.
                    </p>

                    <!-- Features -->
                    <div class="space-y-4 text-left">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <span>Pilih jalur pembelajaran Anda</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <span>Tampilkan bakat Anda</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <span>Terhubung dengan perekrut</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side - Registration Form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-8 bg-gray-50 slide-in-right overflow-y-auto relative">
            <!-- Home Button -->
            <div class="absolute top-6 right-6 z-10">
                <a href="{{ url('/') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-xl shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Beranda
                </a>
            </div>

            <div class="w-full max-w-md">
                <!-- Mobile Logo (visible on small screens) -->
                <div class="lg:hidden text-center mb-8">
                    <div class="w-16 h-16 mx-auto bg-gradient-to-r from-purple-600 to-blue-600 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 14l9-5-9-5-9 5 9 5z"/>
                            <path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                        </svg>
                    </div>
                </div>

                <!-- Header -->
                <div class="text-center mb-8">
                    <h2 class="text-3xl font-bold text-gray-900 mb-2">Buat Akun</h2>
                    <p class="text-gray-600">Bergabunglah dengan platform pelatihan kami hari ini</p>
                </div>

                <!-- Registration Form -->
                <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <!-- Name -->
                    <div class="space-y-2">
                        <x-input-label for="name" :value="__('Nama Lengkap')" class="text-sm font-semibold text-gray-700" />
                        <x-text-input id="name"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition duration-200 bg-white"
                            type="text"
                            name="name"
                            :value="old('name')"
                            placeholder="Masukkan nama lengkap Anda"
                            required autofocus autocomplete="name" />
                        <x-input-error :messages="$errors->get('name')" class="mt-1" />
                    </div>

                    <!-- Email Address -->
                    <div class="space-y-2">
                        <x-input-label for="email" :value="__('Alamat Email')" class="text-sm font-semibold text-gray-700" />
                        <x-text-input id="email"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition duration-200 bg-white"
                            type="email"
                            name="email"
                            :value="old('email')"
                            placeholder="email.anda@contoh.com"
                            required autocomplete="email" />
                        <x-input-error :messages="$errors->get('email')" class="mt-1" />
                    </div>

                    <!-- Pekerjaan -->
                    <div class="space-y-2">
                        <x-input-label for="pekerjaan" :value="__('Pekerjaan')" class="text-sm font-semibold text-gray-700" />
                        <x-text-input id="pekerjaan"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition duration-200 bg-white"
                            type="text"
                            name="pekerjaan"
                            :value="old('pekerjaan')"
                            placeholder="Pekerjaan Anda saat ini"
                            required />
                        <x-input-error :messages="$errors->get('pekerjaan')" class="mt-1" />
                    </div>

                    <!-- Avatar -->
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <x-input-label for="avatar" :value="__('Foto Profil')" class="text-sm font-semibold text-gray-700" />
                            <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-md">Opsional</span>
                        </div>
                        <div class="flex items-center justify-center w-full">
                            <label for="avatar" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-xl cursor-pointer bg-gray-50 hover:bg-gray-100 transition duration-200">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <svg class="w-8 h-8 mb-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                    </svg>
                                    <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">Klik untuk mengunggah</span> atau lewati untuk sekarang</p>
                                    <p class="text-xs text-gray-500">PNG, JPG atau JPEG (MAKS. 2MB)</p>
                                </div>
                                <input id="avatar" type="file" name="avatar" class="hidden" accept="image/*" />
                            </label>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">
                            Anda dapat menambahkan foto profil sekarang atau mengunggahnya nanti dari pengaturan profil.
                        </p>
                        <x-input-error :messages="$errors->get('avatar')" class="mt-1" />
                    </div>

                    <!-- Role Selection -->
                    <div class="space-y-2">
                        <x-input-label for="role" :value="__('Saya ingin bergabung sebagai')" class="text-sm font-semibold text-gray-700" />
                        <select id="role" name="role"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition duration-200 bg-white"
                            required>
                            <option value="">{{ __('Pilih peran Anda...') }}</option>
                            <option value="trainee" {{ old('role') == 'trainee' ? 'selected' : '' }}>
                                {{ __('🎓 Peserta Pelatihan - Belajar dari kursus') }}
                            </option>
                            {{-- <option value="talent" {{ old('role') == 'talent' ? 'selected' : '' }}>
                                {{ __('⭐ Talenta - Tampilkan keahlian Anda') }}
                            </option> --}}
                            <option value="recruiter" {{ old('role') == 'recruiter' ? 'selected' : '' }}>
                                {{ __('👔 Perekrut - Temukan individu berbakat') }}
                            </option>
                        </select>
                        <x-input-error :messages="$errors->get('role')" class="mt-1" />
                    </div>

                    <!-- Password -->
                    <div class="space-y-2">
                        <x-input-label for="password" :value="__('Kata Sandi')" class="text-sm font-semibold text-gray-700" />
                        <div class="relative">
                            <x-text-input id="password"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition duration-200 bg-white pr-12"
                                type="password"
                                name="password"
                                placeholder="Buat kata sandi yang kuat"
                                required autocomplete="new-password" />
                            <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 flex items-center px-4 text-gray-400 hover:text-gray-600 transition duration-200">
                                <svg id="eyeIcon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </button>
                        </div>
                        <div class="text-xs text-gray-500 mt-1">
                            Kata sandi harus minimal 8 karakter
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-1" />
                    </div>

                    <!-- Confirm Password -->
                    <div class="space-y-2">
                        <x-input-label for="password_confirmation" :value="__('Konfirmasi Kata Sandi')" class="text-sm font-semibold text-gray-700" />
                        <div class="relative">
                            <x-text-input id="password_confirmation"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition duration-200 bg-white pr-12"
                                type="password"
                                name="password_confirmation"
                                placeholder="Konfirmasi kata sandi Anda"
                                required autocomplete="new-password" />
                            <button type="button" id="togglePasswordConfirmation" class="absolute inset-y-0 right-0 flex items-center px-4 text-gray-400 hover:text-gray-600 transition duration-200">
                                <svg id="eyeIconConfirmation" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
                    </div>

                    <!-- Submit Button -->
                    <div>
                        <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-semibold text-white bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition duration-200 transform hover:scale-[1.02]">
                            {{ __('Buat Akun') }}
                        </button>
                    </div>

                    <!-- Divider -->
                    <div class="relative my-6">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-4 bg-gray-50 text-gray-500">Sudah punya akun?</span>
                        </div>
                    </div>

                    <!-- Login Link -->
                    <div class="text-center">
                        <a href="{{ route('login') }}" class="w-full inline-flex justify-center py-3 px-4 border border-purple-300 rounded-xl shadow-sm text-sm font-semibold text-purple-600 bg-white hover:bg-purple-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition duration-200 transform hover:scale-[1.02]">
                            {{ __('Masuk Sekarang') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
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

        // Password confirmation toggle functionality
        document.getElementById('togglePasswordConfirmation').addEventListener('click', function() {
            const passwordField = document.getElementById('password_confirmation');
            const eyeIcon = document.getElementById('eyeIconConfirmation');

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

        // File upload preview
        document.getElementById('avatar').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.createElement('img');
                    preview.src = e.target.result;
                    preview.className = 'w-16 h-16 rounded-full object-cover mx-auto mb-2';

                    const label = document.querySelector('label[for="avatar"]');
                    const existingPreview = label.querySelector('img');
                    if (existingPreview) {
                        existingPreview.remove();
                    }
                    label.insertBefore(preview, label.firstChild);
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
</x-guest-layout>
