@extends('layout.template.mainTemplate')

@section('title', 'Detail Proyek')
@section('container')
<div class="container mx-auto px-4 py-8">
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
                    <h4 class="font-semibold">Kesalahan!</h4>
                    <p>{{ session('error') }}</p>
                </div>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700 ml-4">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    <!-- Project Header -->
    <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden mb-8">
        <div class="p-6">
            <!-- Header Navigation -->
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center">
                    <a href="{{ route('projects.index') }}" class="text-blue-600 hover:text-blue-800 mr-4">
                        <i class="fas fa-arrow-left text-lg"></i>
                    </a>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">{{ $viewData->project->title }}</h1>
                        <p class="text-gray-600 mt-1">
                            Dibuat
                            @if($viewData->project->created_at instanceof \Carbon\Carbon)
                                {{ $viewData->project->created_at->diffForHumans() }}
                            @elseif($viewData->project->created_at)
                                {{ \Carbon\Carbon::parse($viewData->project->created_at)->diffForHumans() }}
                            @else
                                baru-baru ini
                            @endif
                        </p>
                    </div>
                </div>

                <!-- Status Badge -->
                <span class="px-4 py-2 rounded-full text-sm font-medium {{ $viewData->getStatusBadgeClass() }}">
                    {{ $viewData->getFormattedStatus() }}
                </span>
            </div>

            <!-- Project Details Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="space-y-4">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Industri</h3>
                        <p class="text-lg text-gray-900">{{ $viewData->project->industry ?? 'Tidak ditentukan' }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Durasi</h3>
                        <p class="text-lg text-gray-900">{{ $viewData->project->estimated_duration_days }} hari</p>
                    </div>
                </div>
                  <div class="space-y-4">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Tanggal Mulai</h3>
                        <p class="text-lg text-gray-900">
                            {{ $viewData->project->expected_start_date ? $viewData->project->expected_start_date->format('M d, Y') : 'Tidak ditetapkan' }}
                        </p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Tanggal Selesai</h3>
                        <p class="text-lg text-gray-900">
                            {{ $viewData->project->expected_end_date ? $viewData->project->expected_end_date->format('M d, Y') : 'Tidak ditetapkan' }}
                        </p>
                    </div>
                </div>

                <div class="space-y-4">
                    @if($viewData->project->overall_budget_min || $viewData->project->overall_budget_max)
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Rentang Budget</h3>
                        <p class="text-lg text-gray-900">
                            @if($viewData->project->overall_budget_min && $viewData->project->overall_budget_max)
                                Rp {{ number_format($viewData->project->overall_budget_min, 0, ',', '.') }} - Rp {{ number_format($viewData->project->overall_budget_max, 0, ',', '.') }}
                            @elseif($viewData->project->overall_budget_min)
                                Dari Rp {{ number_format($viewData->project->overall_budget_min, 0, ',', '.') }}
                            @else
                                Hingga Rp {{ number_format($viewData->project->overall_budget_max, 0, ',', '.') }}
                            @endif
                        </p>
                    </div>
                    @endif

                    <div>
                        <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Interaksi Talenta</h3>
                        <p class="text-lg text-gray-900">{{ $viewData->project->assignments->count() + $viewData->project->talentRequests->count() }} total interaksi</p>
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Deskripsi Proyek</h3>
                <p class="text-gray-700 leading-relaxed">{{ $viewData->project->description }}</p>
            </div>

            <!-- General Requirements -->
            @if($viewData->project->general_requirements)
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Persyaratan Umum</h3>
                <p class="text-gray-700 leading-relaxed">{{ $viewData->project->general_requirements }}</p>
            </div>
            @endif

            <!-- Action Buttons -->
            <div class="flex flex-wrap gap-3">
                @php
                    $hasPendingExtensions = $viewData->project->extensions()->where('status', 'pending')->exists();
                @endphp

                @if($viewData->canEdit() && $viewData->project->status === 'pending_admin')
                    <a href="{{ route('projects.edit', $viewData->project) }}"
                       class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition duration-200">
                        <i class="fas fa-edit mr-2"></i>Edit Proyek
                    </a>
                @endif

                @if($viewData->canRequestTalents())
                    <button onclick="showProjectTalentModal()"
                            class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-medium transition duration-200">
                        <i class="fas fa-user-plus mr-2"></i>Ajukan Permintaan Talenta
                    </button>
                @endif

                @if($viewData->canRequestExtension() && !$hasPendingExtensions)
                    <button onclick="showExtensionModal()"
                            class="bg-orange-600 hover:bg-orange-700 text-white px-6 py-3 rounded-lg font-medium transition duration-200">
                        <i class="fas fa-clock mr-2"></i>Minta Perpanjangan Proyek
                    </button>
                @endif

                @if($viewData->canRequestClosure() && $viewData->project->status !== 'closure_requested')
                    <button onclick="showClosureRequestModal()"
                            class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg font-medium transition duration-200">
                        <i class="fas fa-times mr-2"></i>Minta Penutupan Proyek
                    </button>
                @endif
            </div>
        </div>
    </div>

    <!-- Tabs Navigation -->
    <div class="mb-8">
        <nav class="flex space-x-8" aria-label="Tabs">
            <button onclick="showTab('assignments')"
                    class="tab-button active border-b-2 border-blue-500 py-2 px-1 text-sm font-medium text-blue-600"
                    id="assignments-tab">
                Manajemen Talenta ({{ $viewData->getTabCounts()['assignments'] }})
            </button>
            <button onclick="showTab('timeline')"
                    class="tab-button border-b-2 border-transparent py-2 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300"
                    id="timeline-tab">
                Timeline Peristiwa
            </button>
            @if($viewData->getTabCounts()['extensions'] > 0)
            <button onclick="showTab('extensions')"
                    class="tab-button border-b-2 border-transparent py-2 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300"
                    id="extensions-tab">
                Perpanjangan ({{ $viewData->getTabCounts()['extensions'] }})
            </button>
            @endif
        </nav>
    </div>

    <!-- Assignments Tab -->
    <div id="assignments-content" class="tab-content">
        <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-semibold text-gray-900">Manajemen Talenta</h2>
                    @if($viewData->canRequestTalents())
                        <div class="flex space-x-2">
                            <button onclick="showProjectTalentModal()"
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition duration-200">
                                <i class="fas fa-plus mr-2"></i>Tambah Talenta
                            </button>
                        </div>
                    @endif
                </div>
                @if($viewData->hasInteractions())
                    <div class="space-y-4">
                        @foreach($viewData->talentInteractions as $item)
                            <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow duration-200">
                                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between space-y-3 sm:space-y-0">
                                    <div class="flex items-center space-x-3 flex-1 min-w-0">
                                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-user text-blue-600"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h4 class="font-semibold truncate">
                                @if($item['talent_data'])
                                    <button type="button" 
                                            onclick="viewTalentDetailsInModal({{ json_encode($item['talent_data']) }})"
                                            class="text-blue-600 hover:text-blue-800 hover:underline transition-colors duration-200 text-left">
                                        {{ $item['name'] }}
                                    </button>
                                @else
                                    <span class="text-gray-900">{{ $item['name'] }}</span>
                                @endif
                            </h4>
                                            @if($item['role'])
                                                <p class="text-sm text-gray-600 truncate">{{ $item['role'] }}</p>
                                            @endif
                                            <div class="flex flex-wrap items-center gap-2 mt-2">
                                                <span class="px-2 py-1 text-xs rounded-full {{ $item['type'] === 'assignment' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                                    {{ $item['type'] === 'assignment' ? 'Penugasan' : 'Permintaan Talenta' }}
                                                </span>
                                                <span class="px-2 py-1 text-xs rounded-full {{ $talentService->getStatusColorClass($item['status']) }}">
                                                    {{ ucwords(str_replace('_', ' ', $item['status'])) }}
                                                </span>
                                                @if($item['is_overdue'])
                                                    <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">
                                                        <i class="fas fa-exclamation-triangle mr-1"></i>Terlambat
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Contract Duration Details -->
                                    @if($item['start_date'] || $item['end_date'] || $item['duration_text'])
                                        <div class="sm:ml-4 sm:text-right min-w-0 flex-shrink-0 bg-gray-50 rounded-lg p-3 sm:bg-transparent sm:p-0">
                                            <div class="text-xs font-medium text-gray-500 mb-2 sm:mb-1">Durasi Kontrak</div>

                                            @if($item['start_date'] && $item['end_date'])
                                                <div class="space-y-1 mb-2">
                                                    <div class="flex items-center text-xs text-gray-600">
                                                        <i class="fas fa-calendar-alt mr-2 w-3"></i>
                                                        <span class="font-medium">Mulai:</span>
                                                        <span class="ml-1">{{ \Carbon\Carbon::parse($item['start_date'])->format('M d, Y') }}</span>
                                                    </div>
                                                    <div class="flex items-center text-xs text-gray-600">
                                                        <i class="fas fa-calendar-check mr-2 w-3"></i>
                                                        <span class="font-medium">Selesai:</span>
                                                        <span class="ml-1">{{ \Carbon\Carbon::parse($item['end_date'])->format('M d, Y') }}</span>
                                                    </div>
                                                </div>

                                                @if($item['duration_text'])
                                                    <div class="text-sm font-semibold text-gray-800 mb-2">
                                                        <i class="fas fa-clock mr-1"></i>{{ $item['duration_text'] }}
                                                    </div>
                                                @endif

                                                @if($item['days_remaining'] !== null && $item['type'] === 'assignment' && $item['status'] === 'active')
                                                    @php
                                                        $progressPercent = $talentService->calculateProgress($item);
                                                    @endphp

                                                    <!-- Progress Bar -->
                                                    <div class="w-full sm:w-24 bg-gray-200 rounded-full h-2 mb-2">
                                                        <div class="{{ $talentService->getProgressColorClass($item) }} h-2 rounded-full transition-all duration-300" style="width: {{ $progressPercent }}%"></div>
                                                    </div>

                                                    <div class="text-xs font-medium {{ $talentService->getRemainingDaysColorClass($item) }}">
                                                        @if($item['is_overdue'])
                                                            <i class="fas fa-exclamation-triangle mr-1"></i>Terlambat
                                                        @elseif($item['days_remaining'] == 0)
                                                            <i class="fas fa-flag-checkered mr-1"></i>Jatuh tempo hari ini
                                                        @else
                                                            <i class="fas fa-hourglass-half mr-1"></i>{{ $item['days_remaining'] }} hari tersisa
                                                        @endif
                                                    </div>
                                                @endif
                                            @elseif($item['duration_text'])
                                                <div class="text-sm font-semibold text-gray-800 mb-1">
                                                    <i class="fas fa-clock mr-1"></i>{{ $item['duration_text'] }}
                                                </div>
                                                @if($item['start_date'])
                                                    <div class="text-xs text-gray-600">
                                                        <i class="fas fa-calendar-alt mr-1"></i>From {{ \Carbon\Carbon::parse($item['start_date'])->format('M d, Y') }}
                                                    </div>
                                                @endif
                                            @else
                                                <div class="text-xs text-gray-500">
                                                    <i class="fas fa-clock mr-1"></i>Duration not specified
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-users text-gray-400 text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Tidak Ada Interaksi Talenta</h3>
                        <p class="text-gray-600 mb-6">Mulai membangun tim Anda dengan meminta talenta untuk proyek ini.</p>
                        @if($viewData->canRequestTalents())
                            <button onclick="showProjectTalentModal()"
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition duration-200">
                                <i class="fas fa-plus mr-2"></i>Ajukan Permintaan Talenta
                            </button>
                        @elseif($viewData->project->status === 'pending_admin')
                            <p class="text-sm text-gray-500">Proyek perlu persetujuan admin sebelum Anda dapat meminta talenta.</p>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Timeline Tab -->
    <div id="timeline-content" class="tab-content hidden">
        <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden">
            <div class="p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Timeline Proyek</h2>

                @if($viewData->project->timelineEvents->count() > 0)
                    <div class="space-y-4">
                        @foreach($viewData->project->timelineEvents as $event)                            <div class="flex items-start space-x-4 pb-4 border-b border-gray-100 last:border-b-0">
                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-circle text-blue-600 text-xs"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-gray-900">{{ $event->event_description }}</p>                                    <div class="flex items-center space-x-4 mt-2 text-sm text-gray-500">
                                        <span>
                                            @if($event->created_at instanceof \Carbon\Carbon)
                                                {{ $event->created_at->format('M d, Y H:i') }}
                                            @elseif($event->created_at)
                                                {{ \Carbon\Carbon::parse($event->created_at)->format('M d, Y H:i') }}
                                            @else
                                                Tanggal tidak diketahui
                                            @endif
                                        </span>
                                        @if($event->triggeredBy)
                                            <span>oleh {{ $event->triggeredBy->name }}</span>
                                        @endif
                                        <span class="px-2 py-1 bg-gray-100 rounded text-xs">{{ $event->event_type }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <i class="fas fa-history text-gray-400 text-3xl mb-4"></i>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Tidak Ada Acara Timeline</h3>
                        <p class="text-gray-600">Acara timeline akan muncul di sini seiring dengan kemajuan proyek.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Extensions Tab -->
    @if($viewData->getTabCounts()['extensions'] > 0)
    <div id="extensions-content" class="tab-content hidden">
        <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden">
            <div class="p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Permintaan Perpanjangan</h2>

                <div class="space-y-4">
                    @foreach($viewData->project->extensions as $extension)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="font-semibold text-gray-900">Permintaan Perpanjangan</h3>
                                @php
                                    $extensionStatusColors = [
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'approved' => 'bg-green-100 text-green-800',
                                        'rejected' => 'bg-red-100 text-red-800'
                                    ];
                                    $extensionStatusClass = $extensionStatusColors[$extension->status] ?? 'bg-gray-100 text-gray-800';
                                @endphp
                                <span class="px-3 py-1 rounded-full text-xs font-medium {{ $extensionStatusClass }}">
                                    {{ ucfirst($extension->status) }}
                                </span>
                            </div>
                              <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                                <div>
                                    <span class="text-sm font-medium text-gray-500">Tanggal Selesai Asli:</span>
                                    <p class="text-gray-900">{{ $extension->old_end_date ? $extension->old_end_date->format('M d, Y') : 'Tidak ditetapkan' }}</p>
                                </div>
                                <div>
                                    <span class="text-sm font-medium text-gray-500">Tanggal Selesai yang Diminta:</span>
                                    <p class="text-gray-900">{{ $extension->new_end_date ? $extension->new_end_date->format('M d, Y') : 'Tidak ditetapkan' }}</p>
                                </div>
                            </div>

                            <div class="mb-3">
                                <span class="text-sm font-medium text-gray-500">Justifikasi:</span>
                                <p class="text-gray-700 mt-1">{{ $extension->justification }}</p>
                            </div>

                            @if($extension->review_notes)
                                <div class="bg-gray-50 rounded-lg p-3">
                                    <span class="text-sm font-medium text-gray-500">Catatan Review:</span>
                                    <p class="text-gray-700 mt-1">{{ $extension->review_notes }}</p>
                                </div>
                            @endif
                              <div class="text-sm text-gray-500 mt-3">                                Diminta {{ $extension->created_at ? $extension->created_at->diffForHumans() : 'Waktu tidak diketahui' }}
                                @if($extension->reviewed_at)
                                    • Direview {{ $extension->reviewed_at ? $extension->reviewed_at->diffForHumans() : 'Waktu tidak diketahui' }}
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Unified Talent Request Modal (Canonical Implementation from Dashboard) -->
@if($viewData->canRequestTalents())
<div class="modal fade" id="talentRequestModal" tabindex="-1" role="dialog" aria-labelledby="talentRequestModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content rounded-2xl border-0 shadow-2xl">
            <div class="modal-header bg-gradient-to-r from-green-600 to-emerald-700 text-white rounded-t-2xl border-0 p-6">
                <h5 class="modal-title text-xl font-bold flex items-center" id="talentRequestModalLabel">
                    <div class="w-8 h-8 bg-white bg-opacity-20 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-handshake text-white"></i>
                    </div>
                    Minta Talenta untuk Proyek: {{ $viewData->project->title }}
                </h5>
                <button type="button" class="text-white hover:text-gray-200 transition-colors duration-200" data-dismiss="modal" aria-label="Close">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <form id="talentRequestForm">
                @csrf
                <div class="modal-body p-8">
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6">
                        <div class="flex items-start">
                            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3 mt-0.5">
                                <i class="fas fa-info-circle text-blue-600"></i>
                            </div>
                            <div>
                                <h6 class="font-semibold text-blue-800 mb-1">Permintaan Penugasan Proyek</h6>
                                <p class="text-blue-700 text-sm">Permintaan Anda akan direview oleh Talent Admin yang akan mengoordinasikan penugasan talenta ke proyek ini. Ini menggunakan sistem yang sama dengan permintaan talenta reguler tetapi akan dikaitkan dengan proyek yang disetujui.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Hidden fields for project context -->
                    <input type="hidden" name="project_id" value="{{ $viewData->project->id }}">
                    <input type="hidden" name="is_project_assignment" value="1">

                    <div class="grid grid-cols-1 gap-8">
                        <div class="space-y-6">
                            <!-- Talent Selection (Project-specific Card Interface) -->
                            <div>
                                <div class="flex items-center justify-between mb-4">
                                    <label class="block text-sm font-semibold text-gray-700">
                                        Pilih Talenta <span class="text-red-500">*</span>
                                        <span class="text-xs font-normal text-gray-500">(Pilihan ganda diperbolehkan)</span>
                                    </label>
                                    <div class="flex space-x-2">                                    
                                        <button type="button" onclick="selectAllTalents(event)" class="text-xs text-blue-600 hover:text-blue-800 px-2 py-1 border border-blue-200 rounded">
                                        <i class="fas fa-check-double mr-1"></i>Pilih Semua
                                    </button>
                                    <button type="button" onclick="clearAllTalentSelections(event)" class="text-xs text-gray-600 hover:text-gray-800 px-2 py-1 border border-gray-200 rounded">
                                        <i class="fas fa-times mr-1"></i>Hapus Semua
                                    </button>
                                    </div>
                                </div>
                                @if($viewData->availableTalents->count() > 0)
                                    <!-- Search Bar -->
                                    <div class="mb-6">
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <i class="fas fa-search text-gray-400"></i>
                                            </div>
                                            <input type="text" 
                                                   id="talentSearchInput" 
                                                   class="block w-full pl-10 pr-10 py-3 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500" 
                                                   placeholder="Cari berdasarkan nama talenta atau keahlian..." 
                                                   onkeyup="searchTalents()">
                                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                                <button type="button" 
                                                        id="clearSearchBtn" 
                                                        class="text-gray-400 hover:text-gray-600 hidden" 
                                                        onclick="clearSearch()">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <!-- Search Results Info -->
                                        <div id="searchResultsInfo" class="hidden mt-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                            <p id="searchResultsText" class="text-blue-800 text-sm"></p>
                                        </div>
                                    </div>
                                    <!-- Talent Carousel Container -->
                                    <div class="talent-carousel-container relative">
                                        <!-- Navigation Buttons -->
                                        <button type="button" id="carouselPrevBtn" class="absolute left-0 top-1/2 transform -translate-y-1/2 z-10 bg-white border border-gray-300 rounded-full w-10 h-10 flex items-center justify-center text-gray-600 hover:bg-gray-50 hover:text-gray-800 shadow-lg transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                                            <i class="fas fa-chevron-left text-sm"></i>
                                        </button>
                                        <button type="button" id="carouselNextBtn" class="absolute right-0 top-1/2 transform -translate-y-1/2 z-10 bg-white border border-gray-300 rounded-full w-10 h-10 flex items-center justify-center text-gray-600 hover:bg-gray-50 hover:text-gray-800 shadow-lg transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                                            <i class="fas fa-chevron-right text-sm"></i>
                                        </button>

                                        <!-- Carousel Track -->
                                        <div class="talent-carousel-track overflow-hidden px-12">
                                            <div class="talent-carousel-content flex transition-transform duration-500 ease-in-out" id="talentCarousel">
                                                @foreach($viewData->availableTalents as $talent)
                                                    @php
                                                        $metrics = $talent->parsed_metrics;
                                                        $redflagSummary = $talent->redflag_summary;
                                                    @endphp
                                                    <div class="talent-carousel-slide flex-shrink-0 px-2">
                                                        <div class="talent-selection-card talent-card group relative bg-white border-2 border-gray-200 rounded-xl p-4 hover:border-blue-500 hover:shadow-lg transition-all duration-300 cursor-pointer w-80"
                                                             data-talent-id="{{ $talent->id }}"
                                                             data-talent-name="{{ strtolower($talent->user->name) }}"
                                                             data-talent-skills="{{ strtolower(implode(' ', array_map(function($skill) { return is_array($skill) ? ($skill['skill_name'] ?? ($skill['name'] ?? '')) : $skill; }, $talent->user->getTalentSkillsArray() ?? []))) }}"
                                                             onclick="toggleTalentSelection('{{ $talent->id }}', '{{ addslashes($talent->user->name) }}', this, event)">

                                                <!-- Selection indicator (Checkbox) -->
                                                <div class="absolute top-3 right-3">
                                                    <div class="w-5 h-5 border-2 border-gray-300 rounded bg-white group-hover:border-blue-500 transition-colors flex items-center justify-center">
                                                        <i class="fas fa-check text-white text-xs hidden selection-checkmark"></i>
                                                    </div>
                                                    <input type="checkbox" class="hidden talent-checkbox" value="{{ $talent->id }}" name="talent_ids[]">
                                                </div>

                                                <!-- Red flag indicator -->
                                                @if($redflagSummary['has_redflags'])
                                                    <div class="absolute top-3 left-3">
                                                        <span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded-full font-medium">
                                                            🚩 {{ $redflagSummary['count'] }} flags
                                                        </span>
                                                    </div>
                                                @endif

                                                <!-- Talent Info -->
                                                <div class="flex items-start space-x-3 {{ $redflagSummary['has_redflags'] ? 'mt-6' : 'mt-0' }}">
                                                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center text-white font-bold text-lg flex-shrink-0">
                                                        {{ strtoupper(substr($talent->user->name, 0, 1)) }}
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <h4 class="font-semibold text-gray-900 truncate">
                                                            {{ $talent->user->name }}
                                                        </h4>
                                                        <p class="text-sm text-gray-600 truncate">
                                                            {{ $talent->user->pekerjaan ?? 'Developer' }}
                                                        </p>                                        <div class="flex items-center space-x-2 mt-1">
                                            <span class="text-xs text-gray-500">{{ $talent->project_count }} proyek</span>
                                            @if($talent->user->email_verified_at)
                                                <span class="w-2 h-2 bg-green-400 rounded-full" title="Verified"></span>
                                            @endif
                                        </div>           
                                    </div>  
                                </div>

                                                <!-- Performance Metrics -->
                                                <div class="mt-4 grid grid-cols-3 gap-2">
                                                    <div class="text-center">
                                                        <div class="text-xs text-gray-500">Kecepatan</div>
                                                        <div class="font-semibold text-sm">{{ round(floatval($metrics['learning_velocity']['score'] ?? 0)) }}%</div>
                                                    </div>
                                                    <div class="text-center">
                                                        <div class="text-xs text-gray-500">Konsistensi</div>
                                                        <div class="font-semibold text-sm">{{ round(floatval($metrics['consistency']['score'] ?? 0)) }}%</div>
                                                    </div>
                                                    <div class="text-center">
                                                        <div class="text-xs text-gray-500">Adaptabilitas</div>
                                                        <div class="font-semibold text-sm">{{ round(floatval($metrics['adaptability']['score'] ?? 0)) }}%</div>
                                                    </div>
                                                </div>

                                                <!-- Skills (if available) -->
                                                @if($talent->user->getTalentSkillsArray())
                                                    <div class="mt-3">
                                                        <div class="flex flex-wrap gap-1">
                                                            @foreach(array_slice($talent->user->getTalentSkillsArray(), 0, 3) as $skill)
                                                                <span class="bg-blue-50 text-blue-700 text-xs px-2 py-1 rounded-full">
                                                                    {{ is_array($skill) ? ($skill['skill_name'] ?? ($skill['name'] ?? 'Unknown')) : $skill }}
                                                                </span>
                                                            @endforeach
                                                            @if(count($talent->user->getTalentSkillsArray()) > 3)
                                                                <span class="text-xs text-gray-500 px-2 py-1">+{{ count($talent->user->getTalentSkillsArray()) - 3 }} lainnya</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endif
                                                <!-- View Details Button -->
                                                <div class="mt-3 pt-3 border-t border-gray-100">
                                                    <button type="button" onclick="event.stopPropagation(); viewTalentDetailsInModal('{{ $talent->id }}', '{{ addslashes($talent->user->name) }}');"
                                                            class="text-blue-600 hover:text-blue-800 text-sm font-medium flex items-center">
                                                        <i class="fas fa-eye mr-1"></i>Lihat Detail
                                                    </button>
                                                </div>                                                        
                                            </div>                                                    
                                        </div>                                                
                                        @endforeach                                            
                                    </div>                                        
                                </div>

                                        <!-- Carousel Indicators (optional dots) -->
                                        <div class="flex justify-center mt-4 space-x-2" id="carouselIndicators">                                            
                                        </div>
                                    </div>

                                    <!-- Hidden input for selected talents -->
                                    <input type="hidden" id="talent_select" name="talent_id" required>
                                    <div id="selectedTalentsDisplay" class="hidden mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                                        <div class="flex items-center justify-between mb-3">
                                            <span class="text-blue-800 font-medium flex items-center">
                                                <i class="fas fa-users text-blue-600 mr-2"></i>
                                                Talenta Terpilih (<span id="selectedTalentCount">0</span>)
                                            </span>
                                            <button type="button" onclick="clearAllTalentSelections(event)" class="text-blue-600 hover:text-blue-800 text-sm">
                                                <i class="fas fa-times mr-1"></i>Hapus Semua
                                            </button>
                                        </div>
                                        <div id="selectedTalentsList" class="space-y-2">
                                            <!-- Selected talents will be dynamically inserted here -->
                                        </div>
                                    </div>
                                @else
                                    <div class="text-center py-8 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                                        <i class="fas fa-users text-gray-400 text-3xl mb-3"></i>
                                        <p class="text-gray-600 font-medium">Tidak ada talenta tersedia</p>
                                        <p class="text-sm text-gray-500">Semua talenta sedang ditugaskan atau tidak tersedia.</p>
                                    </div>
                                @endif
                            </div>

                            @if($viewData->availableTalents->count() > 0)
                            <!-- Project Title (Auto-filled from project) -->
                            <div>
                                <label for="projectTitle" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Judul Proyek <span class="text-red-500">*</span>
                                </label>
                                <input type="text" class="w-full px-4 py-3 border border-gray-300 rounded-xl bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                                       id="projectTitle" name="project_title" required readonly
                                       value="{{ $viewData->project->title }}">
                                <p class="text-xs text-gray-500 mt-1">Diisi otomatis dari proyek saat ini</p>
                            </div>

                            <!-- Budget Range (Role-specific) -->
                            <div>
                                <label for="budgetRange" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Rentang Anggaran Individual
                                    <span class="text-xs text-gray-500 block font-normal">Anggaran untuk peran talenta spesifik ini</span>
                                </label>
                                <select class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                                        id="budgetRange" name="budget_range">
                                    <option value="">Pilih rentang anggaran individual</option>
                                    <option value="Under Rp 5.000.000">Di bawah Rp 5.000.000</option>
                                    <option value="Rp 5.000.000 - Rp 15.000.000">Rp 5.000.000 - Rp 15.000.000</option>
                                    <option value="Rp 15.000.000 - Rp 30.000.000">Rp 15.000.000 - Rp 30.000.000</option>
                                    <option value="Rp 30.000.000 - Rp 50.000.000">Rp 30.000.000 - Rp 50.000.000</option>
                                    <option value="Rp 50.000.000+">Rp 50.000.000+</option>
                                    <option value="Negotiable">Dapat Dinegosiasi</option>
                                </select>
                                <p class="text-xs text-gray-500 mt-1">💡 Ini untuk bagian talenta ini dalam proyek</p>
                            </div>

                            <!-- Project Duration (Auto-calculated from project timeline) -->
                            <div>
                                <label for="projectDuration" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Durasi Proyek <span class="text-red-500">*</span>
                                </label>
                                <select class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                                        id="projectDuration" name="project_duration" required>
                                    @php
                                        $projectDurationText = $viewData->project->estimated_duration_days ? "{$viewData->project->estimated_duration_days} hari" : '3-6 bulan';
                                        if ($viewData->project->expected_start_date && $viewData->project->expected_end_date) {
                                            $projectDurationText = $viewData->project->expected_start_date->diffInDays($viewData->project->expected_end_date) . ' hari (' .
                                                                 $viewData->project->expected_start_date->format('M d') . ' - ' .
                                                                 $viewData->project->expected_end_date->format('M d, Y') . ')';
                                        }
                                    @endphp
                                    <option value="">Pilih durasi</option>
                                    <option value="1-2 minggu">1-2 minggu</option>
                                    <option value="1 bulan">1 bulan</option>
                                    <option value="2-3 bulan">2-3 bulan</option>
                                    <option value="3-6 bulan" {{ str_contains($projectDurationText, 'bulan') ? 'selected' : '' }}>3-6 bulan</option>
                                    <option value="6+ bulan">6+ bulan</option>
                                    <option value="Ongoing">Berkelanjutan</option>
                                    @if($viewData->project->expected_start_date && $viewData->project->expected_end_date)
                                        <option value="{{ $projectDurationText }}" selected>{{ $projectDurationText }}</option>
                                    @endif
                                </select>
                                <p class="text-xs text-gray-500 mt-1">Diperlukan untuk time-blocking mencegah tumpang tindih proyek</p>
                            </div>

                            <!-- Project Description (Auto-filled from project) -->
                            <div>
                                <label for="projectDescription" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Deskripsi Proyek <span class="text-red-500">*</span>
                                </label>
                                <textarea class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 resize-none"
                                          id="projectDescription" name="project_description" rows="4" required>{{ $viewData->project->description }}</textarea>
                                <p class="text-xs text-gray-500 mt-1">Diisi sebelumnya dari deskripsi proyek (dapat diedit)</p>
                            </div>

                            <!-- Role-Specific Requirements -->
                            <div>
                                <label for="requirements" class="block text-sm font-semibold text-gray-700 mb-2">Kebutuhan Spesifik</label>
                                <textarea class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 resize-none"
                                          id="requirements" name="requirements" rows="3"
                                          placeholder="Daftarkan skill, teknologi, atau kualifikasi spesifik yang dibutuhkan untuk peran ini...">{{ $viewData->project->general_requirements }}</textarea>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-gray-50 rounded-b-2xl border-0 p-6">
                    <button type="button" class="px-6 py-3 bg-gray-500 text-white rounded-xl hover:bg-gray-600 transition-all duration-200 font-medium mr-3"
                            data-dismiss="modal" onclick="$('#talentRequestModal').modal('hide')">
                        <i class="fas fa-times mr-2"></i>Batal
                    </button>
                    <button type="submit" class="px-6 py-3 bg-green-600 text-white rounded-xl hover:bg-green-700 transition-all duration-200 font-medium">
                        <i class="fas fa-paper-plane mr-2"></i>Minta Penugasan Talenta
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- Extension Request Modal -->
@if(in_array($viewData->project->status, ['active', 'overdue']) && !$viewData->project->extensions()->pending()->exists())
<div id="extensionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-lg w-full">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">Request Project Extension</h3>
                    <button onclick="hideExtensionModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form action="{{ route('projects.request-extension', $viewData->project) }}" method="POST" class="space-y-6">
                    @csrf

                    <div>
                        <label for="new_end_date" class="block text-sm font-medium text-gray-700 mb-2">
                            New End Date <span class="text-red-500">*</span>
                        </label>                        <input type="date" id="new_end_date" name="new_end_date" required
                               min="{{ $viewData->project->expected_end_date ? $viewData->project->expected_end_date->addDay()->format('Y-m-d') : '' }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <p class="text-sm text-gray-500 mt-1">Current end date: {{ $viewData->project->expected_end_date ? $viewData->project->expected_end_date->format('M d, Y') : 'Not set' }}</p>
                    </div>

                    <div>
                        <label for="justification" class="block text-sm font-medium text-gray-700 mb-2">
                            Justification <span class="text-red-500">*</span>
                        </label>
                        <textarea id="justification" name="justification" rows="4" required
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Explain why you need to extend the project timeline..."></textarea>
                    </div>

                    <div class="flex justify-end space-x-4">
                        <button type="button" onclick="hideExtensionModal()"
                                class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition duration-200">
                            Cancel
                        </button>
                        <button type="submit"
                                class="px-6 py-3 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition duration-200">
                            <i class="fas fa-clock mr-2"></i>Request Extension
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Talent Details Modal -->
<div id="talent-details-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden" style="z-index: 1060;">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-3xl shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center pb-3">
            <h3 class="text-2xl font-bold text-gray-900">Detail Talenta</h3>
            <div class="modal-close cursor-pointer" style="z-index: 1070;" onclick="closeModal('talent-details-modal')">
                <svg class="fill-current text-black" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18">
                    <path d="M14.53 4.53l-1.06-1.06L9 7.94 4.53 3.47 3.47 4.53 7.94 9l-4.47 4.47 1.06 1.06L9 10.06l4.47 4.47 1.06-1.06L10.06 9z"></path>
                </svg>
            </div>
        </div>
        <div class="mt-4">
            <div id="modal-talent-content"></div>
            <div id="modal-loading-state" class="text-center p-8">
                <i class="fas fa-spinner fa-spin text-4xl text-blue-500"></i>
                <p class="mt-2 text-gray-600">Memuat detail...</p>
            </div>
            <div id="modal-error-state" class="text-center p-8 hidden">
                <i class="fas fa-exclamation-triangle text-4xl text-red-500"></i>
                <p class="mt-2 text-gray-600">Tidak dapat memuat detail talenta.</p>
            </div>
        </div>
    </div>
</div>

<!-- Closure Request Modal -->
<div id="closureRequestModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 shadow-2xl rounded-2xl bg-white">
        <div class="mt-3">
            <!-- Modal Header -->
            <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
                <h3 class="text-2xl font-bold text-gray-900 flex items-center">
                    <i class="fas fa-times text-red-600 mr-3"></i>
                    Request Project Closure
                </h3>
                <button onclick="hideClosureRequestModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <!-- Modal Content -->
            <form action="{{ route('projects.request-closure', $viewData->project) }}" method="POST">
                @csrf
                <div class="space-y-6">
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <div class="flex items-start">
                            <i class="fas fa-exclamation-triangle text-yellow-600 mt-1 mr-3"></i>
                            <div>
                                <h4 class="font-semibold text-yellow-800 mb-2">Important Notice</h4>
                                <p class="text-yellow-700 text-sm">
                                    Requesting project closure will notify the talent admin for review.
                                    If the project has not reached its deadline, admin approval is required to force-close the project.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="closure_reason" class="block text-sm font-medium text-gray-700 mb-2">
                            Reason for Closure <span class="text-red-500">*</span>
                        </label>
                        <textarea
                            id="closure_reason"
                            name="closure_reason"
                            rows="4"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                            placeholder="Please provide a detailed reason for requesting project closure..."
                            required
                        ></textarea>
                        <p class="text-xs text-gray-500 mt-1">Minimum 10 characters required</p>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="flex items-center justify-end space-x-3 mt-8 pt-6 border-t border-gray-200">
                    <button type="button" onclick="hideClosureRequestModal()"
                            class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 font-medium transition duration-200">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition duration-200">
                        <i class="fas fa-paper-plane mr-2"></i>
                        Submit Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Tab functionality
function showTab(tabName) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });

    // Remove active class from all tab buttons
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('active', 'border-blue-500', 'text-blue-600');
        button.classList.add('border-transparent', 'text-gray-500');
    });

    // Show selected tab content
    document.getElementById(tabName + '-content').classList.remove('hidden');

    // Add active class to selected tab button
    const activeButton = document.getElementById(tabName + '-tab');
    activeButton.classList.add('active', 'border-blue-500', 'text-blue-600');
    activeButton.classList.remove('border-transparent', 'text-gray-500');
}

