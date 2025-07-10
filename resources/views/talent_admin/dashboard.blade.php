@extends('layout.template.mainTemplate')

@section('title', 'Dashboard Admin Talent')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endpush

@section('container')

@php
    $latestRequests = $latestRequests ?? collect([]);
    $latestTalents = $latestTalents ?? collect([]);
    $latestRecruiters = $latestRecruiters ?? collect([]);
@endphp

{{-- Include Talent Request Notifications --}}
@include('components.talent-request-notifications')

<div class="min-h-screen bg-gray-50 p-6">
    <!-- Page Heading with Welcome Message -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">
                <i class="fas fa-tachometer-alt text-purple-600 mr-3"></i>
                Dashboard Pencarian Talent
            </h1>
            <p class="text-gray-600">Selamat datang kembali! Berikut adalah yang terjadi dengan platform pencarian talent Anda.</p>
        </div>
        <div class="mt-4 sm:mt-0 flex gap-3">
            <button onclick="refreshDashboard()" id="refreshBtn"
                    class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-xl hover:bg-purple-700 transition-all duration-200 font-medium shadow-lg">
                <i class="fas fa-sync-alt mr-2"></i>
                Muat Ulang Data
            </button>
            <span class="inline-flex items-center px-4 py-2 bg-purple-100 text-purple-800 rounded-full text-sm font-medium">
                <i class="fas fa-shield-alt mr-2"></i>
                Administrator Talent
            </span>
        </div>
    </div>

    <!-- Overview Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <!-- Total Talents Card -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden hover-lift">
            <div class="bg-gradient-to-br from-blue-500 to-blue-700 p-6">
                <div class="flex items-center justify-between">
                    <div class="text-white">
                        <div class="text-3xl font-bold">{{ $totalTalents }}</div>
                        <div class="text-blue-100 text-sm font-medium">Total Talent</div>
                    </div>
                    <i class="fas fa-users text-blue-200 text-4xl"></i>
                </div>
            </div>
            <div class="p-4 bg-blue-50">
                <div class="flex items-center text-blue-700 text-sm">
                    <i class="fas fa-arrow-up mr-2"></i>
                    <span>Aktif: {{ $activeTalents }}</span>
                </div>
            </div>
        </div>

        <!-- Total Recruiters Card -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden hover-lift">
            <div class="bg-gradient-to-br from-indigo-500 to-indigo-700 p-6">
                <div class="flex items-center justify-between">
                    <div class="text-white">
                        <div class="text-3xl font-bold">{{ $totalRecruiters }}</div>
                        <div class="text-indigo-100 text-sm font-medium">Total Perekrut</div>
                    </div>
                    <i class="fas fa-building text-indigo-200 text-4xl"></i>
                </div>
            </div>
            <div class="p-4 bg-indigo-50">
                <div class="flex items-center text-indigo-700 text-sm">
                    <i class="fas fa-check-circle mr-2"></i>
                    <span>Aktif: {{ $activeRecruiters }}</span>
                </div>
            </div>
        </div>
        <!-- Pending Requests Card -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden hover-lift">
            <div class="bg-gradient-to-br from-orange-500 to-orange-700 p-6">
                <div class="flex items-center justify-between">
                    <div class="text-white">
                        <div class="text-3xl font-bold">{{ $pendingRequests }}</div>
                        <div class="text-orange-100 text-sm font-medium">Permintaan Tertunda</div>
                    </div>
                    <i class="fas fa-hourglass-half text-orange-200 text-4xl"></i>
                </div>
            </div>
            <div class="p-4 bg-orange-50">
                <div class="flex items-center text-orange-700 text-sm">
                    @if($pendingRequests > 0)
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <span>Perlu Perhatian</span>
                    @else
                        <i class="fas fa-check mr-2"></i>
                        <span>Semua Aman</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions & Management Cards -->
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 mb-8">
        <div class="bg-gradient-to-r from-purple-600 to-indigo-600 rounded-t-2xl p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <h3 class="text-lg font-semibold text-white">Aksi Manajemen Cepat</h3>
                </div>
            </div>
        </div>
        <div class="p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-6">
                <!-- Analytics Dashboard (Phase 1 Enhancement) -->
                <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-2xl p-6 hover-lift border border-purple-200">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-purple-700 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                            <i class="fas fa-chart-bar text-white text-xl"></i>
                        </div>
                        <h4 class="text-lg font-semibold text-gray-900 mb-2">Analitik</h4>
                        <p class="text-gray-600 text-sm mb-6">Lihat analitik skill, konversi, dan permintaan pasar talent.</p>
                        <div class="space-y-3">
                            <a href="{{ route('talent_admin.analytics') }}" class="block w-full px-4 py-2 bg-purple-600 text-white rounded-xl hover:bg-purple-700 transition-all duration-200 font-medium text-sm">
                                <i class="fas fa-chart-line mr-2"></i>Lihat Analitik
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Manage Talents -->
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl p-6 hover-lift border border-blue-200">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-700 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                            <i class="fas fa-users text-white text-xl"></i>
                        </div>
                        <h4 class="text-lg font-semibold text-gray-900 mb-2">Kelola Talent</h4>
                        <p class="text-gray-600 text-sm mb-6">Lihat dan kelola profil talent, keahlian, dan status ketersediaan.</p>
                        <div class="space-y-3">
                            <a href="{{ route('talent_admin.manage_talents') }}" class="block w-full px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-all duration-200 font-medium text-sm">
                                <i class="fas fa-eye mr-2"></i>Lihat Semua Talent
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Manage Recruiters -->
                <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-2xl p-6 hover-lift border border-indigo-200">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-gradient-to-br from-indigo-500 to-indigo-700 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                            <i class="fas fa-building text-white text-xl"></i>
                        </div>
                        <h4 class="text-lg font-semibold text-gray-900 mb-2">Kelola Perekrut</h4>
                        <p class="text-gray-600 text-sm mb-6">Awasi akun perekrut dan informasi perusahaan.</p>
                        <div class="space-y-3">
                            <a href="{{ route('talent_admin.manage_recruiters') }}" class="block w-full px-4 py-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition-all duration-200 font-medium text-sm">
                                <i class="fas fa-eye mr-2"></i>Lihat Semua Perekrut
                            </a>
                            <button class="w-full px-4 py-2 bg-white text-indigo-600 border-2 border-indigo-600 rounded-xl hover:bg-indigo-50 transition-all duration-200 font-medium text-sm" onclick="showAddRecruiterModal()">
                                <i class="fas fa-plus mr-2"></i>Tambah Perekrut
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Manage Requests -->
                <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-2xl p-6 hover-lift border border-orange-200">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-gradient-to-br from-orange-500 to-orange-700 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                            <i class="fas fa-handshake text-white text-xl"></i>
                        </div>
                        <h4 class="text-lg font-semibold text-gray-900 mb-2">Kelola Permintaan</h4>
                        <p class="text-gray-600 text-sm mb-6">Tinjau dan proses permintaan pencarian talent dari perekrut.</p>
                        <div class="space-y-3">
                            <a href="{{ route('talent_admin.manage_requests') }}" class="block w-full px-4 py-2 bg-orange-600 text-white rounded-xl hover:bg-orange-700 transition-all duration-200 font-medium text-sm">
                                <i class="fas fa-list mr-2"></i>Lihat Semua Permintaan
                            </a>
                            <a href="{{ route('talent_admin.manage_requests', ['status' => 'pending']) }}" class="block w-full px-4 py-2 bg-white text-orange-600 border-2 border-orange-600 rounded-xl hover:bg-orange-50 transition-all duration-200 font-medium text-sm">
                                <i class="fas fa-clock mr-2"></i>Menunggu ({{ $pendingRequests }})
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Manage Talent Admins -->
                <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-2xl p-6 hover-lift border border-red-200">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-gradient-to-br from-red-500 to-red-700 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                            <i class="fas fa-user-shield text-white text-xl"></i>
                        </div>
                        <h4 class="text-lg font-semibold text-gray-900 mb-2">Kelola Admin</h4>
                        <p class="text-gray-600 text-sm mb-6">Kelola akun talent admin, buat, edit, dan hapus admin.</p>
                        <div class="space-y-3">
                            <a href="{{ route('talent_admin.manage_talent_admins') }}" class="block w-full px-4 py-2 bg-red-600 text-white rounded-xl hover:bg-red-700 transition-all duration-200 font-medium text-sm">
                                <i class="fas fa-users-cog mr-2"></i>Kelola Admin
                            </a>
                            <a href="{{ route('talent_admin.create_talent_admin') }}" class="block w-full px-4 py-2 bg-white text-red-600 border-2 border-red-600 rounded-xl hover:bg-red-50 transition-all duration-200 font-medium text-sm">
                                <i class="fas fa-plus mr-2"></i>Tambah Admin
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Recent Activity Section -->
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 mb-8">
        <div class="bg-gradient-to-r from-green-600 to-teal-600 rounded-t-2xl p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div>
                        <h3 class="text-lg font-semibold text-white">Permintaan yang Perlu Perhatian</h3>
                        <p class="text-green-100 text-sm">Permintaan baru dan yang sudah diterima talent, menunggu persetujuan admin</p>
                    </div>
                </div>
                <a href="{{ route('talent_admin.manage_requests') }}" class="px-4 py-2 bg-white text-green-600 rounded-xl hover:bg-green-50 transition-all duration-200 font-medium text-sm shadow-sm border border-white border-opacity-30 no-underline">
                    <i class="fas fa-eye mr-2"></i>Lihat Semua Permintaan
                </a>
            </div>
        </div>
        <div class="p-6">
            @forelse($latestRequests as $request)
                @if($request && is_object($request))
                <div class="flex items-center p-4 mb-4 bg-gray-50 rounded-xl hover:bg-white hover:shadow-lg transition-all duration-200 border border-gray-100">
                    <div class="mr-4">
                        @if($request->recruiter && $request->recruiter->user && $request->recruiter->user->avatar)
                            <img class="w-12 h-12 rounded-xl object-cover shadow-md" src="{{ asset('storage/' . $request->recruiter->user->avatar) }}"
                                 alt="{{ $request->recruiter->user->name }}">
                        @else
                            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-md">
                                <i class="fas fa-building text-white"></i>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="font-semibold text-gray-900">
                                {{ $request->recruiter && $request->recruiter->user ? $request->recruiter->user->name : 'Unknown Recruiter' }}
                            </h4>
                            <span class="text-gray-500 text-sm">{{ $request->created_at ? $request->created_at->diffForHumans() : 'Unknown time' }}</span>
                        </div>
                        <p class="text-gray-600 text-sm mb-2">{{ Str::limit($request->project_title ?? 'No title', 60) }}</p>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <span class="text-gray-500 text-xs">
                                    Untuk: {{ $request->talent && $request->talent->user ? $request->talent->user->name : ($request->talentUser ? $request->talentUser->name : 'Unknown Talent') }}
                                </span>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $request->getStatusBadgeColorClasses() }}">
                                    <i class="{{ $request->getStatusIcon() }} mr-1"></i>
                                    {{ $request->getUnifiedDisplayStatus() }}
                                </span>
                            </div>
                            <div class="flex space-x-2">
                                @if($request->talent_accepted && !$request->admin_accepted)
                                    <form action="{{ route('talent_admin.update_request_status', $request) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="action" value="admin_approve">
                                        <button type="submit" class="px-3 py-1 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all duration-200 text-sm"
                                                onclick="return confirm('Setujui permintaan talent ini?')">
                                            <i class="fas fa-check mr-1"></i>Setujui
                                        </button>
                                    </form>
                                @endif
                                <a href="{{ route('talent_admin.show_request', $request) }}"
                                   class="px-3 py-1 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-200 text-sm no-underline">
                                    <i class="fas fa-eye mr-1"></i>Detail
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            @empty
                <div class="text-center py-12">
                    <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-tasks text-gray-400 text-2xl"></i>
                    </div>
                    <h4 class="text-lg font-semibold text-gray-700 mb-2">Tidak ada permintaan yang perlu perhatian</h4>
                    <p class="text-gray-500">Semua permintaan sudah diproses atau belum ada permintaan baru dari perekrut.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Recent Project Requests Section -->
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 mb-8">
        <div class="bg-gradient-to-r from-purple-600 to-pink-600 rounded-t-2xl p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div>
                        <h3 class="text-lg font-semibold text-white">Permintaan Proyek Terbaru</h3>
                        <p class="text-purple-100 text-sm">Proyek terbaru yang diajukan oleh perekrut</p>
                    </div>
                </div>
                <a href="{{ route('admin.projects.index') }}" class="px-4 py-2 bg-white text-purple-600 rounded-xl hover:bg-purple-50 transition-all duration-200 font-medium text-sm shadow-sm border border-white border-opacity-30">
                    <i class="fas fa-eye mr-2"></i>Lihat Semua Proyek
                </a>
            </div>
        </div>
        <div class="p-6">
            @php
                // Get recent projects (you may need to pass this from the controller)
                $recentProjects = $recentProjects ?? collect([]);
            @endphp

            @forelse($recentProjects as $project)
                <div class="flex items-start p-4 mb-4 bg-gray-50 rounded-xl hover:bg-white hover:shadow-lg transition-all duration-200 border border-gray-100">
                    <div class="mr-4 mt-1">
                        @if($project->recruiter && $project->recruiter->user && $project->recruiter->user->avatar)
                            <img class="w-12 h-12 rounded-xl object-cover shadow-md" src="{{ asset('storage/' . $project->recruiter->user->avatar) }}"
                                 alt="{{ $project->recruiter->user->name }}">
                        @else
                            <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl flex items-center justify-center shadow-md">
                                <i class="fas fa-project-diagram text-white"></i>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1">
                        <div class="flex items-start justify-between mb-2">
                            <div>
                                <h4 class="font-semibold text-gray-900 mb-1">{{ $project->title }}</h4>
                                <p class="text-gray-600 text-sm">
                                    <i class="fas fa-building mr-1"></i>
                                    {{ $project->recruiter && $project->recruiter->user ? $project->recruiter->user->name : 'Unknown Recruiter' }}
                                </p>
                            </div>
                            <div class="flex flex-col items-end">
                                <span class="text-gray-500 text-xs mb-2">{{ $project->created_at ? $project->created_at->diffForHumans() : 'Unknown time' }}</span>
                                <span class="@if($project->status === 'pending_admin') bg-yellow-100 text-yellow-800 @elseif($project->status === 'approved') bg-green-100 text-green-800 @elseif($project->status === 'active') bg-blue-100 text-blue-800 @elseif($project->status === 'completed') bg-gray-100 text-gray-800 @elseif($project->status === 'cancelled') bg-red-100 text-red-800 @elseif($project->status === 'overdue') bg-red-100 text-red-800 @elseif($project->status === 'closure_requested') bg-purple-100 text-purple-800 @else bg-gray-100 text-gray-800 @endif px-2 py-1 rounded-full text-xs font-medium">
                                    {{ ucwords(str_replace('_', ' ', $project->status)) }}
                                </span>
                            </div>
                        </div>

                        <div class="space-y-1 mb-3 text-sm text-gray-600">
                            <div class="flex items-center justify-between">
                                <span>Anggaran:</span>
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
                            <div class="flex items-center justify-between">
                                <span>Durasi:</span>
                                <span class="font-medium">
                                    @if($project->estimated_duration_days)
                                        {{ $project->estimated_duration_days }} hari
                                    @else
                                        Belum ditentukan
                                    @endif
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span>Industri:</span>
                                <span class="font-medium">{{ $project->industry ?: 'Tidak ditentukan' }}</span>
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <div class="text-xs text-gray-500">
                                <i class="fas fa-users mr-1"></i>
                                {{ $project->assignments->count() }} talent ditugaskan
                            </div>
                            <div class="flex space-x-2">
                                <a href="{{ route('admin.projects.show', $project) }}"
                                   class="px-3 py-1 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-all duration-200 text-sm no-underline">
                                    <i class="fas fa-eye mr-1"></i>Detail
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-12">
                    <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-project-diagram text-gray-400 text-2xl"></i>
                    </div>
                    <h4 class="text-lg font-semibold text-gray-700 mb-2">Belum ada proyek terbaru</h4>
                    <p class="text-gray-500">Belum ada proyek yang diajukan oleh perekrut.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Recent Users Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Talents -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 p-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <h3 class="text-lg font-semibold text-white">Talent Terbaru</h3>
                    </div>
                    <span class="px-3 py-1 bg-white bg-opacity-20 text-black rounded-full text-sm font-medium">{{ $latestTalents->count() }}</span>
                </div>
            </div>
            <div class="p-6">
                @forelse($latestTalents as $talent)
                    @if($talent && is_object($talent))
                    <div class="flex items-center p-3 mb-3 rounded-xl hover:bg-gray-50 transition-all duration-200">
                        <div class="mr-3">
                            @if($talent->user && $talent->user->avatar)
                                <img class="w-11 h-11 rounded-xl object-cover shadow-md" src="{{ asset('storage/' . $talent->user->avatar) }}"
                                     alt="{{ $talent->user->name }}">
                            @else
                                <div class="w-11 h-11 bg-gradient-to-br from-gray-400 to-gray-600 rounded-xl flex items-center justify-center shadow-md">
                                    <i class="fas fa-user text-white"></i>
                                </div>
                            @endif
                        </div>
                        <div class="flex-1">
                            <h4 class="font-semibold text-gray-900">{{ optional($talent->user)->name ?? 'Unknown User' }}</h4>
                            <p class="text-gray-500 text-sm">
                                <i class="fas fa-briefcase mr-1"></i>
                                {{ optional($talent->user)->pekerjaan ?? 'Posisi tidak ditentukan' }}
                            </p>
                        </div>
                        <div>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ ($talent->is_active ?? false) ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                <i class="fas fa-{{ ($talent->is_active ?? false) ? 'check-circle' : 'pause-circle' }} mr-1"></i>
                                {{ ($talent->is_active ?? false) ? 'Aktif' : 'Tidak Aktif' }}
                            </span>
                        </div>
                    </div>
                    @endif
                @empty
                    <div class="text-center py-8">
                        <i class="fas fa-user-plus text-gray-400 text-3xl mb-3"></i>
                        <p class="text-gray-500">Belum ada talent yang terdaftar.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Recent Recruiters -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 p-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <h3 class="text-lg font-semibold text-white">Perekrut Terbaru</h3>
                    </div>
                    <span class="px-3 py-1 bg-white bg-opacity-20 text-black rounded-full text-sm font-medium">{{ $latestRecruiters->count() }}</span>
                </div>
            </div>
            <div class="p-6">
                @forelse($latestRecruiters as $recruiter)
                    @if($recruiter && is_object($recruiter))
                    <div class="flex items-center p-3 mb-3 rounded-xl hover:bg-gray-50 transition-all duration-200">
                        <div class="mr-3">
                            @if($recruiter->user && $recruiter->user->avatar)
                                <img class="w-11 h-11 rounded-xl object-cover shadow-md" src="{{ asset('storage/' . $recruiter->user->avatar) }}"
                                     alt="{{ $recruiter->user->name }}">
                            @else
                                <div class="w-11 h-11 bg-gradient-to-br from-gray-400 to-gray-600 rounded-xl flex items-center justify-center shadow-md">
                                    <i class="fas fa-building text-white"></i>
                                </div>
                            @endif
                        </div>
                        <div class="flex-1">
                            <h4 class="font-semibold text-gray-900">{{ optional($recruiter->user)->name ?? 'Unknown User' }}</h4>
                            <p class="text-gray-500 text-sm">
                                <i class="fas fa-building mr-1"></i>
                                {{ $recruiter->company_name ?? optional($recruiter->user)->pekerjaan ?? 'Perusahaan tidak ditentukan' }}
                            </p>
                        </div>
                        <div>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ ($recruiter->is_active ?? false) ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                <i class="fas fa-{{ ($recruiter->is_active ?? false) ? 'check-circle' : 'pause-circle' }} mr-1"></i>
                                {{ ($recruiter->is_active ?? false) ? 'Aktif' : 'Tidak Aktif' }}
                            </span>
                        </div>
                    </div>
                    @endif
                @empty
                    <div class="text-center py-8">
                        <i class="fas fa-user-plus text-gray-400 text-3xl mb-3"></i>
                        <p class="text-gray-500">Belum ada perekrut yang terdaftar.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Add Recruiter Modal -->
