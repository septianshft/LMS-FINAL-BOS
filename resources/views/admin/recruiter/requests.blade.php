@extends('layout.template.mainTemplate')

@section('title', 'Permintaan Talenta Saya')
@section('container')

{{-- Full Width Dashboard Container like dashboard.blade.php --}}
<div class="space-y-8">

    {{-- Hero welcome greeting card - Full Width --}}
    <div class="w-full bg-gradient-to-r from-emerald-600 to-teal-600 rounded-2xl p-8 text-white shadow-xl mt-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="w-16 h-16 bg-white bg-opacity-20 rounded-2xl flex items-center justify-center mr-6">
                    <i class="fas fa-clipboard-check text-2xl text-white"></i>
                </div>
                <div>
                    <h1 class="text-4xl lg:text-5xl font-bold text-white mb-2">
                        Permintaan Talenta Saya
                    </h1>
                    <p class="text-lg text-emerald-100">Lacak dan kelola permintaan kolaborasi Anda dengan profesional berbakat</p>

                    <!-- Quick Stats -->
                    <div class="flex flex-wrap gap-4 mt-4">
                        <div class="bg-white/20 backdrop-blur-sm rounded-xl px-4 py-2 border border-white/30">
                            <span class="text-sm text-emerald-100">Total Permintaan:</span>
                            <span class="font-bold text-white ml-1">{{ $requests->total() ?? $requests->count() }}</span>
                        </div>
                        <div class="bg-white/20 backdrop-blur-sm rounded-xl px-4 py-2 border border-white/30">
                            <span class="text-sm text-emerald-100">Halaman Ini:</span>
                            <span class="font-bold text-white ml-1">{{ $requests->count() }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-3">
                <!-- PDF Export Dropdown -->
                <div class="relative">
                    <button id="exportDropdownButtonRequests" class="inline-flex items-center px-6 py-3 bg-white/20 backdrop-blur-sm text-white rounded-xl hover:bg-white/30 transition-all duration-300 font-medium border border-white/30 shadow-lg">
                        <i class="fas fa-download mr-2"></i>Ekspor PDF
                        <i class="fas fa-chevron-down ml-2 text-xs"></i>
                    </button>
                    <div id="exportDropdownMenuRequests" class="absolute right-0 top-full mt-1 w-56 bg-white rounded-lg shadow-lg border border-gray-200 opacity-0 invisible transition-all duration-200 z-10">
                        <div class="py-2">
                            <button onclick="window.location.href='{{ route('recruiter.export_request_history') }}'"
                               class="w-full flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                <i class="fas fa-history mr-3 text-blue-500"></i>
                                <div>
                                    <div class="font-medium">Riwayat Permintaan</div>
                                    <div class="text-xs text-gray-500">Semua permintaan talenta Anda</div>
                                </div>
                            </button>
                            <button onclick="window.location.href='{{ route('recruiter.export_onboarded_talents') }}'"
                               class="w-full flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                <i class="fas fa-user-check mr-3 text-green-500"></i>
                                <div>
                                    <div class="font-medium">Talenta Terdaftar</div>
                                    <div class="text-xs text-gray-500">Talenta yang berhasil dipekerjakan</div>
                                </div>
                            </button>
                        </div>
                    </div>
                </div>

                <button onclick="window.location.reload()"
                        class="inline-flex items-center px-6 py-3 bg-white/20 backdrop-blur-sm text-white rounded-xl hover:bg-white/30 transition-all duration-300 font-medium border border-white/30 shadow-lg">
                    <i class="fas fa-sync-alt mr-2"></i>
                    Refresh
                </button>
            </div>
        </div>
    </div>

    {{-- Full Width Requests Section - like dashboard --}}
    <div class="w-full bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
        <!-- Header with gradient background -->
        <div class="bg-gradient-to-r from-emerald-600 via-teal-600 to-cyan-600 text-white p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center mr-4 border border-white/30">
                        <i class="fas fa-handshake text-xl text-white"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold mb-1">Permintaan Kolaborasi Talenta</h2>
                        <p class="text-emerald-100">Hub manajemen jaringan profesional</p>
                    </div>
                </div>

                <!-- Filter & Sort Controls -->
                <div class="hidden lg:flex items-center space-x-4">
                    <div class="relative">
                        <button id="filterButton" class="px-4 py-2 bg-white/20 backdrop-blur-sm rounded-lg hover:bg-white/30 transition-all duration-300 border border-white/30 flex items-center">
                            <i class="fas fa-filter mr-2"></i>Filter
                            <i class="fas fa-chevron-down ml-2 text-sm"></i>
                        </button>
                        <!-- Filter Dropdown -->
                        <div id="filterDropdown" class="absolute right-0 mt-2 w-64 bg-white rounded-xl shadow-xl border border-gray-200 z-50 hidden">
                            <div class="p-4">
                                <h4 class="font-semibold text-gray-800 mb-3">Filter berdasarkan Status</h4>
                                <div class="space-y-2">
                                    <label class="flex items-center">
                                        <input type="checkbox" value="all" class="filter-status rounded text-emerald-600 mr-2" checked>
                                        <span class="text-sm text-gray-700">Semua Permintaan</span>
                                    </label>
                                            <label class="flex items-center">
                                                <input type="checkbox" value="pending" class="filter-status rounded text-yellow-600 mr-2">
                                                <span class="text-sm text-gray-700">Menunggu</span>
                                            </label>
                                            <label class="flex items-center">
                                                <input type="checkbox" value="approved" class="filter-status rounded text-green-600 mr-2">
                                                <span class="text-sm text-gray-700">Disetujui</span>
                                            </label>
                                            <label class="flex items-center">
                                                <input type="checkbox" value="rejected" class="filter-status rounded text-red-600 mr-2">
                                                <span class="text-sm text-gray-700">Ditolak</span>
                                            </label>
                                            <label class="flex items-center">
                                                <input type="checkbox" value="meeting_arranged" class="filter-status rounded text-blue-600 mr-2">
                                                <span class="text-sm text-gray-700">Pertemuan Dijadwalkan</span>
                                            </label>
                                        </div>
                                        <div class="mt-4 flex gap-2">
                                            <button onclick="applyFilters()" class="flex-1 px-3 py-2 bg-emerald-600 text-white rounded-lg text-sm hover:bg-emerald-700">Terapkan</button>
                                            <button onclick="clearFilters()" class="flex-1 px-3 py-2 bg-gray-400 text-white rounded-lg text-sm hover:bg-gray-500">Bersihkan</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="relative">
                                <button id="sortButton" class="px-4 py-2 bg-white/20 backdrop-blur-sm rounded-lg hover:bg-white/30 transition-all duration-300 border border-white/30 flex items-center">
                                    <i class="fas fa-sort mr-2"></i>Urutkan
                                    <i class="fas fa-chevron-down ml-2 text-sm"></i>
                                </button>
                                <!-- Sort Dropdown -->
                                <div id="sortDropdown" class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-xl border border-gray-200 z-50 hidden">
                                    <div class="p-4">
                                        <h4 class="font-semibold text-gray-800 mb-3">Urutkan berdasarkan</h4>
                                        <div class="space-y-2">
                                            <button onclick="sortRequests('date_desc')" class="w-full text-left px-3 py-2 rounded-lg hover:bg-gray-100 text-sm text-black">
                                                <i class="fas fa-calendar-alt mr-2 text-gray-500"></i>Terbaru Dahulu
                                            </button>
                                            <button onclick="sortRequests('date_asc')" class="w-full text-left px-3 py-2 rounded-lg hover:bg-gray-100 text-sm text-black">
                                                <i class="fas fa-calendar-alt mr-2 text-gray-500"></i>Terlama Dahulu
                                            </button>
                                            <button onclick="sortRequests('title_asc')" class="w-full text-left px-3 py-2 rounded-lg hover:bg-gray-100 text-sm text-black">
                                                <i class="fas fa-sort-alpha-down mr-2 text-gray-500"></i>Judul Proyek A-Z
                                            </button>
                                            <button onclick="sortRequests('title_desc')" class="w-full text-left px-3 py-2 rounded-lg hover:bg-gray-100 text-sm text-black">
                                                <i class="fas fa-sort-alpha-up mr-2 text-gray-500"></i>Judul Proyek Z-A
                                            </button>
                                            <button onclick="sortRequests('status')" class="w-full text-left px-3 py-2 rounded-lg hover:bg-gray-100 text-sm text-black">
                                                <i class="fas fa-tasks mr-2 text-gray-500"></i>Status
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
            </div>

            <!-- Mobile Filter & Sort Controls -->
            <div class="lg:hidden mb-6">
                <div class="flex gap-3">
                    <div class="relative flex-1">
                        <button id="mobileFilterButton" class="w-full px-4 py-3 bg-white/20 backdrop-blur-sm text-white rounded-xl hover:bg-white/30 transition-all duration-300 flex items-center justify-center border border-white/30">
                            <i class="fas fa-filter mr-2"></i>Filter
                            <i class="fas fa-chevron-down ml-2 text-sm"></i>
                        </button>
                        <!-- Mobile Filter Dropdown -->
                        <div id="mobileFilterDropdown" class="absolute left-0 right-0 mt-2 bg-white rounded-xl shadow-xl border border-gray-200 z-50 hidden">
                            <div class="p-4">
                                <h4 class="font-semibold text-gray-800 mb-3">Filter berdasarkan Status</h4>
                                <div class="space-y-2">
                                    <label class="flex items-center">
                                        <input type="checkbox" value="all" class="mobile-filter-status rounded text-emerald-600 mr-2" checked>
                                        <span class="text-sm text-gray-700">Semua Permintaan</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" value="pending" class="mobile-filter-status rounded text-yellow-600 mr-2">
                                        <span class="text-sm text-gray-700">Menunggu</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" value="approved" class="mobile-filter-status rounded text-green-600 mr-2">
                                        <span class="text-sm text-gray-700">Disetujui</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" value="rejected" class="mobile-filter-status rounded text-red-600 mr-2">
                                        <span class="text-sm text-gray-700">Ditolak</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" value="meeting_arranged" class="mobile-filter-status rounded text-blue-600 mr-2">
                                        <span class="text-sm text-gray-700">Pertemuan Dijadwalkan</span>
                                    </label>
                                </div>
                                <div class="mt-4 flex gap-2">
                                    <button onclick="applyMobileFilters()" class="flex-1 px-3 py-2 bg-emerald-600 text-white rounded-lg text-sm hover:bg-emerald-700">Terapkan</button>
                                    <button onclick="clearMobileFilters()" class="flex-1 px-3 py-2 bg-gray-400 text-white rounded-lg text-sm hover:bg-gray-500">Bersihkan</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="relative flex-1">
                        <button id="mobileSortButton" class="w-full px-4 py-3 bg-white/20 backdrop-blur-sm text-white rounded-xl hover:bg-white/30 transition-all duration-300 flex items-center justify-center border border-white/30">
                            <i class="fas fa-sort mr-2"></i>Urutkan
                            <i class="fas fa-chevron-down ml-2 text-sm"></i>
                        </button>
                        <!-- Mobile Sort Dropdown -->
                        <div id="mobileSortDropdown" class="absolute left-0 right-0 mt-2 bg-white rounded-xl shadow-xl border border-gray-200 z-50 hidden">
                            <div class="p-4">
                                <h4 class="font-semibold text-gray-800 mb-3">Urutkan berdasarkan</h4>
                                <div class="space-y-2">
                                    <button onclick="sortMobileRequests('date_desc')" class="w-full text-left px-3 py-2 rounded-lg hover:bg-gray-100 text-sm">
                                        <i class="fas fa-calendar-alt mr-2 text-gray-500"></i>Terbaru Dahulu
                                    </button>
                                    <button onclick="sortMobileRequests('date_asc')" class="w-full text-left px-3 py-2 rounded-lg hover:bg-gray-100 text-sm">
                                        <i class="fas fa-calendar-alt mr-2 text-gray-500"></i>Terlama Dahulu
                                    </button>
                                    <button onclick="sortMobileRequests('title_asc')" class="w-full text-left px-3 py-2 rounded-lg hover:bg-gray-100 text-sm">
                                        <i class="fas fa-sort-alpha-down mr-2 text-gray-500"></i>Judul Proyek A-Z
                                    </button>
                                    <button onclick="sortMobileRequests('title_desc')" class="w-full text-left px-3 py-2 rounded-lg hover:bg-gray-100 text-sm">
                                        <i class="fas fa-sort-alpha-up mr-2 text-gray-500"></i>Judul Proyek Z-A
                                    </button>
                                    <button onclick="sortMobileRequests('status')" class="w-full text-left px-3 py-2 rounded-lg hover:bg-gray-100 text-sm">
                                        <i class="fas fa-tasks mr-2 text-gray-500"></i>Status
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="p-6">
            @if($requests->count() > 0)
                <!-- Enhanced Desktop View -->
                <div class="hidden lg:block">
                    <div class="overflow-hidden rounded-2xl border border-gray-100">
                            <table class="w-full">
                                <thead>
                                    <tr class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                                        <th class="text-left py-6 px-6 font-bold text-gray-800 uppercase tracking-wider text-sm">
                                            <div class="flex items-center">
                                                <i class="fas fa-user-tie mr-2 text-emerald-600"></i>Talenta
                                            </div>
                                        </th>
                                        <th class="text-left py-6 px-6 font-bold text-gray-800 uppercase tracking-wider text-sm">
                                            <div class="flex items-center">
                                                <i class="fas fa-project-diagram mr-2 text-blue-600"></i>Judul Proyek
                                            </div>
                                        </th>
                                        <th class="text-left py-6 px-6 font-bold text-gray-800 uppercase tracking-wider text-sm">
                                            <div class="flex items-center">
                                                <i class="fas fa-money-bill mr-2 text-green-600"></i>Budget & Durasi
                                            </div>
                                        </th>
                                        <th class="text-left py-6 px-6 font-bold text-gray-800 uppercase tracking-wider text-sm">
                                            <div class="flex items-center">
                                                <i class="fas fa-chart-line mr-2 text-purple-600"></i>Status
                                            </div>
                                        </th>
                                        <th class="text-left py-6 px-6 font-bold text-gray-800 uppercase tracking-wider text-sm">
                                            <div class="flex items-center">
                                                <i class="fas fa-calendar mr-2 text-orange-600"></i>Dikirim
                                            </div>
                                        </th>
                                        <th class="text-left py-6 px-6 font-bold text-gray-800 uppercase tracking-wider text-sm">
                                            <div class="flex items-center">
                                                <i class="fas fa-cogs mr-2 text-gray-600"></i>Aksi
                                            </div>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50 bg-white">
                                    @foreach($requests as $request)
                                        <tr class="request-row hover:bg-gradient-to-r hover:from-gray-50 hover:to-blue-50 transition-all duration-300 group"
                                            data-status="{{ $request->status }}"
                                            data-title="{{ $request->project_title }}"
                                            data-date="{{ $request->created_at->format('Y-m-d') }}"
                                            data-timestamp="{{ $request->created_at->timestamp }}">
                                            <!-- Talent Column -->
                                            <td class="py-6 px-6">
                                                <div class="flex items-center">
                                                    @if($request->talent->user->avatar)
                                                        <img class="w-16 h-16 rounded-2xl object-cover mr-4 shadow-lg border-2 border-white ring-2 ring-gray-100 group-hover:ring-emerald-200 transition-all duration-300"
                                                             src="{{ asset('storage/' . $request->talent->user->avatar) }}"
                                                             alt="{{ $request->talent->user->name }}">
                                                    @else
                                                        <div class="w-16 h-16 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl flex items-center justify-center mr-4 shadow-lg group-hover:shadow-xl transition-all duration-300">
                                                            <i class="fas fa-user text-white text-xl"></i>
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <div class="font-bold text-gray-900 text-lg group-hover:text-emerald-700 transition-colors duration-300">
                                                            {{ $request->talent->user->name }}
                                                        </div>
                                                        <div class="text-gray-500 text-sm font-medium">{{ $request->talent->user->email }}</div>
                                                        @if($request->talent->user->pekerjaan)
                                                            <div class="text-gray-600 text-sm mt-1 px-3 py-1 bg-gray-100 rounded-full inline-block font-medium">
                                                                <i class="fas fa-briefcase mr-1 text-xs"></i>{{ $request->talent->user->pekerjaan }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>

                                            <!-- Project Details Column -->
                                            <td class="py-6 px-6">
                                                <div class="font-bold text-gray-900 text-lg group-hover:text-blue-700 transition-colors duration-300">
                                                    {{ $request->project_title }}
                                                </div>
                                            </td>

                                            <!-- Budget & Duration Column -->
                                            <td class="py-6 px-6">
                                                <div class="space-y-3">
                                                    @if($request->budget_range)
                                                        <div class="flex items-center p-3 bg-green-50 rounded-xl border border-green-100">
                                                            <div class="w-8 h-8 bg-gradient-to-br from-green-400 to-emerald-500 rounded-xl flex items-center justify-center mr-3 shadow-sm">
                                                                <i class="fas fa-money-bill text-white text-sm"></i>
                                                            </div>
                                                            <div>
                                                                <div class="text-xs text-green-600 font-semibold uppercase tracking-wide">Budget</div>
                                                                <div class="font-bold text-green-800">{{ $request->budget_range }}</div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    @if($request->project_duration)
                                                        <div class="flex items-center p-3 bg-purple-50 rounded-xl border border-purple-100">
                                                            <div class="w-8 h-8 bg-gradient-to-br from-purple-400 to-indigo-500 rounded-xl flex items-center justify-center mr-3 shadow-sm">
                                                                <i class="fas fa-clock text-white text-sm"></i>
                                                            </div>
                                                            <div>
                                                                <div class="text-xs text-purple-600 font-semibold uppercase tracking-wide">Durasi</div>
                                                                <div class="font-bold text-purple-800">{{ preg_replace(['/\b(\d+)\s+months?\b/', '/\b(\d+)\s+weeks?\b/', '/\b(\d+)\s+days?\b/', '/\bmonths?\b/', '/\bweeks?\b/', '/\bdays?\b/'], ['$1 bulan', '$1 minggu', '$1 hari', 'bulan', 'minggu', 'hari'], $request->project_duration) }}</div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </td>

                                            <!-- Status Column -->
                                            <td class="py-6 px-6">
                                                <div class="space-y-3">
                                                    <span class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-bold shadow-lg
                                                        @if($request->getRecruiterStatusBadgeColor() == 'success') bg-gradient-to-r from-green-100 to-emerald-200 text-green-800 border-2 border-green-300
                                                        @elseif($request->getRecruiterStatusBadgeColor() == 'warning') bg-gradient-to-r from-yellow-100 to-orange-200 text-orange-800 border-2 border-orange-300
                                                        @elseif($request->getRecruiterStatusBadgeColor() == 'info') bg-gradient-to-r from-blue-100 to-indigo-200 text-blue-800 border-2 border-blue-300
                                                        @elseif($request->getRecruiterStatusBadgeColor() == 'danger') bg-gradient-to-r from-red-100 to-red-200 text-red-800 border-2 border-red-300
                                                        @else bg-gradient-to-r from-gray-100 to-gray-200 text-gray-800 border-2 border-gray-300 @endif">
                                                        <i class="fas fa-circle mr-2 text-xs
                                                            @if($request->getRecruiterStatusBadgeColor() == 'success') text-green-500
                                                            @elseif($request->getRecruiterStatusBadgeColor() == 'warning') text-orange-500
                                                            @elseif($request->getRecruiterStatusBadgeColor() == 'info') text-blue-500
                                                            @elseif($request->getRecruiterStatusBadgeColor() == 'danger') text-red-500
                                                            @else text-gray-500 @endif"></i>
                                                        {{ $request->getRecruiterDisplayStatus() }}
                                                    </span>
                                                </div>
                                            </td>

                                            <!-- Submitted Date Column -->
                                            <td class="py-6 px-6">
                                                <div class="text-sm">
                                                    <div class="font-semibold text-gray-900">{{ $request->created_at->locale('id')->translatedFormat('d F Y') }}</div>
                                                    <div class="text-gray-500 text-xs">{{ $request->created_at->format('h:i A') }}</div>
                                                    <div class="text-gray-400 text-xs mt-1">{{ $request->created_at->diffForHumans() }}</div>
                                                </div>
                                            </td>

                                            <!-- Actions Column -->
                                            <td class="py-6 px-6">
                                                <div class="flex flex-col space-y-2">
                                                    <button type="button"
                                                            class="inline-flex items-center px-3 py-1.5 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-lg hover:from-blue-600 hover:to-indigo-700 transition-all duration-300 text-xs font-medium shadow-lg hover:shadow-xl hover:scale-105"
                                                            onclick="viewRequestDetails(
                                                                {{ json_encode($request->project_title) }},
                                                                {{ json_encode($request->project_description) }},
                                                                {{ json_encode($request->requirements ?? '') }},
                                                                {{ json_encode($request->budget_range ?? 'Tidak ditentukan') }},
                                                                {{ json_encode($request->project_duration ? preg_replace(['/\b(\d+)\s+months?\b/', '/\b(\d+)\s+weeks?\b/', '/\b(\d+)\s+days?\b/', '/\bmonths?\b/', '/\bweeks?\b/', '/\bdays?\b/'], ['$1 bulan', '$1 minggu', '$1 hari', 'bulan', 'minggu', 'hari'], $request->project_duration) : 'Tidak ditentukan') }}
                                                            )">>
                                                        <i class="fas fa-eye mr-1.5 text-xs"></i>Lihat Detail
                                                    </button>
                                                    <a href="mailto:{{ $request->talent->user->email }}"
                                                       class="inline-flex items-center px-3 py-1.5 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-lg hover:from-green-600 hover:to-emerald-700 transition-all duration-300 text-xs font-medium shadow-lg hover:shadow-xl hover:scale-105">
                                                        <i class="fas fa-envelope mr-1.5 text-xs"></i>Kontak
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                <!-- Enhanced Mobile Card View -->
                <div class="lg:hidden">
                    <div class="flex gap-[2%] flex-wrap">
                        @foreach($requests as $request)
                            <div class="w-full md:w-[49%] h-auto mb-4 request-card bg-gradient-to-br from-white to-gray-50 border-2 border-gray-100 rounded-3xl p-6 shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:scale-[1.02] hover:border-emerald-200"
                                 data-status="{{ $request->status }}"
                                 data-title="{{ $request->project_title }}"
                                 data-date="{{ $request->created_at->format('Y-m-d') }}"
                                 data-timestamp="{{ $request->created_at->timestamp }}">
                            <!-- Mobile Card Header -->
                            <div class="flex items-center justify-between mb-6">
                                <div class="flex items-center">
                                    @if($request->talent->user->avatar)
                                        <img class="w-16 h-16 rounded-2xl object-cover mr-4 shadow-lg border-2 border-white ring-2 ring-gray-100"
                                             src="{{ asset('storage/' . $request->talent->user->avatar) }}"
                                             alt="{{ $request->talent->user->name }}">
                                    @else
                                        <div class="w-16 h-16 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl flex items-center justify-center mr-4 shadow-lg">
                                            <i class="fas fa-user text-white text-xl"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <h3 class="font-bold text-gray-900 text-lg">{{ $request->talent->user->name }}</h3>
                                        <p class="text-gray-500 text-sm font-medium">{{ $request->talent->user->email }}</p>
                                        @if($request->talent->user->pekerjaan)
                                            <div class="text-gray-600 text-sm mt-1 px-3 py-1 bg-gray-100 rounded-full inline-block">
                                                <i class="fas fa-briefcase mr-1 text-xs"></i>{{ $request->talent->user->pekerjaan }}
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Status Badge -->
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold shadow-sm
                                    @if($request->getRecruiterStatusBadgeColor() == 'success') bg-gradient-to-r from-green-100 to-emerald-200 text-green-800 border border-green-300
                                    @elseif($request->getRecruiterStatusBadgeColor() == 'warning') bg-gradient-to-r from-yellow-100 to-orange-200 text-orange-800 border border-orange-300
                                    @elseif($request->getRecruiterStatusBadgeColor() == 'info') bg-gradient-to-r from-blue-100 to-indigo-200 text-blue-800 border border-blue-300
                                    @elseif($request->getRecruiterStatusBadgeColor() == 'danger') bg-gradient-to-r from-red-100 to-red-200 text-red-800 border border-red-300
                                    @else bg-gradient-to-r from-gray-100 to-gray-200 text-gray-800 border border-gray-300 @endif">
                                    <i class="fas fa-circle mr-1 text-xs
                                        @if($request->getRecruiterStatusBadgeColor() == 'success') text-green-500
                                        @elseif($request->getRecruiterStatusBadgeColor() == 'warning') text-orange-500
                                        @elseif($request->getRecruiterStatusBadgeColor() == 'info') text-blue-500
                                        @elseif($request->getRecruiterStatusBadgeColor() == 'danger') text-red-500
                                        @else text-gray-500 @endif"></i>
                                    {{ $request->getRecruiterDisplayStatus() }}
                                </span>
                            </div>

                            <!-- Mobile Card Content -->
                            <div class="space-y-6">
                                <!-- Project Information -->
                                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl p-4 border border-blue-100">
                                    <h4 class="font-bold text-gray-900 text-lg mb-2 flex items-center">
                                        <i class="fas fa-project-diagram mr-2 text-blue-600"></i>
                                        {{ $request->project_title }}
                                    </h4>
                                    <p class="text-gray-600 text-sm leading-relaxed mb-3">{{ Str::limit($request->project_description, 160) }}</p>

                                    @if($request->requirements)
                                        <div class="text-gray-700 text-sm p-3 bg-white rounded-xl border border-blue-200">
                                            <span class="font-semibold text-blue-800">Persyaratan:</span><br>
                                            <span class="text-xs">{{ Str::limit($request->requirements, 120) }}</span>
                                        </div>
                                    @endif
                                </div>

                                <!-- Budget & Timeline -->
                                <div class="grid grid-cols-2 gap-4">
                                    @if($request->budget_range)
                                        <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-2xl p-4 border border-green-100">
                                            <div class="flex items-center mb-2">
                                                <div class="w-8 h-8 bg-gradient-to-br from-green-400 to-emerald-500 rounded-xl flex items-center justify-center mr-2">
                                                    <i class="fas fa-money-bill text-white text-sm"></i>
                                                </div>
                                                <span class="text-xs text-green-600 font-bold uppercase tracking-wide">Budget</span>
                                            </div>
                                            <div class="font-bold text-green-800 text-sm">{{ $request->budget_range }}</div>
                                        </div>
                                    @endif

                                    @if($request->project_duration)
                                        <div class="bg-gradient-to-br from-purple-50 to-indigo-50 rounded-2xl p-4 border border-purple-100">
                                            <div class="flex items-center mb-2">
                                                <div class="w-8 h-8 bg-gradient-to-br from-purple-400 to-indigo-500 rounded-xl flex items-center justify-center mr-2">
                                                    <i class="fas fa-clock text-white text-sm"></i>
                                                </div>
                                                <span class="text-xs text-purple-600 font-bold uppercase tracking-wide">Durasi</span>
                                            </div>
                                            <div class="font-bold text-purple-800 text-sm">{{ preg_replace(['/\b(\d+)\s+months?\b/', '/\b(\d+)\s+weeks?\b/', '/\b(\d+)\s+days?\b/', '/\bmonths?\b/', '/\bweeks?\b/', '/\bdays?\b/'], ['$1 bulan', '$1 minggu', '$1 hari', 'bulan', 'minggu', 'hari'], $request->project_duration) }}</div>
                                        </div>
                                    @endif
                                </div>

                                <!-- Submission Date -->
                                <div class="bg-gradient-to-r from-gray-50 to-slate-50 rounded-2xl p-4 border border-gray-100">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-gradient-to-br from-gray-400 to-slate-500 rounded-xl flex items-center justify-center mr-3">
                                            <i class="fas fa-calendar text-white text-sm"></i>
                                        </div>
                                        <div>
                                            <div class="text-xs text-gray-600 font-semibold uppercase tracking-wide">Dikirim</div>
                                            <div class="font-bold text-gray-800">{{ $request->created_at->locale('id')->translatedFormat('d F Y') }}</div>
                                            <div class="text-gray-500 text-xs">{{ $request->created_at->diffForHumans() }}</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex gap-3">
                                    <button type="button"
                                            class="flex-1 px-4 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-xl hover:from-blue-600 hover:to-indigo-700 transition-all duration-300 text-sm font-bold shadow-lg hover:shadow-xl transform hover:scale-105"
                                            onclick="viewRequestDetails(
                                                {{ json_encode($request->project_title) }},
                                                {{ json_encode($request->project_description) }},
                                                {{ json_encode($request->requirements ?? '') }},
                                                {{ json_encode($request->budget_range ?? 'Tidak ditentukan') }},
                                                {{ json_encode($request->project_duration ? preg_replace(['/\b(\d+)\s+months?\b/', '/\b(\d+)\s+weeks?\b/', '/\b(\d+)\s+days?\b/', '/\bmonths?\b/', '/\bweeks?\b/', '/\bdays?\b/'], ['$1 bulan', '$1 minggu', '$1 hari', 'bulan', 'minggu', 'hari'], $request->project_duration) : 'Tidak ditentukan') }}
                                            )">
                                        <i class="fas fa-eye mr-2"></i>Lihat Detail
                                    </button>
                                    <a href="mailto:{{ $request->talent->user->email }}"
                                       class="flex-1 px-4 py-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-xl hover:from-green-600 hover:to-emerald-700 transition-all duration-300 text-sm font-bold text-center shadow-lg hover:shadow-xl transform hover:scale-105">
                                        <i class="fas fa-envelope mr-2"></i>Kontak
                                    </a>
                                </div>
                            </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Pagination -->
                <div class="flex justify-center mt-12 pt-8 border-t border-gray-200">
                    <div class="pagination-wrapper">
                        {{ $requests->links() }}
                    </div>
                </div>
            @else
                <div class="text-center py-16">
                    <div class="w-24 h-24 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-clipboard-list text-4xl text-gray-400"></i>
                    </div>
                    <h5 class="text-xl font-semibold text-gray-700 mb-3">Belum Ada Permintaan Talenta</h5>
                    <p class="text-gray-500 max-w-md mx-auto mb-6">Anda belum mengirimkan permintaan talenta apapun. Mulai temukan dan terhubung dengan profesional berbakat.</p>
                    <a href="{{ route('recruiter.dashboard') }}"
                       class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-all duration-200 font-medium shadow-lg">
                        <i class="fas fa-search mr-2"></i>
                        Temukan Talenta
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Request Details Modal -->
<div id="requestDetailsModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" onclick="closeRequestModal()"></div>

        <!-- Modal panel -->
        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-white bg-opacity-20 rounded-xl flex items-center justify-center mr-3">
                            <i class="fas fa-clipboard-list text-white"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-white" id="modal-title">Detail Permintaan</h3>
                    </div>
                    <button type="button"
                            class="text-white hover:text-gray-200 transition-colors duration-200 p-2 hover:bg-white hover:bg-opacity-20 rounded-lg"
                            onclick="closeRequestModal()">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>

            <!-- Modal Body -->
            <div class="bg-white px-6 py-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Left Column -->
                    <div class="space-y-6">
                        <div class="bg-gray-50 rounded-xl p-4">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Judul Proyek</label>
                            <p id="modalProjectTitle" class="text-gray-900 text-lg font-medium"></p>
                        </div>

                        <div class="bg-gray-50 rounded-xl p-4">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Rentang Budget</label>
                            <p id="modalBudgetRange" class="text-gray-900 font-medium"></p>
                        </div>

                        <div class="bg-gray-50 rounded-xl p-4">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Durasi Proyek</label>
                            <p id="modalProjectDuration" class="text-gray-900 font-medium"></p>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-6">
                        <div class="bg-gray-50 rounded-xl p-4">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi Proyek</label>
                            <p id="modalProjectDescription" class="text-gray-900 leading-relaxed"></p>
                        </div>

                        <div class="bg-gray-50 rounded-xl p-4">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Persyaratan</label>
                            <p id="modalRequirements" class="text-gray-900 leading-relaxed"></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                <div class="flex justify-end">
                    <button type="button"
                            class="px-6 py-3 bg-gray-600 text-white rounded-xl hover:bg-gray-700 transition-all duration-200 font-medium shadow-lg"
                            onclick="closeRequestModal()">
                        <i class="fas fa-times mr-2"></i>Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Initialize dropdowns when page loads
