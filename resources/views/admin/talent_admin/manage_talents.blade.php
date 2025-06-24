@extends('layout.template.mainTemplate')

@section('title', 'Kelola Talent')

@section('container')
<div class="min-h-screen bg-gray-50 p-6">
    <!-- Page Heading -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">
                <i class="fas fa-user-tie text-blue-600 mr-3"></i>
                Kelola Talent
            </h1>
            <p class="text-gray-600">Kelola profil talent dan status mereka</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('talent_admin.dashboard') }}" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-all duration-200 font-medium shadow-lg hover:shadow-xl">
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

    <!-- Talents Table -->
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100">
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-t-2xl p-6">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-white bg-opacity-20 rounded-xl flex items-center justify-center mr-3">
                    <i class="fas fa-star text-white"></i>
                </div>
                <h3 class="text-lg font-semibold text-white">Daftar Talent</h3>
            </div>
        </div>
        <div class="p-6">
            @if($talents->count() > 0)
                <!-- Desktop Table View -->
                <div class="hidden lg:block overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-50 border-b-2 border-gray-200">
                                <th class="text-left py-4 px-4 font-semibold text-gray-700 text-sm">Talent</th>
                                <th class="text-left py-4 px-4 font-semibold text-gray-700 text-sm">Email</th>
                                <th class="text-left py-4 px-4 font-semibold text-gray-700 text-sm">Status</th>
                                <th class="text-left py-4 px-4 font-semibold text-gray-700 text-sm">Bendera Merah</th>
                                <th class="text-left py-4 px-4 font-semibold text-gray-700 text-sm">Bergabung</th>
                                <th class="text-left py-4 px-4 font-semibold text-gray-700 text-sm">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($talents as $talent)
                                <tr class="hover:bg-gray-50 transition-colors duration-200">
                                    <td class="py-6 px-4">
                                        <div class="flex items-center">
                                            @if($talent->user->avatar)
                                                <img class="w-12 h-12 rounded-xl object-cover mr-4 shadow-md"
                                                     src="{{ asset('storage/' . $talent->user->avatar) }}"
                                                     alt="{{ $talent->user->name }}">
                                            @else
                                                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center mr-4 shadow-md">
                                                    <i class="fas fa-user text-white"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <div class="font-semibold text-gray-900">{{ $talent->user->name }}</div>
                                                <div class="text-gray-500 text-sm">{{ $talent->user->pekerjaan }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-6 px-4">
                                        <span class="text-gray-900 font-medium">{{ $talent->user->email }}</span>
                                    </td>
                                    <td class="py-6 px-4">
                                        @if($talent->is_active)
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                                <i class="fas fa-check-circle mr-1"></i>
                                                Aktif
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                                                <i class="fas fa-pause-circle mr-1"></i>
                                                Tidak Aktif
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-6 px-4">
                                        @php
                                            $redflagSummary = $talent->getRedflagSummary();
                                        @endphp
                                        <div class="space-y-2">
                                            @if($redflagSummary['has_redflags'])
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                                    <i class="fas fa-flag mr-1"></i>
                                                    {{ $redflagSummary['display_text'] }}
                                                </span>
                                                <div class="text-xs text-red-700">Rate: {{ $redflagSummary['rate'] }}%</div>
                                                <button onclick="viewProjectRedflags({{ $talent->id }}, '{{ $talent->user->name }}')"
                                                        class="text-xs text-blue-600 hover:text-blue-800 underline">
                                                    <i class="fas fa-list mr-1"></i>Lihat Proyek
                                                </button>
                                            @else
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                                    <i class="fas fa-check-circle mr-1"></i>
                                                    Tidak Ada Bendera
                                                </span>
                                                <div class="text-xs text-gray-500">{{ $redflagSummary['total_completed'] }} proyek selesai</div>
                                            @endif

                                            <!-- New Project Redflag Button -->
                                            <button onclick="openProjectRedflagModal({{ $talent->id }}, '{{ $talent->user->name }}')"
                                                    class="block w-full text-xs text-white bg-orange-600 hover:bg-orange-700 px-2 py-1 rounded transition-colors">
                                                <i class="fas fa-flag mr-1"></i>Tandai Proyek
                                            </button>
                                        </div>
                                    </td>
                                    <td class="py-6 px-4">
                                        <div class="text-gray-900 font-medium text-sm">{{ $talent->created_at->format('M d, Y') }}</div>
                                        <div class="text-gray-500 text-xs">{{ $talent->created_at->format('H:i') }}</div>
                                    </td>
                                    <td class="py-6 px-4">
                                        <div class="flex space-x-2">
                                            <button onclick="viewTalentDetails({{ $talent->id }})"
                                                    class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all duration-200 font-medium text-sm shadow-lg hover:shadow-xl">
                                                <i class="fas fa-eye mr-2"></i>
                                                Lihat Detail
                                            </button>
                                            <form action="{{ route('talent_admin.toggle_talent_status', $talent) }}" method="POST" class="inline-block">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                        class="inline-flex items-center px-3 py-2 {{ $talent->is_active ? 'bg-orange-600 hover:bg-orange-700' : 'bg-green-600 hover:bg-green-700' }} text-white rounded-lg transition-all duration-200 font-medium text-sm shadow-lg hover:shadow-xl"
                                                        onclick="return confirm('Apakah Anda yakin ingin {{ $talent->is_active ? 'menonaktifkan' : 'mengaktifkan' }} talent ini?')">>
                                                    <i class="fas fa-{{ $talent->is_active ? 'pause' : 'play' }} mr-1"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Card View -->
                <div class="lg:hidden space-y-6">
                    @foreach($talents as $talent)
                        <div class="bg-gradient-to-br from-white to-gray-50 border-2 border-gray-100 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 hover-lift">
                            <!-- Mobile Card Header -->
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center">
                                    @if($talent->user->avatar)
                                        <img class="w-12 h-12 rounded-xl object-cover mr-3 shadow-md"
                                             src="{{ asset('storage/' . $talent->user->avatar) }}"
                                             alt="{{ $talent->user->name }}">
                                    @else
                                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center mr-3 shadow-md">
                                            <i class="fas fa-user text-white"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="font-semibold text-gray-900">{{ $talent->user->name }}</div>
                                        <div class="text-gray-500 text-sm">{{ $talent->user->pekerjaan }}</div>
                                    </div>
                                </div>
                                @if($talent->is_active)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-1"></i>
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                                        <i class="fas fa-pause-circle mr-1"></i>
                                        Tidak Aktif
                                    </span>
                                @endif
                            </div>

                            <!-- Talent Details -->
                            <div class="grid grid-cols-1 gap-4 mb-4">
                                <div class="bg-white p-4 rounded-xl border border-gray-200">
                                    <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Email</div>
                                    <div class="text-gray-900 font-medium">{{ $talent->user->email }}</div>
                                </div>
                                <div class="bg-white p-4 rounded-xl border border-gray-200">
                                    <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Tanggal Bergabung</div>
                                    <div class="text-gray-900 font-medium">{{ $talent->created_at->format('M d, Y H:i') }}</div>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex space-x-3">
                                <button onclick="viewTalentDetails({{ $talent->id }})"
                                        class="flex-1 px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl transition-all duration-200 font-medium shadow-lg hover:shadow-xl">
                                    <i class="fas fa-eye mr-2"></i>
                                    Lihat Detail
                                </button>
                                <form action="{{ route('talent_admin.toggle_talent_status', $talent) }}" method="POST" class="flex-shrink-0">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                            class="px-4 py-3 {{ $talent->is_active ? 'bg-orange-600 hover:bg-orange-700' : 'bg-green-600 hover:bg-green-700' }} text-white rounded-xl transition-all duration-200 font-medium shadow-lg hover:shadow-xl"
                                            onclick="return confirm('Apakah Anda yakin ingin {{ $talent->is_active ? 'menonaktifkan' : 'mengaktifkan' }} talent ini?')">>
                                        <i class="fas fa-{{ $talent->is_active ? 'pause' : 'play' }}"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="flex justify-center mt-12 pt-8 border-t border-gray-200">
                    <div class="pagination-wrapper">
                        {{ $talents->links() }}
                    </div>
                </div>
            @else
                <div class="text-center py-16">
                    <div class="w-24 h-24 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-user-tie text-4xl text-gray-400"></i>
                    </div>
                    <h5 class="text-xl font-semibold text-gray-700 mb-3">Tidak ada talent ditemukan</h5>
                    <p class="text-gray-500 max-w-md mx-auto">Belum ada talent yang terdaftar.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Talent Details Modal -->
<div id="talentDetailsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-2xl bg-white">
        <div class="mt-3">
            <!-- Modal Header -->
            <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
                <h3 class="text-2xl font-bold text-gray-900 flex items-center">
                    <i class="fas fa-user-tie text-blue-600 mr-3"></i>
                    Detail Talent
                </h3>
                <button onclick="closeTalentModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <!-- Modal Content -->
            <div id="talentDetailsContent" class="space-y-6">
                <!-- Content will be loaded here -->
                <div class="text-center py-8">
                    <i class="fas fa-spinner fa-spin text-3xl text-blue-600 mb-4"></i>
                    <p class="text-gray-600">Memuat detail talent...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Talent details functionality
function viewTalentDetails(talentId) {
    console.log('viewTalentDetails called with ID:', talentId);

    const modal = document.getElementById('talentDetailsModal');
    if (!modal) {
        console.error('Modal element not found!');
        alert('Modal element not found. Please refresh the page.');
        return;
    }

    console.log('Opening modal...');
    modal.classList.remove('hidden');

    // Reset content
    const contentElement = document.getElementById('talentDetailsContent');
    if (!contentElement) {
        console.error('Modal content element not found!');
        return;
    }

    contentElement.innerHTML = `
        <div class="text-center py-8">
            <i class="fas fa-spinner fa-spin text-3xl text-blue-600 mb-4"></i>
            <p class="text-gray-600">Memuat detail talent...</p>
        </div>
    `;

    console.log('Making fetch request to:', `/talent-admin/talents/${talentId}/details`);

    // Fetch talent details
    fetch(`/talent-admin/talents/${talentId}/details`)
        .then(response => {
            console.log('Response received:', response.status, response.ok);
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Data received:', data);
            displayTalentDetails(data);
        })
        .catch(error => {
            console.error('Error loading talent details:', error);
            document.getElementById('talentDetailsContent').innerHTML = `
                <div class="text-center py-8">
                    <i class="fas fa-exclamation-triangle text-3xl text-red-600 mb-4"></i>
                    <p class="text-red-600">Error loading talent details</p>
                    <p class="text-gray-600 text-sm mt-2">Error: ${error.message}</p>
                </div>
            `;
        });
}

function displayTalentDetails(talent) {
    const content = `
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Profile Info -->
            <div class="lg:col-span-1">
                <div class="bg-gradient-to-br from-blue-50 to-indigo-100 rounded-2xl p-6 text-center">
                    ${talent.avatar ?
                        `<img class="w-24 h-24 rounded-2xl object-cover mx-auto mb-4 shadow-lg" src="${talent.avatar}" alt="${talent.name}">` :
                        `<div class="w-24 h-24 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                            <i class="fas fa-user text-white text-2xl"></i>
                        </div>`
                    }
                    <h4 class="text-xl font-bold text-gray-900 mb-2">${talent.name}</h4>
                    <p class="text-gray-600 mb-4">${talent.job || 'No job specified'}</p>
                    <div class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold ${talent.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'}">
                        <i class="fas fa-${talent.is_active ? 'check-circle' : 'pause-circle'} mr-1"></i>
                        ${talent.is_active ? 'Active' : 'Inactive'}
                    </div>
                </div>
            </div>

            <!-- Details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Contact Information -->
                <div class="bg-white border border-gray-200 rounded-2xl p-6">
                    <h5 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-address-card text-blue-600 mr-2"></i>
                        Contact Information
                    </h5>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Email</label>
                            <p class="text-gray-900 font-medium">${talent.email}</p>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Phone</label>
                            <p class="text-gray-900 font-medium">${talent.phone || 'Not provided'}</p>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Location</label>
                            <p class="text-gray-900 font-medium">${talent.location || 'Not provided'}</p>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Joined Date</label>
                            <p class="text-gray-900 font-medium">${talent.joined_date}</p>
                        </div>
                    </div>
                </div>

                <!-- Skills -->
                ${talent.skills && talent.skills.length > 0 ? `
                <div class="bg-white border border-gray-200 rounded-2xl p-6">
                    <h5 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-cogs text-purple-600 mr-2"></i>
                        Skills & Expertise
                    </h5>
                    <div class="flex flex-wrap gap-2">
                        ${talent.skills.map(skill => `
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                                ${skill.name} ${skill.level ? `(${skill.level})` : ''}
                            </span>
                        `).join('')}
                    </div>
                </div>
                ` : ''}

                <!-- Portfolio/Projects -->
                ${talent.portfolio && talent.portfolio.length > 0 ? `
                <div class="bg-white border border-gray-200 rounded-2xl p-6">
                    <h5 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-briefcase text-green-600 mr-2"></i>
                        Portfolio & Projects
                    </h5>
                    <div class="space-y-3">
                        ${talent.portfolio.map(project => `
                            <div class="border border-gray-100 rounded-lg p-4">
                                <h6 class="font-semibold text-gray-900">${project.title}</h6>
                                <p class="text-gray-600 text-sm mt-1">${project.description}</p>
                                ${project.url ? `<a href="${project.url}" target="_blank" class="text-blue-600 hover:text-blue-700 text-sm mt-2 inline-flex items-center">
                                    <i class="fas fa-external-link-alt mr-1"></i>
                                    View Project
                                </a>` : ''}
                            </div>
                        `).join('')}
                    </div>
                </div>
                ` : ''}

                <!-- Statistics -->
                <div class="bg-white border border-gray-200 rounded-2xl p-6">
                    <h5 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-chart-bar text-yellow-600 mr-2"></i>
                        Statistics
                    </h5>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-blue-600">${talent.stats?.completed_courses || 0}</div>
                            <div class="text-xs text-gray-500">Courses</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600">${talent.stats?.certificates || 0}</div>
                            <div class="text-xs text-gray-500">Certificates</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-purple-600">${talent.stats?.skill_level || 'N/A'}</div>
                            <div class="text-xs text-gray-500">Avg Skill Level</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-orange-600">${talent.stats?.experience_years || 0}</div>
                            <div class="text-xs text-gray-500">Years Exp</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-end space-x-4 mt-8 pt-6 border-t border-gray-200">
            <button onclick="closeTalentModal()" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                Close
            </button>
            <button onclick="toggleTalentStatus(${talent.id}, ${talent.is_active})" class="px-6 py-2 ${talent.is_active ? 'bg-orange-600 hover:bg-orange-700' : 'bg-green-600 hover:bg-green-700'} text-white rounded-lg transition-colors">
                ${talent.is_active ? 'Deactivate' : 'Activate'} Talent
            </button>
        </div>
    `;

    document.getElementById('talentDetailsContent').innerHTML = content;
}

function closeTalentModal() {
    document.getElementById('talentDetailsModal').classList.add('hidden');
}

function toggleTalentStatus(talentId, isActive) {
    if (confirm(`Are you sure you want to ${isActive ? 'deactivate' : 'activate'} this talent?`)) {
        // Submit the form or make an AJAX request
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/talent-admin/talents/${talentId}/toggle-status`;

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

// Close modal when clicking outside
document.getElementById('talentDetailsModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeTalentModal();
    }
});

// Project-based Redflag Functions
function openProjectRedflagModal(talentId, talentName) {
    fetch(`/talent-admin/talents/${talentId}/completed-projects`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showProjectRedflagModal(talentId, talentName, data.completed_projects);
            } else {
                alert('Failed to load completed projects');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading completed projects');
        });
}

function showProjectRedflagModal(talentId, talentName, completedProjects) {
    const modalHtml = `
    <div id="projectRedflagModal" class="fixed inset-0 z-50 overflow-y-auto" style="background: rgba(0,0,0,0.5);">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="bg-white rounded-xl max-w-2xl w-full max-h-[90vh] overflow-hidden shadow-2xl">
                <div class="bg-gradient-to-r from-orange-500 to-red-500 px-6 py-4 text-white">
                    <h3 class="text-xl font-bold flex items-center">
                        <i class="fas fa-flag mr-2"></i>Flag Project for ${talentName}
                    </h3>
                    <button onclick="closeProjectRedflagModal()"
                            class="absolute top-4 right-4 text-white hover:text-gray-200 text-xl">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="p-6">
                    <p class="text-gray-600 mb-4">Select a completed project to flag:</p>

                    ${completedProjects.length > 0 ? `
                        <div class="space-y-3 max-h-60 overflow-y-auto">
                            ${completedProjects.map(project => `
                                <div class="border rounded-lg p-4 hover:bg-gray-50 cursor-pointer project-item"
                                     onclick="selectProject(${project.id}, '${project.project_title}', ${project.is_redflagged})">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <h4 class="font-medium text-gray-900">${project.project_title}</h4>
                                            <p class="text-sm text-gray-600 mt-1">${project.project_description.substring(0, 100)}${project.project_description.length > 100 ? '...' : ''}</p>
                                            <div class="text-xs text-gray-500 mt-2">
                                                Completed: ${project.completed_at} | Recruiter: ${project.recruiter_name}
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            ${project.is_redflagged ?
                                                '<span class="px-2 py-1 bg-red-100 text-red-800 text-xs rounded-full">Already Flagged</span>' :
                                                '<span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">Not Flagged</span>'
                                            }
                                        </div>
                                    </div>
                                </div>
                            `).join('')}
                        </div>

                        <div id="redflagForm" class="mt-6 hidden">
                            <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                                <h5 class="font-medium text-orange-900 mb-2">Flag Project: <span id="selectedProjectTitle"></span></h5>
                                <form id="projectRedflagForm">
                                    <input type="hidden" id="selectedProjectId" name="project_id">
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Reason for flagging:</label>
                                        <textarea name="redflag_reason" id="redflag_reason" rows="3"
                                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                                                  placeholder="Please provide a detailed reason for flagging this project..."
                                                  required></textarea>
                                    </div>
                                    <div class="flex gap-3">
                                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                                            <i class="fas fa-flag mr-1"></i>Flag Project
                                        </button>
                                        <button type="button" onclick="hideRedflagForm()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                                            Cancel
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    ` : `
                        <div class="text-center py-8">
                            <i class="fas fa-inbox text-4xl text-gray-300 mb-4"></i>
                            <p class="text-gray-500">No completed projects found for this talent.</p>
                        </div>
                    `}
                </div>

                <div class="bg-gray-50 px-6 py-4 border-t flex justify-end">
                    <button onclick="closeProjectRedflagModal()"
                            class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
    `;

    document.body.insertAdjacentHTML('beforeend', modalHtml);
    document.body.style.overflow = 'hidden';

    // Setup form submission
    document.getElementById('projectRedflagForm').addEventListener('submit', function(e) {
        e.preventDefault();
        submitProjectRedflag();
    });
}

function selectProject(projectId, projectTitle, isRedflagged) {
    if (isRedflagged) {
        alert('This project is already flagged. You can remove the flag by contacting the system administrator.');
        return;
    }

    document.getElementById('selectedProjectId').value = projectId;
    document.getElementById('selectedProjectTitle').textContent = projectTitle;
    document.getElementById('redflagForm').classList.remove('hidden');

    // Highlight selected project
    document.querySelectorAll('.project-item').forEach(item => {
        item.classList.remove('border-orange-500', 'bg-orange-50');
    });
    event.currentTarget.classList.add('border-orange-500', 'bg-orange-50');
}

function hideRedflagForm() {
    document.getElementById('redflagForm').classList.add('hidden');
    document.getElementById('redflag_reason').value = '';

    // Remove highlighting
    document.querySelectorAll('.project-item').forEach(item => {
        item.classList.remove('border-orange-500', 'bg-orange-50');
    });
}

function submitProjectRedflag() {
    const formData = new FormData(document.getElementById('projectRedflagForm'));

    fetch('/talent-admin/projects/redflag', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Project flagged successfully!');
            closeProjectRedflagModal();
            location.reload(); // Refresh to show updated redflag status
        } else {
            alert('Failed to flag project: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error flagging project');
    });
}

function closeProjectRedflagModal() {
    const modal = document.getElementById('projectRedflagModal');
    if (modal) {
        modal.remove();
    }
    document.body.style.overflow = '';
}

function viewProjectRedflags(talentId, talentName) {
    fetch(`/talent-admin/talents/${talentId}/redflag-history`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showRedflagHistoryModal(talentName, data.redflag_summary, data.redflagged_projects);
            } else {
                alert('Failed to load redflag history');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading redflag history');
        });
}

function showRedflagHistoryModal(talentName, summary, redflaggedProjects) {
    const modalHtml = `
    <div id="redflagHistoryModal" class="fixed inset-0 z-50 overflow-y-auto" style="background: rgba(0,0,0,0.5);">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="bg-white rounded-xl max-w-2xl w-full max-h-[90vh] overflow-hidden shadow-2xl">
                <div class="bg-gradient-to-r from-red-500 to-pink-500 px-6 py-4 text-white">
                    <h3 class="text-xl font-bold flex items-center">
                        <i class="fas fa-history mr-2"></i>Redflag History for ${talentName}
                    </h3>
                    <button onclick="closeRedflagHistoryModal()"
                            class="absolute top-4 right-4 text-white hover:text-gray-200 text-xl">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="p-6 overflow-y-auto max-h-[calc(90vh-120px)]">
                    <!-- Summary -->
                    <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                        <h4 class="font-semibold text-gray-900 mb-2">Summary</h4>
                        <div class="grid grid-cols-3 gap-4 text-center">
                            <div>
                                <div class="text-2xl font-bold text-blue-600">${summary.total_completed}</div>
                                <div class="text-sm text-gray-600">Total Projects</div>
                            </div>
                            <div>
                                <div class="text-2xl font-bold text-red-600">${summary.count}</div>
                                <div class="text-sm text-gray-600">Red-flagged</div>
                            </div>
                            <div>
                                <div class="text-2xl font-bold text-orange-600">${summary.rate}%</div>
                                <div class="text-sm text-gray-600">Flag Rate</div>
                            </div>
                        </div>
                    </div>

                    <!-- Red-flagged Projects List -->
                    <h4 class="font-semibold text-gray-900 mb-4">Red-flagged Projects</h4>
                    ${redflaggedProjects.length > 0 ? `
                        <div class="space-y-4">
                            ${redflaggedProjects.map(project => `
                                <div class="border border-red-200 bg-red-50 rounded-lg p-4">
                                    <div class="flex justify-between items-start mb-2">
                                        <h5 class="font-medium text-gray-900">${project.project_title}</h5>
                                        <span class="text-xs px-2 py-1 bg-red-100 text-red-800 rounded-full">
                                            ${project.redflagged_at}
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-700 mb-2">${project.project_description.substring(0, 100)}${project.project_description.length > 100 ? '...' : ''}</p>
                                    <div class="text-sm text-red-700">
                                        <strong>Flag reason:</strong> ${project.redflag_reason || 'No specific reason provided'}
                                    </div>
                                    <div class="text-xs text-gray-500 mt-2">
                                        Flagged by: ${project.redflagged_by_name} | Recruiter: ${project.recruiter_name}
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                    ` : `
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-check-circle text-4xl text-green-500 mb-4"></i>
                            <p>No red-flagged projects found.</p>
                        </div>
                    `}
                </div>

                <div class="bg-gray-50 px-6 py-4 border-t flex justify-end">
                    <button onclick="closeRedflagHistoryModal()"
                            class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
    `;

    document.body.insertAdjacentHTML('beforeend', modalHtml);
    document.body.style.overflow = 'hidden';
}

function closeRedflagHistoryModal() {
    const modal = document.getElementById('redflagHistoryModal');
    if (modal) {
        modal.remove();
    }
    document.body.style.overflow = '';
}
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
    background: linear-gradient(135deg, #2563eb, #6366f1);
    border-color: #2563eb;
    color: white;
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
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
</style>
@endsection