<div class="modal fade" id="addRecruiterModal" tabindex="-1" aria-labelledby="addRecruiterModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addRecruiterModalLabel">
                    <i class="fas fa-plus-circle text-indigo-600 mr-2"></i>
                    Tambah Perekrut Baru
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info mb-4">
                    <i class="fas fa-info-circle mr-2"></i>
                    <strong>Info:</strong> Form ini menggunakan validasi yang sama dengan sistem registrasi publik.
                    Perekrut yang dibuat akan memiliki akses dan fitur yang sama seperti yang mendaftar langsung.
                </div>

                <form id="addRecruiterForm" enctype="multipart/form-data">
                    @csrf

                    <!-- Basic Information (matches public registration) -->
                    <div class="mb-3">
                        <label for="recruiter_name" class="form-label">Nama Lengkap *</label>
                        <input type="text" class="form-control" id="recruiter_name" name="name" required>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="mb-3">
                        <label for="recruiter_email" class="form-label">Email *</label>
                        <input type="email" class="form-control" id="recruiter_email" name="email" required>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="mb-3">
                        <label for="recruiter_pekerjaan" class="form-label">Pekerjaan *</label>
                        <input type="text" class="form-control" id="recruiter_pekerjaan" name="pekerjaan" placeholder="misal: HR Manager, Talent Acquisition, dll" required>
                        <div class="invalid-feedback"></div>
                    </div>

                    <!-- Avatar field (optional for admin creation) -->
                    <div class="mb-3">
                        <label for="recruiter_avatar" class="form-label">Avatar (Opsional)</label>
                        <input type="file" class="form-control" id="recruiter_avatar" name="avatar" accept="image/png,image/jpg,image/jpeg">
                        <div class="form-text">File gambar (PNG, JPG, JPEG) maksimal 2MB. Jika tidak diisi, akan menggunakan avatar default.</div>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="mb-3">
                        <label for="recruiter_phone" class="form-label">Nomor Telepon</label>
                        <input type="tel" class="form-control" id="recruiter_phone" name="phone">
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="recruiter_password" class="form-label">Password *</label>
                                <div class="position-relative">
                                    <input type="password" class="form-control pe-5" id="recruiter_password" name="password" required>
                                    <button type="button" class="btn btn-link position-absolute top-50 end-0 translate-middle-y password-toggle-btn"
                                            id="toggleRecruiterPassword">
                                        <i class="fas fa-eye" id="eyeIconPassword"></i>
                                    </button>
                                </div>
                                <div class="form-text">Minimal 8 karakter</div>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="recruiter_password_confirmation" class="form-label">Konfirmasi Password *</label>
                                <div class="position-relative">
                                    <input type="password" class="form-control pe-5" id="recruiter_password_confirmation" name="password_confirmation" required>
                                    <button type="button" class="btn btn-link position-absolute top-50 end-0 translate-middle-y password-toggle-btn"
                                            id="toggleRecruiterPasswordConfirmation">
                                        <i class="fas fa-eye" id="eyeIconPasswordConfirmation"></i>
                                    </button>
                                </div>
                                <div class="form-text">Harus sama dengan password di atas</div>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Optional Company Information -->
                    <hr class="my-4">
                    <h6 class="text-muted mb-3">
                        <i class="fas fa-building mr-2"></i>Informasi Perusahaan (Opsional)
                    </h6>
                    <small class="text-muted mb-3 d-block">Informasi ini dapat diisi nanti oleh perekrut di profil mereka</small>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="company_name" class="form-label">Nama Perusahaan</label>
                                <input type="text" class="form-control" id="company_name" name="company_name">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="industry" class="form-label">Industri</label>
                                <select class="form-select" id="industry" name="industry">
                                    <option value="">Pilih Industri</option>
                                    <option value="Technology">Teknologi</option>
                                    <option value="Finance">Keuangan</option>
                                    <option value="Healthcare">Kesehatan</option>
                                    <option value="Education">Pendidikan</option>
                                    <option value="Retail">Retail</option>
                                    <option value="Manufacturing">Manufaktur</option>
                                    <option value="Construction">Konstruksi</option>
                                    <option value="Transportation">Transportasi</option>
                                    <option value="Hospitality">Perhotelan</option>
                                    <option value="Media">Media</option>
                                    <option value="Other">Lainnya</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="company_size" class="form-label">Ukuran Perusahaan</label>
                                <select class="form-select" id="company_size" name="company_size">
                                    <option value="">Pilih Ukuran</option>
                                    <option value="1-10">1-10 karyawan</option>
                                    <option value="11-50">11-50 karyawan</option>
                                    <option value="51-200">51-200 karyawan</option>
                                    <option value="201-1000">201-1000 karyawan</option>
                                    <option value="1000+">1000+ karyawan</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="address" class="form-label">Alamat</label>
                                <input type="text" class="form-control" id="address" name="address" placeholder="Alamat perusahaan">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="company_description" class="form-label">Deskripsi Perusahaan</label>
                        <textarea class="form-control" id="company_description" name="company_description" rows="3" placeholder="Ceritakan tentang perusahaan..."></textarea>
                        <div class="invalid-feedback"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times mr-2"></i>Batal
                </button>
                <button type="button" class="btn btn-indigo" id="saveRecruiterBtn" onclick="saveRecruiter()">
                    <i class="fas fa-save mr-2"></i>Simpan Perekrut
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function showAddRecruiterModal() {
    // Reset form
    document.getElementById('addRecruiterForm').reset();
    clearFormErrors();

    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('addRecruiterModal'));
    modal.show();
}