// Global variables for error handlers
let currentRequestTalentId = null;
let currentRequestTalentName = null;

// Modal functions
function showProjectTalentModal() {
    // Reset form and show the bootstrap modal
    const form = document.getElementById('talentRequestForm');
    if (form) {
        form.reset();
    }

    // Set project context fields with null checks
    const projectIdField = document.querySelector('input[name="project_id"]');
    if (projectIdField) {
        projectIdField.value = '{{ $viewData->project->id }}';
    }
    
    const isProjectAssignmentField = document.querySelector('input[name="is_project_assignment"]');
    if (isProjectAssignmentField) {
        isProjectAssignmentField.value = '1';
    }
    
    const projectTitleField = document.getElementById('projectTitle');
    if (projectTitleField) {
        projectTitleField.value = '{{ $viewData->project->title }}';
    }
    
    const projectDescriptionField = document.getElementById('projectDescription');
    if (projectDescriptionField) {
        projectDescriptionField.value = '{{ addslashes($viewData->project->description) }}';
    }

    // Pre-fill requirements if available
    const requirementsField = document.getElementById('requirements');
    if (requirementsField && '{{ $viewData->project->general_requirements }}') {
        requirementsField.value = '{{ addslashes($viewData->project->general_requirements) }}';
    }

    // Reset talent selection (both old dropdown and new card-based)
    const talentSelect = document.getElementById('talent_select');
    if (talentSelect) {
        talentSelect.value = '';
    }

    // Clear card-based selection
    if (typeof clearAllTalentSelections === 'function') {
        clearAllTalentSelections();
    }

    $('#talentRequestModal').modal('show');
}

