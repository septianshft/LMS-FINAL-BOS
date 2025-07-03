@extends('layout.template.mainTemplate')

@section('title', 'Kelola Perekrut')

@section('container')
<div class="min-h-screen bg-gray-50 p-6">
    <!-- Page Heading -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">
                <i class="fas fa-building text-indigo-600 mr-3"></i>
                Kelola Perekrut
            </h1>
            <p class="text-gray-600">Kelola akun perekrut dan status mereka</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('talent_admin.dashboard') }}" class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition-all duration-200 font-medium shadow-lg hover:shadow-xl">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali ke Dashboard
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg shadow-md">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-3 text-lg"></i>
                <div>
                    <p class="font-medium">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg shadow-md">
            <div class="flex items-center">
                <i class="fas fa-exclamation-triangle mr-3 text-lg"></i>
                <div>
                    <p class="font-medium">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Recruiters Table -->
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100">
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-t-2xl p-6">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-white bg-opacity-20 rounded-xl flex items-center justify-center mr-3">
                    <i class="fas fa-users text-white"></i>
                </div>
                <h3 class="text-lg font-semibold text-white">Daftar Perekrut</h3>
            </div>
        </div>
        <div class="p-6">
            @if($recruiters->count() > 0)
                <!-- Desktop Table View -->
                <div class="hidden lg:block overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-50 border-b-2 border-gray-200">
                                <th class="text-left py-4 px-4 font-semibold text-gray-700 text-sm">Perekrut</th>
                                <th class="text-left py-4 px-4 font-semibold text-gray-700 text-sm">Email</th>
                                <th class="text-left py-4 px-4 font-semibold text-gray-700 text-sm">Status</th>
                                <th class="text-left py-4 px-4 font-semibold text-gray-700 text-sm">Tanggal Bergabung</th>
                                <th class="text-left py-4 px-4 font-semibold text-gray-700 text-sm">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($recruiters as $recruiter)
                                <tr class="hover:bg-gray-50 transition-colors duration-200">
                                    <td class="py-6 px-4">
                                        <div class="flex items-center">
                                            @if($recruiter->user->avatar)
                                                <img class="w-12 h-12 rounded-xl object-cover mr-4 shadow-md"
                                                     src="{{ asset('storage/' . $recruiter->user->avatar) }}"
                                                     alt="{{ $recruiter->user->name }}">
                                            @else
                                                <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center mr-4 shadow-md">
                                                    <i class="fas fa-building text-white"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <div class="font-semibold text-gray-900">{{ $recruiter->user->name }}</div>
                                                @if($recruiter->user->pekerjaan)
                                                    <div class="text-gray-500 text-sm">{{ $recruiter->user->pekerjaan }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-6 px-4">
                                        <span class="text-gray-900 font-medium">{{ $recruiter->user->email }}</span>
                                    </td>
                                    <td class="py-6 px-4">
                                        @if($recruiter->is_active)
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                                <i class="fas fa-check-circle mr-1"></i>
                                                Aktif
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                                <i class="fas fa-pause-circle mr-1"></i>
                                                Tidak Aktif
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-6 px-4">
                                        <div class="text-gray-900 font-medium text-sm">{{ $recruiter->created_at->locale('id')->translatedFormat('d F Y') }}</div>
                                        <div class="text-gray-500 text-xs">{{ $recruiter->created_at->format('H:i') }}</div>
                                    </td>
                                    <td class="py-6 px-4">
                                        <div class="flex space-x-2">
                                            <button onclick="viewRecruiterDetails({{ $recruiter->id }})"
                                                    class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-all duration-200 font-medium text-sm shadow-lg hover:shadow-xl">
                                                <i class="fas fa-eye mr-2"></i>
                                                Lihat Detail
                                            </button>
                                            <button onclick="editRecruiter({{ $recruiter->id }})"
                                                    class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all duration-200 font-medium text-sm shadow-lg hover:shadow-xl">
                                                <i class="fas fa-edit mr-2"></i>
                                                Edit
                                            </button>
                                            <form action="{{ route('talent_admin.toggle_recruiter_status', $recruiter) }}" method="POST" class="inline-block">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                        class="inline-flex items-center px-3 py-2 {{ $recruiter->is_active ? 'bg-orange-600 hover:bg-orange-700' : 'bg-green-600 hover:bg-green-700' }} text-white rounded-lg transition-all duration-200 font-medium text-sm shadow-lg hover:shadow-xl"
                                                        onclick="return confirm('Apakah Anda yakin ingin {{ $recruiter->is_active ? 'menonaktifkan' : 'mengaktifkan' }} perekrut ini?')">
                                                    <i class="fas fa-{{ $recruiter->is_active ? 'pause' : 'play' }} mr-1"></i>
                                                </button>
                                            </form>
                                            <button onclick="deleteRecruiter({{ $recruiter->id }}, '{{ $recruiter->user->name }}')"
                                                    class="inline-flex items-center px-3 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-all duration-200 font-medium text-sm shadow-lg hover:shadow-xl btn-delete">
                                                <i class="fas fa-trash mr-1"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Card View -->
                <div class="lg:hidden space-y-6">
                    @foreach($recruiters as $recruiter)
                        <div class="bg-gradient-to-br from-white to-gray-50 border-2 border-gray-100 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 hover-lift">
                            <!-- Mobile Card Header -->
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center">
                                    @if($recruiter->user->avatar)
                                        <img class="w-12 h-12 rounded-xl object-cover mr-3 shadow-md"
                                             src="{{ asset('storage/' . $recruiter->user->avatar) }}"
                                             alt="{{ $recruiter->user->name }}">
                                    @else
                                        <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center mr-3 shadow-md">
                                            <i class="fas fa-building text-white"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="font-semibold text-gray-900">{{ $recruiter->user->name }}</div>
                                        @if($recruiter->user->pekerjaan)
                                            <div class="text-gray-500 text-sm">{{ $recruiter->user->pekerjaan }}</div>
                                        @endif
                                    </div>
                                </div>
                                @if($recruiter->is_active)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-1"></i>
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                        <i class="fas fa-pause-circle mr-1"></i>
                                        Tidak Aktif
                                    </span>
                                @endif
                            </div>

                            <!-- Recruiter Details -->
                            <div class="grid grid-cols-1 gap-4 mb-4">
                                <div class="bg-white p-4 rounded-xl border border-gray-200">
                                    <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Email</div>
                                    <div class="text-gray-900 font-medium">{{ $recruiter->user->email }}</div>
                                </div>
                                <div class="bg-white p-4 rounded-xl border border-gray-200">
                                    <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Tanggal Bergabung</div>
                                    <div class="text-gray-900 font-medium">{{ $recruiter->created_at->format('M d, Y H:i') }}</div>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex space-x-3">
                                <button onclick="viewRecruiterDetails({{ $recruiter->id }})"
                                        class="flex-1 px-4 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl transition-all duration-200 font-medium shadow-lg hover:shadow-xl">
                                    <i class="fas fa-eye mr-2"></i>
                                    Lihat Detail
                                </button>
                                <button onclick="editRecruiter({{ $recruiter->id }})"
                                        class="flex-1 px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl transition-all duration-200 font-medium shadow-lg hover:shadow-xl">
                                    <i class="fas fa-edit mr-2"></i>
                                    Edit
                                </button>
                                <form action="{{ route('talent_admin.toggle_recruiter_status', $recruiter) }}" method="POST" class="flex-shrink-0">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                            class="px-4 py-3 {{ $recruiter->is_active ? 'bg-orange-600 hover:bg-orange-700' : 'bg-green-600 hover:bg-green-700' }} text-white rounded-xl transition-all duration-200 font-medium shadow-lg hover:shadow-xl"
                                            onclick="return confirm('Apakah Anda yakin ingin {{ $recruiter->is_active ? 'menonaktifkan' : 'mengaktifkan' }} perekrut ini?')">
                                        <i class="fas fa-{{ $recruiter->is_active ? 'pause' : 'play' }}"></i>
                                    </button>
                                </form>
                                <button onclick="deleteRecruiter({{ $recruiter->id }}, '{{ $recruiter->user->name }}')"
                                        class="flex-shrink-0 px-4 py-3 bg-red-600 hover:bg-red-700 text-white rounded-xl transition-all duration-200 font-medium shadow-lg hover:shadow-xl btn-delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="flex justify-center mt-12 pt-8 border-t border-gray-200">
                    <div class="pagination-wrapper">
                        {{ $recruiters->links() }}
                    </div>
                </div>
            @else
                <div class="text-center py-16">
                    <div class="w-24 h-24 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-users text-4xl text-gray-400"></i>
                    </div>
                    <h5 class="text-xl font-semibold text-gray-700 mb-3">Tidak ada perekrut ditemukan</h5>
                    <p class="text-gray-500 max-w-md mx-auto">Belum ada perekrut yang terdaftar.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Recruiter Details Modal -->
<div id="recruiterDetailsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-2xl bg-white">
        <div class="mt-3">
            <!-- Modal Header -->
            <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
                <h3 class="text-2xl font-bold text-gray-900 flex items-center">
                    <i class="fas fa-building text-indigo-600 mr-3"></i>
                    Detail Perekrut
                </h3>
                <button onclick="closeRecruiterModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <!-- Modal Content -->
            <div id="recruiterDetailsContent" class="space-y-6">
                <!-- Content will be loaded here -->
                <div class="text-center py-8">
                    <i class="fas fa-spinner fa-spin text-3xl text-indigo-600 mb-4"></i>
                    <p class="text-gray-600">Memuat detail perekrut...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Recruiter Modal -->
<div id="editRecruiterModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-10 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-2/3 xl:w-1/2 shadow-lg rounded-2xl bg-white">
        <div class="mt-3">
            <!-- Modal Header -->
            <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
                <h3 class="text-2xl font-bold text-gray-900 flex items-center">
                    <i class="fas fa-edit text-blue-600 mr-3"></i>
                    Edit Perekrut
                </h3>
                <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <!-- Edit Form -->
            <form id="editRecruiterForm" class="space-y-6">
                @csrf
                @method('PUT')
                <input type="hidden" id="editRecruiterId" name="recruiter_id">

                <!-- Basic Information -->
                <div class="bg-gray-50 rounded-xl p-6">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-user text-indigo-600 mr-2"></i>
                        Informasi Dasar
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="editName" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap *</label>
                            <input type="text" id="editName" name="name" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label for="editEmail" class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                            <input type="email" id="editEmail" name="email" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label for="editPhone" class="block text-sm font-medium text-gray-700 mb-1">Telepon</label>
                            <input type="text" id="editPhone" name="phone"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <!-- Empty space for grid alignment -->
                        </div>
                        <div>
                            <label for="editPassword" class="block text-sm font-medium text-gray-700 mb-1">Password Baru (kosongkan untuk tetap menggunakan yang lama)</label>
                            <input type="password" id="editPassword" name="password"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label for="editPasswordConfirmation" class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password Baru</label>
                            <input type="password" id="editPasswordConfirmation" name="password_confirmation"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                </div>

                <!-- Company Information -->
                <div class="bg-blue-50 rounded-xl p-6">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-building text-blue-600 mr-2"></i>
                        Informasi Perusahaan
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="editCompanyName" class="block text-sm font-medium text-gray-700 mb-1">Nama Perusahaan *</label>
                            <input type="text" id="editCompanyName" name="company_name" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label for="editIndustry" class="block text-sm font-medium text-gray-700 mb-1">Industri *</label>
                            <select id="editIndustry" name="industry" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Pilih Industri</option>
                                <option value="Technology">Technology</option>
                                <option value="Finance">Finance</option>
                                <option value="Healthcare">Healthcare</option>
                                <option value="Education">Education</option>
                                <option value="Retail">Retail</option>
                                <option value="Manufacturing">Manufacturing</option>
                                <option value="Consulting">Consulting</option>
                                <option value="Media">Media</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div>
                            <label for="editCompanySize" class="block text-sm font-medium text-gray-700 mb-1">Ukuran Perusahaan</label>
                            <select id="editCompanySize" name="company_size"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Pilih Ukuran</option>
                                <option value="1-10">1-10 karyawan</option>
                                <option value="11-50">11-50 karyawan</option>
                                <option value="51-200">51-200 karyawan</option>
                                <option value="201-500">201-500 karyawan</option>
                                <option value="501-1000">501-1000 karyawan</option>
                                <option value="1000+">1000+ karyawan</option>
                            </select>
                        </div>
                        <div>
                            <label for="editWebsite" class="block text-sm font-medium text-gray-700 mb-1">Website</label>
                            <input type="url" id="editWebsite" name="website"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="https://example.com">
                        </div>
                        <div class="md:col-span-2">
                            <label for="editAddress" class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                            <textarea id="editAddress" name="address" rows="2"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                        </div>
                        <div class="md:col-span-2">
                            <label for="editCompanyDescription" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Perusahaan</label>
                            <textarea id="editCompanyDescription" name="company_description" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                      placeholder="Deskripsi singkat tentang perusahaan..."></textarea>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                    <button type="button" onclick="closeEditModal()"
                            class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                        <i class="fas fa-save mr-2"></i>
                        Perbarui Perekrut
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Edit recruiter functionality
function editRecruiter(recruiterId) {
    // Show the edit modal
    document.getElementById('editRecruiterModal').classList.remove('hidden');

    // Reset form
    document.getElementById('editRecruiterForm').reset();
    document.getElementById('editRecruiterId').value = recruiterId;

    // Fetch recruiter data
    fetch(`/talent-admin/recruiter/${recruiterId}/edit`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const recruiter = data.recruiter;

                // Populate form fields
                document.getElementById('editName').value = recruiter.name || '';
                document.getElementById('editEmail').value = recruiter.email || '';
                document.getElementById('editPhone').value = recruiter.phone || '';
                document.getElementById('editCompanyName').value = recruiter.company_name || '';
                document.getElementById('editIndustry').value = recruiter.industry || '';
                document.getElementById('editCompanySize').value = recruiter.company_size || '';
                document.getElementById('editWebsite').value = recruiter.website || '';
                document.getElementById('editAddress').value = recruiter.address || '';
                document.getElementById('editCompanyDescription').value = recruiter.company_description || '';
            } else {
                showNotification('error', 'Gagal memuat data perekrut');
                closeEditModal();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('error', 'Kesalahan memuat data perekrut');
            closeEditModal();
        });
}