document.addEventListener('DOMContentLoaded', function() {
    initializeExportDropdownRequests();
});

// Export PDF Dropdown Functionality for Requests Page
function initializeExportDropdownRequests() {
    const dropdownButton = document.getElementById('exportDropdownButtonRequests');
    const dropdownMenu = document.getElementById('exportDropdownMenuRequests');

    if (dropdownButton && dropdownMenu) {
        // Toggle dropdown on button click
        dropdownButton.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            toggleExportDropdownRequests();
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!dropdownButton.contains(e.target) && !dropdownMenu.contains(e.target)) {
                closeExportDropdownRequests();
            }
        });

        // Close dropdown on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeExportDropdownRequests();
            }
        });
    }
}

function toggleExportDropdownRequests() {
    const dropdownMenu = document.getElementById('exportDropdownMenuRequests');
    const isVisible = dropdownMenu.classList.contains('opacity-100');

    if (isVisible) {
        closeExportDropdownRequests();
    } else {
        openExportDropdownRequests();
    }
}

function openExportDropdownRequests() {
    const dropdownMenu = document.getElementById('exportDropdownMenuRequests');
    dropdownMenu.classList.remove('opacity-0', 'invisible');
    dropdownMenu.classList.add('opacity-100', 'visible');
}

function closeExportDropdownRequests() {
    const dropdownMenu = document.getElementById('exportDropdownMenuRequests');
    dropdownMenu.classList.remove('opacity-100', 'visible');
    dropdownMenu.classList.add('opacity-0', 'invisible');
}

