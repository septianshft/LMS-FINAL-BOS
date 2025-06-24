@extends('layout.template.mainTemplate')

@section('title', 'Kelola Permintaan Talent')

@section('container')
<div class="min-h-screen bg-gray-50 p-6">
    <!-- Page Heading -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Kelola Permintaan Talent</h1>
            <p class="text-gray-600">Tinjau dan kelola permintaan akuisisi talent</p>
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
            <!-- Export Dropdown -->
            <div class="relative inline-block text-left">
                <button type="button"
                        class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 transition-all duration-200 shadow-lg hover:shadow-xl"
                        id="exportMenuButton"
                        onclick="toggleExportDropdown()">
                    <i class="fas fa-download mr-2"></i>
                    Export Reports
                    <i class="fas fa-chevron-down ml-2"></i>
                </button>

                <div id="exportDropdown"
                     class="hidden absolute right-0 mt-2 w-64 bg-white rounded-lg shadow-xl border border-gray-200 z-50">
                    <div class="py-2">
                        <div class="px-4 py-2 border-b border-gray-100">
                            <p class="text-sm font-semibold text-gray-900">Download Reports</p>
                            <p class="text-xs text-gray-500">Export current filtered data</p>
                        </div>
                        <a href="#"
                           onclick="exportReport('summary')"
                           class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-green-50 hover:text-green-800 transition-colors">
                            <i class="fas fa-chart-pie mr-3 text-green-600"></i>
                            <div>
                                <div class="font-medium">Summary Report</div>
                                <div class="text-xs text-gray-500">Overview with statistics</div>
                            </div>
                        </a>
                        <a href="#"
                           onclick="exportReport('detailed')"
                           class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-800 transition-colors">
                            <i class="fas fa-table mr-3 text-blue-600"></i>
                            <div>
                                <div class="font-medium">Detailed Report</div>
                                <div class="text-xs text-gray-500">Complete data analysis</div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <span class="inline-flex items-center px-4 py-2 bg-purple-100 text-purple-800 rounded-full text-sm font-medium">
                <i class="fas fa-shield-alt mr-2"></i>
                Administrator Talent
            </span>
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

    <!-- Filters Card -->
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 mb-8">
        <div class="bg-gradient-to-r from-purple-600 to-indigo-600 rounded-t-2xl p-6">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-white bg-opacity-20 rounded-xl flex items-center justify-center mr-3">
                    <i class="fas fa-filter text-white"></i>
                </div>
                <h3 class="text-lg font-semibold text-white">Filter Permintaan</h3>
            </div>
        </div>
        <div class="p-6">
            <form method="GET" action="{{ route('talent_admin.manage_requests') }}" id="filterForm">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
                        <select name="status" id="status"
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200"
                                onchange="document.getElementById('filterForm').submit();">
                            <option value="">Semua Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu Review</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                            <option value="meeting_arranged" {{ request('status') == 'meeting_arranged' ? 'selected' : '' }}>Pertemuan Diatur</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                        </select>
                    </div>
                    <div>
                        <label for="search" class="block text-sm font-semibold text-gray-700 mb-2">Pencarian</label>
                        <input type="text" name="search" id="search"
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200"
                               placeholder="Cari nama perekrut atau talent..." value="{{ request('search') }}">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">&nbsp;</label>
                        <div class="flex space-x-3">
                            <button type="submit"
                                    class="flex-1 px-6 py-3 bg-purple-600 text-white rounded-xl hover:bg-purple-700 transition-all duration-200 font-medium shadow-lg hover:shadow-xl">
                                <i class="fas fa-search mr-2"></i> Filter
                            </button>
                            <a href="{{ route('talent_admin.manage_requests') }}"
                               class="flex-1 px-6 py-3 bg-gray-600 text-white rounded-xl hover:bg-gray-700 transition-all duration-200 font-medium shadow-lg hover:shadow-xl text-center">
                                <i class="fas fa-refresh mr-2"></i> Reset
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Requests Table -->
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100">
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-t-2xl p-6">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-white bg-opacity-20 rounded-xl flex items-center justify-center mr-3">
                    <i class="fas fa-list text-white"></i>
                </div>
                <h3 class="text-lg font-semibold text-white">Permintaan Talent</h3>
            </div>
        </div>
        <div class="p-6">
            @if($requests->count() > 0)
                <!-- Desktop Table View -->
                <div class="hidden lg:block overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-50 border-b-2 border-gray-200">
                                <th class="text-left py-4 px-4 font-semibold text-gray-700 text-sm">ID</th>
                                <th class="text-left py-4 px-4 font-semibold text-gray-700 text-sm">Perekrut</th>
                                <th class="text-left py-4 px-4 font-semibold text-gray-700 text-sm">Talent</th>
                                <th class="text-left py-4 px-4 font-semibold text-gray-700 text-sm">Status</th>
                                <th class="text-left py-4 px-4 font-semibold text-gray-700 text-sm">Project End Date</th>
                                <th class="text-left py-4 px-4 font-semibold text-gray-700 text-sm">Tanggal Diminta</th>
                                <th class="text-left py-4 px-4 font-semibold text-gray-700 text-sm">Terakhir Diperbarui</th>
                                <th class="text-left py-4 px-4 font-semibold text-gray-700 text-sm">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($requests as $request)
                                <tr class="hover:bg-gray-50 transition-colors duration-200">
                                    <td class="py-6 px-4">
                                        <span class="font-mono text-sm text-gray-600">#{{ $request->id }}</span>
                                    </td>
                                    <td class="py-6 px-4">
                                        <div class="flex items-center">
                                            @if($request->recruiter->user->avatar)
                                                <img class="w-10 h-10 rounded-xl object-cover mr-3 shadow-md"
                                                     src="{{ asset('storage/' . $request->recruiter->user->avatar) }}"
                                                     alt="{{ $request->recruiter->user->name }}">
                                            @else
                                                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center mr-3 shadow-md">
                                                    <i class="fas fa-building text-white text-sm"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <div class="font-semibold text-gray-900">{{ $request->recruiter->user->name }}</div>
                                                <div class="text-gray-500 text-sm">{{ $request->recruiter->user->pekerjaan }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-6 px-4">
                                        <div class="flex items-center">
                                            @if($request->talent->user->avatar)
                                                <img class="w-10 h-10 rounded-xl object-cover mr-3 shadow-md"
                                                     src="{{ asset('storage/' . $request->talent->user->avatar) }}"
                                                     alt="{{ $request->talent->user->name }}">
                                            @else
                                                <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center mr-3 shadow-md">
                                                    <i class="fas fa-user text-white text-sm"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <div class="font-semibold text-gray-900">{{ $request->talent->user->name }}</div>
                                                <div class="text-gray-500 text-sm">{{ $request->talent->user->pekerjaan }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-6 px-4">
                                        <div class="space-y-1">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $request->getStatusBadgeColorClasses() }}">
                                                <i class="{{ $request->getStatusIcon() }} mr-1"></i>
                                                {{ $request->getUnifiedDisplayStatus() }}
                                            </span>
                                            @if($request->status === 'completed' && $request->workflow_completed_at && $request->project_id)
                                                <div class="flex items-center text-xs text-blue-600">
                                                    <i class="fas fa-info-circle mr-1"></i>
                                                    <span class="font-medium">Auto-completed via project closure</span>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="py-6 px-4">
                                        @php
                                            // Determine which end date to display
                                            $endDate = null;
                                            $source = '';

                                            if($request->project && $request->project->expected_end_date) {
                                                $endDate = $request->project->expected_end_date;
                                                $source = 'project';
                                            } elseif($request->project_end_date) {
                                                $endDate = $request->project_end_date;
                                                $source = 'request';
                                            }
                                        @endphp

                                        @if($endDate)
                                            <div class="space-y-1">
                                                <div class="text-gray-900 font-medium text-sm">
                                                    {{ $endDate->format('d M Y') }}
                                                    @if($source === 'project')
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 ml-1">
                                                            <i class="fas fa-project-diagram mr-1"></i>Project
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 ml-1">
                                                            <i class="fas fa-calendar mr-1"></i>Request
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="text-xs flex items-center">
                                                    @if($endDate->isPast())
                                                        <i class="fas fa-exclamation-triangle text-red-500 mr-1"></i>
                                                        <span class="text-red-600 font-medium">{{ $endDate->diffForHumans() }} (Overdue)</span>
                                                    @elseif($endDate->diffInDays() <= 7)
                                                        <i class="fas fa-clock text-orange-500 mr-1"></i>
                                                        <span class="text-orange-600 font-medium">{{ $endDate->diffForHumans() }} (Due Soon)</span>
                                                    @else
                                                        <i class="fas fa-calendar-check text-green-500 mr-1"></i>
                                                        <span class="text-green-600">{{ $endDate->diffForHumans() }}</span>
                                                    @endif
                                                </div>
                                                @if($request->project && $request->project->status)
                                                    <div class="text-xs">
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                                            @if($request->project->status === 'active') bg-green-100 text-green-800
                                                            @elseif($request->project->status === 'completed') bg-blue-100 text-blue-800
                                                            @elseif($request->project->status === 'overdue') bg-red-100 text-red-800
                                                            @elseif($request->project->status === 'pending_admin') bg-yellow-100 text-yellow-800
                                                            @else bg-gray-100 text-gray-800
                                                            @endif">
                                                            {{ ucwords(str_replace('_', ' ', $request->project->status)) }}
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>
                                        @else
                                            <div class="text-gray-400 text-sm">
                                                <i class="fas fa-minus-circle mr-1"></i>
                                                Not specified
                                            </div>
                                        @endif
                                    </td>
                                    <td class="py-6 px-4">
                                        <div class="text-gray-900 font-medium text-sm">{{ $request->created_at->format('d M Y') }}</div>
                                        <div class="text-gray-500 text-xs">{{ $request->created_at->format('H:i') }}</div>
                                    </td>
                                    <td class="py-6 px-4">
                                        <div class="text-gray-900 font-medium text-sm">{{ $request->updated_at->format('d M Y') }}</div>
                                        <div class="text-gray-500 text-xs">{{ $request->updated_at->format('H:i') }}</div>
                                    </td>
                                    <td class="py-6 px-4">
                                        <div class="flex items-center space-x-2">
                                            <a href="{{ route('talent_admin.show_request', $request) }}"
                                               class="inline-flex items-center px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-200 text-sm font-medium shadow-lg hover:shadow-xl"
                                               title="Lihat Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($request->talent_accepted && !$request->admin_accepted)
                                                {{-- Talent accepted, awaiting admin approval --}}
                                                <button type="button"
                                                        class="inline-flex items-center px-3 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all duration-200 text-sm font-medium shadow-lg hover:shadow-xl"
                                                        onclick="updateStatus({{ $request->id }}, 'approved')" title="Setujui Permintaan">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button type="button"
                                                        class="inline-flex items-center px-3 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-all duration-200 text-sm font-medium shadow-lg hover:shadow-xl"
                                                        onclick="updateStatus({{ $request->id }}, 'rejected')" title="Tolak">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            @elseif(!$request->talent_accepted && !$request->admin_accepted && $request->status == 'pending')
                                                {{-- Initial pending state - no acceptances yet --}}
                                                <button type="button"
                                                        class="inline-flex items-center px-3 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all duration-200 text-sm font-medium shadow-lg hover:shadow-xl"
                                                        onclick="updateStatus({{ $request->id }}, 'approved')" title="Setujui">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button type="button"
                                                        class="inline-flex items-center px-3 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-all duration-200 text-sm font-medium shadow-lg hover:shadow-xl"
                                                        onclick="updateStatus({{ $request->id }}, 'rejected')" title="Tolak">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            @elseif($request->admin_accepted && !$request->talent_accepted)
                                                {{-- Admin approved, waiting for talent acceptance --}}
                                                <span class="inline-flex items-center px-3 py-2 bg-blue-100 text-blue-800 rounded-lg text-sm font-medium">
                                                    <i class="fas fa-clock mr-2"></i>
                                                    Menunggu Talent
                                                </span>
                                            @elseif($request->both_parties_accepted && $request->canAdminArrangeMeeting())
                                                {{-- Both parties accepted, can proceed to meeting --}}
                                                <button type="button"
                                                        class="inline-flex items-center px-3 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-all duration-200 text-sm font-medium shadow-lg hover:shadow-xl"
                                                        onclick="updateStatus({{ $request->id }}, 'meeting_arranged')" title="Atur Pertemuan">
                                                    <i class="fas fa-calendar"></i>
                                                </button>
                                            @elseif($request->status == 'meeting_arranged')
                                                <button type="button"
                                                        class="inline-flex items-center px-3 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-all duration-200 text-sm font-medium shadow-lg hover:shadow-xl"
                                                        onclick="updateStatus({{ $request->id }}, 'agreement_reached')" title="Tandai Kesepakatan Tercapai">
                                                    <i class="fas fa-handshake"></i>
                                                </button>
                                            @elseif($request->status == 'agreement_reached')
                                                <button type="button"
                                                        class="inline-flex items-center px-3 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all duration-200 text-sm font-medium shadow-lg hover:shadow-xl"
                                                        onclick="updateStatus({{ $request->id }}, 'onboarded')" title="Tandai Bergabung">
                                                    <i class="fas fa-user-plus"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Card View -->
                <div class="lg:hidden space-y-6">
                    @foreach($requests as $request)
                        <div class="bg-gradient-to-br from-white to-gray-50 border-2 border-gray-100 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300">
                            <!-- Mobile Card Header -->
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center">
                                    <span class="inline-flex items-center px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-xs font-semibold">
                                        #{{ $request->id }}
                                    </span>
                                </div>
                                <div class="text-right">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $request->getStatusBadgeColorClasses() }}">
                                        {{ $request->getUnifiedDisplayStatus() }}
                                    </span>
                                    @if($request->status === 'completed' && $request->workflow_completed_at && $request->project_id)
                                        <div class="flex items-center justify-end text-xs text-blue-600 mt-1">
                                            <i class="fas fa-info-circle mr-1"></i>
                                            <span class="font-medium">Auto-completed</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Recruiter and Talent Info -->
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div class="bg-white p-4 rounded-xl border border-gray-200">
                                    <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Perekrut</div>
                                    <div class="flex items-center">
                                        @if($request->recruiter->user->avatar)
                                            <img class="w-8 h-8 rounded-lg object-cover mr-2"
                                                 src="{{ asset('storage/' . $request->recruiter->user->avatar) }}"
                                                 alt="{{ $request->recruiter->user->name }}">
                                        @else
                                            <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center mr-2">
                                                <i class="fas fa-building text-white text-xs"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <div class="font-semibold text-gray-900 text-sm">{{ $request->recruiter->user->name }}</div>
                                            <div class="text-gray-500 text-xs">{{ $request->recruiter->user->pekerjaan }}</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-white p-4 rounded-xl border border-gray-200">
                                    <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Talent</div>
                                    <div class="flex items-center">
                                        @if($request->talent->user->avatar)
                                            <img class="w-8 h-8 rounded-lg object-cover mr-2"
                                                 src="{{ asset('storage/' . $request->talent->user->avatar) }}"
                                                 alt="{{ $request->talent->user->name }}">
                                        @else
                                            <div class="w-8 h-8 bg-gradient-to-br from-green-500 to-emerald-600 rounded-lg flex items-center justify-center mr-2">
                                                <i class="fas fa-user text-white text-xs"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <div class="font-semibold text-gray-900 text-sm">{{ $request->talent->user->name }}</div>
                                            <div class="text-gray-500 text-xs">{{ $request->talent->user->pekerjaan }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Dates -->
                            <div class="grid grid-cols-3 gap-3 mb-4 text-sm">
                                <div class="bg-gray-50 p-3 rounded-xl">
                                    <div class="text-gray-500 text-xs font-medium uppercase tracking-wide">Diminta</div>
                                    <div class="text-gray-900 font-semibold mt-1">{{ $request->created_at->format('d M Y') }}</div>
                                    <div class="text-gray-500 text-xs">{{ $request->created_at->format('H:i') }}</div>
                                </div>
                                <div class="bg-gray-50 p-3 rounded-xl">
                                    <div class="text-gray-500 text-xs font-medium uppercase tracking-wide">Diperbarui</div>
                                    <div class="text-gray-900 font-semibold mt-1">{{ $request->updated_at->format('d M Y') }}</div>
                                    <div class="text-gray-500 text-xs">{{ $request->updated_at->format('H:i') }}</div>
                                </div>
                                <div class="bg-gray-50 p-3 rounded-xl">
                                    <div class="text-gray-500 text-xs font-medium uppercase tracking-wide">End Date</div>
                                    @if($request->project_end_date)
                                        <div class="text-gray-900 font-semibold mt-1">{{ $request->project_end_date->format('d M Y') }}</div>
                                        <div class="text-xs mt-1">
                                            @if($request->project_end_date->isPast())
                                                <span class="text-red-600 font-medium">
                                                    <i class="fas fa-exclamation-triangle mr-1"></i>Overdue
                                                </span>
                                            @elseif($request->project_end_date->diffInDays() <= 7)
                                                <span class="text-orange-600 font-medium">
                                                    <i class="fas fa-clock mr-1"></i>Due Soon
                                                </span>
                                            @else
                                                <span class="text-green-600">
                                                    <i class="fas fa-calendar mr-1"></i>{{ $request->project_end_date->diffForHumans() }}
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <div class="text-gray-400 font-medium mt-1">
                                            <i class="fas fa-minus-circle mr-1"></i>Not set
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex flex-wrap gap-2">
                                <a href="{{ route('talent_admin.show_request', $request) }}"
                                   class="flex-1 min-w-0 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-200 font-medium text-sm text-center shadow-lg hover:shadow-xl">
                                    <i class="fas fa-eye mr-1"></i> Lihat
                                </a>
                                @if($request->talent_accepted && !$request->admin_accepted)
                                    {{-- Talent accepted, awaiting admin approval --}}
                                    <button type="button"
                                            class="flex-1 min-w-0 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all duration-200 font-medium text-sm shadow-lg hover:shadow-xl"
                                            onclick="updateStatus({{ $request->id }}, 'approved')">
                                        <i class="fas fa-check mr-1"></i> Setujui
                                    </button>
                                    <button type="button"
                                            class="flex-1 min-w-0 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-all duration-200 font-medium text-sm shadow-lg hover:shadow-xl"
                                            onclick="updateStatus({{ $request->id }}, 'rejected')">
                                        <i class="fas fa-times mr-1"></i> Tolak
                                    </button>
                                @elseif(!$request->talent_accepted && !$request->admin_accepted && $request->status == 'pending')
                                    {{-- Initial pending state - no acceptances yet --}}
                                    <button type="button"
                                            class="flex-1 min-w-0 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all duration-200 font-medium text-sm shadow-lg hover:shadow-xl"
                                            onclick="updateStatus({{ $request->id }}, 'approved')">
                                        <i class="fas fa-check mr-1"></i> Setujui
                                    </button>
                                    <button type="button"
                                            class="flex-1 min-w-0 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-all duration-200 font-medium text-sm shadow-lg hover:shadow-xl"
                                            onclick="updateStatus({{ $request->id }}, 'rejected')">
                                        <i class="fas fa-times mr-1"></i> Tolak
                                    </button>
                                @elseif($request->admin_accepted && !$request->talent_accepted)
                                    {{-- Admin approved, waiting for talent acceptance --}}
                                    <div class="flex-1 min-w-0 px-4 py-2 bg-blue-100 text-blue-800 rounded-lg text-sm font-medium text-center">
                                        <i class="fas fa-clock mr-1"></i>
                                        Menunggu Talent
                                    </div>
                                @elseif($request->both_parties_accepted && $request->canAdminArrangeMeeting())
                                    {{-- Both parties accepted, can proceed to meeting --}}
                                    <button type="button"
                                            class="flex-1 min-w-0 px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-all duration-200 font-medium text-sm shadow-lg hover:shadow-xl"
                                            onclick="updateStatus({{ $request->id }}, 'meeting_arranged')">
                                        <i class="fas fa-calendar mr-1"></i> Atur Pertemuan
                                    </button>
                                @elseif($request->status == 'meeting_arranged')
                                    <button type="button"
                                            class="flex-1 min-w-0 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-all duration-200 font-medium text-sm shadow-lg hover:shadow-xl"
                                            onclick="updateStatus({{ $request->id }}, 'agreement_reached')">
                                        <i class="fas fa-handshake mr-1"></i> Kesepakatan
                                    </button>
                                @elseif($request->status == 'agreement_reached')
                                    <button type="button"
                                            class="flex-1 min-w-0 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all duration-200 font-medium text-sm shadow-lg hover:shadow-xl"
                                            onclick="updateStatus({{ $request->id }}, 'onboarded')">
                                        <i class="fas fa-user-plus mr-1"></i> Bergabung
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="flex justify-center mt-12 pt-8 border-t border-gray-200">
                    <div class="pagination-wrapper">
                        {{ $requests->appends(request()->query())->links() }}
                    </div>
                </div>
            @else
                <div class="text-center py-16">
                    <div class="w-24 h-24 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-clipboard-list text-4xl text-gray-400"></i>
                    </div>
                    <h5 class="text-xl font-semibold text-gray-700 mb-3">Tidak Ada Permintaan Talent</h5>
                    <p class="text-gray-500 max-w-md mx-auto">Tidak ada permintaan talent yang sesuai dengan filter saat ini.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Status Update Modal -->
<div id="statusModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" onclick="closeStatusModal()"></div>

        <!-- Modal panel -->
        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-purple-600 to-indigo-600 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-white bg-opacity-20 rounded-xl flex items-center justify-center mr-3">
                            <i class="fas fa-edit text-white"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-white" id="modal-title">Perbarui Status Permintaan</h3>
                    </div>
                    <button type="button"
                            class="text-white hover:text-gray-200 transition-colors duration-200 p-2 hover:bg-white hover:bg-opacity-20 rounded-lg"
                            onclick="closeStatusModal()">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>

            <!-- Modal Body -->
            <form id="statusForm">
                <div class="bg-white px-6 py-6">
                    <input type="hidden" id="requestId" name="request_id">
                    <input type="hidden" id="newStatus" name="status">

                    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-info-circle text-blue-500 mr-3"></i>
                            <div>
                                <p class="text-sm font-medium text-blue-800">Aksi:</p>
                                <p class="text-sm text-blue-700" id="statusAction"></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                    <div class="flex justify-end space-x-3">
                        <button type="button"
                                class="px-6 py-3 bg-gray-600 text-white rounded-xl hover:bg-gray-700 transition-all duration-200 font-medium"
                                onclick="closeStatusModal()">
                            Batal
                        </button>
                        <button type="submit"
                                class="px-6 py-3 bg-purple-600 text-white rounded-xl hover:bg-purple-700 transition-all duration-200 font-medium shadow-lg"
                                id="confirmButton">
                            Konfirmasi
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
function updateStatus(requestId, status) {
    const statusActions = {
        'approved': 'Setujui permintaan ini',
        'rejected': 'Tolak permintaan ini',
        'meeting_arranged': 'Tandai sebagai pertemuan diatur',
        'agreement_reached': 'Tandai kesepakatan telah tercapai',
        'onboarded': 'Tandai talent telah bergabung'
    };

    // Use semantic color mapping instead of hardcoded colors
    const getStatusColor = (status) => {
        switch(status) {
            case 'approved':
            case 'onboarded':
                return 'bg-green-600 hover:bg-green-700';
            case 'rejected':
                return 'bg-red-600 hover:bg-red-700';
            case 'meeting_arranged':
                return 'bg-yellow-600 hover:bg-yellow-700';
            case 'agreement_reached':
                return 'bg-purple-600 hover:bg-purple-700';
            default:
                return 'bg-purple-600 hover:bg-purple-700';
        }
    };

    document.getElementById('requestId').value = requestId;
    document.getElementById('newStatus').value = status;
    document.getElementById('statusAction').textContent = statusActions[status];

    // Update button color using function
    const confirmButton = document.getElementById('confirmButton');
    confirmButton.className = `px-6 py-3 ${getStatusColor(status)} text-white rounded-xl transition-all duration-200 font-medium shadow-lg`;

    // Show modal
    const modal = document.getElementById('statusModal');
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeStatusModal() {
    const modal = document.getElementById('statusModal');
    modal.classList.add('hidden');
    document.body.style.overflow = 'auto';

    // Clear form
    document.getElementById('statusForm').reset();
}

// Handle form submission
document.getElementById('statusForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const requestId = document.getElementById('requestId').value;
    const status = document.getElementById('newStatus').value;

    // Disable submit button during request
    const confirmButton = document.getElementById('confirmButton');
    const originalText = confirmButton.innerHTML;
    confirmButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...';
    confirmButton.disabled = true;

    fetch(`/talent-admin/request/${requestId}/status`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            status: status
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeStatusModal();
            location.reload(); // Refresh page to show updated status
        } else {
            alert('Error memperbarui status. Silakan coba lagi.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error memperbarui status. Silakan coba lagi.');
    })
    .finally(() => {
        // Re-enable submit button
        confirmButton.innerHTML = originalText;
        confirmButton.disabled = false;
    });
});