function closeEditModal() {
    document.getElementById('editRecruiterModal').classList.add('hidden');
}

// Handle form submission
document.getElementById('editRecruiterForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const recruiterId = document.getElementById('editRecruiterId').value;

    // Show loading state
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Memperbarui...';
    submitBtn.disabled = true;

    // Convert FormData to JSON
    const data = {};
    formData.forEach((value, key) => {
        if (key !== 'recruiter_id') {
            // Only include password if it's not empty
            if (key === 'password' || key === 'password_confirmation') {
                if (value.trim() !== '') {
                    data[key] = value;
                }
            } else if (value.trim() !== '') {
                data[key] = value;
            }
        }
    });

    fetch(`/talent-admin/recruiter/${recruiterId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('success', data.message || 'Perekrut berhasil diperbarui!');
            closeEditModal();

            // Refresh the page to show updated data
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            showNotification('error', data.message || 'Gagal memperbarui perekrut');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('error', 'Terjadi kesalahan saat memperbarui perekrut');
    })
    .finally(() => {
        // Restore button state
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
});

// Close modal when clicking outside
document.getElementById('editRecruiterModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeEditModal();
    }
});

// Recruiter details functionality
function viewRecruiterDetails(recruiterId) {
    document.getElementById('recruiterDetailsModal').classList.remove('hidden');

    // Reset content
    document.getElementById('recruiterDetailsContent').innerHTML = `
        <div class="text-center py-8">
            <i class="fas fa-spinner fa-spin text-3xl text-indigo-600 mb-4"></i>
            <p class="text-gray-600">Memuat detail perekrut...</p>
        </div>
    `;

    // Fetch recruiter details (you would implement this endpoint)
    fetch(`/talent-admin/recruiters/${recruiterId}/details`)
        .then(response => response.json())
        .then(data => {
            displayRecruiterDetails(data);
        })
        .catch(error => {
            document.getElementById('recruiterDetailsContent').innerHTML = `
                <div class="text-center py-8">
                    <i class="fas fa-exclamation-triangle text-3xl text-red-600 mb-4"></i>
                    <p class="text-red-600">Kesalahan memuat detail perekrut</p>
                    <p class="text-gray-600 text-sm mt-2">Silakan coba lagi nanti</p>
                </div>
            `;
        });
}

function displayRecruiterDetails(recruiter) {
    const content = `
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Profile Info -->
            <div class="lg:col-span-1">
                <div class="bg-gradient-to-br from-indigo-50 to-purple-100 rounded-2xl p-6 text-center">
                    ${recruiter.avatar ?
                        `<img class="w-24 h-24 rounded-2xl object-cover mx-auto mb-4 shadow-lg" src="${recruiter.avatar}" alt="${recruiter.name}">` :
                        `<div class="w-24 h-24 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                            <i class="fas fa-building text-white text-2xl"></i>
                        </div>`
                    }
                    <h4 class="text-xl font-bold text-gray-900 mb-2">${recruiter.name}</h4>
                    <p class="text-gray-600 mb-2">${recruiter.company || 'Perusahaan tidak ditentukan'}</p>
                    <p class="text-gray-500 text-sm mb-4">${recruiter.job || 'Posisi tidak ditentukan'}</p>
                    <div class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold ${recruiter.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'}">
                        <i class="fas fa-${recruiter.is_active ? 'check-circle' : 'pause-circle'} mr-1"></i>
                        ${recruiter.is_active ? 'Aktif' : 'Tidak Aktif'}
                    </div>
                </div>
            </div>

            <!-- Details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Contact Information -->
                <div class="bg-white border border-gray-200 rounded-2xl p-6">
                    <h5 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-address-card text-indigo-600 mr-2"></i>
                        Informasi Kontak
                    </h5>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Email</label>
                            <p class="text-gray-900 font-medium">${recruiter.email}</p>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Phone</label>
                            <p class="text-gray-900 font-medium">${recruiter.phone || 'Tidak disediakan'}</p>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Company</label>
                            <p class="text-gray-900 font-medium">${recruiter.company || 'Tidak disediakan'}</p>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Tanggal Bergabung</label>
                            <p class="text-gray-900 font-medium">${recruiter.joined_date}</p>
                        </div>
                    </div>
                </div>

                <!-- Company Information -->
                ${recruiter.company_details ? `
                <div class="bg-white border border-gray-200 rounded-2xl p-6">
                    <h5 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-building text-purple-600 mr-2"></i>
                        Informasi Perusahaan
                    </h5>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Industri</label>
                            <p class="text-gray-900 font-medium">${recruiter.company_details.industry || 'Tidak ditentukan'}</p>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Ukuran Perusahaan</label>
                            <p class="text-gray-900 font-medium">${recruiter.company_details.size || 'Tidak ditentukan'}</p>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Website</label>
                            <p class="text-gray-900 font-medium">
                                ${recruiter.company_details.website ?
                                    `<a href="${recruiter.company_details.website}" target="_blank" class="text-indigo-600 hover:text-indigo-700">
                                        ${recruiter.company_details.website}
                                        <i class="fas fa-external-link-alt ml-1 text-xs"></i>
                                    </a>` :
                                    'Tidak disediakan'
                                }
                            </p>
                        </div>
                        ${recruiter.company_details.description ? `
                        <div class="sm:col-span-2">
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Deskripsi</label>
                            <p class="text-gray-900 font-medium">${recruiter.company_details.description}</p>
                        </div>
                        ` : ''}
                    </div>
                </div>
                ` : ''}

                <!-- Recruitment Activity -->
                <div class="bg-white border border-gray-200 rounded-2xl p-6">
                    <h5 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-chart-bar text-green-600 mr-2"></i>
                        Aktivitas Rekrutmen
                    </h5>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-blue-600">${recruiter.stats?.total_requests || 0}</div>
                            <div class="text-xs text-gray-500">Total Permintaan</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600">${recruiter.stats?.approved_requests || 0}</div>
                            <div class="text-xs text-gray-500">Disetujui</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-orange-600">${recruiter.stats?.pending_requests || 0}</div>
                            <div class="text-xs text-gray-500">Menunggu</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-purple-600">${recruiter.stats?.success_rate || 0}%</div>
                            <div class="text-xs text-gray-500">Tingkat Keberhasilan</div>
                        </div>
                    </div>
                </div>

                <!-- Recent Requests -->
                ${recruiter.recent_requests && recruiter.recent_requests.length > 0 ? `
                <div class="bg-white border border-gray-200 rounded-2xl p-6">
                    <h5 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-history text-yellow-600 mr-2"></i>
                        Permintaan Talenta Terbaru
                    </h5>
                    <div class="space-y-3">
                        ${recruiter.recent_requests.slice(0, 3).map(request => `
                            <div class="border border-gray-100 rounded-lg p-4">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h6 class="font-semibold text-gray-900">${request.project_title}</h6>
                                        <p class="text-gray-600 text-sm mt-1">${request.description}</p>
                                        <p class="text-xs text-gray-500 mt-2">${request.created_at}</p>
                                    </div>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold
                                        ${request.status === 'approved' ? 'bg-green-100 text-green-800' :
                                          request.status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                                          'bg-red-100 text-red-800'}">
                                        ${request.status}
                                    </span>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                </div>
                ` : ''}
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-end space-x-4 mt-8 pt-6 border-t border-gray-200">
            <button onclick="closeRecruiterModal()" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                Tutup
            </button>
            <button onclick="deleteRecruiter(${recruiter.id}, '${recruiter.name}')" class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors btn-delete">
                <i class="fas fa-trash mr-2"></i>
                Hapus Akun
            </button>
            <button onclick="toggleRecruiterStatus(${recruiter.id}, ${recruiter.is_active})" class="px-6 py-2 ${recruiter.is_active ? 'bg-orange-600 hover:bg-orange-700' : 'bg-green-600 hover:bg-green-700'} text-white rounded-lg transition-colors">
                ${recruiter.is_active ? 'Nonaktifkan' : 'Aktifkan'} Perekrut
            </button>
        </div>
    `;

    document.getElementById('recruiterDetailsContent').innerHTML = content;
}

function closeRecruiterModal() {
    document.getElementById('recruiterDetailsModal').classList.add('hidden');
}

function toggleRecruiterStatus(recruiterId, isActive) {
    if (confirm(`Apakah Anda yakin ingin ${isActive ? 'menonaktifkan' : 'mengaktifkan'} perekrut ini?`)) {
        // Submit the form or make an AJAX request
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/talent-admin/recruiters/${recruiterId}/toggle-status`;

        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'PATCH';

        form.appendChild(csrfToken);
        form.appendChild(methodField);
        document.body.appendChild(form);
        form.submit();
    }
}

// Delete recruiter functionality
function deleteRecruiter(recruiterId, recruiterName) {
    // Enhanced confirmation dialog
    const confirmed = confirm(
        `⚠️ PERINGATAN: Hapus Akun Perekrut\n\n` +
        `Apakah Anda benar-benar yakin ingin menghapus "${recruiterName}"?\n\n` +
        `Tindakan ini akan:\n` +
        `• Menghapus akun perekrut secara permanen\n` +
        `• Menghapus semua data terkait\n` +
        `• Tidak dapat dibatalkan\n\n` +
        `Ketik "DELETE" untuk mengkonfirmasi tindakan ini.`
    );

    if (!confirmed) {
        return;
    }

    // Additional security confirmation
    const confirmText = prompt(
        `Untuk mengkonfirmasi penghapusan "${recruiterName}", silakan ketik "DELETE" (dalam huruf besar):`
    );

    if (confirmText !== 'DELETE') {
        alert('❌ Penghapusan dibatalkan. Teks konfirmasi tidak cocok.');
        return;
    }

    // Show loading state
    const loadingModal = document.createElement('div');
    loadingModal.id = 'deleteLoadingModal';
    loadingModal.className = 'fixed inset-0 bg-gray-600 bg-opacity-75 flex items-center justify-center z-50';
    loadingModal.innerHTML = `
        <div class="bg-white rounded-2xl p-8 max-w-md mx-4 text-center shadow-2xl">
            <div class="mb-4">
                <i class="fas fa-spinner fa-spin text-4xl text-red-600"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Menghapus Perekrut...</h3>
            <p class="text-gray-600">Harap tunggu sementara kami memproses penghapusan.</p>
        </div>
    `;
    document.body.appendChild(loadingModal);

    // Perform AJAX delete request
    fetch(`/talent-admin/recruiter/${recruiterId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        // Remove loading modal
        document.body.removeChild(loadingModal);

        if (data.success) {
            // Success notification
            showNotification('success', data.message || 'Perekrut berhasil dihapus!');

            // Close modal if open
            closeRecruiterModal();

            // Refresh the page to update the list
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            // Error notification
            showNotification('error', data.message || 'Gagal menghapus perekrut.');
        }
    })
    .catch(error => {
        // Remove loading modal
        if (document.getElementById('deleteLoadingModal')) {
            document.body.removeChild(loadingModal);
        }

        console.error('Delete error:', error);
        showNotification('error', 'Terjadi kesalahan saat menghapus perekrut. Silakan coba lagi.');
    });
}

// Notification system
function showNotification(type, message) {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 max-w-md rounded-lg shadow-lg p-4 transform transition-all duration-300 notification-enter ${
        type === 'success'
            ? 'bg-green-500 text-white'
            : 'bg-red-500 text-white'
    }`;

    notification.innerHTML = `
        <div class="flex items-center">
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} mr-3 text-lg"></i>
            <div class="flex-1">
                <p class="font-medium">${message}</p>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white hover:text-gray-200 transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;

    document.body.appendChild(notification);

    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentElement) {
            notification.style.opacity = '0';
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.remove();
                }
            }, 300);
        }
    }, 5000);
}