function viewRequestDetails(title, description, requirements, budget, duration) {
    console.log('viewRequestDetails called with:', {
        title, description, requirements, budget, duration
    });

    try {
        // Populate modal content
        document.getElementById('modalProjectTitle').textContent = title;
        document.getElementById('modalProjectDescription').textContent = description;
        document.getElementById('modalRequirements').textContent = requirements || 'Tidak ditentukan';
        document.getElementById('modalBudgetRange').textContent = budget;
        document.getElementById('modalProjectDuration').textContent = duration;

        // Show modal
        const modal = document.getElementById('requestDetailsModal');
        if (!modal) {
            console.error('Modal element not found!');
            return;
        }

        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';

        // Add fade-in animation
        setTimeout(() => {
            modal.classList.add('animate-fade-in');
        }, 10);

        console.log('Modal opened successfully');
    } catch (error) {
        console.error('Error in viewRequestDetails:', error);
        alert('Terjadi kesalahan saat membuka detail permintaan. Silakan periksa konsol untuk informasi lebih lanjut.');
    }
}

function closeRequestModal() {
    const modal = document.getElementById('requestDetailsModal');
    modal.classList.add('animate-fade-out');

    setTimeout(() => {
        modal.classList.add('hidden');
        modal.classList.remove('animate-fade-in', 'animate-fade-out');
        document.body.style.overflow = 'auto';
    }, 200);
}