// Close modal when pressing Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const modal = document.getElementById('statusModal');
        if (!modal.classList.contains('hidden')) {
            closeStatusModal();
        }
    }
});

// Export functionality
function toggleExportDropdown() {
    const dropdown = document.getElementById('exportDropdown');
    dropdown.classList.toggle('hidden');
}

function exportReport(type) {
    // Get current filter values
    const currentUrl = new URL(window.location.href);
    const status = currentUrl.searchParams.get('status') || '';
    const search = currentUrl.searchParams.get('search') || '';

    // Close dropdown
    document.getElementById('exportDropdown').classList.add('hidden');

    // Show loading state
    const button = document.getElementById('exportMenuButton');
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Generating...';
    button.disabled = true;

    // Build export URL based on type
    let exportUrl = '';
    if (type === 'summary') {
        exportUrl = '{{ route("talent_admin.export_requests_summary") }}';
    } else if (type === 'detailed') {
        exportUrl = '{{ route("talent_admin.export_requests_detailed") }}';
    }

    // Add current filters to export URL
    const params = new URLSearchParams();
    if (status) params.append('status', status);
    if (search) params.append('search', search);

    if (params.toString()) {
        exportUrl += '?' + params.toString();
    }

    // Create hidden link and trigger download
    const link = document.createElement('a');
    link.href = exportUrl;
    link.download = true;
    link.style.display = 'none';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);

    // Reset button state after download starts
    setTimeout(() => {
        button.innerHTML = originalText;
        button.disabled = false;
    }, 2000);
}

// Close export dropdown when clicking outside
document.addEventListener('click', function(event) {
    const dropdown = document.getElementById('exportDropdown');
    const button = document.getElementById('exportMenuButton');

    if (!dropdown.contains(event.target) && !button.contains(event.target)) {
        dropdown.classList.add('hidden');
    }
});
</script>
@endsection

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
    background: linear-gradient(135deg, #7c3aed, #4f46e5);
    border-color: #7c3aed;
    color: white;
    box-shadow: 0 4px 12px rgba(124, 58, 237, 0.3);
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