function hideTalentRequestModal() {
    $('#talentRequestModal').modal('hide');
}

function showAssignTalentModal() {
    // Show assign talent modal
    showProjectTalentModal();
}

function hideAssignTalentModal() {
    // Hide talent request modal
    hideTalentRequestModal();
}

function showExtensionModal() {
    const modal = document.getElementById('extensionModal');
    if (modal) {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
}

function hideExtensionModal() {
    const modal = document.getElementById('extensionModal');
    if (modal) {
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
}

function showClosureRequestModal() {
    const modal = document.getElementById('closureRequestModal');
    if (modal) {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
}

function hideClosureRequestModal() {
    const modal = document.getElementById('closureRequestModal');
    if (modal) {
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
        // Reset form
        const form = modal.querySelector('form');
        if (form) {
            form.reset();
        }
    }
}

// Assignment actions
function editAssignment(assignmentId) {
    alert('Fungsi edit penugasan akan diimplementasikan');
}

function removeAssignment(assignmentId) {
    if (confirm('Apakah Anda yakin ingin menghapus penugasan talenta ini?')) {
        // Create form and submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/assignments/${assignmentId}`;

        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';

        form.appendChild(csrfToken);
        form.appendChild(methodField);
        document.body.appendChild(form);
        form.submit();
    }
}

// Close modals when clicking outside
document.addEventListener('click', function(event) {
    const assignModal = document.getElementById('assignTalentModal');
    const extensionModal = document.getElementById('extensionModal');
    const talentDetailsModal = document.getElementById('talentDetailsModal');
    const talentDetailsModalNew = document.getElementById('talent-details-modal');

    if (assignModal && event.target === assignModal) {
        hideAssignTalentModal();
    }

    if (extensionModal && event.target === extensionModal) {
        hideExtensionModal();
    }

    if (talentDetailsModal && event.target === talentDetailsModal) {
        closeTalentDetailsModal();
    }

    if (talentDetailsModalNew && event.target === talentDetailsModalNew) {
        closeModal('talent-details-modal');
    }
});

// Consolidated DOM Content Loaded Handler
document.addEventListener('DOMContentLoaded', function() {
    // Date validation and form handling
    const startDateInput = document.getElementById('talent_start_date');
    const endDateInput = document.getElementById('talent_end_date');

    if (startDateInput && endDateInput) {
        startDateInput.addEventListener('change', function() {
            endDateInput.min = this.value;
        });
    }

    // Handle talent request form submission
    const talentRequestForm = document.getElementById('talentRequestForm');
    if (talentRequestForm) {
        talentRequestForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const submitButton = this.querySelector('button[type="submit"]');
            const originalText = submitButton.innerHTML;

            // Store current talent info for error handlers
            const selectedTalentIds = Array.from(selectedTalents);
            currentRequestTalentId = selectedTalentIds[0]; // For backward compatibility

            // Get talent names for display purposes
            const selectedTalentNames = [];
            selectedTalentIds.forEach(talentId => {
                const talentCard = document.querySelector(`[onclick*="${talentId}"]`);
                if (talentCard) {
                    const talentName = talentCard.querySelector('h4').textContent.trim();
                    selectedTalentNames.push(talentName);
                }
            });
            currentRequestTalentName = selectedTalentNames.join(', ');

            // Validate talent selection
            if (selectedTalents.size === 0) {
                alert('Silakan pilih setidaknya satu talenta sebelum mengirimkan permintaan.');
                return;
            }

            // Show loading state
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Mengirim Permintaan...';

            // Debug: Log form data
            console.log('Form Data being submitted:');
            for (let [key, value] of formData.entries()) {
                console.log(key, value);
            }

            // Use the existing talent request endpoint
            fetch('{{ route("recruiter.submit_talent_request") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);

                if (!response.ok) {
                    return response.text().then(text => {
                        console.log('Error response text:', text);
                        let data;
                        try {
                            data = JSON.parse(text);
                        } catch (e) {
                            console.log('Failed to parse JSON, raw response:', text);
                            data = { error: 'Invalid server response', message: text.substring(0, 200) };
                        }
                        throw { status: response.status, data: data };
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Success - talent request submitted
                    $('#talentRequestModal').modal('hide');

                    // Clear talent selection state immediately after successful submission
                    selectedTalents.clear();
                    clearAllTalentSelections();
                    updateSelectedTalentsDisplay();

                    // Show success message
                    const talentCount = data.successful_requests ? data.successful_requests.length : 1;
                    const talentText = talentCount === 1 ? 'talenta' : 'talenta';
                    const successHtml = `
                        <div class="fixed inset-0 z-50 overflow-y-auto" style="background: rgba(0,0,0,0.5);">
                            <div class="flex items-center justify-center min-h-screen px-4">
                                <div class="bg-white rounded-xl max-w-lg w-full p-6 shadow-2xl">
                                    <div class="text-center">
                                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                            <i class="fas fa-check text-green-600 text-2xl"></i>
                                        </div>
                                        <h3 class="text-xl font-bold text-gray-900 mb-2">Permintaan Berhasil Dikirim!</h3>
                                        <p class="text-gray-600 mb-4">Permintaan Anda untuk ${talentCount} ${talentText} telah dikirim untuk direview.</p>
                                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                                            <div class="text-sm">
                                                <p><strong>Proyek:</strong> {{ $viewData->project->title }}</p>
                                                <p><strong>Talenta Terpilih:</strong> ${currentRequestTalentName}</p>
                                                <p><strong>ID Permintaan:</strong> ${data.request_id || 'Dibuat'}</p>
                                                <p><strong>Timeline:</strong> ${data.project_timeline?.start_date || 'TBD'} - ${data.project_timeline?.end_date || 'TBD'}</p>
                                            </div>
                                        </div>
                                        <button onclick="location.reload()"
                                                class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                            <i class="fas fa-refresh mr-2"></i>Refresh Page
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    document.body.insertAdjacentHTML('beforeend', successHtml);
                } else {
                    throw { status: 400, data: data };
                }
            })
            .catch(error => {
                console.error('Error:', error);

                if (error.status === 409 && error.data) {
                    // Time-blocking conflict - show detailed availability info
                    showTimeBlockingConflict(error.data);
                } else if (error.status === 400 && error.data?.error === 'talent_already_onboarded') {
                    // Talent already onboarded with this recruiter - show specific modal
                    showTalentAlreadyOnboardedModal(error.data);
                } else if (error.status === 400 && error.data?.error === 'active_request_exists') {
                    // Active request exists - show detailed info
                    showActiveRequestExistsModal(error.data);
                } else {
                    // Regular error message - show more details for debugging
                    console.error('Full error object:', error);
                    console.error('Error status:', error.status);
                    console.error('Error data:', error.data);

                    let errorMessage = 'Failed to submit request. Please try again.';

                    if (error.data) {
                        if (typeof error.data === 'string') {
                            errorMessage = error.data.substring(0, 200);
                        } else if (error.data.message) {
                            errorMessage = error.data.message;
                        } else if (error.data.error) {
                            errorMessage = error.data.error;
                        } else if (error.data.errors) {
                            // Laravel validation errors
                            const validationErrors = Object.values(error.data.errors).flat();
                            errorMessage = validationErrors.join(', ');
                        }
                    }

                    const errorAlert = document.createElement('div');
                    errorAlert.className = 'fixed top-4 right-4 bg-red-100 border border-red-400 text-red-700 px-6 py-4 rounded-xl shadow-lg z-50 max-w-md';
                    errorAlert.innerHTML = `
                        <div class="flex items-start">
                            <i class="fas fa-exclamation-circle mr-2 mt-1"></i>
                            <div>
                                <div class="font-semibold">Permintaan Gagal</div>
                                <div class="text-sm mt-1">${errorMessage}</div>
                                ${error.status ? `<div class="text-xs mt-1 opacity-75">Status: ${error.status}</div>` : ''}
                            </div>
                            <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-red-500 hover:text-red-700">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    `;
                    document.body.appendChild(errorAlert);

                    setTimeout(() => {
                        if (errorAlert.parentElement) {
                            errorAlert.remove();
                        }
                    }, 10000); // Show for 10 seconds for debugging
                }
            })
            .finally(() => {
                submitButton.disabled = false;
                submitButton.innerHTML = originalText;
            });
        });
    }

    // Auto-dismiss notifications
    const successNotification = document.getElementById('success-notification');
    const errorNotification = document.getElementById('error-notification');

    if (successNotification) {
        setTimeout(function() {
            successNotification.style.transition = 'opacity 0.5s ease-out';
            successNotification.style.opacity = '0';
            setTimeout(function() {
                successNotification.remove();
            }, 500);
        }, 5000);
    }

    if (errorNotification) {
        setTimeout(function() {
            errorNotification.style.transition = 'opacity 0.5s ease-out';
            errorNotification.style.opacity = '0';
            setTimeout(function() {
                errorNotification.remove();
            }, 500);
        }, 7000);
    }

    // Close modals on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const talentDetailsModal = document.getElementById('talent-details-modal');
            if (talentDetailsModal && !talentDetailsModal.classList.contains('hidden')) {
                closeModal('talent-details-modal');
            }
        }
    });

    // Talent Carousel Functionality
    const carousel = document.getElementById('talentCarousel');
    const prevBtn = document.getElementById('carouselPrevBtn');
    const nextBtn = document.getElementById('carouselNextBtn');
    const indicatorsContainer = document.getElementById('carouselIndicators');

    if (carousel && prevBtn && nextBtn) {
        const slides = carousel.querySelectorAll('.talent-carousel-slide');
        let slideWidth = 320;
        let visibleSlides = 1;
        let totalSlides = slides.length;
        let maxPosition = 0;

        // Calculate responsive values
        function calculateLayout() {
            const containerWidth = carousel.parentElement.offsetWidth - 96;

            if (window.innerWidth <= 640) {
                slideWidth = 260;
            } else if (window.innerWidth <= 768) {
                slideWidth = 280;
            } else if (window.innerWidth <= 1024) {
                slideWidth = 300;
            } else {
                slideWidth = 320;
            }

            visibleSlides = Math.max(1, Math.floor(containerWidth / slideWidth));
            maxPosition = Math.max(0, totalSlides - visibleSlides);
        }

        let currentPosition = 0;

        // Generate indicators
        function generateIndicators() {
            indicatorsContainer.innerHTML = '';
            const indicatorCount = Math.ceil(totalSlides / visibleSlides);

            for (let i = 0; i < indicatorCount; i++) {
                const indicator = document.createElement('button');
                indicator.className = 'w-2 h-2 rounded-full transition-all duration-200 bg-gray-300 hover:bg-gray-400';
                indicator.addEventListener('click', () => goToSlide(i * visibleSlides));
                indicatorsContainer.appendChild(indicator);
            }
            updateIndicators();
        }

        // Update indicators
        function updateIndicators() {
            const indicators = indicatorsContainer.querySelectorAll('button');
            const activeIndex = Math.floor(currentPosition / visibleSlides);

            indicators.forEach((indicator, index) => {
                if (index === activeIndex) {
                    indicator.classList.remove('bg-gray-300');
                    indicator.classList.add('bg-blue-500', 'w-6');
                } else {
                    indicator.classList.remove('bg-blue-500', 'w-6');
                    indicator.classList.add('bg-gray-300');
                }
            });
        }

        // Update carousel position
        function updateCarousel() {
            const translateX = -currentPosition * slideWidth;
            carousel.style.transform = `translateX(${translateX}px)`;

            prevBtn.disabled = currentPosition === 0;
            nextBtn.disabled = currentPosition >= maxPosition;

            updateIndicators();
        }

        // Go to specific slide
        function goToSlide(position) {
            currentPosition = Math.max(0, Math.min(position, maxPosition));
            updateCarousel();
        }

        // Previous slide
        function prevSlide() {
            if (currentPosition > 0) {
                currentPosition = Math.max(0, currentPosition - visibleSlides);
                updateCarousel();
            }
        }

        // Next slide
        function nextSlide() {
            if (currentPosition < maxPosition) {
                currentPosition = Math.min(maxPosition, currentPosition + visibleSlides);
                updateCarousel();
            }
        }

        // Event listeners
        prevBtn.addEventListener('click', prevSlide);
        nextBtn.addEventListener('click', nextSlide);

        // Touch/swipe support
        let startX = 0;
        let currentX = 0;
        let isDragging = false;
        let startElement = null;

        carousel.addEventListener('touchstart', (e) => {
            startElement = e.target;
            // Don't start dragging if touching a talent card or interactive element
            if (startElement.closest('.talent-selection-card') ||
                startElement.closest('button') ||
                startElement.closest('input') ||
                startElement.closest('[onclick]')) {
                return;
            }

            startX = e.touches[0].clientX;
            isDragging = true;
        });

        carousel.addEventListener('touchmove', (e) => {
            if (!isDragging) return;
            currentX = e.touches[0].clientX;
            e.preventDefault();
        });

        carousel.addEventListener('touchend', () => {
            if (!isDragging) return;
            isDragging = false;

            const diffX = startX - currentX;
            const threshold = 50;

            if (Math.abs(diffX) > threshold) {
                if (diffX > 0) {
                    nextSlide();
                } else {
                    prevSlide();
                }
            }
        });

        // Mouse drag support
        carousel.addEventListener('mousedown', (e) => {
            startElement = e.target;
            // Don't start dragging if clicking on a talent card or interactive element
            if (startElement.closest('.talent-selection-card') ||
                startElement.closest('button') ||
                startElement.closest('input') ||
                startElement.closest('[onclick]')) {
                return;
            }

            startX = e.clientX;
            isDragging = true;
            carousel.style.cursor = 'grabbing';
            e.preventDefault();
        });

        document.addEventListener('mousemove', (e) => {
            if (!isDragging) return;
            currentX = e.clientX;
            e.preventDefault();
        });

        document.addEventListener('mouseup', () => {
            if (!isDragging) return;
            isDragging = false;
            carousel.style.cursor = 'grab';

            const diffX = startX - currentX;
            const threshold = 50;

            if (Math.abs(diffX) > threshold) {
                if (diffX > 0) {
                    nextSlide();
                } else {
                    prevSlide();
                }
            }
        });

        // Keyboard navigation
        document.addEventListener('keydown', (e) => {
            if (e.target.closest('.talent-carousel-container')) {
                if (e.key === 'ArrowLeft') {
                    e.preventDefault();
                    prevSlide();
                } else if (e.key === 'ArrowRight') {
                    e.preventDefault();
                    nextSlide();
                }
            }
        });

        // Responsive handling
        function handleResize() {
            const oldVisibleSlides = visibleSlides;
            calculateLayout();

            if (oldVisibleSlides !== visibleSlides) {
                currentPosition = Math.min(currentPosition, maxPosition);
                generateIndicators();
                updateCarousel();
            }
        }

        window.addEventListener('resize', handleResize);

        // Initialize carousel
        calculateLayout();
        generateIndicators();
        updateCarousel();
        carousel.style.cursor = 'grab';
    }
});

