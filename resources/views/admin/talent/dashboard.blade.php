@extends('layout.template.mainTemplate')

@section('title', 'Dashboard Talenta')
@section('container')

{{-- Include Talent Request Notifications --}}
@include('components.talent-request-notifications')

<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50 p-6">
    <div class="max-w-7xl mx-auto space-y-8">

        {{-- Welcome Header --}}
        <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-2xl p-8 text-white shadow-xl">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold mb-2">Selamat datang kembali, {{ $user->name }}! 👋</h1>
                    <p class="text-blue-100 text-lg">Siap untuk menjelajahi peluang baru dan menunjukkan talenta Anda?</p>
                </div>
                <div class="hidden md:block">
                    <div class="bg-white/20 backdrop-blur-sm rounded-xl p-4 text-center">
                        <i class="fas fa-star text-4xl text-yellow-300 mb-2"></i>
                        <div class="text-sm font-medium">Status Talenta</div>
                        <div class="text-xs opacity-90">
                            {{ $user->is_active_talent ? 'Aktif' : 'Tidak Aktif' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Red Flag Alert --}}
        @if($talent && $talent->redflagged)
            <div class="bg-gradient-to-r from-red-600 to-red-700 rounded-2xl p-6 text-white shadow-xl border-l-4 border-red-400">
                <div class="flex items-start space-x-4">
                    <div class="flex-shrink-0">
                        <div class="bg-red-500/30 backdrop-blur-sm rounded-full p-3">
                            <i class="fas fa-exclamation-triangle text-2xl text-red-200"></i>
                        </div>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-xl font-bold mb-2 flex items-center">
                            <i class="fas fa-flag mr-2"></i>
                            Peringatan Status Akun
                        </h3>
                        <p class="text-red-100 mb-2">
                            Akun talenta Anda telah ditandai dan memerlukan perhatian. Ini mungkin mempengaruhi kemampuan Anda untuk mengakses peluang tertentu.
                        </p>
                        @if($talent->redflag_reason)
                            <div class="bg-red-500/20 backdrop-blur-sm rounded-lg p-3 mt-3">
                                <div class="text-sm font-medium text-red-200 mb-1">Alasan:</div>
                                <div class="text-red-100">{{ $talent->redflag_reason }}</div>
                            </div>
                        @endif
                        <div class="mt-4">
                            <p class="text-red-200 text-sm">
                                <i class="fas fa-info-circle mr-1"></i>
                                Silakan hubungi administrator talenta untuk bantuan menyelesaikan masalah ini.
                            </p>
                        </div>
                    </div>
                    <div class="flex-shrink-0">
                        <button onclick="showContactModal()" class="bg-white/20 hover:bg-white/30 backdrop-blur-sm rounded-lg px-4 py-2 text-sm font-medium transition-colors">
                            <i class="fas fa-envelope mr-1"></i>
                            Hubungi Admin
                        </button>
                    </div>
                </div>
            </div>
        @endif

        {{-- Quick Stats --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            {{-- Profile Completeness --}}
            <div class="bg-white rounded-xl p-6 shadow-lg border border-gray-100 hover:shadow-xl transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-blue-100 p-3 rounded-lg">
                        <i class="fas fa-user-circle text-blue-600 text-xl"></i>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-bold text-gray-800">{{ $profileCompleteness }}%</div>
                        <div class="text-sm text-gray-500">Lengkap</div>
                    </div>
                </div>
                <div class="text-sm font-medium text-gray-700 mb-2">Status Profil</div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $profileCompleteness }}%"></div>
                </div>
            </div>

            {{-- Active Opportunities (with warning if red-flagged) --}}
            <div class="bg-white rounded-xl p-6 shadow-lg border {{ $talent && $talent->redflagged ? 'border-yellow-200' : 'border-gray-100' }} hover:shadow-xl transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-green-100 p-3 rounded-lg {{ $talent && $talent->redflagged ? 'relative' : '' }}">
                        <i class="fas fa-briefcase text-green-600 text-xl"></i>
                        @if($talent && $talent->redflagged)
                            <div class="absolute -top-1 -right-1 w-3 h-3 bg-yellow-500 rounded-full"></div>
                        @endif
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-bold text-gray-800">{{ $jobOpportunities->count() }}</div>
                        <div class="text-sm text-gray-500">Tersedia</div>
                    </div>
                </div>
                <div class="text-sm font-medium text-gray-700">Peluang Kerja</div>
                <div class="text-xs {{ $talent && $talent->redflagged ? 'text-yellow-600' : 'text-green-600' }} font-medium">
                    @if($talent && $talent->redflagged)
                        Akun ditandai - hubungi support
                    @elseif($jobOpportunities->where('created_at', '>=', now()->subDays(7))->count() > 0)
                        +{{ $jobOpportunities->where('created_at', '>=', now()->subDays(7))->count() }} baru minggu ini
                    @else
                        Periksa kembali untuk peluang baru
                    @endif
                </div>
            </div>

            {{-- Applications Sent --}}
            <div class="bg-white rounded-xl p-6 shadow-lg border border-gray-100 hover:shadow-xl transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-purple-100 p-3 rounded-lg">
                        <i class="fas fa-paper-plane text-purple-600 text-xl"></i>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-bold text-gray-800">{{ $talentStats['total_applications'] }}</div>
                        <div class="text-sm text-gray-500">Dikirim</div>
                    </div>
                </div>
                <div class="text-sm font-medium text-gray-700">Aplikasi</div>
                <div class="text-xs text-purple-600 font-medium">{{ $talentStats['pending_applications'] }} menunggu review</div>
            </div>

            {{-- Completed Collaborations --}}
            <div class="bg-white rounded-xl p-6 shadow-lg border border-gray-100 hover:shadow-xl transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-orange-100 p-3 rounded-lg">
                        <i class="fas fa-trophy text-orange-600 text-xl"></i>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-bold text-gray-800">{{ $talentStats['completed_collaborations'] }}</div>
                        <div class="text-sm text-gray-500">Selesai</div>
                    </div>
                </div>
                <div class="text-sm font-medium text-gray-700">Kolaborasi</div>
                <div class="text-xs text-orange-600 font-medium">{{ $talentStats['approved_applications'] }} proyek berhasil diselesaikan</div>
            </div>
        </div>

        {{-- Main Content Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- Latest Opportunities --}}
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-xl shadow-lg border border-gray-100">
                    <div class="p-6 border-b border-gray-100">
                        <div class="flex items-center justify-between">
                            <h2 class="text-xl font-bold text-gray-800">🚀 Peluang Terbaru</h2>
                            <a href="{{ route('talent.my_requests') }}" data-testid="view-all-link" class="text-blue-600 hover:text-blue-700 text-sm font-medium">Lihat Semua</a>
                        </div>
                    </div>
                    <div class="p-6 space-y-4">
                        @if($talent && $talent->redflagged)
                            {{-- Warning message for red-flagged talents - they can still see opportunities --}}
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                                <div class="flex items-start space-x-3">
                                    <i class="fas fa-exclamation-triangle text-yellow-600 text-lg mt-1"></i>
                                    <div class="flex-1">
                                        <h4 class="text-sm font-semibold text-yellow-800 mb-1">Pemberitahuan Status Akun</h4>
                                        <p class="text-yellow-700 text-sm mb-2">
                                            Akun Anda telah ditandai. Meskipun Anda masih dapat melihat dan merespons peluang,
                                            silakan hubungi support untuk menyelesaikan masalah apa pun.
                                        </p>
                                        @if($talent->redflag_reason)
                                            <div class="text-xs text-yellow-600 italic">
                                                Alasan: {{ $talent->redflag_reason }}
                                            </div>
                                        @endif
                                    </div>
                                    <button onclick="showContactModal()" class="text-yellow-700 hover:text-yellow-800 text-xs underline whitespace-nowrap">
                                        Hubungi Support
                                    </button>
                                </div>
                            </div>
                        @endif

                        @forelse($jobOpportunities->take(3) as $opportunity)
                            <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-300 hover:shadow-md transition-all
                                @if(isset($opportunity['is_pre_approved']) && $opportunity['is_pre_approved'])
                                    ring-2 ring-emerald-200 bg-gradient-to-br from-emerald-50 to-white 
                                @endif">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-2 mb-2">
                                            <h3 class="font-semibold text-gray-800">{{ $opportunity['title'] }}</h3>

                                            {{-- Pre-approved Badge --}}
                                            @if(isset($opportunity['is_pre_approved']) && $opportunity['is_pre_approved'])
                                                <span class="px-2 py-1 bg-emerald-500 text-white text-xs rounded-full font-bold">
                                                    <i class="fas fa-star mr-1"></i>PRA-DISETUJUI
                                                </span>
                                            @elseif($opportunity['posted_date']->diffInDays() <= 3)
                                                <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full font-medium">Baru</span>
                                            @else
                                                <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full font-medium">Tersedia</span>
                                            @endif
                                        </div>
                                        <p class="text-gray-600 text-sm mb-2">{{ $opportunity['company'] }} • Remote</p>
                                        <div class="flex items-center space-x-4 text-sm text-gray-500 mb-3">
                                            <span class="flex items-center"><i class="fas fa-dollar-sign mr-1"></i> {{ $opportunity['budget'] }}</span>
                                            <span class="flex items-center"><i class="fas fa-clock mr-1"></i> {{ $opportunity['duration'] }}</span>
                                            <span class="flex items-center"><i class="fas fa-calendar mr-1"></i> {{ $opportunity['posted_date']->diffForHumans() }}</span>
                                        </div>

                                        {{-- Acceptance Status --}}
                                        @if(isset($opportunity['acceptance_status']))
                                            <div class="mb-3">
                                                <div class="text-xs font-medium text-gray-600 mb-1">Status:</div>
                                                <span class="px-2 py-1 text-xs rounded-full
                                                    @if($opportunity['both_parties_accepted']) bg-green-100 text-green-800
                                                    @elseif($opportunity['talent_accepted']) bg-blue-100 text-blue-800
                                                    @else bg-yellow-100 text-yellow-800 @endif">
                                                    {{ $opportunity['acceptance_status'] }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Action Buttons --}}
                                    <div class="flex flex-col space-y-2 ml-4">
                                        @if(isset($opportunity['can_accept']) && $opportunity['can_accept'])
                                            <button onclick="acceptRequest({{ $opportunity['request_id'] }})"
                                                    class="px-4 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition-colors">
                                                <i class="fas fa-check mr-1"></i> Terima
                                            </button>
                                            <button onclick="rejectRequest({{ $opportunity['request_id'] }})"
                                                    class="px-4 py-2 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700 transition-colors">
                                                <i class="fas fa-times mr-1"></i> Tolak
                                            </button>
                                        @elseif(isset($opportunity['can_reject']) && $opportunity['can_reject'])
                                            <button onclick="rejectRequest({{ $opportunity['request_id'] }})"
                                                    class="px-4 py-2 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700 transition-colors">
                                                <i class="fas fa-times mr-1"></i> Tolak
                                            </button>
                                        @else
                                            <button class="px-4 py-2 bg-gray-400 text-white text-sm rounded-lg cursor-not-allowed" disabled>
                                                {{ isset($opportunity['both_parties_accepted']) && $opportunity['both_parties_accepted'] ? 'Diterima' : 'Menunggu' }}
                                            </button>
                                        @endif

                                        <button onclick="viewRequestDetails({{ $opportunity['request_id'] }})"
                                                class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
                                            <i class="fas fa-eye mr-1"></i> Detail
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <i class="fas fa-briefcase text-gray-300 text-4xl mb-4"></i>
                                <p class="text-gray-500">Tidak ada peluang kerja yang tersedia saat ini.</p>
                                <p class="text-gray-400 text-sm">Periksa kembali nanti untuk peluang baru!</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Recent Activity --}}
                <div class="bg-white rounded-xl shadow-lg border border-gray-100">
                    <div class="p-6 border-b border-gray-100">
                        <h2 class="text-xl font-bold text-gray-800">📋 Aktivitas Terbaru</h2>
                    </div>
                    <div class="p-6 space-y-4">
                        @forelse($recentActivity as $activity)
                            <div class="flex items-center space-x-3">
                                <div class="bg-{{ $activity['color'] }}-100 p-2 rounded-full">
                                    <i class="{{ $activity['icon'] }} text-{{ $activity['color'] }}-600"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-800">{{ $activity['title'] }}</p>
                                    <p class="text-xs text-gray-500">{{ $activity['time'] }}</p>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <i class="fas fa-history text-gray-300 text-4xl mb-4"></i>
                                <p class="text-gray-500">Belum ada aktivitas terbaru.</p>
                                <p class="text-gray-400 text-sm">Mulai melamar pekerjaan atau menyelesaikan kursus!</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Job History (Accepted Collaborations) --}}
                <div class="bg-white rounded-xl shadow-lg border border-gray-100">
                    <div class="p-6 border-b border-gray-100">
                        <div class="flex items-center justify-between">
                            <h2 class="text-xl font-bold text-gray-800">🏆 Riwayat Kolaborasi</h2>
                            <span class="text-sm text-gray-500">{{ $jobHistory->count() }} total</span>
                        </div>
                    </div>
                    <div class="p-6 space-y-4">
                        @forelse($jobHistory as $job)
                            <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-300 hover:shadow-md transition-all">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-2 mb-2">
                                            <h3 class="font-semibold text-gray-800">{{ $job['project_title'] }}</h3>
                                            <span class="px-2 py-1 text-xs rounded-full
                                                @if($job['status_color'] === 'green') bg-green-100 text-green-800
                                                @elseif($job['status_color'] === 'blue') bg-blue-100 text-blue-800
                                                @elseif($job['status_color'] === 'yellow') bg-yellow-100 text-yellow-800
                                                @elseif($job['status_color'] === 'purple') bg-purple-100 text-purple-800
                                                @else bg-gray-100 text-gray-800 @endif">
                                                {{ $job['formatted_status'] }}
                                            </span>
                                        </div>
                                        <div class="flex items-center space-x-2 mb-2">
                                            <p class="text-gray-600 text-sm">{{ $job['company'] }}</p>
                                            @if($job['company_role'])
                                                <span class="text-gray-400">•</span>
                                                <p class="text-gray-500 text-sm">{{ $job['company_role'] }}</p>
                                            @endif
                                        </div>
                                        <div class="flex items-center space-x-4 text-sm text-gray-500 mb-2">
                                            <span class="flex items-center"><i class="fas fa-dollar-sign mr-1"></i> {{ $job['budget_range'] }}</span>
                                            <span class="flex items-center"><i class="fas fa-clock mr-1"></i> {{ $job['duration_worked'] }}</span>
                                            @if($job['talent_accepted_at'])
                                                <span class="flex items-center"><i class="fas fa-calendar-check mr-1"></i> <span>Dimulai {{ \Carbon\Carbon::parse($job['talent_accepted_at'])->format('d M Y') }}</span>
                                            @endif
                                        </div>
                                        @if($job['project_description'])
                                            <p class="text-sm text-gray-600 mt-2">{{ Str::limit($job['project_description'], 120) }}</p>
                                        @endif
                                    </div>
                                    <div class="flex flex-col items-end space-y-2 ml-4">
                                        @if($job['is_completed'])
                                            <div class="flex items-center text-green-600 text-sm">
                                                <i class="fas fa-check-circle mr-1"></i>
                                                <span>Selesai</span>
                                            </div>
                                        @elseif($job['is_in_progress'])
                                            <div class="flex items-center text-blue-600 text-sm">
                                                <i class="fas fa-spinner fa-pulse mr-1"></i>
                                                <span>Sedang Berlangsung</span>
                                            </div>
                                        @endif
                                        <button onclick="viewJobDetails({{ $job['id'] }})"
                                                class="px-3 py-1 bg-gray-100 text-gray-700 text-sm rounded-lg hover:bg-gray-200 transition-colors">
                                            <i class="fas fa-eye mr-1"></i> Lihat Detail
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <i class="fas fa-briefcase text-gray-300 text-4xl mb-4"></i>
                                <p class="text-gray-500">Belum ada riwayat kolaborasi.</p>
                                <p class="text-gray-400 text-sm">Terima peluang kerja untuk mulai membangun portofolio Anda!</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">
                {{-- Profile Quick Actions --}}
                <div class="bg-white rounded-xl shadow-lg border border-gray-100">
                    <div class="p-6 border-b border-gray-100">
                        <h2 class="text-lg font-bold text-gray-800">⚡ Aksi Cepat</h2>
                    </div>
                    <div class="p-6 space-y-3">
                        {{-- Red Flag Status Action --}}
                        @if($talent && $talent->redflagged)
                            <div class="p-3 rounded-lg bg-red-50 border border-red-200">
                                <div class="flex items-center">
                                    <div class="bg-red-100 p-2 rounded-lg">
                                        <i class="fas fa-exclamation-triangle text-red-600"></i>
                                    </div>
                                    <div class="ml-3 flex-1">
                                        <div class="text-sm font-medium text-red-800">Akun Ditandai</div>
                                        <div class="text-xs text-red-600">Selesaikan untuk melanjutkan</div>
                                    </div>
                                    <button onclick="showContactModal()" class="text-red-600 hover:text-red-700 text-xs underline">
                                        Hubungi
                                    </button>
                                </div>
                            </div>
                        @endif

                        <a href="{{ route('profile.edit') }}" class="flex items-center p-3 rounded-lg hover:bg-blue-50 transition-colors group">
                            <div class="bg-blue-100 p-2 rounded-lg group-hover:bg-blue-200 transition-colors">
                                <i class="fas fa-user-edit text-blue-600"></i>
                            </div>
                            <div class="ml-3">
                                <div class="text-sm font-medium text-gray-800">Edit Profil</div>
                                <div class="text-xs text-gray-500">Perbarui informasi Anda</div>
                            </div>
                        </a>
                        {{-- <a href="#" onclick="document.getElementById('resumeUpload').click()" class="flex items-center p-3 rounded-lg hover:bg-green-50 transition-colors group">
                            <div class="bg-green-100 p-2 rounded-lg group-hover:bg-green-200 transition-colors">
                                <i class="fas fa-upload text-green-600"></i>
                            </div>
                            <div class="ml-3">
                                <div class="text-sm font-medium text-gray-800">Unggah Resume</div>
                                <div class="text-xs text-gray-500">Tetap terbaru</div>
                            </div>
                        </a>
                        <a href="#" onclick="showAlert('Fitur Penilaian Keahlian akan segera hadir!', 'info')" class="flex items-center p-3 rounded-lg hover:bg-purple-50 transition-colors group">
                            <div class="bg-purple-100 p-2 rounded-lg group-hover:bg-purple-200 transition-colors">
                                <i class="fas fa-cogs text-purple-600"></i>
                            </div>
                            <div class="ml-3">
                                <div class="text-sm font-medium text-gray-800">Penilaian Keahlian</div>
                                <div class="text-xs text-gray-500">Uji kemampuan Anda</div>
                            </div>
                        </a> --}}
                        <!-- Hidden file input for resume upload -->
                        <input type="file" id="resumeUpload" accept=".pdf,.doc,.docx" style="display: none;" onchange="handleResumeUpload(this)">
                    </div>
                </div>

                {{-- Skill Progress --}}
                <div class="bg-white rounded-xl shadow-lg border border-gray-100">
                    <div class="p-6 border-b border-gray-100">
                        <h2 class="text-lg font-bold text-gray-800">🎯 Progress Keahlian</h2>
                    </div>
                    <div class="p-6 space-y-4">
                        @forelse($userSkills as $skill)
                            <div>
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm font-medium text-gray-700">{{ $skill['name'] }}</span>
                                    <span class="text-sm text-gray-500">{{ $skill['percentage'] }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    @php
                                        $colorClass = $skill['percentage'] >= 80 ? 'bg-green-600' :
                                                     ($skill['percentage'] >= 60 ? 'bg-blue-600' : 'bg-purple-600');
                                    @endphp
                                    <div class="{{ $colorClass }} h-2 rounded-full" style="width: {{ $skill['percentage'] }}%"></div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <i class="fas fa-cogs text-gray-300 text-4xl mb-4"></i>
                                <p class="text-gray-500">Belum ada keahlian yang dilacak.</p>
                                <p class="text-gray-400 text-sm">Selesaikan kursus untuk membangun profil keahlian Anda!</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Recent Requests --}}
                <div class="bg-white rounded-xl shadow-lg border border-gray-100">
                    <div class="p-6 border-b border-gray-100">
                        <div class="flex items-center justify-between">
                            <h2 class="text-lg font-bold text-gray-800">📝 Permintaan Terbaru</h2>
                            <span class="bg-blue-500 text-white text-xs px-2 py-1 rounded-full">{{ $recentRequests->count() }}</span>
                        </div>
                    </div>
                    <div class="p-6 space-y-4">
                        @forelse($recentRequests->take(2) as $request)
                            <div class="flex items-center space-x-3 cursor-pointer hover:bg-gray-50 p-2 rounded-lg transition-colors"
                                 onclick="viewRequestDetails({{ $request->id }})">
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-briefcase text-blue-600 text-sm"></i>
                                </div>
                                <div class="flex-1">
                                    <div class="text-sm font-medium text-gray-800">{{ $request->project_title ?? 'Peluang baru' }}</div>
                                    <div class="text-xs text-gray-500">dari {{ $request->recruiter->user->name ?? 'Perekrut' }}</div>
                                </div>
                                <span class="px-2 py-1 bg-{{ $request->status === 'pending' ? 'yellow' : ($request->status === 'accepted' ? 'green' : 'red') }}-100 text-{{ $request->status === 'pending' ? 'yellow' : ($request->status === 'accepted' ? 'green' : 'red') }}-800 text-xs rounded-full">
                                    @if($request->status === 'pending')
                                        Menunggu
                                    @elseif($request->status === 'accepted')
                                        Diterima
                                    @elseif($request->status === 'rejected')
                                        Ditolak
                                    @else
                                        {{ ucfirst($request->status) }}
                                    @endif
                                </span>
                            </div>
                        @empty
                            <div class="text-center py-4">
                                <i class="fas fa-inbox text-gray-300 text-2xl mb-2"></i>
                                <p class="text-gray-500 text-sm">Belum ada permintaan</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Request Details Modal --}}
