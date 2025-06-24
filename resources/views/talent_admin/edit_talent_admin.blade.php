@extends('layout.template.mainTemplate')

@section('title', 'Edit Talent Admin')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endpush

@section('container')

<div class="min-h-screen bg-gray-50 p-6">
    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="max-w-2xl mx-auto mb-6">
            <div class="bg-green-50 border border-green-200 rounded-xl p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-green-600"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="max-w-2xl mx-auto mb-6">
            <div class="bg-red-50 border border-red-200 rounded-xl p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-red-600"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="max-w-2xl mx-auto mb-6">
            <div class="bg-red-50 border border-red-200 rounded-xl p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-red-600"></i>
                    </div>
                    <div class="ml-3">
                        <div class="text-sm font-medium text-red-800">
                            <p class="mb-2">Terdapat kesalahan pada form:</p>
                            <ul class="list-disc list-inside space-y-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">
                <i class="fas fa-user-edit text-yellow-600 mr-3"></i>
                Edit Talent Admin
            </h1>
            <p class="text-gray-600">Edit informasi akun talent admin: {{ $user->name }}</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('talent_admin.manage_talent_admins') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-xl font-semibold text-white hover:bg-gray-700 transition-all duration-200">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali
            </a>
        </div>
    </div>

    <!-- Edit Form -->
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100">
            <div class="bg-gradient-to-r from-yellow-600 to-yellow-700 rounded-t-2xl p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center mr-4">
                        <i class="fas fa-user-edit text-white text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-white">Edit Informasi Admin</h3>
                        <p class="text-yellow-100 text-sm">Perbarui informasi talent admin</p>
                    </div>
                </div>
            </div>

            <form action="{{ route('talent_admin.update_talent_admin', $user) }}" method="POST" enctype="multipart/form-data" class="p-8 space-y-6">
                @csrf
                @method('PUT')

                <!-- Current Admin Info -->
                <div class="bg-gray-50 rounded-xl p-4 mb-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            @if($user->avatar)
                                <img class="h-16 w-16 rounded-xl object-cover" src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}">
                            @else
                                <div class="h-16 w-16 bg-gradient-to-br from-yellow-500 to-yellow-700 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-user text-white text-xl"></i>
                                </div>
                            @endif
                        </div>
                        <div class="ml-4">
                            <h4 class="text-lg font-semibold text-gray-900">{{ $user->name }}</h4>
                            <p class="text-gray-600">{{ $user->email }}</p>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 mt-1">
                                <i class="fas fa-shield-alt mr-1"></i>
                                Talent Admin
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Avatar Upload -->
                <div class="text-center">
                    <div class="flex justify-center mb-4">
                        <div class="relative">
                            <div id="avatar-preview" class="w-24 h-24 rounded-xl overflow-hidden">
                                @if($user->avatar)
                                    <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="w-24 h-24 object-cover">
                                @else
                                    <div class="w-24 h-24 bg-gradient-to-br from-yellow-500 to-yellow-700 flex items-center justify-center">
                                        <i class="fas fa-user text-white text-2xl"></i>
                                    </div>
                                @endif
                            </div>
                            <label for="avatar" class="absolute bottom-0 right-0 w-8 h-8 bg-white border-2 border-yellow-600 rounded-full flex items-center justify-center cursor-pointer hover:bg-yellow-50 transition-colors duration-200">
                                <i class="fas fa-camera text-yellow-600 text-sm"></i>
                            </label>
                        </div>
                    </div>
                    <input type="file" id="avatar" name="avatar" accept="image/*" class="hidden" onchange="previewAvatar(this)">
                    <p class="text-sm text-gray-600">Klik ikon kamera untuk mengubah foto profil</p>
                    @error('avatar')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-user mr-2 text-yellow-600"></i>
                        Nama Lengkap *
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition-colors duration-200"
                           placeholder="Masukkan nama lengkap admin">
                    @error('name')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-envelope mr-2 text-yellow-600"></i>
                        Email *
                    </label>
                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition-colors duration-200"
                           placeholder="admin@example.com">
                    @error('email')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password (Optional) -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-lock mr-2 text-yellow-600"></i>
                        Password Baru (Opsional)
                    </label>
                    <div class="relative">
                        <input type="password" name="password" id="password"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition-colors duration-200 pr-12"
                               placeholder="Kosongkan jika tidak ingin mengubah password">
                        <button type="button" onclick="togglePassword('password')" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <i class="fas fa-eye" id="password-eye"></i>
                        </button>
                    </div>
                    <p class="text-sm text-gray-500 mt-1">Minimal 8 karakter. Kosongkan jika tidak ingin mengubah password.</p>
                    @error('password')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirm Password (Optional) -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-lock mr-2 text-yellow-600"></i>
                        Konfirmasi Password Baru
                    </label>
                    <div class="relative">
                        <input type="password" name="password_confirmation" id="password_confirmation"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition-colors duration-200 pr-12"
                               placeholder="Ulangi password baru">
                        <button type="button" onclick="togglePassword('password_confirmation')" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <i class="fas fa-eye" id="password_confirmation-eye"></i>
                        </button>
                    </div>
                    @error('password_confirmation')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Account Info -->
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-info-circle text-blue-600"></i>
                        </div>
                        <div class="ml-3">
                            <h4 class="text-sm font-medium text-blue-800">Informasi Akun</h4>
                            <div class="mt-2 text-sm text-blue-700 space-y-1">
                                <p><strong>Tanggal Bergabung:</strong> {{ $user->created_at->format('d M Y H:i') }}</p>
                                <p><strong>Terakhir Diperbarui:</strong> {{ $user->updated_at->format('d M Y H:i') }}</p>
                                @if($user->last_login_at)
                                    <p><strong>Login Terakhir:</strong> {{ $user->last_login_at->format('d M Y H:i') }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                    <a href="{{ route('talent_admin.manage_talent_admins') }}" class="px-6 py-3 bg-gray-200 text-gray-800 rounded-xl hover:bg-gray-300 transition-colors duration-200 font-medium">
                        <i class="fas fa-times mr-2"></i>
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-3 bg-yellow-600 text-white rounded-xl hover:bg-yellow-700 transition-colors duration-200 font-medium">
                        <i class="fas fa-save mr-2"></i>
                        Perbarui Admin
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Preview avatar
function previewAvatar(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('avatar-preview').innerHTML =
                `<img src="${e.target.result}" alt="Avatar Preview" class="w-24 h-24 rounded-xl object-cover">`;
        }
        reader.readAsDataURL(input.files[0]);
    }
}

// Toggle password visibility
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const eye = document.getElementById(fieldId + '-eye');

    if (field.type === 'password') {
        field.type = 'text';
        eye.classList.remove('fa-eye');
        eye.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        eye.classList.remove('fa-eye-slash');
        eye.classList.add('fa-eye');
    }
}

// Password validation
document.getElementById('password_confirmation').addEventListener('input', function() {
    const password = document.getElementById('password').value;
    const confirmPassword = this.value;

    if (password !== confirmPassword && confirmPassword !== '') {
        this.setCustomValidity('Password tidak cocok');
        this.classList.add('border-red-500');
        this.classList.remove('border-gray-300');
    } else {
        this.setCustomValidity('');
        this.classList.remove('border-red-500');
        this.classList.add('border-gray-300');
    }
});

// Clear password confirmation when password is changed
document.getElementById('password').addEventListener('input', function() {
    document.getElementById('password_confirmation').value = '';
});
</script>

@endsection