// Error handling functions
function showTimeBlockingConflict(errorData) {
    $('#talentRequestModal').modal('hide');

    let blockingProjectsHtml = '';
    if (errorData.blocking_projects && errorData.blocking_projects.length > 0) {
        blockingProjectsHtml = '<div class="mt-4"><h6 class="font-semibold text-gray-900 mb-3">Conflicting Projects:</h6><div class="space-y-2">';
        errorData.blocking_projects.forEach(project => {
            blockingProjectsHtml += `
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                    <div class="flex justify-between items-center text-sm">
                        <span class="font-medium text-yellow-900">${project.title}</span>
                        <span class="text-yellow-700">${project.end_date}</span>
                    </div>
                </div>
            `;
        });
        blockingProjectsHtml += '</div></div>';
    }

    const nextAvailableDate = errorData.next_available_date ?
        new Date(errorData.next_available_date).toLocaleDateString('en-US', {
            year: 'numeric', month: 'long', day: 'numeric'
        }) : 'Unknown';

    const modalHtml = `
        <div class="fixed inset-0 z-50 overflow-y-auto" style="background: rgba(0,0,0,0.5);">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="bg-white rounded-xl max-w-lg w-full p-6 shadow-2xl">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-calendar-times text-red-600 text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900">Talent Not Available</h3>
                        <p class="text-gray-600 mt-2">${errorData.message || 'This talent is currently committed to other projects.'}</p>
                    </div>

                    ${errorData.next_available_date ? `
                        <div class="text-center mb-4">
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <div class="text-sm font-medium text-blue-800">Next Available:</div>
                                <div class="text-lg font-bold text-blue-900">${nextAvailableDate}</div>
                            </div>
                        </div>
                    ` : ''}

                    ${blockingProjectsHtml}

                    <div class="mt-6 space-y-3">
                        <button onclick="showProjectTalentModal(); this.closest('.fixed').remove();"
                                class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-calendar-plus mr-2"></i>Try Different Talent
                        </button>
                        <button onclick="this.closest('.fixed').remove()"
                                class="w-full px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;

    document.body.insertAdjacentHTML('beforeend', modalHtml);
}

function showTalentAlreadyOnboardedModal(errorData) {
    // Close the request modal first
    $('#talentRequestModal').modal('hide');

    const onboardedDate = errorData.existing_project?.onboarded_date || 'Tidak diketahui';
    const projectTitle = errorData.existing_project?.title || 'Proyek Saat Ini';

    const modalHtml = `
        <div class="fixed inset-0 z-50 overflow-y-auto" style="background: rgba(0,0,0,0.5);">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="bg-white rounded-xl max-w-lg w-full p-6 shadow-2xl">
                    <div class="text-center mb-4">
                        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-user-check text-blue-600 text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900">Talenta Sudah Terdaftar</h3>
                        <p class="text-gray-600 mt-2">${errorData.message}</p>
                    </div>

                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                        <h6 class="font-semibold text-blue-900 mb-2">Detail Proyek Saat Ini:</h6>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-blue-700">Proyek:</span>
                                <span class="font-medium text-blue-900">${projectTitle}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-blue-700">Terdaftar:</span>
                                <span class="font-medium text-blue-900">${onboardedDate}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-blue-700">Status:</span>
                                <span class="font-medium text-blue-900">${errorData.existing_project?.status || 'Terdaftar'}</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                        <div class="flex items-start">
                            <i class="fas fa-lightbulb text-yellow-600 mr-2 mt-0.5"></i>
                            <div class="text-sm text-yellow-800">
                                <p class="font-medium mb-1">Saran:</p>
                                <p>Karena talenta ini sudah menjadi bagian dari tim Anda, pertimbangkan untuk menghubungi langsung atau menggunakan alat manajemen proyek internal untuk penugasan baru.</p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <button onclick="this.closest('.fixed').remove()"
                                class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-check mr-2"></i>Mengerti
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;

    document.body.insertAdjacentHTML('beforeend', modalHtml);
}