<div id="talentRequestDetailsModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" onclick="closeRequestModal()"></div>

        <!-- Modal panel -->
        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-blue-600 to-purple-600 px-6 py-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-medium text-white" id="modal-title">Detail Permintaan</h3>
                    <button type="button" class="text-white hover:text-gray-200 transition-colors" onclick="closeRequestModal()">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>

            <!-- Modal Body -->
            <div id="modalContent" class="px-6 py-6">
                <div class="text-center py-8">
                    <i class="fas fa-spinner fa-spin text-4xl text-blue-600 mb-4"></i>
                    <p class="text-gray-600">Memuat detail permintaan...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Global variables for modal management (simplified - no auto-close)
let isModalOpen = false;
let modalInitialized = false;
let processingAction = false; // Flag to prevent modal close during action processing

// Initialize everything when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM Content Loaded - Initializing modal system');
    // Reset states
    isModalOpen = false;
    modalInitialized = false;
    processingAction = false;

    initializeModal();

    // Setup escape key handler
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && isModalOpen && !processingAction) {
            closeRequestModal();
        }
    });
});

// Initialize modal and set up event listeners
// Initialize modal and set up event listeners (simplified - no auto-close)
function initializeModal() {
    if (modalInitialized && document.getElementById('talentRequestDetailsModal')) {
        console.log('Modal already initialized and element exists.');
        return;
    }

    const modal = document.getElementById('talentRequestDetailsModal');
    const modalContent = document.getElementById('modalContent');

    console.log('Attempting modal initialization. Found modal:', !!modal, 'Found modalContent:', !!modalContent);

    if (!modal || !modalContent) {
        console.error('Modal elements not found during initialization.');
        modalInitialized = false;
        return;
    }

    modalInitialized = true;
    console.log('Modal initialized successfully.');

    // Click outside to close
    modal.addEventListener('click', function(e) {
        if (e.target === modal && !processingAction) {
            closeRequestModal();
        }
    });

    // Prevent closing when clicking inside modal content
    const modalPanel = modal.querySelector('.bg-white.rounded-2xl');
    if (modalPanel) {
        modalPanel.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }
}