function saveRecruiter() {
    const form = document.getElementById('addRecruiterForm');
    const formData = new FormData(form);
    const saveBtn = document.getElementById('saveRecruiterBtn');

    // Clear previous errors
    clearFormErrors();

    // Frontend validation before sending
    const name = formData.get('name');
    const email = formData.get('email');
    const pekerjaan = formData.get('pekerjaan');
    const password = formData.get('password');
    const passwordConfirmation = formData.get('password_confirmation');

    // Basic validation
    if (!name || name.trim() === '') {
        showFieldError('name', 'Nama tidak boleh kosong');
        return;
    }
    if (!email || email.trim() === '') {
        showFieldError('email', 'Email tidak boleh kosong');
        return;
    }
    if (!pekerjaan || pekerjaan.trim() === '') {
        showFieldError('pekerjaan', 'Pekerjaan tidak boleh kosong');
        return;
    }
    if (!password || password.length < 8) {
        showFieldError('password', 'Password minimal 8 karakter');
        return;
    }
    if (password !== passwordConfirmation) {
        showFieldError('password_confirmation', 'Konfirmasi password tidak cocok');
        return;
    }

    // Show loading state
    const originalText = saveBtn.innerHTML;
    saveBtn.disabled = true;
    saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan...';

    // Check CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken) {
        console.error('CSRF token meta tag not found!');
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Kesalahan!',
                text: 'Token keamanan tidak ditemukan. Silakan refresh halaman.',
                icon: 'error',
                confirmButtonColor: '#7c3aed'
            });
        } else {
            alert('Token keamanan tidak ditemukan. Silakan refresh halaman.');
        }
        saveBtn.disabled = false;
        saveBtn.innerHTML = originalText;
        return;
    }

    console.log('CSRF token found:', csrfToken.getAttribute('content').substring(0, 10) + '...');

    fetch('/talent-admin/recruiter', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken.getAttribute('content')
        },
        body: formData
    })
    .then(response => {
        console.log('Response status:', response.status);
        console.log('Response ok:', response.ok);

        if (!response.ok) {
            return response.text().then(text => {
                console.log('Error response body:', text);
                throw new Error(`HTTP ${response.status}: ${text}`);
            });
        }

        // Try to parse JSON response
        return response.json().catch(jsonError => {
            console.error('JSON parsing error:', jsonError);
            return response.text().then(text => {
                console.log('Raw response text:', text);
                throw new Error('Invalid JSON response: ' + text.substring(0, 100));
            });
        });
    })
    .then(data => {
        console.log('Parsed response data:', data);

        if (data.success) {
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('addRecruiterModal'));
            modal.hide();

            // Show success message
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Berhasil!',
                    text: data.message,
                    icon: 'success',
                    confirmButtonColor: '#7c3aed'
                }).then(() => {
                    // Reload page to show new recruiter
                    window.location.reload();
                });
            } else {
                alert('Berhasil: ' + data.message);
                window.location.reload();
            }
        } else {
            // Show validation errors
            if (data.errors) {
                showFormErrors(data.errors);
            }

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Kesalahan!',
                    text: data.message || 'Terjadi kesalahan saat menyimpan data.',
                    icon: 'error',
                    confirmButtonColor: '#7c3aed'
                });
            } else {
                alert('Kesalahan: ' + (data.message || 'Terjadi kesalahan saat menyimpan data.'));
            }
        }
    })
    .catch(error => {
        console.error('Fetch error:', error);
        console.error('Error details:', error.message);

        let errorMessage = 'Terjadi kesalahan koneksi. Silakan coba lagi.';

        // Provide more specific error messages based on the error
        if (error.message.includes('JSON')) {
            errorMessage = 'Server mengembalikan respons yang tidak valid. Silakan periksa log server.';
        } else if (error.message.includes('419')) {
            errorMessage = 'Token keamanan tidak valid. Silakan refresh halaman dan coba lagi.';
        } else if (error.message.includes('500')) {
            errorMessage = 'Terjadi kesalahan pada server. Silakan periksa log aplikasi.';
        } else if (error.message.includes('422')) {
            errorMessage = 'Data yang dimasukkan tidak valid. Silakan periksa form dan coba lagi.';
        }

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Kesalahan!',
                text: errorMessage,
                icon: 'error',
                confirmButtonColor: '#7c3aed'
            });
        } else {
            alert('Kesalahan: ' + errorMessage);
        }
    })
    .finally(() => {
        // Restore button state
        saveBtn.disabled = false;
        saveBtn.innerHTML = originalText;
    });
}