function showActiveRequestExistsModal(errorData) {
    // Close the request modal first
    $('#talentRequestModal').modal('hide');

    const submittedDate = errorData.existing_request?.submitted_date || 'Unknown';
    const projectTitle = errorData.existing_request?.project_title || 'Previous Request';
    const requestStatus = errorData.existing_request?.status || 'In Progress';

    const modalHtml = `
        <div class="fixed inset-0 z-50 overflow-y-auto" style="background: rgba(0,0,0,0.5);">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="bg-white rounded-xl max-w-lg w-full p-6 shadow-2xl">
                    <div class="text-center mb-4">
                        <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-hourglass-half text-orange-600 text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900">Active Request Pending</h3>
                        <p class="text-gray-600 mt-2">${errorData.message}</p>
                    </div>

                    <div class="bg-orange-50 border border-orange-200 rounded-lg p-4 mb-4">
                        <h6 class="font-semibold text-orange-900 mb-2">Your Current Request:</h6>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-orange-700">Project:</span>
                                <span class="font-medium text-orange-900">${projectTitle}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-orange-700">Submitted:</span>
                                <span class="font-medium text-orange-900">${submittedDate}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-orange-700">Status:</span>
                                <span class="font-medium text-orange-900">${requestStatus}</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                        <div class="flex items-start">
                            <i class="fas fa-info-circle text-blue-600 mr-2 mt-0.5"></i>
                            <div class="text-sm text-blue-800">
                                <p class="font-medium mb-1">What's Next:</p>
                                <p>Your request is being processed. You can track its progress in the "My Requests" section of your dashboard.</p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <button onclick="window.location.href='{{ route('recruiter.my_requests') }}'"
                                class="w-full px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors">
                            <i class="fas fa-list mr-2"></i>View My Requests
                        </button>
                        <button onclick="this.closest('.fixed').remove()"
                                class="w-full px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;

    document.body.insertAdjacentHTML('beforeend', modalHtml);
}

// Helper function to update the hidden talent ID field when selection changes
function updateTalentIdField(talentId) {
    document.getElementById('talent_select').value = talentId;
}

// New card-based talent selection functions (Multi-select)
let selectedTalents = new Set(); // Track selected talent IDs

function toggleTalentSelection(talentId, talentName, cardElement, event) {
    // Prevent event propagation to avoid carousel drag interference
    if (event) {
        event.stopPropagation();
        event.preventDefault();
    }

    // Add null checks for cardElement and its children
    if (!cardElement || !cardElement.classList) {
        console.warn('Invalid card element provided to toggleTalentSelection');
        return;
    }

    const checkbox = cardElement.querySelector('.talent-checkbox');
    const checkmark = cardElement.querySelector('.selection-checkmark');
    const checkboxContainer = cardElement.querySelector('.absolute.top-3.right-3 > div');

    if (selectedTalents.has(talentId)) {
        // Deselect talent
        selectedTalents.delete(talentId);
        cardElement.classList.remove('border-blue-500', 'bg-blue-50');
        cardElement.classList.add('border-gray-200');
        if (checkmark && checkmark.classList) checkmark.classList.add('hidden');
        if (checkboxContainer && checkboxContainer.classList) {
            checkboxContainer.classList.remove('bg-blue-500', 'border-blue-500');
            checkboxContainer.classList.add('bg-white', 'border-gray-300');
        }
        if (checkbox) checkbox.checked = false;
    } else {
        // Select talent
        selectedTalents.add(talentId);
        cardElement.classList.remove('border-gray-200');
        cardElement.classList.add('border-blue-500', 'bg-blue-50');
        if (checkmark && checkmark.classList) checkmark.classList.remove('hidden');
        if (checkboxContainer && checkboxContainer.classList) {
            checkboxContainer.classList.remove('bg-white', 'border-gray-300');
            checkboxContainer.classList.add('bg-blue-500', 'border-blue-500');
        }
        if (checkbox) checkbox.checked = true;
    }

    updateSelectedTalentsDisplay();
}

function updateSelectedTalentsDisplay() {
    const selectedDisplay = document.getElementById('selectedTalentsDisplay');
    const selectedCount = document.getElementById('selectedTalentCount');
    const selectedList = document.getElementById('selectedTalentsList');
    const talentInput = document.getElementById('talent_select');

    // Add null checks to prevent classList errors
    if (!selectedDisplay || !selectedCount || !selectedList || !talentInput) {
        console.warn('Required elements for talent display not found');
        return;
    }

    if (selectedTalents.size === 0) {
        selectedDisplay.classList.add('hidden');
        talentInput.value = '';
        return;
    }

    selectedDisplay.classList.remove('hidden');
    selectedCount.textContent = selectedTalents.size;

    // Update hidden input with comma-separated talent IDs
    talentInput.value = Array.from(selectedTalents).join(',');

    // Build the selected talents list
    selectedList.innerHTML = '';
    selectedTalents.forEach(talentId => {
        // Primary: Use data-talent-id selector to find the talent card
        let talentCard = document.querySelector(`[data-talent-id="${talentId}"]`);
        
        // Fallback: Use onclick selector if data-talent-id not found
        if (!talentCard) {
            talentCard = document.querySelector(`[onclick*="toggleTalentSelection('${talentId}'"]`);
        }
        
        if (talentCard) {
            const h4Element = talentCard.querySelector('h4');
            const pElement = talentCard.querySelector('p');
            
            const talentName = h4Element ? h4Element.textContent.trim() : 'Unknown Talent';
            const talentRole = pElement ? pElement.textContent.trim() : 'Unknown Role';

            // Debug logging to identify the issue
            if (talentName === 'Unknown Talent') {
                console.warn(`Could not find talent name for ID: ${talentId}`);
                console.warn('Talent card found:', talentCard);
                console.warn('H4 element:', h4Element);
                console.warn('All h4 elements in card:', talentCard.querySelectorAll('h4'));
            }

            const talentItem = document.createElement('div');
            talentItem.className = 'flex items-center justify-between bg-white rounded-lg p-3 border border-blue-200';
            talentItem.innerHTML = `
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center text-white font-bold text-sm">
                        ${talentName.charAt(0).toUpperCase()}
                    </div>
                    <div>
                        <div class="font-medium text-gray-900 text-sm">${talentName}</div>
                        <div class="text-xs text-gray-500">${talentRole}</div>
                    </div>
                </div>
                <button type="button" onclick="removeTalentSelection('${talentId}', event)" class="text-red-500 hover:text-red-700 p-1">
                    <i class="fas fa-times text-xs"></i>
                </button>
            `;
            selectedList.appendChild(talentItem);
        } else {
            console.warn(`Could not find talent card for ID: ${talentId}`);
        }
    });
}

function removeTalentSelection(talentId, event) {
    // Prevent event bubbling
    if (event) {
        event.stopPropagation();
        event.preventDefault();
    }

    // Primary: Use data-talent-id selector to find the talent card
    let talentCard = document.querySelector(`[data-talent-id="${talentId}"]`);
    
    // Fallback: Use onclick selector if data-talent-id not found
    if (!talentCard) {
        talentCard = document.querySelector(`[onclick*="toggleTalentSelection('${talentId}'"]`);
    }
    
    if (talentCard) {
        // Find the talent name for the card
        const h4Element = talentCard.querySelector('h4');
        const talentName = h4Element ? h4Element.textContent.trim() : 'Unknown Talent';
        
        toggleTalentSelection(talentId, talentName, talentCard, event);
    } else {
        console.warn(`Could not find talent card for removal, ID: ${talentId}`);
        // Fallback: just remove from selectedTalents set
        selectedTalents.delete(talentId);
        updateSelectedTalentsDisplay();
    }
}

function selectAllTalents(event) {
    // Prevent event bubbling
    if (event) {
        event.stopPropagation();
        event.preventDefault();
    }

    document.querySelectorAll('.talent-selection-card').forEach(card => {
        const onclickAttr = card.getAttribute('onclick');
        if (!onclickAttr) {
            return;
        }
        
        const match = onclickAttr.match(/'([^']+)'/);
        if (!match) {
            return;
        }
        
        const talentId = match[1];
        const h4Element = card.querySelector('h4');
        const talentName = h4Element ? h4Element.textContent.trim() : 'Unknown Talent';

        if (!selectedTalents.has(talentId)) {
            selectedTalents.add(talentId);
            card.classList.remove('border-gray-200');
            card.classList.add('border-blue-500', 'bg-blue-50');

            const checkmark = card.querySelector('.selection-checkmark');
            const checkboxContainer = card.querySelector('.absolute.top-3.right-3 > div');
            const checkbox = card.querySelector('.talent-checkbox');

            if (checkmark) checkmark.classList.remove('hidden');
            if (checkboxContainer) {
                checkboxContainer.classList.remove('bg-white', 'border-gray-300');
                checkboxContainer.classList.add('bg-blue-500', 'border-blue-500');
            }
            if (checkbox) checkbox.checked = true;
        }
    });

    updateSelectedTalentsDisplay();
}

function clearAllTalentSelections(event) {
    // Prevent event bubbling when called from button click
    if (event) {
        event.stopPropagation();
        event.preventDefault();
    }

    // Clear all selections with null checks
    const talentCards = document.querySelectorAll('.talent-selection-card');
    if (talentCards.length === 0) {
        console.warn('No talent selection cards found');
        return;
    }

    talentCards.forEach(card => {
        if (card && card.classList) {
            card.classList.remove('border-blue-500', 'bg-blue-50');
            card.classList.add('border-gray-200');

            const checkmark = card.querySelector('.selection-checkmark');
            const checkboxContainer = card.querySelector('.absolute.top-3.right-3 > div');
            const checkbox = card.querySelector('.talent-checkbox');

            if (checkmark && checkmark.classList) checkmark.classList.add('hidden');
            if (checkboxContainer && checkboxContainer.classList) {
                checkboxContainer.classList.remove('bg-blue-500', 'border-blue-500');
                checkboxContainer.classList.add('bg-white', 'border-gray-300');
            }
            if (checkbox) checkbox.checked = false;
        }
    });

    selectedTalents.clear();
    updateSelectedTalentsDisplay();
}

// Legacy function aliases for backward compatibility
function selectTalent(talentId, talentName, cardElement, event) {
    toggleTalentSelection(talentId, talentName, cardElement, event);
}

function clearTalentSelection(event) {
    clearAllTalentSelections(event);
}

// Main function that HTML buttons call - handles both assignments and talent requests
function viewTalentDetails(interactionData) {
    console.log('viewTalentDetails called with:', interactionData);

    // Extract the correct talent ID based on the data type
    let talentId = null;
    let isUserId = false;

    if (interactionData.talent_id) {
        // This is an assignment or talent request with talent_id
        talentId = interactionData.talent_id;
    } else if (interactionData.talent_user_id) {
        // This is a talent request with talent_user_id
        talentId = interactionData.talent_user_id;
        isUserId = true;
    } else {
        console.error('No valid talent ID found in interaction data');
        alert('Tidak dapat memuat detail talenta - tidak ditemukan ID talenta yang valid');
        return;
    }

    // Show the modal
    const modal = document.getElementById('talent-details-modal');
    if (!modal) {
        console.error('Talent details modal not found');
        alert('Modal detail talenta tidak ditemukan. Silakan refresh halaman.');
        return;
    }

    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';

    // Show loading state
    const contentDiv = document.getElementById('modal-talent-content');
    const loadingDiv = document.getElementById('modal-loading-state');

    if (contentDiv) contentDiv.classList.add('hidden');
    if (loadingDiv) loadingDiv.classList.remove('hidden');

    // Choose the correct endpoint based on whether we have talent_id or user_id
    const endpoint = isUserId
        ? `/recruiter/users/${talentId}/talent-details`
        : `/recruiter/talents/${talentId}/details`;

    // Fetch talent details
    fetch(endpoint)
        .then(response => {
            // Check if response is ok
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }

            // Check if response is JSON
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                throw new Error('Server returned non-JSON response (likely an error page)');
            }

            return response.json();
        })
        .then(data => {
            if (loadingDiv) loadingDiv.classList.add('hidden');

            if (data.success && data.talent) {
                displayTalentDetailsInModal(data.talent);
                if (contentDiv) contentDiv.classList.remove('hidden');
            } else {
                if (contentDiv) {
                    contentDiv.innerHTML = '<div class="text-center p-8"><p class="text-red-600">Error memuat detail talenta</p><p class="text-gray-600 text-sm mt-2">' + (data.message || 'Error tidak diketahui') + '</p></div>';
                    contentDiv.classList.remove('hidden');
                }
            }
        })
        .catch(error => {
            console.error('Error fetching talent details:', error);
            if (loadingDiv) loadingDiv.classList.add('hidden');
            if (contentDiv) {
                let errorMessage = 'Network error occurred';
                if (error.message.includes('HTTP 404')) {
                    errorMessage = 'Talent not found';
                } else if (error.message.includes('HTTP 403')) {
                    errorMessage = 'Access denied - insufficient permissions';
                } else if (error.message.includes('HTTP 500')) {
                    errorMessage = 'Server error occurred';
                } else if (error.message.includes('non-JSON response')) {
                    errorMessage = 'Server returned an error page instead of data';
                }

                contentDiv.innerHTML = '<div class="text-center p-8"><p class="text-red-600">Error memuat detail talenta</p><p class="text-gray-600 text-sm mt-2">' + errorMessage + '</p><p class="text-gray-500 text-xs mt-2">Endpoint: ' + endpoint + '</p></div>';
                contentDiv.classList.remove('hidden');
            }
        });
}

// Function to display talent details in the modal
function displayTalentDetailsInModal(talent) {
    const content = `
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Profile Info -->
            <div class="lg:col-span-1">
                <div class="bg-gradient-to-br from-blue-50 to-indigo-100 rounded-2xl p-6 text-center">
                    ${talent.avatar
                        ? `<img class="w-24 h-24 rounded-2xl object-cover mx-auto mb-4 shadow-lg" src="${talent.avatar}" alt="${talent.name}">`
                        : `<div class="w-24 h-24 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
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
                            <p class="text-gray-900 font-medium">${talent.joined_date || 'Unknown'}</p>
                        </div>
                    </div>
                </div>

                <!-- Skills and Experience -->
                ${talent.skills ? `
                <div class="bg-white border border-gray-200 rounded-2xl p-6">
                    <h5 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-cogs text-blue-600 mr-2"></i>
                        Skills & Experience
                    </h5>
                    <div class="flex flex-wrap gap-2">
                        ${talent.skills.map(skill => `
                            <span class="bg-blue-100 text-blue-800 text-sm font-medium px-3 py-1 rounded-full">
                                ${skill.name || skill}${skill.level ? ` (${skill.level})` : ''}
                            </span>
                        `).join('')}
                    </div>
                </div>
                ` : ''}

                <!-- Bio -->
                ${talent.bio ? `
                <div class="bg-white border border-gray-200 rounded-2xl p-6">
                    <h5 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-user-edit text-blue-600 mr-2"></i>
                        Professional Bio
                    </h5>
                    <p class="text-gray-700 leading-relaxed">${talent.bio}</p>
                </div>
                ` : ''}

                <!-- Statistics -->
                ${talent.stats ? `
                <div class="bg-white border border-gray-200 rounded-2xl p-6">
                    <h5 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-chart-bar text-blue-600 mr-2"></i>
                        Performance Statistics
                    </h5>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="text-center">
                            <div class="text-xs text-gray-500">Velocity</div>
                            <div class="font-semibold text-sm">${Math.round(parseFloat(talent.stats.learning_velocity?.score || 0))}%</div>
                        </div>
                        <div class="text-center">
                            <div class="text-xs text-gray-500">Consistency</div>
                            <div class="font-semibold text-sm">${Math.round(parseFloat(talent.stats.consistency?.score || 0))}%</div>
                        </div>
                        <div class="text-center">
                            <div class="text-xs text-gray-500">Adaptability</div>
                            <div class="font-semibold text-sm">${Math.round(parseFloat(talent.stats.adaptability?.score || 0))}%</div>
                        </div>
                        <div class="text-center">
                            <div class="text-xs text-gray-500">Years Exp</div>
                            <div class="font-semibold text-sm">${talent.stats.experience_years || 0}</div>
                        </div>
                    </div>
                </div>
                ` : ''}
            </div>
        </div>
    `;

    const contentDiv = document.getElementById('modal-talent-content');
    if (contentDiv) {
        contentDiv.innerHTML = content;
    }
}

// Close modal function
function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
}