// Handle modal state changes (simplified - no auto-reload)
function handleModalStateChange(modalIsOpen) {
    if (modalIsOpen) {
        console.log('Modal is now OPEN.');
        document.body.style.overflow = 'hidden';
        document.body.classList.add('modal-is-active');
    } else {
        console.log('Modal is now CLOSED.');
        document.body.style.overflow = '';
        document.body.classList.remove('modal-is-active');

        if (processingAction) {
            console.log('Resetting processingAction because modal closed.');
            processingAction = false;
        }
    }
}

// Modified accept request function
function acceptRequest(requestId) {
    if (processingAction) {
        console.log('Already processing an action, ignoring...');
        return;
    }

    if (!confirm('Apakah Anda yakin ingin menerima permintaan kolaborasi ini?')) {
        return;
    }

    processingAction = true;
    console.log('Starting accept request process for ID:', requestId);

    // Show loading state in modal
    const modalContent = document.getElementById('modalContent');
    const originalContent = modalContent ? modalContent.innerHTML : '';

    if (modalContent) {
        modalContent.innerHTML = `
            <div class="text-center py-8">
                <i class="fas fa-spinner fa-spin text-4xl text-green-600 mb-4"></i>
                <p class="text-gray-600">Memproses penerimaan Anda...</p>
                <p class="text-gray-500 text-sm mt-2">Silakan tunggu sementara kami memperbarui status permintaan.</p>
            </div>
        `;
    }

    fetch(`/talent/request/${requestId}/accept`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        console.log('Accept request response:', data);
        processingAction = false;

        if (data.success) {
            showAlert('Permintaan berhasil diterima! ' + data.message, 'success');

            // Simple reload after success
            setTimeout(() => {
                console.log('Memuat ulang halaman setelah penerimaan berhasil...');
                location.reload();
            }, 2000);
        } else {
            showAlert('Error: ' + (data.message || 'Gagal menerima permintaan'), 'error');
            // Restore original modal content on error
            if (modalContent && originalContent) {
                modalContent.innerHTML = originalContent;
            }
        }
    })
    .catch(error => {
        console.error('Error accepting request:', error);
        processingAction = false;
        showAlert('Terjadi error jaringan. Silakan coba lagi.', 'error');
        // Restore original modal content on error
        if (modalContent && originalContent) {
            modalContent.innerHTML = originalContent;
        }
    });
}