function clearFormErrors() {
    // Remove all error classes and messages
    const form = document.getElementById('addRecruiterForm');
    const inputs = form.querySelectorAll('.form-control, .form-select');
    const feedbacks = form.querySelectorAll('.invalid-feedback');

    inputs.forEach(input => {
        input.classList.remove('is-invalid');
    });

    feedbacks.forEach(feedback => {
        feedback.textContent = '';
    });
}

function showFormErrors(errors) {
    for (const [field, messages] of Object.entries(errors)) {
        const input = document.querySelector(`[name="${field}"]`);
        const feedback = input?.parentElement.querySelector('.invalid-feedback');

        if (input && feedback) {
            input.classList.add('is-invalid');
            feedback.textContent = Array.isArray(messages) ? messages[0] : messages;
        }
    }
}

function showFieldError(fieldName, message) {
    const input = document.querySelector(`[name="${fieldName}"]`);
    const feedback = input?.parentElement.querySelector('.invalid-feedback');

    if (input && feedback) {
        input.classList.add('is-invalid');
        feedback.textContent = message;
    }
}

// Password visibility toggle functionality
document.addEventListener('DOMContentLoaded', function() {
    // Toggle for main password field
    const togglePassword = document.getElementById('toggleRecruiterPassword');
    const passwordField = document.getElementById('recruiter_password');
    const eyeIconPassword = document.getElementById('eyeIconPassword');

    if (togglePassword && passwordField && eyeIconPassword) {
        togglePassword.addEventListener('click', function() {
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                eyeIconPassword.classList.remove('fa-eye');
                eyeIconPassword.classList.add('fa-eye-slash');
            } else {
                passwordField.type = 'password';
                eyeIconPassword.classList.remove('fa-eye-slash');
                eyeIconPassword.classList.add('fa-eye');
            }
        });
    }

    // Toggle for password confirmation field
    const togglePasswordConfirmation = document.getElementById('toggleRecruiterPasswordConfirmation');
    const passwordConfirmationField = document.getElementById('recruiter_password_confirmation');
    const eyeIconPasswordConfirmation = document.getElementById('eyeIconPasswordConfirmation');

    if (togglePasswordConfirmation && passwordConfirmationField && eyeIconPasswordConfirmation) {
        togglePasswordConfirmation.addEventListener('click', function() {
            if (passwordConfirmationField.type === 'password') {
                passwordConfirmationField.type = 'text';
                eyeIconPasswordConfirmation.classList.remove('fa-eye');
                eyeIconPasswordConfirmation.classList.add('fa-eye-slash');
            } else {
                passwordConfirmationField.type = 'password';
                eyeIconPasswordConfirmation.classList.remove('fa-eye-slash');
                eyeIconPasswordConfirmation.classList.add('fa-eye');
            }
        });
    }
});
</script>