function viewTalentDetailsInModal(talentId, talentName) {
    // Show the modal
    const modal = document.getElementById('talent-details-modal');
    const contentDiv = document.getElementById('modal-talent-content');
    const loadingDiv = document.getElementById('modal-loading-state');
    
    if (!modal) {
        console.error('Talent details modal not found');
        return;
    }
    
    if (!talentId) {
        console.error('No talent ID provided');
        alert('ID talenta tidak tersedia');
        return;
    }
    
    // Show modal and loading state
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    
    // Show loading state
    if (loadingDiv) {
        loadingDiv.classList.remove('hidden');
    }
    if (contentDiv) {
        contentDiv.classList.add('hidden');
    }
    
    // Fetch talent details from server
    const endpoint = `/recruiter/talents/${talentId}/details`;
    
    fetch(endpoint)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Talent data received:', data);
            
            // Hide loading state
            if (loadingDiv) {
                loadingDiv.classList.add('hidden');
            }
            
            // Check if response is successful and has talent data
            if (data.success && data.talent) {
                // Display talent details with the talent object
                displayTalentDetails(data.talent);
            } else {
                throw new Error('Invalid response format or missing talent data');
            }
        })
        .catch(error => {
            console.error('Error fetching talent details:', error);
            
            // Hide loading state
            if (loadingDiv) {
                loadingDiv.classList.add('hidden');
            }
            
            // Show error message
            if (contentDiv) {
                let errorMessage = 'Network error occurred';
                if (error.message.includes('HTTP 404')) {
                    errorMessage = 'Talent not found';
                } else if (error.message.includes('HTTP 403')) {
                    errorMessage = 'Access denied - insufficient permissions';
                } else if (error.message.includes('HTTP 500')) {
                    errorMessage = 'Server error occurred';
                }
                
                contentDiv.innerHTML = `
                    <div class="text-center p-8">
                        <i class="fas fa-exclamation-triangle text-4xl text-red-500 mb-4"></i>
                        <p class="text-red-600 font-medium mb-2">Error memuat detail talenta</p>
                        <p class="text-gray-600 text-sm">${errorMessage}</p>
                        <p class="text-gray-500 text-xs mt-2">Endpoint: ${endpoint}</p>
                        <button onclick="viewTalentDetailsInModal('${talentId}', '${talentName || 'Unknown'}')" 
                                class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-redo mr-2"></i>Coba Lagi
                        </button>
                    </div>
                `;
                contentDiv.classList.remove('hidden');
            }
        });
}