// Modified reject request function
function rejectRequest(requestId) {
    if (processingAction) {
        console.log('Already processing an action, ignoring...');
        return;
    }

    if (!confirm('Apakah Anda yakin ingin menolak permintaan kolaborasi ini?')) {
        return;
    }

    processingAction = true;
    console.log('Starting reject request process for ID:', requestId);

    // Show loading state in modal
    const modalContent = document.getElementById('modalContent');
    const originalContent = modalContent ? modalContent.innerHTML : '';

    if (modalContent) {
        modalContent.innerHTML = `
            <div class="text-center py-8">
                <i class="fas fa-spinner fa-spin text-4xl text-red-600 mb-4"></i>
                <p class="text-gray-600">Memproses penolakan Anda...</p>
                <p class="text-gray-500 text-sm mt-2">Silakan tunggu sementara kami memperbarui status permintaan.</p>
            </div>
        `;
    }

    fetch(`/talent/request/${requestId}/reject`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        console.log('Reject request response:', data);
        processingAction = false;

        if (data.success) {
            showAlert('Permintaan berhasil ditolak.', 'success');

            // Simple reload after success
            setTimeout(() => {
                console.log('Memuat ulang halaman setelah penolakan berhasil...');
                location.reload();
            }, 2000);
        } else {
            showAlert('Error: ' + (data.message || 'Gagal menolak permintaan'), 'error');
            // Restore original modal content on error
            if (modalContent && originalContent) {
                modalContent.innerHTML = originalContent;
            }
        }
    })
    .catch(error => {
        console.error('Error rejecting request:', error);
        processingAction = false;
        showAlert('Terjadi error jaringan. Silakan coba lagi.', 'error');
        // Restore original modal content on error
        if (modalContent && originalContent) {
            modalContent.innerHTML = originalContent;
        }
    });
}