<style>
/* Card hover effects */
.hover-lift {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.hover-lift:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

/* Button animations */
.transition-all {
    transition: all 0.2s ease;
}

/* Custom indigo button */
.btn-indigo {
    background-color: #6366f1;
    border-color: #6366f1;
    color: white;
}

.btn-indigo:hover {
    background-color: #4f46e5;
    border-color: #4f46e5;
    color: white;
}

.btn-indigo:focus {
    background-color: #4f46e5;
    border-color: #4f46e5;
    color: white;
    box-shadow: 0 0 0 0.2rem rgba(99, 102, 241, 0.5);
}

.btn-indigo:disabled {
    background-color: #9ca3af;
    border-color: #9ca3af;
    color: white;
}

/* Modal customizations */
.modal-content {
    border-radius: 1rem;
    border: none;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

.modal-header {
    border-bottom: 1px solid #e5e7eb;
    padding: 1.5rem;
}

.modal-body {
    padding: 1.5rem;
}

.modal-footer {
    border-top: 1px solid #e5e7eb;
    padding: 1.5rem;
}

/* Form customizations */
.form-label {
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.5rem;
}

.form-control:focus,
.form-select:focus {
    border-color: #6366f1;
    box-shadow: 0 0 0 0.2rem rgba(99, 102, 241, 0.25);
}

.invalid-feedback {
    display: block;
}

/* Password toggle button styling */
.password-toggle-btn {
    border: none !important;
    background: none !important;
    color: #6b7280 !important;
    padding: 0.375rem 0.75rem !important;
    font-size: 0.875rem;
    line-height: 1.5;
    transition: color 0.15s ease-in-out;
}

.password-toggle-btn:hover {
    color: #374151 !important;
}

.password-toggle-btn:focus {
    box-shadow: none !important;
    outline: none !important;
}

.password-toggle-btn i {
    font-size: 1rem;
}
</style>

@if (session('success'))
    <script>
        // Show success message if available
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                icon: 'success',
                confirmButtonColor: '#7c3aed'
            });
        } else {
            alert('Berhasil: {{ session('success') }}');
        }
    </script>