function displayTalentDetails(talent) {
    const contentDiv = document.getElementById('modal-talent-content');
    const loadingDiv = document.getElementById('modal-loading-state');
    
    const html = `
        <div class="space-y-6">
            <!-- Header Section -->
            <div class="flex items-start space-x-4">
                <div class="flex-shrink-0">
                    ${talent.avatar ? 
                        `<img src="${talent.avatar}" alt="${talent.name}" class="w-20 h-20 rounded-full object-cover border-2 border-gray-200">` :
                        `<div class="w-20 h-20 rounded-full bg-gray-200 flex items-center justify-center border-2 border-gray-200">
                            <i class="fas fa-user text-2xl text-gray-400"></i>
                        </div>`
                    }
                </div>
                <div class="flex-1">
                    <h4 class="text-xl font-bold text-gray-900">${talent.name}</h4>
                    <p class="text-gray-600">${talent.email}</p>
                    ${talent.phone ? `<p class="text-gray-600"><i class="fas fa-phone mr-2"></i>${talent.phone}</p>` : ''}
                    ${talent.location ? `<p class="text-gray-600"><i class="fas fa-map-marker-alt mr-2"></i>${talent.location}</p>` : ''}
                    ${talent.job ? `<p class="text-gray-600"><i class="fas fa-briefcase mr-2"></i>${talent.job}</p>` : ''}
                    ${talent.joined_date ? `<p class="text-gray-600"><i class="fas fa-calendar mr-2"></i>Bergabung: ${talent.joined_date}</p>` : ''}
                </div>
            </div>
            
            <!-- Skills Section -->
            ${talent.skills && talent.skills.length > 0 ? `
                <div>
                    <h5 class="text-lg font-semibold text-gray-900 mb-3">Keahlian</h5>
                    <div class="flex flex-wrap gap-2">
                        ${talent.skills.map(skill => `
                            <span class="px-3 py-1 bg-blue-100 text-blue-800 text-sm rounded-full">
                                ${typeof skill === 'object' ? (skill.skill_name || skill.name || 'Unknown Skill') : skill}
                            </span>
                        `).join('')}
                    </div>
                </div>
            ` : `
                <div>
                    <h5 class="text-lg font-semibold text-gray-900 mb-3">Keahlian</h5>
                    <p class="text-gray-500 text-sm">Belum ada keahlian yang terdaftar</p>
                </div>
            `}            
            <!-- Status Section -->
            <div>
                <h5 class="text-lg font-semibold text-gray-900 mb-3">Status</h5>
                <div class="flex items-center space-x-4">
                    <span class="px-3 py-1 rounded-full text-sm ${
                        talent.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                    }">
                        ${talent.is_active ? 'Aktif' : 'Tidak Aktif'}
                    </span>
                </div>
            </div>
        </div>
    `;
    
    contentDiv.innerHTML = html;
    loadingDiv.classList.add('hidden');
    contentDiv.classList.remove('hidden');
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
}

    // Search Functions for Talent Selection
    function searchTalents() {
        const searchInput = document.getElementById('talentSearchInput');
        const searchTerm = searchInput.value.toLowerCase().trim();
        const talentCards = document.querySelectorAll('.talent-card');
        const clearBtn = document.getElementById('clearSearchBtn');
        const searchResultsInfo = document.getElementById('searchResultsInfo');
        const searchResultsText = document.getElementById('searchResultsText');
        
        let visibleCount = 0;
        let totalCount = talentCards.length;
        
        // Show/hide clear button
        if (searchTerm.length > 0) {
            clearBtn.classList.remove('hidden');
        } else {
            clearBtn.classList.add('hidden');
        }
        
        // Filter talent cards
        talentCards.forEach(card => {
            const talentName = card.getAttribute('data-talent-name') || '';
            const talentSkills = card.getAttribute('data-talent-skills') || '';
            
            const nameMatch = talentName.includes(searchTerm.toLowerCase());
            const skillsMatch = talentSkills.includes(searchTerm.toLowerCase());
            
            const slideContainer = card.closest('.talent-carousel-slide');
            
            if (searchTerm === '' || nameMatch || skillsMatch) {
                if (slideContainer) {
                    slideContainer.style.display = 'block';
                    slideContainer.classList.remove('hidden');
                }
                card.style.display = 'block';
                card.classList.remove('hidden');
                visibleCount++;
            } else {
                if (slideContainer) {
                    slideContainer.style.display = 'none';
                    slideContainer.classList.add('hidden');
                }
                card.style.display = 'none';
                card.classList.add('hidden');
            }
        });
        
        // Update search results info
        if (searchTerm.length > 0) {
            searchResultsInfo.classList.remove('hidden');
            if (visibleCount === 0) {
                searchResultsText.textContent = `Tidak ada talenta yang ditemukan untuk "${searchInput.value}"`;
                searchResultsInfo.className = 'mt-3 p-3 bg-red-50 border border-red-200 rounded-lg';
                searchResultsText.className = 'text-red-800 text-sm';
            } else {
                searchResultsText.textContent = `Ditemukan ${visibleCount} talenta${visibleCount !== 1 ? '' : ''} untuk "${searchInput.value}"`;
                searchResultsInfo.className = 'mt-3 p-3 bg-blue-50 border border-blue-200 rounded-lg';
                searchResultsText.className = 'text-blue-800 text-sm';
            }
        } else {
            searchResultsInfo.classList.add('hidden');
        }
        
        // Recalculate carousel layout after filtering
        if (typeof calculateLayout === 'function') {
            calculateLayout();
        }
        if (typeof updateCarousel === 'function') {
            updateCarousel();
        }
    }

    function clearSearch() {
        const searchInput = document.getElementById('talentSearchInput');
        const clearBtn = document.getElementById('clearSearchBtn');
        const searchResultsInfo = document.getElementById('searchResultsInfo');
        const talentCards = document.querySelectorAll('.talent-card');
        
        // Clear input
        searchInput.value = '';
        
        // Hide clear button and results info
        clearBtn.classList.add('hidden');
        searchResultsInfo.classList.add('hidden');
        
        // Show all talent cards
        talentCards.forEach(card => {
            const slideContainer = card.closest('.talent-carousel-slide');
            if (slideContainer) {
                slideContainer.style.display = 'block';
                slideContainer.classList.remove('hidden');
            }
            card.style.display = 'block';
            card.classList.remove('hidden');
        });
        
        // Recalculate carousel layout after clearing search
        if (typeof calculateLayout === 'function') {
            calculateLayout();
        }
        if (typeof updateCarousel === 'function') {
            updateCarousel();
        }
    }
