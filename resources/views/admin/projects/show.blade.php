@extends('layout.template.mainTemplate')

@section('title', 'Detail Proyek - Admin')
@section('container')
<div class="container mx-auto px-6 py-8">
    <!-- Header -->
    <div class="flex justify-between items-start mb-8">
        <div>
            <div class="flex items-center space-x-4 mb-2">
                <a href="{{ route('admin.projects.index') }}" class="text-blue-600 hover:text-blue-800">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <h1 class="text-3xl font-bold text-gray-900">{{ $project->title }}</h1>                <span class="@if($project->status === 'pending_admin') bg-yellow-100 text-yellow-800 @elseif($project->status === 'approved') bg-green-100 text-green-800 @elseif($project->status === 'active') bg-blue-100 text-blue-800 @elseif($project->status === 'completed') bg-gray-100 text-gray-800 @else bg-red-100 text-red-800 @endif px-3 py-1 rounded-full text-sm font-medium">
                    {{ ucwords(str_replace('_', ' ', $project->status)) }}
                </span>
            </div>
            <p class="text-gray-600">{{ $project->recruiter->company_name ?? 'Perusahaan Tidak Diketahui' }}</p>
        </div>

        @if($project->status === 'pending_admin')
            <div class="flex space-x-3">
                <button onclick="openApprovalModal()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                    Setujui Proyek
                </button>
                <button onclick="openRejectionModal()" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors">
                    Tolak Proyek
                </button>
            </div>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Project Details -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Ringkasan Proyek</h2>
                <div class="prose max-w-none">
                    <p class="text-gray-700 mb-4">{{ $project->description }}</p>
                </div>                <div class="grid grid-cols-2 gap-4 mt-6">
                    <div>
                        <h3 class="font-medium text-gray-900 mb-2">Detail Proyek</h3>
                        <dl class="space-y-2">
                            <div class="flex justify-between">
                                <dt class="text-gray-600">Rentang Anggaran:</dt>
                                <dd class="font-medium">Rp {{ number_format($project->overall_budget_min ?? 0, 0, ',', '.') }} - Rp {{ number_format($project->overall_budget_max ?? 0, 0, ',', '.') }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-600">Durasi:</dt>
                                <dd class="font-medium">{{ $project->estimated_duration_days ?? 0 }} hari</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-600">Industri:</dt>
                                <dd class="font-medium">{{ $project->industry ?? 'Tidak ditentukan' }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-600">Status:</dt>
                                <dd class="font-medium">{{ ucfirst($project->status) }}</dd>
                            </div>
                        </dl>
                    </div>
                    <div>
                        <h3 class="font-medium text-gray-900 mb-2">Jadwal</h3>
                        <dl class="space-y-2">
                            <div class="flex justify-between">
                                <dt class="text-gray-600">Dibuat:</dt>
                                <dd class="font-medium">{{ $project->created_at->format('M j, Y') }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-600">Tanggal Mulai:</dt>
                                <dd class="font-medium">{{ $project->expected_start_date ? $project->expected_start_date->format('M j, Y') : 'Belum Ditentukan' }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-600">Tanggal Selesai:</dt>
                                <dd class="font-medium">{{ $project->expected_end_date ? $project->expected_end_date->format('M j, Y') : 'Belum Ditentukan' }}</dd>
                            </div>
                            @if($project->admin_approved_at)
                            <div class="flex justify-between">
                                <dt class="text-gray-600">Disetujui:</dt>
                                <dd class="font-medium">{{ $project->admin_approved_at->format('M j, Y') }}</dd>
                            </div>
                            @endif
                        </dl>
                    </div>
                </div>

                @if($project->general_requirements)
                    <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                        <h3 class="font-medium text-gray-900 mb-2">Persyaratan</h3>
                        <p class="text-gray-700">{{ $project->general_requirements }}</p>
                    </div>
                @endif
            </div>

            <!-- Project Assignments -->            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold text-gray-900">Penugasan Talenta</h2>
                    <span class="text-sm text-gray-600">{{ $project->assignments->count() }} ditugaskan</span>
                </div>

                @if($project->assignments->count() > 0)
                    <div class="space-y-4">
                        @foreach($project->assignments as $assignment)
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <h3 class="font-medium text-gray-900">{{ $assignment->talent->user->name }}</h3>
                                        <p class="text-sm text-gray-600">{{ $assignment->talent->user->email }}</p>
                                        @if($assignment->specific_role)
                                            <p class="text-sm text-gray-600 mt-1"><strong>Peran:</strong> {{ $assignment->specific_role }}</p>
                                        @endif
                                        <div class="mt-2 flex items-center space-x-2">
                                            <span class="@if($assignment->status === 'assigned') bg-yellow-100 text-yellow-800 @elseif($assignment->status === 'accepted') bg-green-100 text-green-800 @elseif($assignment->status === 'declined') bg-red-100 text-red-800 @elseif($assignment->status === 'active') bg-blue-100 text-blue-800 @else bg-gray-100 text-gray-800 @endif px-2 py-1 rounded-full text-xs font-medium">
                                                {{ ucfirst($assignment->status) }}
                                            </span>
                                            @if($assignment->status === 'assigned' && in_array($project->status, ['approved', 'active']))
                                                <button onclick="onboardTalent({{ $assignment->id }})"
                                                        class="bg-green-600 text-white px-3 py-1 rounded text-xs hover:bg-green-700 transition-colors">
                                                    Onboard Talenta
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm text-gray-600">Ditugaskan: {{ $assignment->created_at->format('M j, Y') }}</div>
                                        @if($assignment->talent_accepted_at)
                                            <div class="text-sm text-gray-600">Diterima: {{ $assignment->talent_accepted_at->format('M j, Y') }}</div>
                                        @endif
                                    </div>
                                </div>

                                @if($assignment->specific_requirements)
                                    <div class="mt-3 p-3 bg-gray-50 rounded text-sm">
                                        <strong>Persyaratan:</strong> {{ $assignment->specific_requirements }}
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada talenta yang ditugaskan</h3>
                        <p class="mt-1 text-sm text-gray-500">Proyek ini belum memiliki talenta yang ditugaskan.</p>
                    </div>
                @endif
            </div>

            <!-- Extension Requests -->
            @if($project->extensions->count() > 0)
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Permintaan Perpanjangan</h2>
                    <div class="space-y-4">
                        @foreach($project->extensions as $extension)
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex justify-between items-start mb-3">
                                    <div>
                                        <h3 class="font-medium text-gray-900">Permintaan Perpanjangan</h3>
                                        <p class="text-sm text-gray-600">Diminta oleh: {{ $extension->requester->name }}</p>
                                    </div>
                                    <span class="@if($extension->status === 'pending') bg-yellow-100 text-yellow-800 @elseif($extension->status === 'approved') bg-green-100 text-green-800 @else bg-red-100 text-red-800 @endif px-2 py-1 rounded-full text-xs font-medium">
                                        {{ ucfirst($extension->status) }}
                                    </span>
                                </div>
                                  <div class="grid grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <span class="text-gray-600">Hari Perpanjangan:</span>
                                        <span class="font-medium ml-2">{{ $extension->extension_days }} hari</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-600">Tanggal Selesai Baru:</span>
                                        <span class="font-medium ml-2">{{ $extension->new_end_date->format('M j, Y') }}</span>
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <p class="text-sm text-gray-700"><strong>Justifikasi:</strong> {{ $extension->justification }}</p>
                                </div>

                                @if($extension->status === 'pending')
                                    <div class="mt-4 flex space-x-3">
                                        <button onclick="reviewExtension({{ $extension->id }}, 'approved')"
                                                class="bg-green-600 text-white px-4 py-2 rounded text-sm hover:bg-green-700 transition-colors">
                                            Setujui
                                        </button>
                                        <button onclick="reviewExtension({{ $extension->id }}, 'rejected')"
                                                class="bg-red-600 text-white px-4 py-2 rounded text-sm hover:bg-red-700 transition-colors">
                                            Tolak
                                        </button>
                                    </div>
                                @endif
                                  @if($extension->review_notes)
                                    <div class="mt-3 p-3 bg-gray-50 rounded text-sm">
                                        <strong>Respon Admin:</strong> {{ $extension->review_notes }}
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Recruiter Info -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Perekrut</h3>
                <div class="space-y-3">
                    <div>
                        <span class="text-gray-600">Perusahaan:</span>
                        <p class="font-medium">{{ $project->recruiter->company_name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <span class="text-gray-600">Kontak:</span>
                        <p class="font-medium">{{ $project->recruiter->user->name }}</p>
                        <p class="text-sm text-gray-600">{{ $project->recruiter->user->email }}</p>
                    </div>
                    @if($project->recruiter->phone)
                        <div>
                            <span class="text-gray-600">Telepon:</span>
                            <p class="font-medium">{{ $project->recruiter->phone }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Project Stats -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Statistik Proyek</h3>                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Rentang Anggaran:</span>
                        <span class="font-medium">Rp {{ number_format($project->overall_budget_min ?? 0, 0, ',', '.') }} - Rp {{ number_format($project->overall_budget_max ?? 0, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Penugasan:</span>
                        <span class="font-medium">{{ $project->assignments->count() }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Diterima:</span>
                        <span class="font-medium">{{ $project->assignments->where('status', 'accepted')->count() }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Perpanjangan:</span>
                        <span class="font-medium">{{ $project->extensions->count() }}</span>
                    </div>
                    @if($project->expected_start_date && $project->expected_end_date)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Progres:</span>
                            <span class="font-medium">{{ $project->getProgressPercentage() }}%</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Timeline Events -->
            @if($project->timelineEvents->count() > 0)
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Timeline Peristiwa</h3>
                    <div class="space-y-3">
                        @foreach($project->timelineEvents->take(5) as $event)
                            <div class="flex">
                                <div class="flex-shrink-0 w-2 h-2 bg-blue-600 rounded-full mt-2"></div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900">{{ $event->event_type }}</p>
                                    <p class="text-xs text-gray-600">{{ $event->created_at->format('M j, Y g:i A') }}</p>
                                    @if($event->description)
                                        <p class="text-xs text-gray-700 mt-1">{{ $event->description }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Approval Modal -->
<div id="approvalModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Setujui Proyek</h3>
            <form method="POST" action="{{ route('admin.projects.approve', $project) }}">
                @csrf
                @method('PATCH')
                <input type="hidden" name="status" value="approved">

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Persetujuan</label>
                    <textarea name="admin_notes" rows="3"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="Tambahkan catatan persetujuan atau persyaratan..."></textarea>
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeApprovalModal()"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        Setujui Proyek
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Rejection Modal -->
<div id="rejectionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Tolak Proyek</h3>
            <form method="POST" action="{{ route('admin.projects.approve', $project) }}">
                @csrf
                @method('PATCH')
                <input type="hidden" name="status" value="rejected">

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Alasan Penolakan</label>
                    <textarea name="admin_notes" rows="3" required
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="Mohon berikan alasan penolakan..."></textarea>
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeRejectionModal()"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                        Tolak Proyek
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openApprovalModal() {
    document.getElementById('approvalModal').classList.remove('hidden');
}

function closeApprovalModal() {
    document.getElementById('approvalModal').classList.add('hidden');
}

function openRejectionModal() {
    document.getElementById('rejectionModal').classList.remove('hidden');
}

function closeRejectionModal() {
    document.getElementById('rejectionModal').classList.add('hidden');
}

function reviewExtension(extensionId, status) {
    if (confirm(`Apakah Anda yakin ingin ${status === 'approved' ? 'menyetujui' : 'menolak'} permintaan perpanjangan ini?`)) {
        fetch(`/admin/projects/extensions/${extensionId}/review`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ status: status })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Terjadi kesalahan saat memperbarui permintaan perpanjangan');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat memperbarui permintaan perpanjangan');
        });
    }
}

// Close modals when clicking outside
window.onclick = function(event) {
    const approvalModal = document.getElementById('approvalModal');
    const rejectionModal = document.getElementById('rejectionModal');

    if (event.target === approvalModal) {
        closeApprovalModal();
    }
    if (event.target === rejectionModal) {
        closeRejectionModal();
    }
}
</script>
@endsection