// Close modal when clicking outside
document.getElementById('recruiterDetailsModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeRecruiterModal();
    }
});
</script>

<style>
/* Enhanced pagination styling */
.pagination-wrapper .pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 0.5rem;
}

.pagination-wrapper .page-link {
    padding: 0.75rem 1rem;
    background: white;
    border: 2px solid #e5e7eb;
    color: #6b7280;
    border-radius: 0.75rem;
    font-weight: 500;
    transition: all 0.2s ease;
    text-decoration: none;
}

.pagination-wrapper .page-link:hover {
    background: #f3f4f6;
    border-color: #d1d5db;
    color: #374151;
    transform: translateY(-1px);
}

.pagination-wrapper .page-item.active .page-link {
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    border-color: #6366f1;
    color: white;
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
}

.pagination-wrapper .page-item.disabled .page-link {
    background: #f9fafb;
    border-color: #f3f4f6;
    color: #d1d5db;
    cursor: not-allowed;
}

/* Card hover effects */
.hover-lift {
    transition: all 0.3s ease;
}

.hover-lift:hover {
    transform: translateY(-4px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
}

/* Delete button specific styling */
.btn-delete {
    position: relative;
    overflow: hidden;
}

.btn-delete:before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.5s;
}

.btn-delete:hover:before {
    left: 100%;
}

/* Loading spinner animation */
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.fa-spin {
    animation: spin 1s linear infinite;
}

/* Notification animations */
.notification-enter {
    opacity: 0;
    transform: translateX(100%);
    animation: slideInRight 0.3s ease-out forwards;
}

@keyframes slideInRight {
    to {
        opacity: 1;
        transform: translateX(0);
    }
}
</style>
@endsection