</script>

<style>
/* Talent Selection Card Styles */
.talent-selection-card {
    transition: all 0.3s ease;
    position: relative;
}

.talent-selection-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.talent-selection-card.selected {
    border-color: #3b82f6 !important;
    background-color: #eff6ff !important;
}

.talent-selection-card .selection-checkmark {
    transition: all 0.2s ease;
}

.talent-selection-card .absolute.top-3.right-3 > div {
    transition: all 0.2s ease;
}

/* Custom scrollbar for talent grid */
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 3px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Performance metrics styling */
.talent-selection-card .metric-value {
    font-weight: 600;
    color: #1f2937;
}

/* Red flag indicator */
.talent-selection-card .red-flag-indicator {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.7;
    }
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .talent-selection-card {
        margin-bottom: 1rem;
    }

    .talent-selection-card .grid-cols-3 {
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 0.25rem;
    }
}

/* Talent Carousel Styles */
.talent-carousel-container {
    position: relative;
    padding: 0 0 1rem 0;
}

.talent-carousel-track {
    margin: 0 12px;
    position: relative;
}

.talent-carousel-content {
    display: flex;
    transition: transform 0.5s ease-in-out;
    cursor: grab;
}

.talent-carousel-content:active {
    cursor: grabbing;
}

/* Talent cards inside carousel should show pointer cursor and prevent drag cursor */
.talent-carousel-content .talent-selection-card {
    cursor: pointer !important;
}

.talent-carousel-content .talent-selection-card:hover {
    cursor: pointer !important;
}

/* Show grabbing cursor only on empty areas of carousel */
.talent-carousel-content .talent-carousel-slide {
    pointer-events: none; /* Allow events to pass through to cards */
}

.talent-carousel-content .talent-selection-card {
    pointer-events: auto; /* Re-enable events on cards */
}

.talent-carousel-slide {
    flex: 0 0 auto;
    width: 320px;
    padding: 0 8px;
}

/* Navigation buttons */
#carouselPrevBtn, #carouselNextBtn {
    background-color: white;
    border: 1px solid #d1d5db;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6b7280;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    transition: all 0.2s ease;
    z-index: 10;
}

#carouselPrevBtn:hover:not(:disabled), #carouselNextBtn:hover:not(:disabled) {
    background-color: #f9fafb;
    color: #374151;
    box-shadow: 0 6px 10px -1px rgba(0, 0, 0, 0.15), 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

#carouselPrevBtn:disabled, #carouselNextBtn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    color: #9ca3af;
}

/* Carousel indicators */
#carouselIndicators {
    margin-top: 1rem;
}

#carouselIndicators button {
    transition: all 0.2s ease;
    cursor: pointer;
}

#carouselIndicators button:hover {
    background-color: #6b7280 !important;
}

/* Responsive carousel */
@media (max-width: 1024px) {
    .talent-carousel-slide {
        width: 300px;
    }
}

@media (max-width: 768px) {
    .talent-carousel-slide {
        width: 280px;
    }

    .talent-carousel-track {
        margin: 0 8px;
    }

    #carouselPrevBtn, #carouselNextBtn {
        width: 36px;
        height: 36px;
    }
}

@media (max-width: 640px) {
    .talent-carousel-slide {
        width: 260px;
    }

    .talent-carousel-track {
        margin: 0 4px;
    }
}
</style>

@endpush