// Simplified modal opening function (no auto-close timeouts)
function openModal() {
    console.log('Attempting to open modal. modalInitialized:', modalInitialized);

    let modal = document.getElementById('talentRequestDetailsModal');

    if (!modal) {
        console.error('Modal element not found in the DOM.');
        if (modalInitialized) {
            console.warn('Modal was marked initialized, but element is missing. Forcing re-init.');
            modalInitialized = false;
        }
    }

    if (!modalInitialized) {
        console.log('Modal not initialized. Attempting to initialize.');
        initializeModal();

        if (!modalInitialized) {
            console.error('Modal initialization failed. Cannot open modal.');
            showAlert('Error: Sistem modal tidak siap. Silakan refresh halaman dan coba lagi.', 'error');
            return false;
        }

        modal = document.getElementById('talentRequestDetailsModal');
        if (!modal) {
            console.error('Modal initialized, but element still not found.');
            showAlert('Error: Masalah komponen modal. Silakan refresh.', 'error');
            return false;
        }
    }

    processingAction = false; // Reset processing flag
    modal.classList.remove('hidden');
    isModalOpen = true;
    handleModalStateChange(true);

    console.log('Modal opened successfully.');
    return true;
}

// View Job Details Function (for history)
function viewJobDetails(jobId) {
    if (!openModal()) return;

    const modalContent = document.getElementById('modalContent');

    // Show loading state
    modalContent.innerHTML = `
        <div class="text-center py-8">
            <i class="fas fa-spinner fa-spin text-4xl text-blue-600 mb-4"></i>
            <p class="text-gray-600">Memuat detail kolaborasi...</p>
        </div>
    `;

    fetch(`/talent/api/my-requests`)
    .then(response => response.json())
    .then(data => {
        if (data.success && data.requests) {
            const job = data.requests.find(r => r.id == jobId);

            if (job) {
                modalContent.innerHTML = `
                    <div class="space-y-6">
                        <div class="bg-gradient-to-r from-blue-50 to-purple-50 p-4 rounded-xl border border-blue-200">
                            <h4 class="font-bold text-lg text-gray-900 mb-3">📋 ${job.project_title || 'Detail Proyek'}</h4>
                            <p class="text-gray-700">${job.project_description || 'Tidak ada deskripsi tersedia'}</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="bg-green-50 p-4 rounded-xl">
                                <h4 class="font-semibold text-green-900 mb-3">🏢 Detail Kolaborasi</h4>
                                <div class="space-y-2">
                                    <div><span class="font-medium">Perusahaan:</span> ${job.recruiter_name || 'Tidak diketahui'}</div>
                                    <div><span class="font-medium">Peran:</span> ${job.recruiter_company || 'Tidak ditentukan'}</div>
                                    <div><span class="font-medium">Anggaran:</span> ${job.budget_range || 'Anggaran belum ditentukan'}</div>
                                    <div><span class="font-medium">Durasi:</span> ${job.project_duration || 'Durasi belum ditentukan'}</div>
                                </div>
                            </div>
                            <div class="bg-blue-50 p-4 rounded-xl">
                                <h4 class="font-semibold text-blue-900 mb-3">📊 Progres & Jadwal</h4>
                                <div class="space-y-2">
                                    <div><span class="font-medium">Status:</span>
                                        <span class="px-2 py-1 text-xs rounded-full ${getStatusBadgeClasses(job.status_badge_color)}">
                                            <i class="${job.status_icon} mr-1"></i>
                                            ${job.formatted_status}
                                        </span>
                                    </div>
                                    <div><span class="font-medium">Progres:</span> ${job.workflow_progress || 0}%</div>
                                    <div><span class="font-medium">Dimulai:</span> ${job.created_at}</div>
                                    ${job.both_parties_accepted ? '<div><span class="font-medium">Selesai:</span> <span class="text-green-600">✓ Selesai</span></div>' : ''}
                                </div>
                                <div class="mt-3">
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-blue-600 h-2 rounded-full" style="width: ${job.workflow_progress || 0}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        ${job.both_parties_accepted ? `
                            <div class="bg-green-50 p-4 rounded-xl border border-green-200">
                                <div class="flex items-center justify-center space-x-2 text-green-700">
                                    <i class="fas fa-trophy text-2xl"></i>
                                    <div>
                                        <h4 class="font-bold text-lg">Kolaborasi Berhasil Diselesaikan!</h4>
                                        <p class="text-sm">Proyek ini telah berhasil diselesaikan dan kedua belah pihak puas.</p>
                                    </div>
                                </div>
                            </div>
                        ` : `
                            <div class="bg-blue-50 p-4 rounded-xl border border-blue-200">
                                <div class="flex items-center justify-center space-x-2 text-blue-700">
                                    <i class="fas fa-clock text-xl"></i>
                                    <div>
                                        <h4 class="font-semibold">Kolaborasi Sedang Berlangsung</h4>
                                        <p class="text-sm">Proyek ini sedang aktif. Terus pertahankan kerja yang bagus!</p>
                                    </div>
                                </div>
                            </div>
                        `}
                    </div>
                `;
            } else {
                modalContent.innerHTML = `
                    <div class="text-center py-8">
                        <i class="fas fa-exclamation-triangle text-4xl text-red-600 mb-4"></i>
                        <p class="text-gray-600">Detail kolaborasi tidak ditemukan.</p>
                    </div>
                `;
            }
        } else {
            modalContent.innerHTML = `
                <div class="text-center py-8">
                    <i class="fas fa-exclamation-circle text-4xl text-red-600 mb-4"></i>
                    <p class="text-gray-600">Error memuat detail kolaborasi.</p>
                </div>
            `;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        modalContent.innerHTML = `
            <div class="text-center py-8">
                <i class="fas fa-wifi text-4xl text-red-600 mb-4"></i>
                <p class="text-gray-600">Terjadi error jaringan.</p>
                <button onclick="viewJobDetails(` + jobId + `)" class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-refresh mr-2"></i>Coba Lagi
                </button>
            </div>
        `;
    });
}

// View Request Details Function
function viewRequestDetails(requestId) {
    if (!openModal()) return;

    const modalContent = document.getElementById('modalContent');

    // Show loading state
    modalContent.innerHTML = `
        <div class="text-center py-8">
            <i class="fas fa-spinner fa-spin text-4xl text-blue-600 mb-4"></i>
            <p class="text-gray-600">Memuat detail permintaan...</p>
        </div>
    `;

    console.log('Fetching details for request ID:', requestId);

    // Add timeout to prevent infinite loading
    const controller = new AbortController();
    const timeoutId = setTimeout(() => controller.abort(), 10000); // 10 second timeout

    fetch(`/talent/api/my-requests`, {
        signal: controller.signal,
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        clearTimeout(timeoutId);
        console.log('Response status:', response.status);

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }

        return response.json();
    })
    .then(data => {
        console.log('API Response:', data);

        if (data.success && data.requests && Array.isArray(data.requests)) {
            const request = data.requests.find(r => r.id == requestId);
            console.log('Found request:', request);

            if (request) {
                modalContent.innerHTML = `
                    <div class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="bg-blue-50 p-4 rounded-xl">
                                <h4 class="font-semibold text-blue-900 mb-3">📋 Informasi Proyek</h4>
                                <div class="space-y-2">
                                    <div><span class="font-medium">Judul:</span> ${request.project_title || 'Tidak ditentukan'}</div>
                                    <div><span class="font-medium">Deskripsi:</span> <div class="text-sm text-gray-700 mt-1 max-h-20 overflow-y-auto">${request.project_description || 'Tidak ada deskripsi'}</div></div>
                                    <div><span class="font-medium">Anggaran:</span> ${request.budget_range || 'Anggaran belum ditentukan'}</div>
                                    <div><span class="font-medium">Durasi:</span> ${request.project_duration || 'Durasi belum ditentukan'}</div>
                                </div>
                            </div>
                            <div class="bg-green-50 p-4 rounded-xl">
                                <h4 class="font-semibold text-green-900 mb-3">🏢 Informasi Perekrut</h4>
                                <div class="space-y-2">
                                    <div><span class="font-medium">Nama:</span> ${request.recruiter_name || 'Tidak diketahui'}</div>
                                    <div><span class="font-medium">Perusahaan:</span> ${request.recruiter_company || 'Tidak ditentukan'}</div>
                                    <div><span class="font-medium">Status:</span> <span class="capitalize">${request.formatted_status || request.status}</span></div>
                                    <div><span class="font-medium">Dikirim:</span> ${request.created_at}</div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 p-4 rounded-xl">
                            <h4 class="font-semibold text-gray-900 mb-3">📊 Status & Progres</h4>
                            <div class="space-y-3">
                                <div><span class="font-medium">Status Saat Ini:</span>
                                    <span class="px-2 py-1 text-xs rounded-full ${getStatusBadgeClasses(request.status_badge_color)}">
                                        <i class="${request.status_icon || 'fas fa-clock'} mr-1"></i>
                                        ${request.formatted_status}
                                    </span>
                                </div>
                                <div>
                                    <div class="flex justify-between items-center mb-1">
                                        <span class="font-medium">Progres:</span>
                                        <span class="text-sm text-gray-600">${request.workflow_progress || 0}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-3">
                                        <div class="bg-blue-600 h-3 rounded-full transition-all duration-300" style="width: ${request.workflow_progress || 0}%"></div>
                                    </div>
                                </div>
                                ${request.acceptance_status ? `
                                    <div><span class="font-medium">Status Penerimaan:</span>
                                        <span class="text-sm text-gray-700">${request.acceptance_status}</span>
                                    </div>
                                ` : ''}
                            </div>
                        </div>

                        <div class="flex space-x-4">
                            ${request.can_accept ? `
                                <button onclick="acceptRequest(${request.id})"
                                        class="flex-1 px-6 py-3 bg-green-600 text-white rounded-xl hover:bg-green-700 transition-colors font-semibold">
                                    <i class="fas fa-check mr-2"></i>Terima Permintaan
                                </button>
                            ` : ''}
                            ${request.can_reject ? `
                                <button onclick="rejectRequest(${request.id})"
                                        class="flex-1 px-6 py-3 bg-red-600 text-white rounded-xl hover:bg-red-700 transition-colors font-semibold">
                                    <i class="fas fa-times mr-2"></i>Tolak Permintaan
                                </button>
                            ` : ''}
                            ${!request.can_accept && !request.can_reject ? `
                                <div class="flex-1 text-center py-3 bg-gray-100 text-gray-600 rounded-xl">
                                    ${request.both_parties_accepted ?
                                        '<i class="fas fa-check-circle text-green-600 mr-2"></i>Permintaan diterima oleh kedua belah pihak' :
                                        '<i class="fas fa-clock text-gray-500 mr-2"></i>Tidak ada tindakan tersedia'}
                                </div>
                            ` : ''}
                        </div>
                    </div>
                `;
            } else {
                modalContent.innerHTML = `
                    <div class="text-center py-8">
                        <i class="fas fa-exclamation-triangle text-4xl text-red-600 mb-4"></i>
                        <p class="text-gray-600">Permintaan dengan ID ${requestId} tidak ditemukan.</p>
                        <p class="text-gray-500 text-sm mt-2">Permintaan mungkin telah dihapus atau Anda tidak memiliki akses ke permintaan tersebut.</p>
                    </div>
                `;
            }
        } else {                modalContent.innerHTML = `
                    <div class="text-center py-8">
                        <i class="fas fa-exclamation-circle text-4xl text-red-600 mb-4"></i>
                        <p class="text-gray-600">Error memuat detail permintaan.</p>
                        <p class="text-gray-500 text-sm mt-2">${data.message || 'Format respons tidak valid'}</p>
                    </div>
                `;
        }
    })
    .catch(error => {
        clearTimeout(timeoutId);
        console.error('Error fetching request details:', error);

        let errorMessage = 'Terjadi error jaringan.';
        let errorDetail = 'Silakan periksa koneksi internet Anda dan coba lagi.';

        if (error.name === 'AbortError') {
            errorMessage = 'Permintaan timeout.';
            errorDetail = 'Server terlalu lama merespons. Silakan coba lagi.';
        } else if (error.message.includes('HTTP')) {
            errorMessage = 'Terjadi error server.';
            errorDetail = error.message;
        }

        modalContent.innerHTML = `
            <div class="text-center py-8">
                <i class="fas fa-wifi text-4xl text-red-600 mb-4"></i>
                <p class="text-gray-600">${errorMessage}</p>
                <p class="text-gray-500 text-sm mt-2">${errorDetail}</p>
                <button onclick="viewRequestDetails(${requestId})" class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-refresh mr-2"></i>Coba Lagi
                </button>
            </div>
        `;
    });
}

