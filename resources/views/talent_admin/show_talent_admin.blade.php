@extends('layout.template.mainTemplate')

@section('title', 'Detail Talent Admin')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@section('container')

<div class="min-h-screen bg-gray-50 p-6">
    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="mb-6">
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
        <div class="mb-6">
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
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">
                <i class="fas fa-user-shield text-blue-600 mr-3"></i>
                Detail Talent Admin
            </h1>
            <p class="text-gray-600">Informasi lengkap dan statistik untuk {{ $user->name }}</p>
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
            <a href="{{ route('talent_admin.edit_talent_admin', $user) }}" class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-xl font-semibold text-white hover:bg-yellow-700 transition-all duration-200">
                <i class="fas fa-edit mr-2"></i>
                Edit Admin
            </a>
            <a href="{{ route('talent_admin.manage_talent_admins') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-xl font-semibold text-white hover:bg-gray-700 transition-all duration-200">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Admin Profile Card -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 p-6">
                    <div class="text-center">
                        <div class="mx-auto mb-4">
                            @if($user->avatar)
                                <img class="h-24 w-24 rounded-2xl object-cover mx-auto border-4 border-white shadow-lg" src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}">
                            @else
                                <div class="h-24 w-24 bg-white bg-opacity-20 rounded-2xl flex items-center justify-center mx-auto border-4 border-white shadow-lg">
                                    <i class="fas fa-user text-white text-3xl"></i>
                                </div>
                            @endif
                        </div>
                        <h3 class="text-xl font-semibold text-white mb-1">{{ $user->name }}</h3>
                        <p class="text-blue-100 text-sm mb-3">{{ $user->email }}</p>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-white bg-opacity-20 text-white">
                            <i class="fas fa-shield-alt mr-1"></i>
                            Talent Admin
                        </span>
                    </div>
                </div>

                <div class="p-6 space-y-4">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">ID Admin</span>
                            <span class="text-sm font-medium text-gray-900">#{{ $user->id }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Tanggal Bergabung</span>
                            <span class="text-sm font-medium text-gray-900">{{ $stats['join_date'] }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Login Terakhir</span>
                            <span class="text-sm font-medium text-gray-900">{{ $stats['last_login'] }}</span>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-gray-200">
                        <div class="flex space-x-2">
                            <a href="{{ route('talent_admin.edit_talent_admin', $user) }}" class="flex-1 bg-yellow-600 text-white px-3 py-2 rounded-xl text-center hover:bg-yellow-700 transition-colors duration-200 text-sm font-medium">
                                <i class="fas fa-edit mr-1"></i>
                                Edit
                            </a>
                            @if($user->id !== $authUser->id)
                                <button onclick="confirmDelete()" class="flex-1 bg-red-600 text-white px-3 py-2 rounded-xl text-center hover:bg-red-700 transition-colors duration-200 text-sm font-medium">
                                    <i class="fas fa-trash mr-1"></i>
                                    Hapus
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics and Activities -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-tasks text-green-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <div class="text-2xl font-bold text-gray-900">{{ $stats['requests_handled'] }}</div>
                            <div class="text-gray-600 text-sm">Permintaan Diproses</div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-users text-blue-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <div class="text-2xl font-bold text-gray-900">{{ $stats['talents_managed'] }}</div>
                            <div class="text-gray-600 text-sm">Total Talent</div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-building text-purple-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <div class="text-2xl font-bold text-gray-900">{{ $stats['recruiters_managed'] }}</div>
                            <div class="text-gray-600 text-sm">Total Recruiter</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Account Information -->
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100">
                <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 rounded-t-2xl p-6">
                    <h3 class="text-lg font-semibold text-white">
                        <i class="fas fa-info-circle mr-2"></i>
                        Informasi Akun
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                                <div class="px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-900">
                                    {{ $user->name }}
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                <div class="px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-900">
                                    {{ $user->email }}
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                                <div class="px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <i class="fas fa-shield-alt mr-1"></i>
                                        Talent Admin
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Bergabung</label>
                                <div class="px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-900">
                                    {{ $user->created_at->format('d M Y H:i') }}
                                    <div class="text-xs text-gray-500 mt-1">{{ $user->created_at->diffForHumans() }}</div>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Terakhir Diperbarui</label>
                                <div class="px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-900">
                                    {{ $user->updated_at->format('d M Y H:i') }}
                                    <div class="text-xs text-gray-500 mt-1">{{ $user->updated_at->diffForHumans() }}</div>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Login Terakhir</label>
                                <div class="px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-900">
                                    {{ $stats['last_login'] }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Admin Permissions -->
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100">
                <div class="bg-gradient-to-r from-green-600 to-green-700 rounded-t-2xl p-6">
                    <h3 class="text-lg font-semibold text-white">
                        <i class="fas fa-key mr-2"></i>
                        Hak Akses Admin
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex items-center p-3 bg-green-50 rounded-xl">
                            <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-users text-green-600"></i>
                            </div>
                            <div>
                                <div class="font-medium text-gray-900">Kelola Talent</div>
                                <div class="text-sm text-gray-600">Mengelola profil dan status talent</div>
                            </div>
                        </div>
                        <div class="flex items-center p-3 bg-blue-50 rounded-xl">
                            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-building text-blue-600"></i>
                            </div>
                            <div>
                                <div class="font-medium text-gray-900">Kelola Recruiter</div>
                                <div class="text-sm text-gray-600">Mengelola akun recruiter</div>
                            </div>
                        </div>
                        <div class="flex items-center p-3 bg-purple-50 rounded-xl">
                            <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-handshake text-purple-600"></i>
                            </div>
                            <div>
                                <div class="font-medium text-gray-900">Kelola Permintaan</div>
                                <div class="text-sm text-gray-600">Meninjau dan menyetujui permintaan</div>
                            </div>
                        </div>
                        <div class="flex items-center p-3 bg-yellow-50 rounded-xl">
                            <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-chart-bar text-yellow-600"></i>
                            </div>
                            <div>
                                <div class="font-medium text-gray-900">Analytics</div>
                                <div class="text-sm text-gray-600">Akses penuh ke analytics dan laporan</div>
                            </div>
                        </div>
                        <div class="flex items-center p-3 bg-red-50 rounded-xl">
                            <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-user-shield text-red-600"></i>
                            </div>
                            <div>
                                <div class="font-medium text-gray-900">Kelola Admin</div>
                                <div class="text-sm text-gray-600">Mengelola akun talent admin</div>
                            </div>
                        </div>
                        <div class="flex items-center p-3 bg-indigo-50 rounded-xl">
                            <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-cogs text-indigo-600"></i>
                            </div>
                            <div>
                                <div class="font-medium text-gray-900">Pengaturan Sistem</div>
                                <div class="text-sm text-gray-600">Akses ke pengaturan platform</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete() {
    Swal.fire({
        title: 'Hapus Talent Admin?',
        text: `Apakah Anda yakin ingin menghapus admin "{{ $user->name }}"? Tindakan ini tidak dapat dibatalkan.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // Create and submit form
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route('talent_admin.destroy_talent_admin', $user) }}';

            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';

            const tokenInput = document.createElement('input');
            tokenInput.type = 'hidden';
            tokenInput.name = '_token';
            tokenInput.value = '{{ csrf_token() }}';

            form.appendChild(methodInput);
            form.appendChild(tokenInput);
            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>

@if (session('success'))
    <script>
        Swal.fire({
            title: 'Berhasil!',
            text: '{{ session('success') }}',
            icon: 'success',
            confirmButtonColor: '#3b82f6'
        });
    </script>
@endif

@if (session('error'))
    <script>
        Swal.fire({
            title: 'Kesalahan!',
            text: '{{ session('error') }}',
            icon: 'error',
            confirmButtonColor: '#3b82f6'
        });
    </script>
@endif

@endsection
