@extends('layout.template.mainTemplate')

@section('title', 'Kelola Talent Admin')

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
                <i class="fas fa-user-shield text-red-600 mr-3"></i>
                Kelola Talent Admin
            </h1>
            <p class="text-gray-600">Kelola akun talent admin, lihat detail, dan kelola izin admin.</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('talent_admin.create_talent_admin') }}" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-xl font-semibold text-white hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all duration-200">
                <i class="fas fa-plus mr-2"></i>
                Tambah Admin Baru
            </a>
        </div>
    </div>

    <!-- Admin List -->
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100">
        <div class="bg-gradient-to-r from-red-600 to-red-700 rounded-t-2xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-white">Daftar Talent Admin</h3>
                    <p class="text-red-100 text-sm">Kelola semua akun talent admin</p>
                </div>
                <div class="text-white">
                    <i class="fas fa-user-shield text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="p-6">
            @if($talentAdmins->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Admin
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Email
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tanggal Bergabung
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Login Terakhir
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($talentAdmins as $admin)
                                <tr class="hover:bg-gray-50 transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-12 w-12">
                                                @if($admin->avatar)
                                                    <img class="h-12 w-12 rounded-xl object-cover" src="{{ asset('storage/' . $admin->avatar) }}" alt="{{ $admin->name }}">
                                                @else
                                                    <div class="h-12 w-12 bg-gradient-to-br from-red-500 to-red-700 rounded-xl flex items-center justify-center">
                                                        <i class="fas fa-user text-white"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $admin->name }}</div>
                                                <div class="text-sm text-gray-500">ID: {{ $admin->id }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $admin->email }}</div>
                                        <div class="text-sm text-gray-500">
                                            <i class="fas fa-shield-alt mr-1"></i>
                                            Talent Admin
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $admin->created_at->format('d M Y') }}
                                        <div class="text-xs text-gray-500">{{ $admin->created_at->diffForHumans() }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        @if($admin->last_login_at)
                                            {{ $admin->last_login_at->format('d M Y H:i') }}
                                            <div class="text-xs text-gray-500">{{ $admin->last_login_at->diffForHumans() }}</div>
                                        @else
                                            <span class="text-gray-400">Belum pernah login</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex items-center space-x-2">
                                            <!-- View Details Button -->
                                            <button onclick="viewAdminDetails({{ $admin->id }})" class="inline-flex items-center px-3 py-1 bg-purple-100 text-purple-700 rounded-lg hover:bg-purple-200 transition-colors duration-200">
                                                <i class="fas fa-eye mr-1"></i>
                                                Detail
                                            </button>

                                            <!-- Edit Button -->
                                            <a href="{{ route('talent_admin.edit_talent_admin', $admin) }}" class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors duration-200">
                                                <i class="fas fa-edit mr-1"></i>
                                                Edit
                                            </a>

                                            <!-- Delete Button (if not current user) -->
                                            @if($admin->id !== $user->id)
                                                <button onclick="confirmDelete({{ $admin->id }}, '{{ $admin->name }}')" class="inline-flex items-center px-3 py-1 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors duration-200">
                                                    <i class="fas fa-trash mr-1"></i>
                                                    Hapus
                                                </button>
                                            @else
                                                <span class="inline-flex items-center px-3 py-1 bg-gray-100 text-gray-500 rounded-lg">
                                                    <i class="fas fa-lock mr-1"></i>
                                                    Anda
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $talentAdmins->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-user-shield text-gray-400 text-2xl"></i>
                    </div>
                    <h4 class="text-lg font-semibold text-gray-700 mb-2">Belum ada talent admin</h4>
                    <p class="text-gray-500 mb-6">Mulai dengan menambahkan talent admin pertama.</p>
                    <a href="{{ route('talent_admin.create_talent_admin') }}" class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-xl hover:bg-red-700 transition-colors duration-200">
                        <i class="fas fa-plus mr-2"></i>
                        Tambah Admin Pertama
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Talent Admin Details Modal -->
<div id="adminDetailsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-2xl bg-white">
        <div class="mt-3">
            <!-- Modal Header -->
            <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
                <h3 class="text-2xl font-bold text-gray-900 flex items-center">
                    <i class="fas fa-user-shield text-red-600 mr-3"></i>
                    Detail Talent Admin
                </h3>
                <button onclick="closeAdminModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <!-- Modal Content -->
            <div id="adminDetailsContent" class="space-y-6">
                <!-- Content will be loaded here -->
                <div class="text-center py-8">
                    <i class="fas fa-spinner fa-spin text-3xl text-red-600 mb-4"></i>
                    <p class="text-gray-600">Memuat detail admin...</p>
                </div>
            </div>
        </div>
    </div>
</div>



<script>
// Talent Admin details functionality
function viewAdminDetails(adminId) {
    console.log('viewAdminDetails called with ID:', adminId);

    const modal = document.getElementById('adminDetailsModal');
    if (!modal) {
        console.error('Admin modal element not found!');
        Swal.fire({
            title: 'Kesalahan!',
            text: 'Modal tidak ditemukan. Silakan refresh halaman.',
            icon: 'error',
            confirmButtonColor: '#dc2626'
        });
        return;
    }

    console.log('Opening admin modal...');
    modal.classList.remove('hidden');

    // Reset content
    const contentElement = document.getElementById('adminDetailsContent');
    if (!contentElement) {
        console.error('Admin modal content element not found!');
        return;
    }

    contentElement.innerHTML = `
        <div class="text-center py-8">
            <i class="fas fa-spinner fa-spin text-3xl text-red-600 mb-4"></i>
            <p class="text-gray-600">Memuat detail admin...</p>
        </div>
    `;

    console.log('Making fetch request to:', `/talent-admin/admin/${adminId}/details`);

    // Fetch admin details
    fetch(`/talent-admin/admin/${adminId}/details`)
        .then(response => {
            console.log('Response received:', response.status, response.ok);
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Admin data received:', data);
            displayAdminDetails(data);
        })
        .catch(error => {
            console.error('Error loading admin details:', error);
            document.getElementById('adminDetailsContent').innerHTML = `
                <div class="text-center py-8">
                    <i class="fas fa-exclamation-triangle text-3xl text-red-600 mb-4"></i>
                    <p class="text-red-600">Error memuat detail admin</p>
                    <p class="text-gray-600 text-sm mt-2">Error: ${error.message}</p>
                </div>
            `;
        });
}

function displayAdminDetails(admin) {
    console.log('Displaying admin details:', admin);
    const content = `
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Profile Info -->
            <div class="lg:col-span-1">
                <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-2xl p-6 text-center">
                    ${admin.avatar ?
                        `<img class="w-24 h-24 rounded-2xl object-cover mx-auto mb-4 shadow-lg" src="${admin.avatar}" alt="${admin.name}">` :
                        `<div class="w-24 h-24 bg-gradient-to-br from-red-500 to-red-700 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                            <i class="fas fa-user text-white text-2xl"></i>
                        </div>`
                    }
                    <h4 class="text-xl font-bold text-gray-900 mb-2">${admin.name}</h4>
                    <p class="text-gray-600 mb-4">${admin.pekerjaan || 'Talent Admin'}</p>
                    <div class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-red-100 text-red-800">
                        <i class="fas fa-shield-alt mr-1"></i>
                        Talent Admin
                    </div>
                </div>
            </div>

            <!-- Details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Contact Information -->
                <div class="bg-white border border-gray-200 rounded-2xl p-6">
                    <h5 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-address-card text-red-600 mr-2"></i>
                        Informasi Kontak
                    </h5>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Email</label>
                            <p class="text-gray-900 font-medium">${admin.email}</p>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">ID Admin</label>
                            <p class="text-gray-900 font-medium">${admin.id}</p>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Tanggal Bergabung</label>
                            <p class="text-gray-900 font-medium">${admin.joined_date}</p>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Login Terakhir</label>
                            <p class="text-gray-900 font-medium">${admin.last_login || 'Belum pernah login'}</p>
                        </div>
                    </div>
                </div>

                <!-- Admin Statistics -->
                <div class="bg-white border border-gray-200 rounded-2xl p-6">
                    <h5 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-chart-bar text-red-600 mr-2"></i>
                        Statistik Admin
                    </h5>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-red-600">${admin.stats?.days_since_joined || 0}</div>
                            <div class="text-xs text-gray-500">Hari Bergabung</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-blue-600">${admin.stats?.total_logins || 0}</div>
                            <div class="text-xs text-gray-500">Total Login</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600">${admin.stats?.recent_activity || 0}</div>
                            <div class="text-xs text-gray-500">Aktivitas 7 hari</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-purple-600">${admin.stats?.permissions || 'Admin'}</div>
                            <div class="text-xs text-gray-500">Level Akses</div>
                        </div>
                    </div>
                </div>

                <!-- Permissions & Roles -->
                <div class="bg-white border border-gray-200 rounded-2xl p-6">
                    <h5 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-key text-red-600 mr-2"></i>
                        Izin & Peran
                    </h5>
                    <div class="flex flex-wrap gap-2">
                        ${admin.roles && admin.roles.length > 0 ?
                            admin.roles.map(role => `
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                    <i class="fas fa-shield-alt mr-1"></i>
                                    ${role}
                                </span>
                            `).join('') :
                            `<span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                <i class="fas fa-shield-alt mr-1"></i>
                                Talent Admin
                            </span>`
                        }
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-end space-x-4 mt-8 pt-6 border-t border-gray-200">
            <button onclick="closeAdminModal()" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                Tutup
            </button>
            <a href="/talent-admin/admin/${admin.id}/edit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                Edit Admin
            </a>
        </div>
    `;

    document.getElementById('adminDetailsContent').innerHTML = content;
}

function closeAdminModal() {
    console.log('Closing admin modal...');
    const modal = document.getElementById('adminDetailsModal');
    if (modal) {
        modal.classList.add('hidden');
        console.log('✅ Admin modal closed');
    }
}

// Close modal when clicking outside
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('adminDetailsModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeAdminModal();
            }
        });
    }
});

// Confirm delete
function confirmDelete(adminId, adminName) {
    Swal.fire({
        title: 'Hapus Talent Admin?',
        text: `Apakah Anda yakin ingin menghapus admin "${adminName}"? Tindakan ini tidak dapat dibatalkan.`,
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
            form.action = `/talent-admin/admin/${adminId}`;

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

<style>
/* Modal positioning */
.fixed {
    position: fixed;
}
.inset-0 {
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
}
.hidden {
    display: none;
}
.z-50 {
    z-index: 50;
}

/* Enhanced hover effects */
.transition-colors {
    transition: background-color 0.2s ease, color 0.2s ease;
}
</style>

@if (session('success'))
    <script>
        Swal.fire({
            title: 'Berhasil!',
            text: '{{ session('success') }}',
            icon: 'success',
            confirmButtonColor: '#dc2626'
        });
    </script>
@endif

@if (session('error'))
    <script>
        Swal.fire({
            title: 'Kesalahan!',
            text: '{{ session('error') }}',
            icon: 'error',
            confirmButtonColor: '#dc2626'
        });
    </script>
@endif

@endsection