// Close Request Modal (simplified)
function closeRequestModal() {
    const modal = document.getElementById('talentRequestDetailsModal');
    if (modal) {
        // Don't close if we're processing an action
        if (processingAction) {
            console.log('Cannot close modal while processing action');
            return;
        }

        if (modal.classList.contains('hidden')) {
            console.log('Modal already hidden.');
            if (isModalOpen) {
                isModalOpen = false;
                handleModalStateChange(false);
            }
            return;
        }

        modal.classList.add('hidden');
        isModalOpen = false;
        handleModalStateChange(false);
        console.log('Modal closed successfully.');
    } else {
        console.warn('Modal element not found during close.');
        if (isModalOpen) {
            isModalOpen = false;
            handleModalStateChange(false);
        }
    }
}

// Helper function to process status badge classes
function getStatusBadgeClasses(statusBadgeColor) {
    // If statusBadgeColor is already a complete class string, return it
    if (typeof statusBadgeColor === 'string' && statusBadgeColor.includes('bg-')) {
        return statusBadgeColor;
    }

    // Map status types to Tailwind classes
    const colorMapping = {
        'success': 'bg-green-100 text-green-800',
        'warning': 'bg-yellow-100 text-yellow-800',
        'info': 'bg-blue-100 text-blue-800',
        'primary': 'bg-indigo-100 text-indigo-800',
        'danger': 'bg-red-100 text-red-800',
        'secondary': 'bg-gray-100 text-gray-800'
    };

    return colorMapping[statusBadgeColor] || 'bg-gray-100 text-gray-800';
}