@endif

@if (session('error'))
    <script>
        // Show error message if available
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Kesalahan!',
                text: '{{ session('error') }}',
                icon: 'error',
                confirmButtonColor: '#7c3aed'
            });
        } else {
            alert('Kesalahan: {{ session('error') }}');
        }
    </script>
@endif

<script>
// Dashboard refresh functionality
function refreshDashboard() {
    const refreshBtn = document.getElementById('refreshBtn');
    const originalContent = refreshBtn.innerHTML;

    // Show loading state
    refreshBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Refreshing...';
    refreshBtn.disabled = true;

    // Clear cache and reload
    fetch('/talent-admin/clear-dashboard-cache', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message briefly, then reload
            refreshBtn.innerHTML = '<i class="fas fa-check mr-2"></i>Refreshed!';
            setTimeout(() => {
                window.location.reload();
            }, 500);
        } else {
            throw new Error('Failed to clear cache');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        refreshBtn.innerHTML = '<i class="fas fa-exclamation-triangle mr-2"></i>Error';
        setTimeout(() => {
            refreshBtn.innerHTML = originalContent;
            refreshBtn.disabled = false;
        }, 2000);
    });
}

// Show notification when page loads if there are pending requests
document.addEventListener('DOMContentLoaded', function() {
    const pendingRequests = {{ $pendingRequests ?? 0 }};
    if (pendingRequests > 0) {
        // Show a small notification
        const notification = document.createElement('div');
        notification.className = 'fixed top-4 right-4 bg-orange-500 text-white px-4 py-2 rounded-lg shadow-lg z-50';
        notification.innerHTML = `<i class="fas fa-bell mr-2"></i>${pendingRequests} permintaan pending`;
        document.body.appendChild(notification);

        // Auto-hide after 5 seconds
        setTimeout(() => {
            notification.remove();
        }, 5000);
    }

    // Auto-refresh dashboard data every 30 seconds if there are pending requests
    if (pendingRequests > 0) {
        setInterval(function() {
            checkForNewRequests();
        }, 30000); // 30 seconds
    }
});

// Function to check for new requests without full page reload
function checkForNewRequests() {
    fetch('/talent-admin/dashboard-data', {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.newRequestsCount > 0) {
            // Show notification for new requests
            const notification = document.createElement('div');
            notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50';
            notification.innerHTML = `<i class="fas fa-plus mr-2"></i>${data.newRequestsCount} permintaan baru`;
            document.body.appendChild(notification);

            // Auto-hide and refresh after 3 seconds
            setTimeout(() => {
                notification.remove();
                window.location.reload();
            }, 3000);
        }
    })
    .catch(error => {
        console.log('Auto-refresh check failed:', error);
    });
}
</script>

@endsection
