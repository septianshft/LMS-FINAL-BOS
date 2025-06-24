@extends('layout.template.mainTemplate')

@section('title', 'Manajemen Proyek - Admin')
@section('container')
<div class="container mx-auto px-6 py-8">
    <!-- Success/Error Messages -->
    @if(session('success'))
        <div id="success-notification" class="bg-green-100 border border-green-400 text-green-700 px-6 py-4 rounded-lg mb-6 flex items-center justify-between shadow-sm">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-3 text-lg"></i>
                <div>
                    <h4 class="font-semibold">Berhasil!</h4>
                    <p>{{ session('success') }}</p>
                </div>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-green-500 hover:text-green-700 ml-4">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div id="error-notification" class="bg-red-100 border border-red-400 text-red-700 px-6 py-4 rounded-lg mb-6 flex items-center justify-between shadow-sm">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle mr-3 text-lg"></i>
                <div>
                    <h4 class="font-semibold">Error!</h4>
                    <p>{{ session('error') }}</p>
                </div>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700 ml-4">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Manajemen Proyek</h1>
            <p class="text-gray-600 mt-2">Tinjau dan kelola permintaan proyek dan penugasan</p>
        </div>
        <div class="flex space-x-3">
            <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-sm font-medium">
                {{ $pendingCount }} Menunggu Persetujuan
            </span>
            <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium">
                {{ $activeCount }} Proyek Aktif
            </span>
            <span class="bg-purple-100 text-purple-800 px-3 py-1 rounded-full text-sm font-medium">
                {{ $closureRequestCount ?? 0 }} Permintaan Penutupan
            </span>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="mb-6">
        <nav class="flex space-x-8" aria-label="Tabs">
            <a href="{{ route('admin.projects.index') }}"
               class="@if(request('status') == '' || !request('status')) text-blue-600 border-blue-500 @else text-gray-500 border-transparent hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                Semua Proyek
            </a>
            <a href="{{ route('admin.projects.index', ['status' => 'pending']) }}"
               class="@if(request('status') == 'pending') text-blue-600 border-blue-500 @else text-gray-500 border-transparent hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                Menunggu Persetujuan
            </a>
            <a href="{{ route('admin.projects.index', ['status' => 'active']) }}"
               class="@if(request('status') == 'active') text-blue-600 border-blue-500 @else text-gray-500 border-transparent hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                Proyek Aktif
            </a>
            <a href="{{ route('admin.projects.index', ['status' => 'extensions']) }}"
               class="@if(request('status') == 'extensions') text-blue-600 border-blue-500 @else text-gray-500 border-transparent hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                Permintaan Perpanjangan Proyek
            </a>
            <a href="{{ route('admin.projects.index', ['status' => 'closure_requested']) }}"
               class="@if(request('status') == 'closure_requested') text-blue-600 border-blue-500 @else text-gray-500 border-transparent hover:text-gray-700 hover:border-gray-300 @endif whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                Permintaan Penutupan Proyek
            </a>
        </nav>
    </div>

    <!-- Projects Grid -->
    <div class="grid gap-6 lg:grid-cols-2 xl:grid-cols-3">
        @forelse($projects as $project)
            <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-200">
                <div class="p-6">
                    <!-- Project Header -->
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ $project->title }}</h3>
                            <p class="text-sm text-gray-600">
                                @if($project->recruiter)
                                    {{ $project->recruiter->company_name ?: ($project->recruiter->user->name ?? 'Perusahaan Tidak Dikenal') }}
                                @else
                                    Perusahaan Tidak Dikenal
                                @endif
                            </p>
                        </div>
                        <span class="@if($project->status === 'pending_admin') bg-yellow-100 text-yellow-800 @elseif($project->status === 'approved') bg-green-100 text-green-800 @elseif($project->status === 'active') bg-blue-100 text-blue-800 @elseif($project->status === 'completed') bg-gray-100 text-gray-800 @elseif($project->status === 'cancelled') bg-red-100 text-red-800 @elseif($project->status === 'overdue') bg-red-100 text-red-800 @elseif($project->status === 'closure_requested') bg-purple-100 text-purple-800 @else bg-gray-100 text-gray-800 @endif px-2 py-1 rounded-full text-xs font-medium">
                            {{ ucwords(str_replace('_', ' ', $project->status)) }}
                        </span>
                    </div>

                    <!-- Project Details -->
                    <div class="space-y-2 mb-4">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Anggaran:</span>
                            <span class="font-medium">
                                @if($project->overall_budget_min && $project->overall_budget_max)
                                    Rp {{ number_format($project->overall_budget_min, 0, ',', '.') }} - Rp {{ number_format($project->overall_budget_max, 0, ',', '.') }}
                                @elseif($project->overall_budget_min)
                                    Dari Rp {{ number_format($project->overall_budget_min, 0, ',', '.') }}
                                @elseif($project->overall_budget_max)
                                    Hingga Rp {{ number_format($project->overall_budget_max, 0, ',', '.') }}
                                @else
                                    Tidak ditentukan
                                @endif
                            </span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Durasi:</span>
                            <span class="font-medium">
                                @if($project->estimated_duration_days)
                                    {{ $project->estimated_duration_days }} hari
                                @else
                                    TBD
                                @endif
                            </span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Industri:</span>
                            <span class="font-medium">{{ $project->industry ?: 'Tidak ditentukan' }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Talent yang Ditugaskan:</span>
                            <span class="font-medium">{{ $project->assignments->count() }}</span>
                        </div>
                    </div>

                    <!-- Timeline -->
                    <div class="mb-4">
                        <div class="flex justify-between text-xs text-gray-600 mb-1">
                            <span>Mulai: {{ $project->expected_start_date ? $project->expected_start_date->format('M j, Y') : 'TBD' }}</span>
                            <span>Selesai: {{ $project->expected_end_date ? $project->expected_end_date->format('M j, Y') : 'TBD' }}</span>
                        </div>
                        @if($project->expected_start_date && $project->expected_end_date)
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $project->getProgressPercentage() }}%"></div>
                            </div>
                        @endif
                    </div>

                    <!-- Extension Notice -->
                    @if($project->pendingExtensions->count() > 0)
                        <div class="bg-orange-50 border border-orange-200 rounded-lg p-3 mb-4">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 text-orange-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                                <span class="text-sm text-orange-700 font-medium">
                                    {{ $project->pendingExtensions->count() }} permintaan perpanjangan tertunda
                                </span>
                            </div>
                        </div>
                    @endif

                    <!-- Closure Request Notice -->
                    @if($project->status === 'closure_requested')
                        <div class="bg-purple-50 border border-purple-200 rounded-lg p-3 mb-4">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 text-purple-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                                <span class="text-sm text-purple-700 font-medium">
                                    Permintaan penutupan menunggu persetujuan Anda
                                </span>
                            </div>
                            @if($project->closure_reason)
                                <p class="text-purple-600 text-xs mt-2">
                                    <strong>Alasan:</strong> {{ Str::limit($project->closure_reason, 100) }}
                                </p>
                            @endif
                        </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="flex space-x-2">
                        @if($project->status === 'pending_admin')
                            <button onclick="window.location.href='{{ route('admin.projects.show', $project) }}'"
                                    class="flex-1 bg-green-600 text-white py-2 px-4 rounded-lg text-sm font-medium hover:bg-green-700 transition-colors">
                                Tinjau
                            </button>
                        @else
                            <a href="{{ route('admin.projects.show', $project) }}"
                               class="flex-1 bg-blue-600 text-white text-center py-2 px-4 rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
                                Lihat Detail
                            </a>
                        @endif

                        @if($project->pendingExtensions->count() > 0)
                            <button onclick="openExtensionModal({{ $project->id }})"
                                    class="flex-1 bg-orange-600 text-white py-2 px-4 rounded-lg text-sm font-medium hover:bg-orange-700 transition-colors">
                                Perpanjangan
                            </button>
                        @endif

                        @if($project->status === 'closure_requested')
                            <button onclick="openClosureModal({{ $project->id }})"
                                    class="flex-1 bg-purple-600 text-white py-2 px-4 rounded-lg text-sm font-medium hover:bg-purple-700 transition-colors">
                                Tinjau Penutupan
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full">
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada proyek ditemukan</h3>
                    <p class="mt-1 text-sm text-gray-500">Tidak ada proyek yang sesuai dengan kriteria filter Anda saat ini.</p>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($projects->hasPages())
        <div class="mt-8">
            {{ $projects->links() }}
        </div>
    @endif