// Close modal when pressing Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const modal = document.getElementById('requestDetailsModal');
        if (!modal.classList.contains('hidden')) {
            closeRequestModal();
        }
    }
});

// Add smooth transitions for better UX
document.addEventListener('DOMContentLoaded', function() {
    // Add hover effects to action buttons
    const actionButtons = document.querySelectorAll('.bg-indigo-600, .bg-green-600');
    actionButtons.forEach(button => {
        button.addEventListener('mouseover', function() {
            this.style.transform = 'translateY(-1px)';
            this.style.boxShadow = '0 4px 12px rgba(0, 0, 0, 0.15)';
        });

        button.addEventListener('mouseout', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = '';
        });
    });

    // Initialize filter and sort functionality
    initializeFiltersAndSort();
});

// Filter and Sort Functionality
function initializeFiltersAndSort() {
    // Toggle filter dropdown
    document.getElementById('filterButton').addEventListener('click', function(e) {
        e.stopPropagation();
        const dropdown = document.getElementById('filterDropdown');
        const sortDropdown = document.getElementById('sortDropdown');

        dropdown.classList.toggle('hidden');
        sortDropdown.classList.add('hidden');
    });

    // Toggle sort dropdown
    document.getElementById('sortButton').addEventListener('click', function(e) {
        e.stopPropagation();
        const dropdown = document.getElementById('sortDropdown');
        const filterDropdown = document.getElementById('filterDropdown');

        dropdown.classList.toggle('hidden');
        filterDropdown.classList.add('hidden');
    });

    // Mobile filter and sort controls
    if (document.getElementById('mobileFilterButton')) {
        document.getElementById('mobileFilterButton').addEventListener('click', function(e) {
            e.stopPropagation();
            const dropdown = document.getElementById('mobileFilterDropdown');
            const sortDropdown = document.getElementById('mobileSortDropdown');

            dropdown.classList.toggle('hidden');
            sortDropdown.classList.add('hidden');
        });
    }

    if (document.getElementById('mobileSortButton')) {
        document.getElementById('mobileSortButton').addEventListener('click', function(e) {
            e.stopPropagation();
            const dropdown = document.getElementById('mobileSortDropdown');
            const filterDropdown = document.getElementById('mobileFilterDropdown');

            dropdown.classList.toggle('hidden');
            filterDropdown.classList.add('hidden');
        });
    }

    // Close dropdowns when clicking outside
    document.addEventListener('click', function() {
        document.getElementById('filterDropdown').classList.add('hidden');
        document.getElementById('sortDropdown').classList.add('hidden');
        if (document.getElementById('mobileFilterDropdown')) {
            document.getElementById('mobileFilterDropdown').classList.add('hidden');
        }
        if (document.getElementById('mobileSortDropdown')) {
            document.getElementById('mobileSortDropdown').classList.add('hidden');
        }
    });

    // Handle "All Requests" checkbox - Desktop
    document.querySelector('input[value="all"]').addEventListener('change', function() {
        if (this.checked) {
            document.querySelectorAll('.filter-status:not([value="all"])').forEach(cb => cb.checked = false);
        }
    });

    // Handle individual status checkboxes - Desktop
    document.querySelectorAll('.filter-status:not([value="all"])').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            if (this.checked) {
                document.querySelector('input[value="all"]').checked = false;
            }
        });
    });

    // Handle "All Requests" checkbox - Mobile
    if (document.querySelector('.mobile-filter-status[value="all"]')) {
        document.querySelector('.mobile-filter-status[value="all"]').addEventListener('change', function() {
            if (this.checked) {
                document.querySelectorAll('.mobile-filter-status:not([value="all"])').forEach(cb => cb.checked = false);
            }
        });

        // Handle individual status checkboxes - Mobile
        document.querySelectorAll('.mobile-filter-status:not([value="all"])').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                if (this.checked) {
                    document.querySelector('.mobile-filter-status[value="all"]').checked = false;
                }
            });
        });
    }
}