// Show Alert Function
function showAlert(message, type) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `fixed top-4 right-4 z-50 px-6 py-4 rounded-xl shadow-lg transition-all duration-300 ${
        type === 'success' ? 'bg-green-100 border border-green-400 text-green-700' :
        type === 'info' ? 'bg-blue-100 border border-blue-400 text-blue-700' :
        'bg-red-100 border border-red-400 text-red-700'
    }`;
    alertDiv.innerHTML = `
        <div class="flex items-center">
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'info' ? 'info-circle' : 'exclamation-circle'} mr-2"></i>
            ${message}
        </div>
    `;

    document.body.appendChild(alertDiv);

    setTimeout(() => {
        alertDiv.style.transform = 'translateX(100%)';
        alertDiv.style.opacity = '0';
        setTimeout(() => alertDiv.remove(), 300);
    }, 5000);
}

// Handle resume upload
function handleResumeUpload(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const maxSize = 5 * 1024 * 1024; // 5MB

        if (file.size > maxSize) {
            showAlert('Ukuran file harus kurang dari 5MB', 'error');
            return;
        }

        // Here you would typically upload the file to your server
        showAlert('Fitur upload resume akan segera diimplementasikan!', 'info');
    }
}

// Show contact modal for red-flagged talents
function showContactModal() {
    // Create a simple alert or modal for contacting admin
    const message = `Untuk bantuan dengan status akun Anda, silakan hubungi tim support kami:\n\n` +
                   `Email: talent-support@webpelatihan.com\n` +
                   `Telepon: +62-XXX-XXXX-XXXX\n\n` +
                   `Silakan sertakan nama Anda dan jelaskan pertanyaan yang Anda miliki tentang akun Anda.`;

    if (confirm(message + '\n\nApakah Anda ingin menyalin alamat email ke clipboard?')) {
        // Copy email to clipboard if supported
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText('talent-support@webpelatihan.com').then(() => {
                showAlert('Alamat email berhasil disalin ke clipboard!', 'success');
            }).catch(() => {
                showAlert('Silakan salin manual: talent-support@webpelatihan.com', 'info');
            });
        } else {
            showAlert('Silakan salin manual: talent-support@webpelatihan.com', 'info');
        }
    }
}
</script>
@endsection