</div>

<!-- Project Approval Modal -->
<div id="approvalModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Tinjau Proyek</h3>
            <form id="approvalForm" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Keputusan</label>
                    <select name="status" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Pilih keputusan</option>
                        <option value="approved">Setujui Proyek</option>
                        <option value="rejected">Tolak Proyek</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Komentar</label>
                    <textarea name="admin_notes" rows="3"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="Tambahkan komentar atau persyaratan..."></textarea>
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeApprovalModal()"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Kirim Tinjauan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Extension Review Modal -->
<div id="extensionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Tinjau Permintaan Perpanjangan</h3>
            <div id="extensionContent">
                <!-- Dynamic content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Closure Review Modal -->
<div id="closureModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 shadow-2xl rounded-2xl bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
                <h3 class="text-2xl font-bold text-gray-900 flex items-center">
                    <i class="fas fa-times-circle text-purple-600 mr-3"></i>
                    Tinjau Permintaan Penutupan
                </h3>
                <button onclick="closeClosureModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <div id="closureContent">
                <!-- Dynamic content will be loaded here -->
                <div class="text-center py-8">
                    <i class="fas fa-spinner fa-spin text-3xl text-purple-600 mb-4"></i>
                    <p class="text-gray-600">Memuat detail permintaan penutupan...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function openApprovalModal(projectId) {
    document.getElementById('approvalModal').classList.remove('hidden');
    document.getElementById('approvalForm').action = `/admin/projects/${projectId}/approve`;
}

function closeApprovalModal() {
    document.getElementById('approvalModal').classList.add('hidden');
}