function applyFilters() {
    const statusFilters = Array.from(document.querySelectorAll('.filter-status:checked')).map(cb => cb.value);

    // Get all request rows and cards
    const desktopRows = document.querySelectorAll('.request-row');
    const mobileCards = document.querySelectorAll('.request-card');

    let visibleCount = 0;

    function filterElement(element) {
        const status = element.dataset.status;

        let showStatus = statusFilters.includes('all') || statusFilters.includes(status);

        if (showStatus) {
            element.style.display = '';
            visibleCount++;
        } else {
            element.style.display = 'none';
        }
    }

    // Apply filters
    desktopRows.forEach(filterElement);
    mobileCards.forEach(filterElement);

    // Update filter button text to show active filters
    const filterButton = document.getElementById('filterButton');
    const activeFilters = statusFilters.length;
    if (activeFilters === 0 || statusFilters.includes('all')) {
        filterButton.innerHTML = '<i class="fas fa-filter mr-2"></i>Filter<i class="fas fa-chevron-down ml-2 text-sm"></i>';
    } else {
        filterButton.innerHTML = `<i class="fas fa-filter mr-2"></i>Filter (${activeFilters})<i class="fas fa-chevron-down ml-2 text-sm"></i>`;
    }

    // Close dropdown
    document.getElementById('filterDropdown').classList.add('hidden');

    // Show message if no results
    updateNoResultsMessage(visibleCount);
}