function openExtensionModal(projectId) {
    document.getElementById('extensionModal').classList.remove('hidden');
    // Load extension requests via AJAX
    fetch(`/admin/projects/${projectId}/extensions`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('extensionContent').innerHTML = html;
        });
}

function closeExtensionModal() {
    document.getElementById('extensionModal').classList.add('hidden');
}

function openClosureModal(projectId) {
    const modal = document.getElementById('closureModal');
    modal.classList.remove('hidden');

    // Load closure request details via AJAX
    fetch(`/admin/projects/${projectId}/closure-details`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayClosureDetails(data.project, projectId);
            } else {
                document.getElementById('closureContent').innerHTML = `
                    <div class="text-center py-8">
                        <i class="fas fa-exclamation-triangle text-3xl text-red-600 mb-4"></i>
                        <p class="text-red-600">Error memuat detail permintaan penutupan</p>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('closureContent').innerHTML = `
                <div class="text-center py-8">
                    <i class="fas fa-exclamation-triangle text-3xl text-red-600 mb-4"></i>
                    <p class="text-red-600">Error memuat detail permintaan penutupan</p>
                </div>
            `;
        });
}

function closeClosureModal() {
    document.getElementById('closureModal').classList.add('hidden');
}

function displayClosureDetails(project, projectId) {
    const content = `
        <div class="space-y-6">
            <!-- Project Information -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h4 class="font-semibold text-gray-900 mb-3">Informasi Proyek</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="font-medium text-gray-700">Judul:</span>
                        <span class="text-gray-900">${project.title}</span>
                    </div>
                    <div>
                        <span class="font-medium text-gray-700">Status:</span>
                        <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded text-xs">${project.status.replace('_', ' ')}</span>
                    </div>
                    <div>
                        <span class="font-medium text-gray-700">Tanggal Mulai:</span>
                        <span class="text-gray-900">${project.expected_start_date || 'N/A'}</span>
                    </div>
                    <div>
                        <span class="font-medium text-gray-700">Tanggal Selesai:</span>
                        <span class="text-gray-900">${project.expected_end_date || 'N/A'}</span>
                    </div>
                    <div>
                        <span class="font-medium text-gray-700">Talent yang Ditugaskan:</span>
                        <span class="text-gray-900">${project.assignments_count || 0}</span>
                    </div>
                    <div>
                        <span class="font-medium text-gray-700">Perekrut:</span>
                        <span class="text-gray-900">${project.recruiter_name || 'Tidak Diketahui'}</span>
                    </div>
                </div>
            </div>

            <!-- Closure Request Details -->
            <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                <h4 class="font-semibold text-purple-900 mb-3 flex items-center">
                    <i class="fas fa-times-circle mr-2"></i>
                    Detail Permintaan Penutupan
                </h4>
                <div class="space-y-3">
                    <div>
                        <span class="font-medium text-purple-700">Diminta Pada:</span>
                        <span class="text-purple-900">${project.closure_requested_at || 'N/A'}</span>
                    </div>
                    ${project.closure_reason ? `
                        <div>
                            <span class="font-medium text-purple-700">Alasan:</span>
                            <p class="text-purple-900 mt-1 bg-white p-3 rounded border">${project.closure_reason}</p>
                        </div>
                    ` : ''}
                </div>
            </div>

            <!-- Decision Form -->
            <form id="closureDecisionForm" method="POST" action="/projects/${projectId}/approve-closure">
                <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').getAttribute('content')}">

                <div class="bg-white border border-gray-200 rounded-lg p-4">
                    <h4 class="font-semibold text-gray-900 mb-4">Keputusan Admin</h4>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Keputusan *</label>
                            <select name="decision" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500">
                                <option value="">Pilih keputusan Anda</option>
                                <option value="approved">Setujui Penutupan</option>
                                <option value="rejected">Tolak Permintaan Penutupan</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Admin</label>
                            <textarea name="admin_notes" rows="3"
                                      class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500"
                                      placeholder="Tambahkan komentar tentang keputusan Anda..."></textarea>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end space-x-4 mt-6 pt-6 border-t border-gray-200">
                    <button type="button" onclick="closeClosureModal()"
                            class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-6 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-colors">
                        <i class="fas fa-check mr-2"></i>Kirim Keputusan
                    </button>
                </div>
            </form>
        </div>
    `;

    document.getElementById('closureContent').innerHTML = content;

    // Handle form submission
    document.getElementById('closureDecisionForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const decision = this.decision.value;
        if (!decision) {
            alert('Silakan pilih keputusan.');
            return;
        }

        // Update form action based on decision
        if (decision === 'approved') {
            this.action = `/projects/${projectId}/approve-closure`;
        } else {
            this.action = `/projects/${projectId}/reject-closure`;
        }

        this.submit();
    });
}

// Close modals when clicking outside
window.onclick = function(event) {
    const approvalModal = document.getElementById('approvalModal');
    const extensionModal = document.getElementById('extensionModal');
    const closureModal = document.getElementById('closureModal');

    if (event.target === approvalModal) {
        closeApprovalModal();
    }
    if (event.target === extensionModal) {
        closeExtensionModal();
    }
    if (event.target === closureModal) {
        closeClosureModal();
    }
}
</script>
@endsection