function clearFilters() {
    // Reset all checkboxes
    document.querySelectorAll('.filter-status').forEach(cb => cb.checked = false);
    document.querySelector('input[value="all"]').checked = true;

    // Show all elements
    document.querySelectorAll('.request-row, .request-card').forEach(element => {
        element.style.display = '';
    });

    // Reset filter button text
    document.getElementById('filterButton').innerHTML = '<i class="fas fa-filter mr-2"></i>Filter<i class="fas fa-chevron-down ml-2 text-sm"></i>';

    // Close dropdown
    document.getElementById('filterDropdown').classList.add('hidden');

    // Hide no results message
    updateNoResultsMessage(document.querySelectorAll('.request-row, .request-card').length);
}

function sortRequests(sortType) {
    const desktopContainer = document.querySelector('.request-row')?.parentElement;
    const mobileContainer = document.querySelector('.request-card')?.parentElement;

    if (desktopContainer) {
        const rows = Array.from(document.querySelectorAll('.request-row'));
        sortElements(rows, sortType);
        rows.forEach(row => desktopContainer.appendChild(row));
    }

    if (mobileContainer) {
        const cards = Array.from(document.querySelectorAll('.request-card'));
        sortElements(cards, sortType);
        cards.forEach(card => mobileContainer.appendChild(card));
    }

    // Update sort button text
    const sortButton = document.getElementById('sortButton');
    const sortLabels = {
        'date_desc': 'Terbaru Dahulu',
        'date_asc': 'Terlama Dahulu',
        'title_asc': 'Judul A-Z',
        'title_desc': 'Judul Z-A',
        'status': 'Status'
    };

    sortButton.innerHTML = `<i class="fas fa-sort mr-2"></i>${sortLabels[sortType]}<i class="fas fa-chevron-down ml-2 text-sm"></i>`;

    // Close dropdown
    document.getElementById('sortDropdown').classList.add('hidden');
}

function sortElements(elements, sortType) {
    elements.sort((a, b) => {
        switch (sortType) {
            case 'date_desc':
                return parseInt(b.dataset.timestamp) - parseInt(a.dataset.timestamp);
            case 'date_asc':
                return parseInt(a.dataset.timestamp) - parseInt(b.dataset.timestamp);
            case 'title_asc':
                return a.dataset.title.localeCompare(b.dataset.title);
            case 'title_desc':
                return b.dataset.title.localeCompare(a.dataset.title);
            case 'status':
                const statusOrder = { 'pending': 1, 'approved': 2, 'meeting_arranged': 3, 'rejected': 4 };
                return (statusOrder[a.dataset.status] || 5) - (statusOrder[b.dataset.status] || 5);
            default:
                return 0;
        }
    });
}

function updateNoResultsMessage(visibleCount) {
    let noResultsDiv = document.getElementById('noFilterResults');

    if (visibleCount === 0) {
        if (!noResultsDiv) {
            noResultsDiv = document.createElement('div');
            noResultsDiv.id = 'noFilterResults';
            noResultsDiv.className = 'text-center py-16 col-span-full';
            noResultsDiv.innerHTML = `
                <div class="w-24 h-24 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-search text-4xl text-gray-400"></i>
                </div>
                <h5 class="text-xl font-semibold text-gray-700 mb-3">Tidak Ada Permintaan yang Cocok</h5>
                <p class="text-gray-500 max-w-md mx-auto mb-6">Tidak ada permintaan yang sesuai dengan kriteria filter Anda saat ini. Coba sesuaikan filter Anda.</p>
                <button onclick="clearFilters()" class="inline-flex items-center px-6 py-3 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 transition-all duration-200 font-medium shadow-lg">
                    <i class="fas fa-times mr-2"></i>Bersihkan Filter
                </button>
            `;

            // Insert after the table/cards container
            const mainContainer = document.querySelector('.p-8');
            const paginationDiv = mainContainer.querySelector('.flex.justify-center');
            if (paginationDiv) {
                mainContainer.insertBefore(noResultsDiv, paginationDiv);
            } else {
                mainContainer.appendChild(noResultsDiv);
            }
        }
        noResultsDiv.style.display = 'block';
    } else {
        if (noResultsDiv) {
            noResultsDiv.style.display = 'none';
        }
    }
}

// Mobile-specific filter and sort functions
function applyMobileFilters() {
    const statusFilters = Array.from(document.querySelectorAll('.mobile-filter-status:checked')).map(cb => cb.value);

    // Get all request cards (mobile view)
    const mobileCards = document.querySelectorAll('.request-card');

    let visibleCount = 0;

    function filterElement(element) {
        const status = element.dataset.status;

        let showStatus = statusFilters.includes('all') || statusFilters.includes(status);

        if (showStatus) {
            element.style.display = '';
            element.classList.remove('hidden');
            visibleCount++;
        } else {
            element.style.display = 'none';
            element.classList.add('hidden');
        }
    }

    // Apply filters to mobile cards
    mobileCards.forEach(filterElement);

    // Update filter button text to show active filters
    const filterButton = document.getElementById('mobileFilterButton');
    const activeFilters = statusFilters.length;
    if (activeFilters === 0 || statusFilters.includes('all')) {
        filterButton.innerHTML = '<i class="fas fa-filter mr-2"></i>Filter<i class="fas fa-chevron-down ml-2 text-sm"></i>';
    } else {
        filterButton.innerHTML = `<i class="fas fa-filter mr-2"></i>Filter (${activeFilters})<i class="fas fa-chevron-down ml-2 text-sm"></i>`;
    }

    // Close dropdown
    document.getElementById('mobileFilterDropdown').classList.add('hidden');

    // Show message if no results
    updateNoResultsMessage(visibleCount);
}

function clearMobileFilters() {
    // Reset all checkboxes
    document.querySelectorAll('.mobile-filter-status').forEach(cb => cb.checked = false);
    document.querySelector('.mobile-filter-status[value="all"]').checked = true;

    // Show all elements
    document.querySelectorAll('.request-card').forEach(element => {
        element.style.display = '';
    });

    // Reset filter button text
    document.getElementById('mobileFilterButton').innerHTML = '<i class="fas fa-filter mr-2"></i>Filter<i class="fas fa-chevron-down ml-2 text-sm"></i>';

    // Close dropdown
    document.getElementById('mobileFilterDropdown').classList.add('hidden');

    // Hide no results message
    updateNoResultsMessage(document.querySelectorAll('.request-card').length);
}

function sortMobileRequests(sortType) {
    const mobileContainer = document.querySelector('.flex.gap-\\[2\\%\\].flex-wrap');

    if (mobileContainer) {
        const cards = Array.from(document.querySelectorAll('.request-card'));
        sortElements(cards, sortType);
        cards.forEach(card => mobileContainer.appendChild(card));
    }

    // Update sort button text
    const sortButton = document.getElementById('mobileSortButton');
    const sortLabels = {
        'date_desc': 'Terbaru Dahulu',
        'date_asc': 'Terlama Dahulu',
        'title_asc': 'Judul A-Z',
        'title_desc': 'Judul Z-A',
        'status': 'Status'
    };

    sortButton.innerHTML = `<i class="fas fa-sort mr-2"></i>${sortLabels[sortType]}<i class="fas fa-chevron-down ml-2 text-sm"></i>`;

    // Close dropdown
    document.getElementById('mobileSortDropdown').classList.add('hidden');
}
</script>

<style>
/* Custom animations for modal */
.animate-fade-in {
    animation: fadeIn 0.2s ease-out;
}

.animate-fade-out {
    animation: fadeOut 0.2s ease-in;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: scale(0.95);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

@keyframes fadeOut {
    from {
        opacity: 1;
        transform: scale(1);
    }
    to {
        opacity: 0;
        transform: scale(0.95);
    }
}

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
    background: linear-gradient(135deg, #4f46e5, #7c3aed);
    border-color: #4f46e5;
    color: white;
    box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
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

/* Status badge animations */
.status-badge {
    transition: all 0.2s ease;
}

.status-badge:hover {
    transform: scale(1.05);
}

/* Enhanced flex layout for request cards */
.request-card {
    min-height: 400px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

/* Responsive adjustments for the flex layout */
@media (max-width: 768px) {
    .request-card {
        width: 100% !important;
        margin-bottom: 1rem;
    }
}

@media (min-width: 769px) and (max-width: 1024px) {
    .request-card {
        width: 49% !important;
    }
}

/* Ensure equal height for cards in the same row */
.flex-wrap > .request-card {
    align-self: stretch;
}
</style>
@endsection
