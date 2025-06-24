@extends('layout.template.mainTemplate')

@section('title', 'Dashboard Perekrut')
@section('container')

{{-- Dashboard Container with Flexbox Layout --}}
<div class="space-y-8">

    {{-- Hero welcome greeting card - Full Width --}}
    <div class="w-full bg-gradient-to-r from-blue-600 to-purple-600 rounded-2xl p-8 text-white shadow-xl mt-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold mb-2">Selamat datang kembali, {{ $user->name }}! 👋</h1>
                <p class="text-blue-100 text-lg">Siap untuk menemukan bakat luar biasa dan membangun tim impian Anda?</p>
            </div>
            <div class="hidden md:block">
                <div class="bg-white/20 backdrop-blur-sm rounded-xl p-4 text-center">
                    <i class="fas fa-star text-4xl text-yellow-300 mb-2"></i>
                    <div class="text-sm font-medium">Status Akun</div>
                    <div class="text-xs opacity-90">
                        {{ $user->is_active_talent ? 'Aktif' : 'Tidak Aktif' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Stats Cards Row - 32% Width Each --}}
    <div class="flex flex-wrap gap-[2%]">
        {{-- Available Talents Card - 32% Width --}}
        <div class="w-[32%] bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-all duration-300">
            <div class="flex items-center mb-4">
                <div class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center mr-4">
                    <i class="fas fa-users text-xl text-white"></i>
                </div>
                <div>
                    <p class="text-sm font-semibold text-blue-600 uppercase">Kandidat terseida</p>
                    <p class="text-gray-500 text-sm">Siap untuk berkolaborasi</p>
                </div>
            </div>

            <div class="mb-4">
                <div class="text-4xl font-bold text-gray-900 mb-1">
                    {{ $talents->total() }}
                </div>
                <p class="text-gray-600">Kandidat aktif</p>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                <div class="flex items-center text-sm text-green-600">
                    <i class="fas fa-check-circle mr-2"></i>
                    <span class="font-medium">Siap untuk merekrut</span>
                </div>
            </div>
        </div>

        {{-- Account Status Card - 32% Width --}}
        <div class="w-[32%] bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-all duration-300">
            <div class="flex items-center mb-4">
                <div class="w-12 h-12 bg-{{ $user->recruiter && $user->recruiter->is_active ? 'emerald' : 'red' }}-500 rounded-xl flex items-center justify-center mr-4">
                    <i class="fas fa-{{ $user->recruiter && $user->recruiter->is_active ? 'shield-check' : 'shield-exclamation' }} text-xl text-white"></i>
                </div>
                <div>
                    <p class="text-sm font-semibold text-{{ $user->recruiter && $user->recruiter->is_active ? 'emerald' : 'red' }}-600 uppercase">Status Akun</p>
                    <p class="text-gray-500 text-sm">Akses rekrutmen Anda</p>
                </div>
            </div>

            <div class="mb-4">
                <div class="text-4xl font-bold text-gray-900 mb-1">
                    {{ $user->recruiter && $user->recruiter->is_active ? 'Aktif' : 'Tidak Aktif' }}
                </div>
                <p class="text-gray-600">{{ $user->recruiter && $user->recruiter->is_active ? 'Semua sistem beroperasi' : 'Mode akses terbatas' }}</p>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                <div class="flex items-center text-sm text-{{ $user->recruiter && $user->recruiter->is_active ? 'emerald' : 'red' }}-600">
                    <i class="fas fa-{{ $user->recruiter && $user->recruiter->is_active ? 'check-circle' : 'exclamation-triangle' }} mr-2"></i>
                    <span class="font-medium">{{ $user->recruiter && $user->recruiter->is_active ? 'Siap digunakan' : 'Hubungi admin' }}</span>
                </div>
                <div class="inline-flex items-center px-3 py-1 bg-{{ $user->recruiter && $user->recruiter->is_active ? 'emerald' : 'red' }}-50 text-{{ $user->recruiter && $user->recruiter->is_active ? 'emerald' : 'red' }}-700 text-xs font-medium rounded-full">
                    <div class="w-2 h-2 bg-{{ $user->recruiter && $user->recruiter->is_active ? 'emerald' : 'red' }}-500 rounded-full mr-2"></div>
                    {{ $user->recruiter && $user->recruiter->is_active ? 'Aktif' : 'Offline' }}
                </div>
            </div>
        </div>        {{-- My Requests Card - 32% Width --}}
        <div class="w-[32%] bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-all duration-300">
            <div class="flex items-center mb-4">
                <div class="w-12 h-12 bg-purple-500 rounded-xl flex items-center justify-center mr-4">
                    <i class="fas fa-paper-plane text-xl text-white"></i>
                </div>
                <div>
                    <p class="text-sm font-semibold text-purple-600 uppercase">Permintaan saya</p>
                    <p class="text-gray-500 text-sm">Permintaan kolaborasi</p>
                </div>
            </div>

            <div class="mb-4">
                <div class="text-4xl font-bold text-gray-900 mb-1">
                    {{ isset($myRequests) && (method_exists($myRequests, 'count') ? $myRequests->count() : (is_countable($myRequests) ? count($myRequests) : 0)) }}
                </div>
                <p class="text-gray-600">Pengajuan aktif</p>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                <div class="flex items-center text-sm text-purple-600">
                    <i class="fas fa-chart-line mr-2"></i>
                    <span class="font-medium">Lacak progres</span>
                </div>
                <div class="inline-flex items-center px-3 py-1 bg-purple-50 text-purple-700 text-xs font-medium rounded-full">
                    <div class="w-2 h-2 bg-purple-500 rounded-full mr-2"></div>
                    Aktif
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Requests Section - Full Width --}}
    @if(isset($myRequests) && (method_exists($myRequests, 'count') ? $myRequests->count() > 0 : (is_countable($myRequests) ? count($myRequests) > 0 : false)))
    <div class="w-full bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
        <!-- Header with gradient background -->
        <div class="bg-blue-600 text-white p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold mb-2">Permintaan Terbaru</h2>
                    <p class="text-blue-100">Pipeline kolaborasi Anda</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('recruiter.my_requests') }}"
                       class="px-4 py-2 bg-white text-blue-600 rounded-lg hover:bg-gray-50 transition-colors font-medium">
                        <i class="fas fa-eye mr-2"></i>Lihat Semua
                    </a>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="p-6">
            @if(isset($myRequests) && is_iterable($myRequests) && count($myRequests) > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($myRequests as $request)
                        <div class="bg-white border rounded-xl p-6 hover:shadow-lg transition-shadow">
                            <!-- Profile -->
                            <div class="text-center mb-4">
                                <img class="w-16 h-16 rounded-full mx-auto mb-3 object-cover"
                                     src="{{ $request->talent->user->avatar_url }}"
                                     alt="{{ $request->talent->user->name }}">

                                <h3 class="font-bold text-lg text-gray-900">{{ $request->talent->user->name }}</h3>
                                @if($request->talent->user->pekerjaan)
                                    <p class="text-gray-600 text-sm">{{ $request->talent->user->pekerjaan }}</p>
                                @endif
                            </div>

                            <!-- Project Title -->
                            <div class="text-center mb-4">
                                <div class="bg-gray-50 py-2 px-3 rounded-lg">
                                    <div class="text-xs text-gray-600">Proyek</div>
                                    <div class="font-medium text-sm">{{ $request->project_title }}</div>
                                </div>
                            </div>

                            <!-- Status and Date -->
                            <div class="grid grid-cols-1 gap-3 mb-4 text-center">
                                <div class="bg-gray-50 py-2 rounded">
                                    <div class="text-xs text-gray-600">Status</div>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                        @if($request->getRecruiterStatusBadgeColor() == 'success') bg-green-100 text-green-800
                                        @elseif($request->getRecruiterStatusBadgeColor() == 'warning') bg-yellow-100 text-yellow-800
                                        @elseif($request->getRecruiterStatusBadgeColor() == 'info') bg-blue-100 text-blue-800
                                        @elseif($request->getRecruiterStatusBadgeColor() == 'danger') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ $request->getRecruiterDisplayStatus() }}
                                    </span>
                                </div>
                                <div class="bg-gray-50 py-2 rounded">
                                    <div class="text-xs text-gray-600">Diminta</div>
                                    <div class="font-bold text-sm">{{ $request->created_at->diffForHumans() }}</div>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="space-y-2">
                                <div class="grid grid-cols-2 gap-2">
                                    <button onclick="viewRequestDetails('{{ $request->id }}')"
                                            class="px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm">
                                        <i class="fas fa-eye mr-1"></i>Detail
                                    </button>
                                    <a href="mailto:{{ $request->talent->user->email }}"
                                       class="px-3 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors text-center text-sm">
                                        <i class="fas fa-envelope mr-1"></i>Email
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <div class="w-16 h-16 bg-gray-100 rounded-full mx-auto mb-4 flex items-center justify-center">
                        <i class="fas fa-inbox text-2xl text-gray-400"></i>
                    </div>
                    <h5 class="text-lg font-medium text-gray-700 mb-2">Belum ada permintaan</h5>
                    <p class="text-gray-500">Mulai mencari talenta untuk melihat permintaan Anda di sini.</p>
                </div>
            @endif
        </div>
    </div>
    @endif

    {{-- Premium Talent Discovery Section - Full Width --}}
    <div class="w-full bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
        <!-- Simple Header -->
        <div class="bg-emerald-600 text-white p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold mb-2">Pencarian Talenta</h2>
                    <p class="text-emerald-100">Temukan dan terhubung dengan profesional berbakat</p>
                </div>
                <div class="flex gap-3">
                    <button onclick="toggleScoutingFilters()"
                            class="px-4 py-2 bg-white/20 rounded-lg hover:bg-white/30 transition-colors">
                        <i class="fas fa-filter mr-2"></i>Filter
                    </button>
                    <button onclick="toggleCompareMode()"
                            class="px-4 py-2 bg-white/20 rounded-lg hover:bg-white/30 transition-colors" id="compareModeBtn">
                        <i class="fas fa-balance-scale mr-2"></i>Bandingkan
                    </button>
                    <button onclick="refreshTalents()"
                            class="px-4 py-2 bg-white text-emerald-600 rounded-lg hover:bg-gray-50 transition-colors font-medium">
                        <i class="fas fa-sync-alt mr-2"></i>Refresh
                    </button>
                </div>
            </div>
        </div>

        <!-- Search Bar Section -->
        <div class="bg-white border-b p-4">
            <div class="max-w-md mx-auto">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" 
                           id="talentSearchInput" 
                           class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500" 
                           placeholder="Search by talent name or skills/certificates..." 
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
            </div>
        </div>

        <!-- Enhanced Filters Panel -->
        <div id="scoutingFilters" class="hidden bg-gradient-to-r from-gray-50 to-gray-100 border-b">
            <div class="p-6">
                <!-- Filter Header -->
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                        <i class="fas fa-filter mr-2 text-emerald-600"></i>
                        Advanced Filters
                    </h3>
                    <div class="flex items-center gap-3">
                        <span id="activeFiltersCount" class="hidden px-2 py-1 bg-emerald-100 text-emerald-800 text-xs rounded-full font-medium"></span>
                        <button onclick="resetAllFilters()" class="text-sm text-gray-600 hover:text-gray-800 underline">
                            Clear All
                        </button>
                    </div>
                </div>

                <!-- Filter Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-4 gap-6">
                    <!-- Certificates Filter -->
                    <div class="filter-group">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-certificate mr-1 text-yellow-500"></i>
                            Certificates Count
                        </label>
                        <select id="certificatesFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="">Any number of certificates</option>
                            <option value="0">No certificates</option>
                            <option value="1-2">1-2 certificates</option>
                            <option value="3-5">3-5 certificates</option>
                            <option value="6-10">6-10 certificates</option>
                            <option value="10+">10+ certificates</option>
                        </select>
                    </div>

                    <!-- Quiz Performance Filter -->
                    <div class="filter-group">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-brain mr-1 text-indigo-500"></i>
                            Quiz Average Score
                        </label>
                        <select id="quizFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="">Any quiz performance</option>
                            <option value="90-100">Excellent (90-100%)</option>
                            <option value="80-89">Very Good (80-89%)</option>
                            <option value="70-79">Good (70-79%)</option>
                            <option value="60-69">Average (60-69%)</option>
                            <option value="0-59">Below Average (0-59%)</option>
                        </select>
                    </div>

                    <!-- Availability Filter -->
                    <div class="filter-group">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-clock mr-1 text-blue-500"></i>
                            Availability Status
                        </label>
                        <select id="availabilityFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="">All talents</option>
                            <option value="available">Available</option>
                            <option value="busy">Currently busy</option>
                            <option value="partially-available">Partially available</option>
                        </select>
                    </div>

                    <!-- Red Flags Filter -->
                    <div class="filter-group">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-flag mr-1 text-red-500"></i>
                            Red Flags Status
                        </label>
                        <select id="redflagsFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="">All talents</option>
                            <option value="no-redflags">No red flags</option>
                            <option value="has-redflags">Has red flags</option>
                        </select>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-between mt-6 pt-4 border-t border-gray-200">
                    <div class="flex items-center gap-4">
                        <button onclick="resetAllFilters()" 
                                class="px-4 py-2 text-gray-600 hover:text-gray-800 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                            <i class="fas fa-undo mr-1"></i>
                            Reset All
                        </button>
                        <button onclick="saveFilterPreset()" 
                                class="px-4 py-2 text-emerald-600 hover:text-emerald-800 border border-emerald-300 rounded-lg hover:bg-emerald-50 transition-colors">
                            <i class="fas fa-save mr-1"></i>
                            Save Preset
                        </button>
                    </div>
                    <div class="flex items-center gap-3">
                        <span id="filteredResultsCount" class="text-sm text-gray-600"></span>
                        <button onclick="applyAdvancedFilters()" 
                                class="px-6 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors font-medium">
                            <i class="fas fa-search mr-1"></i>
                            Apply Filters
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Results Summary -->
        <div id="filterResultsSummary" class="hidden bg-blue-50 border-b border-blue-200 px-6 py-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center text-blue-800">
                    <i class="fas fa-info-circle mr-2"></i>
                    <span id="filterSummaryText"></span>
                </div>
                <button onclick="clearFilterResults()" class="text-blue-600 hover:text-blue-800 text-sm underline">
                    Clear filters
                </button>
            </div>
                        Terapkan
                    </button>
                </div>
            </div>
        </div>

        <!-- Talent Cards -->
        <div class="p-6">
            <!-- Search Results Info -->
            <div id="searchResultsInfo" class="hidden mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                    <span id="searchResultsText" class="text-blue-800 text-sm"></span>
                </div>
            </div>
            
            @if(isset($talents) && is_iterable($talents) && (method_exists($talents, 'count') ? $talents->count() > 0 : count($talents) > 0))
                <div id="talentCardsContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($talents as $talent)
                        @php
                            $metrics = $talent->scouting_metrics ?? [];
                            $overallScore = 0;
                            // Get talent skills/certificates for search
                            $searchSkills = [];
                            
                            // Add certificates
                            if(isset($talent->certificates) && is_iterable($talent->certificates)) {
                                foreach($talent->certificates as $cert) {
                                    $searchSkills[] = $cert->name ?? '';
                                }
                            }
                            
                            // Add actual talent skills
                            $talentSkillsArray = $talent->user->getTalentSkillsArray();
                            if(is_array($talentSkillsArray)) {
                                foreach($talentSkillsArray as $skill) {
                                    if(is_string($skill)) {
                                        $searchSkills[] = $skill;
                                    } elseif(is_array($skill)) {
                                        // Handle both 'skill_name' and 'name' properties
                                        $skillName = $skill['skill_name'] ?? $skill['name'] ?? null;
                                        if($skillName) {
                                            $searchSkills[] = $skillName;
                                        }
                                    } elseif(is_object($skill)) {
                                        // Handle both 'skill_name' and 'name' properties
                                        $skillName = $skill->skill_name ?? $skill->name ?? null;
                                        if($skillName) {
                                            $searchSkills[] = $skillName;
                                        }
                                    }
                                }
                            }
                            
                            $skillsString = implode(' ', array_filter($searchSkills));
                            
                            // Continue with existing metrics calculation
                            $scoreCount = 0;
                            foreach(['learning_velocity', 'consistency', 'certifications'] as $key) {
                                if(isset($metrics[$key]['score'])) {
                                    $overallScore += $metrics[$key]['score'];
                                    $scoreCount++;
                                }
                            }
                            $overallScore = $scoreCount > 0 ? round($overallScore / $scoreCount) : 0;
                            
                            // Get redflag summary early for use in template
                            $redflagSummary = $talent->getRedflagSummary();
                        @endphp

                        <div class="talent-card bg-white rounded-xl shadow-md hover:shadow-lg transition-all duration-300 border border-gray-100 p-6 relative {{ $redflagSummary['has_redflags'] ? 'has-redflags' : '' }}" 
                             data-talent-id="{{ $talent->id }}"
                             data-talent-name="{{ strtolower($talent->user->name ?? '') }}" 
                             data-talent-skills="{{ strtolower($skillsString) }}">
                            <!-- Compare Checkbox (Hidden by default) -->
                            <div class="compare-checkbox hidden absolute top-4 right-4 z-10">
                                @php
                                    $talentSkills = $talent->user->getTalentSkillsArray();
                                    // Ensure skills is always a valid array
                                    if (!is_array($talentSkills)) {
                                        $talentSkills = [];
                                    }
                                    // Use proper JSON encoding for HTML attributes with additional safety
                                    $skillsJson = json_encode($talentSkills, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
                                    // Fallback for invalid JSON
                                    if (json_last_error() !== JSON_ERROR_NONE) {
                                        $skillsJson = '[]';
                                    }
                                @endphp
                                <input type="checkbox"
                                       class="talent-compare-check w-5 h-5 text-emerald-600 rounded focus:ring-emerald-500"
                                       data-talent-id="{{ $talent->id }}"
                                       data-talent-name="{{ $talent->user->name }}"
                                       data-talent-email="{{ $talent->user->email }}"
                                       data-talent-position="{{ $talent->user->pekerjaan ?? 'Tidak ditentukan' }}"
                                       data-talent-score="{{ $overallScore }}"
                                       data-talent-courses="{{ $metrics['progress_tracking']['completed_courses'] ?? 0 }}"
                                       data-talent-certificates="{{ $metrics['certifications']['total_certificates'] ?? 0 }}"
                                       data-talent-quiz-avg="{{ $metrics['quiz_performance']['average_score'] ?? 0 }}"
                                       data-talent-skills="{{ $skillsJson }}"
                                       data-talent-redflag-count="{{ $redflagSummary['count'] }}"
                                       data-talent-completed-projects="{{ $redflagSummary['total_completed'] }}"
                                       data-talent-redflag-rate="{{ $redflagSummary['rate'] }}"
                                       data-talent-has-redflags="{{ $redflagSummary['has_redflags'] ? 'true' : 'false' }}">
                            </div>

                            <!-- Profile -->
                            <div class="text-center mb-4">
                                <img class="w-16 h-16 rounded-full mx-auto mb-3 object-cover"
                                     src="{{ $talent->user->avatar_url }}"
                                     alt="{{ $talent->user->name }}">

                                <h3 class="font-bold text-lg text-gray-900">{{ $talent->user->name }}</h3>
                                @if($talent->user->pekerjaan)
                                    <p class="text-gray-600 text-sm">{{ $talent->user->pekerjaan }}</p>
                                @endif

                                {{-- New Project-based Redflag System --}}
                                @if($redflagSummary['has_redflags'])
                                    <div class="mt-2 flex flex-col items-center space-y-1">
                                        <span class="inline-flex items-center px-3 py-1 {{ $redflagSummary['badge_class'] }} text-xs font-semibold rounded-full shadow-sm">
                                            <i class="fas fa-flag mr-1"></i>{{ $redflagSummary['display_text'] }}
                                        </span>
                                        <button onclick="showTalentRedflagHistory('{{ $talent->id }}', '{{ $talent->user->name }}')"
                                                class="text-xs text-red-600 hover:text-red-800 hover:bg-red-50 px-2 py-1 rounded transition-all duration-200 border border-transparent hover:border-red-200">
                                            <i class="fas fa-history mr-1"></i>Lihat Riwayat
                                        </button>
                                    </div>
                                @endif
                            </div>

                            <!-- Score -->
                            <div class="text-center mb-4">
                                <div class="inline-flex items-center px-3 py-1 bg-emerald-100 text-emerald-800 rounded-full">
                                    <span class="font-bold">Skor: {{ $overallScore }}/100</span>
                                </div>
                            </div>

                            <!-- Quick Stats -->
                            <div class="grid grid-cols-3 gap-2 mb-4 text-center">
                                <div class="bg-gray-50 py-2 rounded">
                                    <div class="text-xs text-gray-600">Kursus</div>
                                    <div class="font-bold">{{ $metrics['progress_tracking']['completed_courses'] ?? 0 }}</div>
                                </div>
                                <div class="bg-gray-50 py-2 rounded">
                                    <div class="text-xs text-gray-600">Sertifikat</div>
                                    <div class="font-bold">{{ $metrics['certifications']['total_certificates'] ?? 0 }}</div>
                                </div>
                                <div class="bg-gray-50 py-2 rounded">
                                    <div class="text-xs text-gray-600">Rata-rata Kuis</div>
                                    <div class="font-bold">{{ $metrics['quiz_performance']['average_score'] ?? 0 }}%</div>
                                </div>
                            </div>
                            <!-- Completed Projects Count -->
                            <div class="mb-4 text-center">
                                <span class="inline-flex items-center px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-xs font-medium">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    Completed Projects: <span class="font-bold ml-1">{{ $redflagSummary['total_completed'] ?? 0 }}</span>
                                </span>
                            </div>

                            <!-- Availability Status -->
                            <div class="mb-4 text-center">
                                @if(isset($talent->availability_status))
                                    @if($talent->availability_status['available'])
                                        <div class="inline-flex items-center px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">
                                            <div class="w-2 h-2 bg-green-500 rounded-full mr-2"></div>
                                            Tersedia Sekarang
                                        </div>
                                    @else
                                        <div class="inline-flex items-center px-3 py-1 bg-orange-100 text-orange-800 rounded-full text-sm font-medium">
                                            <div class="w-2 h-2 bg-orange-500 rounded-full mr-2"></div>
                                            {{ $talent->availability_status['status'] }}
                                        </div>
                                        @if(isset($talent->availability_status['next_available_date']))
                                            <div class="text-xs text-gray-500 mt-1">
                                                Available: {{ \Carbon\Carbon::parse($talent->availability_status['next_available_date'])->format('M d, Y') }}
                                            </div>
                                        @endif
                                    @endif
                                @else
                                    <div class="inline-flex items-center px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-sm font-medium">
                                        <div class="w-2 h-2 bg-gray-500 rounded-full mr-2"></div>
                                        Status Tidak Diketahui
                                    </div>
                                @endif
                            </div>

                            <!-- Skills Section -->
                            <div class="mb-4">
                                @php
                                    $skills = $talent->user->getTalentSkillsArray();
                                @endphp
                                @if(!empty($skills))
                                    <div class="bg-gray-50 rounded-lg p-3">
                                        <h4 class="text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                            <i class="fas fa-star text-yellow-500 mr-1"></i>Keahlian
                                        </h4>
                                        <div class="space-y-1">
                                            @foreach(array_slice($skills, 0, 3) as $skill)
                                                @php
                                                    // Handle both string skills and object skills
                                                    if (is_string($skill)) {
                                                        $skillName = $skill;
                                                        $skillProficiency = 'intermediate'; // Default
                                                    } else {
                                                        $skillName = $skill['skill_name'] ?? $skill['name'] ?? (is_string($skill) ? $skill : 'Unknown Skill');
                                                        $skillProficiency = $skill['proficiency'] ?? $skill['level'] ?? 'intermediate';
                                                    }
                                                @endphp
                                                <div class="flex justify-between items-center text-xs">
                                                    <span class="text-gray-700 font-medium">{{ $skillName }}</span>
                                                    <span class="px-2 py-1 rounded-full text-xs font-medium
                                                        @if(strtolower($skillProficiency) == 'advanced' || strtolower($skillProficiency) == 'expert') bg-green-100 text-green-800
                                                        @elseif(strtolower($skillProficiency) == 'intermediate') bg-blue-100 text-blue-800
                                                        @elseif(strtolower($skillProficiency) == 'beginner') bg-yellow-100 text-yellow-800
                                                        @else bg-gray-100 text-gray-800 @endif">
                                                        {{ ucfirst($skillProficiency) }}
                                                    </span>
                                                </div>
                                            @endforeach
                                            @if(count($skills) > 3)
                                                <div class="mt-2 text-center">
                                                    <button onclick="showAllSkills('{{ $talent->id }}', '{{ $talent->user->name }}', {{ json_encode($skills) }}, {{ $talent->redflagged ? 'true' : 'false' }}, '{{ e($talent->redflag_reason ?? '') }}')"
                                                            class="text-xs text-blue-600 hover:text-blue-800 font-medium underline decoration-dotted hover:decoration-solid transition-all">
                                                        <i class="fas fa-eye mr-1"></i>Lihat semua {{ count($skills) }} keahlian
                                                    </button>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <div class="bg-gray-50 rounded-lg p-3">
                                        <div class="text-center text-gray-500 text-sm">
                                            <i class="fas fa-graduation-cap text-gray-400 mb-1"></i>
                                            <div>Belum ada keahlian yang diperoleh</div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Status -->
                            @php $existingRequest = $talent->talentRequests->first(); @endphp
                            @if($existingRequest && !in_array($existingRequest->status, ['rejected', 'completed']))
                                <div class="mb-4 text-center">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                        @if($existingRequest->getRecruiterStatusBadgeColor() == 'success') bg-green-100 text-green-800
                                        @elseif($existingRequest->getRecruiterStatusBadgeColor() == 'warning') bg-yellow-100 text-yellow-800
                                        @elseif($existingRequest->getRecruiterStatusBadgeColor() == 'info') bg-blue-100 text-blue-800
                                        @elseif($existingRequest->getRecruiterStatusBadgeColor() == 'danger') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ $existingRequest->getRecruiterDisplayStatus() }}
                                    </span>
                                </div>
                            @endif

                            <!-- Actions -->
                            <div class="space-y-2">
                                @if(!$existingRequest || in_array($existingRequest->status, ['rejected', 'completed']))
                                    @if(isset($talent->availability_status) && $talent->availability_status['available'])
                                        <button onclick="openRequestModal('{{ $talent->id }}', '{{ $talent->user->name }}', {{ $redflagSummary['has_redflags'] ? 'true' : 'false' }}, '{{ $redflagSummary['count'] }}')"
                                                class="w-full px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors">
                                            <i class="fas fa-handshake mr-2"></i>Request Talent
                                        </button>
                                    @endif
                                @endif
                                <div class="grid grid-cols-2 gap-2">
                                    <button onclick="viewScoutingReport('{{ $talent->id }}', '{{ $talent->user->name }}', {{ json_encode($metrics) }}, {{ $redflagSummary['has_redflags'] ? 'true' : 'false' }}, '{{ $redflagSummary['count'] }}', {{ json_encode($talent->completed_projects ?? []) }})"
                                            class="px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm">
                                        <i class="fas fa-chart-line mr-1"></i>Report
                                    </button>
                                    <a href="mailto:{{ $talent->user->email }}"
                                       class="px-3 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors text-center text-sm">
                                        <i class="fas fa-envelope mr-1"></i>Email
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if(isset($talents) && method_exists($talents, 'links'))
                    <div class="mt-8 flex justify-center">
                        {{ $talents->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-12">
                    <div class="w-16 h-16 bg-gray-100 rounded-full mx-auto mb-4 flex items-center justify-center">
                        <i class="fas fa-user-tie text-2xl text-gray-400"></i>
                    </div>
                    <h5 class="text-lg font-medium text-gray-700 mb-2">No Talents Available</h5>
                    <p class="text-gray-500">Check back later or contact your administrator.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Talent Details Modal -->
<div class="modal fade" id="talentDetailsModal" tabindex="-1" role="dialog" aria-labelledby="talentDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content rounded-2xl border-0 shadow-2xl">
            <div class="modal-header bg-gradient-to-r from-blue-600 to-indigo-700 text-white rounded-t-2xl border-0 p-6">
                <h5 class="modal-title text-xl font-bold flex items-center" id="talentDetailsModalLabel">
                    <div class="w-8 h-8 bg-white bg-opacity-20 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-user-tie text-white"></i>
                    </div>
                    Talent Profile Details
                </h5>
                <button type="button" class="text-white hover:text-gray-200 transition-colors duration-200" data-dismiss="modal" aria-label="Close">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="modal-body p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div class="bg-gray-50 p-4 rounded-xl">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Full Name</label>
                            <p id="modalTalentName" class="text-gray-900 font-medium"></p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-xl">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Email Address</label>
                            <p id="modalTalentEmail" class="text-gray-900 font-medium"></p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-xl">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Phone Number</label>
                            <p id="modalTalentPhone" class="text-gray-900 font-medium"></p>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div class="bg-gray-50 p-4 rounded-xl">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Profession</label>
                            <p id="modalTalentProfession" class="text-gray-900 font-medium"></p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-xl">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Address</label>
                            <p id="modalTalentAddress" class="text-gray-900 font-medium"></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-gray-50 rounded-b-2xl border-0 p-6">
                <button type="button" class="px-6 py-3 bg-gray-500 text-white rounded-xl hover:bg-gray-600 transition-all duration-200 font-medium mr-3" data-dismiss="modal">
                    <i class="fas fa-times mr-2"></i>Close
                </button>
                <button type="button" class="px-6 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-all duration-200 font-medium" onclick="contactTalent()">
                    <i class="fas fa-envelope mr-2"></i>Send Email
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Talent Request Modal -->
<div class="modal fade" id="talentRequestModal" tabindex="-1" role="dialog" aria-labelledby="talentRequestModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content rounded-2xl border-0 shadow-2xl">
            <div class="modal-header bg-gradient-to-r from-green-600 to-emerald-700 text-white rounded-t-2xl border-0 p-6">
                <h5 class="modal-title text-xl font-bold flex items-center" id="talentRequestModalLabel">
                    <div class="w-8 h-8 bg-white bg-opacity-20 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-handshake text-white"></i>
                    </div>
                    Request Talent Collaboration
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
                                <h6 class="font-semibold text-blue-800 mb-1">Important Information</h6>
                                <p class="text-blue-700 text-sm">Your request will be reviewed by the Talent Admin who will coordinate a meeting between you and the talent. Please provide detailed project information to expedite the process.</p>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" id="requestTalentId" name="talent_id">

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <div class="space-y-6">
                            <div>
                                <label for="projectTitle" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Project Title <span class="text-red-500">*</span>
                                </label>
                                <input type="text" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                                       id="projectTitle" name="project_title" required
                                       placeholder="e.g., Mobile App Development">
                            </div>

                            <div>
                                <label for="budgetRange" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Project Budget Range
                                    <span class="text-xs text-gray-500 block font-normal">Total budget for the entire project/freelance work</span>
                                </label>
                                <select class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                                        id="budgetRange" name="budget_range">
                                    <option value="">Select project budget range</option>
                                    <option value="Under Rp 10.000.000">Under Rp 10.000.000</option>
                                    <option value="Rp 10.000.000 - Rp 50.000.000">Rp 10.000.000 - Rp 50.000.000</option>
                                    <option value="Rp 50.000.000 - Rp 100.000.000">Rp 50.000.000 - Rp 100.000.000</option>
                                    <option value="Rp 100.000.000 - Rp 250.000.000">Rp 100.000.000 - Rp 250.000.000</option>
                                    <option value="Rp 250.000.000+">Rp 250.000.000+</option>
                                    <option value="Negotiable">Negotiable</option>
                                </select>
                                <p class="text-xs text-gray-500 mt-1">💡 This is for freelance projects, not monthly employment salaries</p>
                            </div>

                            <div>
                                <label for="projectDuration" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Project Duration <span class="text-red-500">*</span>
                                </label>
                                <select class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                                        id="projectDuration" name="project_duration" required>
                                    <option value="">Select duration</option>
                                    <option value="1-2 weeks">1-2 weeks</option>
                                    <option value="1 month">1 month</option>
                                    <option value="2-3 months">2-3 months</option>
                                    <option value="3-6 months">3-6 months</option>
                                    <option value="6+ months">6+ months</option>
                                    <option value="Ongoing">Ongoing</option>
                                </select>
                                <p class="text-xs text-gray-500 mt-1">Required for time-blocking to prevent overlapping projects</p>
                            </div>
                        </div>

                        <div class="space-y-6">
                            <div>
                                <label for="projectDescription" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Project Description <span class="text-red-500">*</span>
                                </label>
                                <textarea class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 resize-none"
                                          id="projectDescription" name="project_description" rows="5" required
                                          placeholder="Describe your project, goals, and what you're looking for..."></textarea>
                            </div>

                            <div>
                                <label for="requirements" class="block text-sm font-semibold text-gray-700 mb-2">Specific Requirements</label>
                                <textarea class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 resize-none"
                                          id="requirements" name="requirements" rows="3"
                                          placeholder="List any specific skills, technologies, or qualifications needed..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-gray-50 rounded-b-2xl border-0 p-6">
                    <button type="button" class="px-6 py-3 bg-gray-500 text-white rounded-xl hover:bg-gray-600 transition-all duration-200 font-medium mr-3"
                            data-dismiss="modal" onclick="$('#talentRequestModal').modal('hide')">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </button>
                    <button type="submit" class="px-6 py-3 bg-green-600 text-white rounded-xl hover:bg-green-700 transition-all duration-200 font-medium">
                        <i class="fas fa-paper-plane mr-2"></i>Submit Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Talent Skills Modal -->
<div class="modal fade" id="talentSkillsModal" tabindex="-1" role="dialog" aria-labelledby="talentSkillsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content rounded-2xl border-0 shadow-2xl">
            <div class="modal-header bg-gradient-to-r from-yellow-500 to-orange-600 text-white rounded-t-2xl border-0 p-6">
                <h5 class="modal-title text-xl font-bold flex items-center" id="talentSkillsModalLabel">
                    <div class="w-8 h-8 bg-white bg-opacity-20 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-star text-white"></i>
                    </div>
                    All Skills
                </h5>
                <button type="button" class="text-white hover:text-gray-200 transition-colors duration-200" data-dismiss="modal" aria-label="Close">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="modal-body p-8">
                <div class="mb-4">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-user-graduate text-yellow-600 text-xl"></i>
                        </div>
                        <div>
                            <h6 class="font-bold text-gray-900" id="skillsModalTalentName">Talent Name</h6>
                            <p class="text-gray-600 text-sm">Complete Skills Overview</p>
                        </div>
                    </div>
                </div>

                <!-- Skills Grid -->
                <div id="allSkillsContainer" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Skills will be populated here by JavaScript -->
                </div>

                <!-- Skills Summary -->
                <div class="mt-6 bg-gray-50 rounded-xl p-4">
                    <h6 class="font-semibold text-gray-900 mb-3">Skills Summary</h6>
                    <div class="grid grid-cols-3 gap-4 text-center">
                        <div class="bg-white rounded-lg p-3">
                            <div class="text-2xl font-bold text-gray-900" id="totalSkillsCount">0</div>
                            <div class="text-xs text-gray-600">Total Skills</div>
                        </div>
                        <div class="bg-white rounded-lg p-3">
                            <div class="text-2xl font-bold text-green-600" id="advancedSkillsCount">0</div>
                            <div class="text-xs text-gray-600">Advanced</div>
                        </div>
                        <div class="bg-white rounded-lg p-3">
                            <div class="text-2xl font-bold text-blue-600" id="intermediateSkillsCount">0</div>
                            <div class="text-xs text-gray-600">Intermediate</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-gray-50 rounded-b-2xl border-0 p-6">
                <button type="button" class="px-6 py-3 bg-gray-500 text-white rounded-xl hover:bg-gray-600 transition-all duration-200 font-medium mr-3" data-dismiss="modal">
                    <i class="fas fa-times mr-2"></i>Close
                </button>
                <button type="button" class="px-6 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-all duration-200 font-medium" onclick="requestTalentFromSkillsModal()">
                    <i class="fas fa-handshake mr-2"></i>Request This Talent
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let currentTalentEmail = '';
let currentRequestTalentId = '';
let currentRequestTalentName = '';
let currentSkillsModalTalentId = '';
let currentSkillsModalTalentIsRedflagged = false;
let currentSkillsModalTalentRedflagReason = '';

function showAllSkills(talentId, talentName, skills, isRedflagged, redflagReason) {
    currentSkillsModalTalentId = talentId;
    currentSkillsModalTalentIsRedflagged = isRedflagged;
    currentSkillsModalTalentRedflagReason = redflagReason;

    // Update modal title
    document.getElementById('skillsModalTalentName').textContent = talentName;
    document.getElementById('talentSkillsModalLabel').innerHTML = `
        <div class="w-8 h-8 bg-white bg-opacity-20 rounded-lg flex items-center justify-center mr-3">
            <i class="fas fa-star text-white"></i>
        </div>
        ${talentName}'s Skills
    `;

    // Populate skills container
    const skillsContainer = document.getElementById('allSkillsContainer');
    skillsContainer.innerHTML = '';

    // Count skills by proficiency
    let totalSkills = skills.length;
    let advancedCount = 0;
    let intermediateCount = 0;
    let beginnerCount = 0;

    skills.forEach(skill => {
        const proficiency = skill.proficiency ? skill.proficiency.toLowerCase() : 'unknown';
        if (proficiency === 'advanced') advancedCount++;
        else if (proficiency === 'intermediate') intermediateCount++;
        else if (proficiency === 'beginner') beginnerCount++;

        // Create skill card
        const skillCard = document.createElement('div');
        skillCard.className = 'bg-white border rounded-lg p-4 hover:shadow-md transition-shadow';

        let proficiencyColorClass = 'bg-gray-100 text-gray-800';
        if (proficiency === 'advanced') proficiencyColorClass = 'bg-green-100 text-green-800';
        else if (proficiency === 'intermediate') proficiencyColorClass = 'bg-blue-100 text-blue-800';
        else if (proficiency === 'beginner') proficiencyColorClass = 'bg-yellow-100 text-yellow-800';

        skillCard.innerHTML = `
            <div class="flex justify-between items-start mb-2">
                <h6 class="font-semibold text-gray-900 text-sm">${skill.skill_name || 'Unknown Skill'}</h6>
                <span class="px-2 py-1 rounded-full text-xs font-medium ${proficiencyColorClass}">
                    ${skill.proficiency ? skill.proficiency.charAt(0).toUpperCase() + skill.proficiency.slice(1) : 'Unknown'}
                </span>
            </div>
            ${skill.completed_date ? `
                <div class="text-xs text-gray-500 flex items-center">
                    <i class="fas fa-calendar-check mr-1"></i>
                    Completed: ${new Date(skill.completed_date).toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric'
                    })}
                </div>
            ` : ''}
        `;

        skillsContainer.appendChild(skillCard);
    });

    // Update summary counts
    document.getElementById('totalSkillsCount').textContent = totalSkills;
    document.getElementById('advancedSkillsCount').textContent = advancedCount;
    document.getElementById('intermediateSkillsCount').textContent = intermediateCount;

    // Show modal
    $('#talentSkillsModal').modal('show');
}

function requestTalentFromSkillsModal() {
    // Close skills modal and open request modal
    $('#talentSkillsModal').modal('hide');

    // Wait for modal to close then open request modal
    setTimeout(() => {
        openRequestModal(
            currentSkillsModalTalentId,
            document.getElementById('skillsModalTalentName').textContent,
            currentSkillsModalTalentIsRedflagged,
            currentSkillsModalTalentRedflagReason
        );
    }, 300);
}

function viewTalentDetails(name, email, profession, address, phone) {
    document.getElementById('modalTalentName').textContent = name;
    document.getElementById('modalTalentEmail').textContent = email;
    document.getElementById('modalTalentProfession').textContent = profession;
    document.getElementById('modalTalentAddress').textContent = address;
    document.getElementById('modalTalentPhone').textContent = phone;
    currentTalentEmail = email;

    $('#talentDetailsModal').modal('show');
}

function proceedWithRequest(talentId, talentName) {
    // Close any existing warning modals
    const warningModal = document.querySelector('.fixed.z-50[data-modal-type="redflag-warning"]');
    if (warningModal) {
        warningModal.remove();
    }

    // Example: Show completed projects in a modal or tooltip (if needed)
    // You can use the data-talent-completed-projects attribute from the checkbox above
    // Example usage:
    // const completedProjects = card.querySelector('.talent-compare-check').dataset.talentCompletedProjects;
    // Display or use as needed in your JS logic

    currentRequestTalentId = talentId;
    currentRequestTalentName = talentName;

    // Reset form
    document.getElementById('talentRequestForm').reset();
    document.getElementById('requestTalentId').value = talentId;

    // Update modal title
    document.getElementById('talentRequestModalLabel').innerHTML =
        `<div class="w-8 h-8 bg-white bg-opacity-20 rounded-lg flex items-center justify-center mr-3"><i class="fas fa-handshake text-white"></i></div>Request Talent: ${talentName}`;

    $('#talentRequestModal').modal('show');
}

function openRequestModal(talentId, talentName, hasRedflags, redflagCount) {
    if (hasRedflags) {
        // Show a warning modal with new redflag information
        const warningModalHtml = `
        <div class="fixed inset-0 z-50 overflow-y-auto" style="background: rgba(0,0,0,0.5);" data-modal-type="redflag-warning">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="bg-white rounded-xl max-w-lg w-full p-6 shadow-2xl">
                    <div class="text-center mb-4">
                        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-flag text-red-600 text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900">Notice: Talent with Project Flags</h3>
                        <p class="text-gray-600 mt-2">This talent has ${redflagCount} red-flagged project(s).</p>
                    </div>

                    <div class="bg-orange-50 border border-orange-200 rounded-lg p-4 mb-4">
                        <h6 class="font-semibold text-orange-900 mb-2">Project History:</h6>
                        <p class="text-sm text-orange-800">${redflagCount} out of their completed projects have been flagged for quality or performance issues.</p>
                        <button onclick="showTalentRedflagHistory('${talentId}', '${talentName}')" class="text-sm text-orange-700 hover:text-orange-900 underline mt-2">
                            <i class="fas fa-history mr-1"></i>View detailed history
                        </button>
                    </div>

                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                        <div class="flex items-start">
                            <i class="fas fa-info-circle text-blue-600 mr-2 mt-0.5"></i>
                            <div class="text-sm text-blue-800">
                                <p class="font-medium mb-1">You can still proceed:</p>
                                <p>This information is for your awareness. You can proceed with the request and discuss expectations during the meeting phase.</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex gap-4">
                        <button onclick="this.closest('.fixed').remove()"
                                class="w-full px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                            Cancel
                        </button>
                        <button onclick="proceedWithRequest('${talentId}', '${talentName.replace(/'/g, "\\'")}')"
                                class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                            Proceed Anyway
                        </button>
                    </div>
                </div>
            </div>
        </div>
        `;
        document.body.insertAdjacentHTML('beforeend', warningModalHtml);
    } else {
        // If not red-flagged, open the request modal directly
        proceedWithRequest(talentId, talentName);
    }
}

function showTimeBlockingConflict(errorData) {
    // Close the request modal first
    $('#talentRequestModal').modal('hide');

    let blockingProjectsHtml = '';
    if (errorData.blocking_projects && errorData.blocking_projects.length > 0) {
        blockingProjectsHtml = '<div class="mt-4"><h6 class="font-semibold text-gray-900 mb-3">Conflicting Projects:</h6><div class="space-y-2">';
        errorData.blocking_projects.forEach(project => {
            blockingProjectsHtml += `
                <div class="bg-red-50 border border-red-200 p-3 rounded-lg">
                    <div class="font-medium text-red-900">${project.title}</div>
                    <div class="text-sm text-red-700">Company: ${project.company}</div>
                    <div class="text-sm text-red-700">Until: ${project.end_date}</div>
                </div>`;
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
                    <div class="text-center mb-4">
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
                        <button onclick="proceedWithRequest('${currentRequestTalentId}', '${currentRequestTalentName.replace(/'/g, "\\'")}')"
                                class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-calendar-plus mr-2"></i>Try Different Duration
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

    const onboardedDate = errorData.existing_project?.onboarded_date || 'Unknown';
    const projectTitle = errorData.existing_project?.title || 'Current Project';

    const modalHtml = `
        <div class="fixed inset-0 z-50 overflow-y-auto" style="background: rgba(0,0,0,0.5);">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="bg-white rounded-xl max-w-lg w-full p-6 shadow-2xl">
                    <div class="text-center mb-4">
                        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-user-check text-blue-600 text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900">Talent Already Onboarded</h3>
                        <p class="text-gray-600 mt-2">${errorData.message}</p>
                    </div>

                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                        <h6 class="font-semibold text-blue-900 mb-2">Current Project Details:</h6>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-blue-700">Project:</span>
                                <span class="font-medium text-blue-900">${projectTitle}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-blue-700">Onboarded:</span>
                                <span class="font-medium text-blue-900">${onboardedDate}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-blue-700">Status:</span>
                                <span class="font-medium text-blue-900">${errorData.existing_project?.status || 'Onboarded'}</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                        <div class="flex items-start">
                            <i class="fas fa-lightbulb text-yellow-600 mr-2 mt-0.5"></i>
                            <div class="text-sm text-yellow-800">
                                <p class="font-medium mb-1">Suggestion:</p>
                                <p>Since this talent is already part of your team, consider reaching out directly or using your internal project management tools for new assignments.</p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <button onclick="this.closest('.fixed').remove()"
                                class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-check mr-2"></i>Got It
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

function contactTalent() {
    if (currentTalentEmail) {
        window.location.href = 'mailto:' + currentTalentEmail;
    }
}

// New Scouting Functions
function toggleScoutingFilters() {
    const filtersPanel = document.getElementById('scoutingFilters');
    const isHidden = filtersPanel.classList.contains('hidden');

    if (isHidden) {
        filtersPanel.classList.remove('hidden');
        filtersPanel.classList.add('animate-slideDown');
    } else {
        filtersPanel.classList.add('hidden');
        filtersPanel.classList.remove('animate-slideDown');
    }
}

function toggleViewMode() {
    const talentCards = document.querySelectorAll('.talent-card');
    const grid = talentCards[0]?.parentElement;

    if (grid.classList.contains('grid-cols-3')) {
        // Switch to list view
        grid.classList.remove('grid-cols-1', 'md:grid-cols-2', 'xl:grid-cols-3');
        grid.classList.add('grid-cols-1');

        talentCards.forEach(card => {
            card.classList.add('flex', 'flex-row');
            card.classList.remove('flex-col');
        });
    } else {
        // Switch back to grid view
        grid.classList.remove('grid-cols-1');
        grid.classList.add('grid-cols-1', 'md:grid-cols-2', 'xl:grid-cols-3');

        talentCards.forEach(card => {
            card.classList.remove('flex', 'flex-row');
            card.classList.add('flex-col');
        });
    }
}

// Add completedItems: array of { title, type, status, date }
function viewScoutingReport(talentId, talentName, metrics, isRedflagged, redflagReason, completedItems = []) {
    // Create a detailed scouting report modal
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4';
    modal.innerHTML = `
        <div class="bg-white rounded-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-2xl font-bold text-gray-900 flex items-center">
                        <i class="fas fa-chart-line mr-3 text-blue-600"></i>
                        Scouting Report: ${talentName}
                    </h2>
                    <button onclick="this.closest('.fixed').remove()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>
            <div class="p-6">
                <div class="text-center py-8">
                    <i class="fas fa-spinner fa-spin text-4xl text-blue-600 mb-4"></i>
                    <p class="text-gray-600">Loading detailed scouting report...</p>
                </div>
            </div>
        </div>
    `;
    document.body.appendChild(modal);

    // Display real metrics data
    setTimeout(() => {
        const content = modal.querySelector('.p-6:last-child');

        // Extract metric values with safe defaults
        const completedCourses = metrics?.progress_tracking?.completed_courses || 0;
        const totalCertificates = metrics?.certifications?.total_certificates || 0;
        const quizAverage = metrics?.quiz_performance?.average_score || 0;
        const completionRate = metrics?.progress_tracking?.completion_rate || 0;
        const learningVelocity = metrics?.learning_velocity?.score || 0;
        const consistency = metrics?.consistency?.score || 0;
        const adaptability = metrics?.adaptability?.score || 0;

        // Helper function to get performance level and color
        const getPerformanceLevel = (score) => {
            if (score >= 80) return { level: 'Excellent', color: 'text-green-600' };
            if (score >= 60) return { level: 'Good', color: 'text-blue-600' };
            if (score >= 40) return { level: 'Average', color: 'text-orange-600' };
            return { level: 'Needs Improvement', color: 'text-red-600' };
        };

        const velocityLevel = getPerformanceLevel(learningVelocity);
        const consistencyLevel = getPerformanceLevel(consistency);
        const adaptabilityLevel = getPerformanceLevel(adaptability);

        // Render completed projects/requests section
        let completedHtml = '';
        if (Array.isArray(completedItems) && completedItems.length > 0) {
            completedHtml = `
                <div class="bg-purple-50 p-4 rounded-xl">
                    <h3 class="font-semibold text-purple-900 mb-3">Completed Projects / Requests</h3>
                    <ul class="space-y-3">
                        ${completedItems.map(item => `
                            <li class="bg-white p-3 rounded-lg border border-gray-200 shadow-sm">
                                <div class="flex flex-col space-y-2">
                                    <div class="flex items-center justify-between">
                                        <span class="font-medium text-gray-800">${item.title || item.name || 'Untitled'}</span>
                                        ${item.is_redflagged ? '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800"><i class="fas fa-flag mr-1"></i>Red Flagged</span>' : ''}
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-2 text-xs text-gray-600">
                                        <div><span class="font-medium">Duration:</span> ${item.duration || 'Not specified'}</div>
                                        <div><span class="font-medium">Industry:</span> ${item.industry || 'Not specified'}</div>
                                        <div><span class="font-medium">Completed:</span> ${item.date || 'Unknown'}</div>
                                    </div>
                                </div>
                            </li>
                        `).join('')}
                    </ul>
                </div>
            `;
        } else {
            completedHtml = `
                <div class="bg-purple-50 p-4 rounded-xl">
                    <h3 class="font-semibold text-purple-900 mb-3">Completed Projects / Requests</h3>
                    <p class="text-gray-500">No completed projects or requests found for this talent.</p>
                </div>
            `;
        }

        content.innerHTML = `
            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-blue-50 p-4 rounded-xl">
                        <h3 class="font-semibold text-blue-900 mb-3">Learning Performance</h3>
                        <div class="space-y-2">
                            <div class="flex justify-between"><span>Learning Velocity:</span><span class="font-semibold ${velocityLevel.color}">${velocityLevel.level} (${Math.round(learningVelocity)}%)</span></div>
                            <div class="flex justify-between"><span>Performance Consistency:</span><span class="font-semibold ${consistencyLevel.color}">${consistencyLevel.level} (${Math.round(consistency)}%)</span></div>
                            <div class="flex justify-between"><span>Skill Adaptability:</span><span class="font-semibold ${adaptabilityLevel.color}">${adaptabilityLevel.level} (${Math.round(adaptability)}%)</span></div>
                        </div>
                    </div>
                    <div class="bg-green-50 p-4 rounded-xl">
                        <h3 class="font-semibold text-green-900 mb-3">Achievement Metrics</h3>
                        <div class="space-y-2">
                            <div class="flex justify-between"><span>Courses Completed:</span><span class="font-semibold">${completedCourses} completed</span></div>
                            <div class="flex justify-between"><span>Certifications:</span><span class="font-semibold">${totalCertificates} earned</span></div>
                            <div class="flex justify-between"><span>Quiz Performance:</span><span class="font-semibold">${Math.round(quizAverage)}% avg</span></div>
                            <div class="flex justify-between"><span>Completion Rate:</span><span class="font-semibold">${Math.round(completionRate)}%</span></div>
                        </div>
                    </div>
                </div>

                ${completedHtml}

                <div class="bg-gray-50 p-4 rounded-xl">
                    <h3 class="font-semibold text-gray-900 mb-3">Recommendation</h3>
                    <p class="text-gray-700">
                        ${completedCourses > 0 || totalCertificates > 0 ?
                            `This talent has completed ${completedCourses} courses and earned ${totalCertificates} certificates with an average quiz performance of ${Math.round(quizAverage)}%. ${
                                consistency >= 70 ? 'Shows excellent learning consistency and' : 'Has potential for growth with'
                            } ${
                                learningVelocity >= 70 ? 'strong learning velocity.' : 'room for improvement in learning pace.'
                            }` :
                            'This talent is new to the platform. Consider their background and potential for growth.'
                        }
                    </p>
                </div>

                <div class="flex gap-4 pt-4">
                    <button onclick="openRequestModal('${talentId}', '${talentName.replace(/'/g, "\\'")}', ${isRedflagged}, '${(redflagReason || '').replace(/'/g, "\\'")})" class="flex-1 px-6 py-3 bg-green-600 text-white rounded-xl hover:bg-green-700 transition-colors font-semibold">
                        <i class="fas fa-handshake mr-2"></i>Request This Talent
                    </button>
                    <button onclick="this.closest('.fixed').remove()" class="px-6 py-3 bg-gray-600 text-white rounded-xl hover:bg-gray-700 transition-colors font-semibold">
                        Close Report
                    </button>
                </div>
            </div>
        `;
    }, 300);
}

function viewRequestDetails(requestId) {
    // Create a modal to show request details
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4';
    modal.innerHTML = `
        <div class="bg-white rounded-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-2xl font-bold text-gray-900 flex items-center">
                        <i class="fas fa-clipboard-list mr-3 text-blue-600"></i>
                        Request Details
                    </h2>
                    <button onclick="this.closest('.fixed').remove()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>
            <div class="p-6">
                <div class="text-center py-8">
                    <i class="fas fa-spinner fa-spin text-4xl text-blue-600 mb-4"></i>
                    <p class="text-gray-600">Loading request details...</p>
                </div>
            </div>
        </div>
    `;
    document.body.appendChild(modal);

    // Fetch request details from server
    fetch(`/recruiter/request-details/${requestId}`, {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const request = data.request;
            const content = modal.querySelector('.p-6:last-child');
            content.innerHTML = `
                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-blue-50 p-4 rounded-xl">
                            <h3 class="font-semibold text-blue-900 mb-3">Talent Information</h3>
                            <div class="space-y-2">
                                <div class="flex justify-between"><span>Name:</span><span class="font-semibold">${request.talent_name}</span></div>
                                <div class="flex justify-between"><span>Email:</span><span class="font-semibold">${request.talent_email}</span></div>
                                <div class="flex justify-between"><span>Position:</span><span class="font-semibold">${request.talent_position || 'Not specified'}</span></div>
                            </div>
                        </div>
                        <div class="bg-green-50 p-4 rounded-xl">
                            <h3 class="font-semibold text-green-900 mb-3">Request Status</h3>
                            <div class="space-y-2">
                                <div class="flex justify-between"><span>Status:</span><span class="font-semibold">${request.status}</span></div>
                                <div class="flex justify-between"><span>Requested:</span><span class="font-semibold">${request.created_at}</span></div>
                                <div class="flex justify-between"><span>Updated:</span><span class="font-semibold">${request.updated_at}</span></div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-xl">
                        <h3 class="font-semibold text-gray-900 mb-3">Project Details</h3>
                        <div class="space-y-2">
                            <div><span class="font-medium">Title:</span> ${request.project_title}</div>
                            <div><span class="font-medium">Description:</span> ${request.project_description || 'No description provided'}</div>
                            <div><span class="font-medium">Budget:</span> ${request.budget || 'Not specified'}</div>
                            <div><span class="font-medium">Duration:</span> ${request.project_duration || 'Not specified'}</div>
                        </div>
                    </div>

                    <div class="flex gap-4 pt-4">
                        <a href="mailto:${request.talent_email}" class="flex-1 px-6 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors font-semibold text-center">
                            <i class="fas fa-envelope mr-2"></i>Contact Talent
                        </a>
                        <button onclick="this.closest('.fixed').remove()" class="px-6 py-3 bg-gray-600 text-white rounded-xl hover:bg-gray-700 transition-colors font-semibold">
                            Close
                        </button>
                    </div>
                </div>
            `;
        } else {
            const content = modal.querySelector('.p-6:last-child');
            content.innerHTML = `
                <div class="text-center py-8">
                    <i class="fas fa-exclamation-triangle text-4xl text-red-500 mb-4"></i>
                    <p class="text-gray-600">Error loading request details: ${data.message || 'Unknown error'}</p>
                    <button onclick="this.closest('.fixed').remove()" class="mt-4 px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                        Close
                    </button>
                </div>
            `;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        const content = modal.querySelector('.p-6:last-child');
        content.innerHTML = `
            <div class="text-center py-8">
                <i class="fas fa-exclamation-triangle text-4xl text-red-500 mb-4"></i>
                <p class="text-gray-600">Failed to load request details. Please try again.</p>
                <button onclick="this.closest('.fixed').remove()" class="mt-4 px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    Close
                </button>
            </div>
        `;
    });
}

// Simplified Filtering System
let filterPresets = JSON.parse(localStorage.getItem('talentFilterPresets') || '[]');

// Initialize filters when page loads
document.addEventListener('DOMContentLoaded', function() {
    initializeSimplifiedFilters();
    setupFilterEventListeners();
});

function initializeSimplifiedFilters() {
    // Update filter counts
    updateActiveFiltersCount();
    updateFilteredResultsCount();
}

function setupFilterEventListeners() {
    // Add event listeners for all filter dropdowns
    const filterIds = ['certificatesFilter', 'quizFilter', 'availabilityFilter', 'redflagsFilter'];
    
    filterIds.forEach(filterId => {
        const element = document.getElementById(filterId);
        if (element) {
            element.addEventListener('change', function() {
                updateActiveFiltersCount();
            });
        }
    });
}

function updateActiveFiltersCount() {
    let count = 0;
    const countElement = document.getElementById('activeFiltersCount');
    
    // Check simplified filters
    const filters = ['certificatesFilter', 'quizFilter', 'availabilityFilter', 'redflagsFilter'];
    filters.forEach(filterId => {
        const element = document.getElementById(filterId);
        if (element && element.value !== '') count++;
    });
    
    if (count > 0) {
        countElement.textContent = `${count} active`;
        countElement.classList.remove('hidden');
    } else {
        countElement.classList.add('hidden');
    }
}

function updateFilteredResultsCount() {
    const totalCards = document.querySelectorAll('.talent-card').length;
    const visibleCards = document.querySelectorAll('.talent-card:not([style*="display: none"]):not(.hidden)').length;
    const countElement = document.getElementById('filteredResultsCount');
    
    if (countElement) {
        countElement.textContent = `${visibleCards} of ${totalCards} talents`;
    }
}

function applyAdvancedFilters() {
    const button = event.target;
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Filtering...';
    button.disabled = true;
    
    setTimeout(() => {
        performAdvancedFiltering();
        button.innerHTML = originalText;
        button.disabled = false;
        updateFilteredResultsCount();
        showFilterResultsSummary();
    }, 300);
}

function performAdvancedFiltering() {
    const talentCards = document.querySelectorAll('.talent-card');
    let visibleCount = 0;
    
    // Get filter values
    const certificatesFilter = document.getElementById('certificatesFilter').value;
    const quizFilter = document.getElementById('quizFilter').value;
    const availabilityFilter = document.getElementById('availabilityFilter').value;
    const redflagsFilter = document.getElementById('redflagsFilter').value;
    
    talentCards.forEach(card => {
        let shouldShow = true;
        
        // Certificates filter
        if (certificatesFilter && shouldShow) {
            const certificatesCount = parseInt(card.getAttribute('data-talent-certificates') || 0);
            if (!matchesRangeFilter(certificatesCount, certificatesFilter)) {
                shouldShow = false;
            }
        }
        
        // Quiz performance filter
        if (quizFilter && shouldShow) {
            const quizAvg = parseFloat(card.getAttribute('data-talent-quiz-avg') || 0);
            if (!matchesScoreRange(quizAvg, quizFilter)) {
                shouldShow = false;
            }
        }
        
        // Availability filter
        if (availabilityFilter && shouldShow) {
            const availability = card.getAttribute('data-talent-availability') || '';
            if (availability !== availabilityFilter) {
                shouldShow = false;
            }
        }
        
        // Red flags filter
        if (redflagsFilter && shouldShow) {
            const hasRedflags = card.classList.contains('has-redflags');
            if ((redflagsFilter === 'has-redflags' && !hasRedflags) || 
                (redflagsFilter === 'no-redflags' && hasRedflags)) {
                shouldShow = false;
            }
        }
        
        // Show/hide card
        if (shouldShow) {
            card.style.display = 'block';
            card.classList.remove('hidden');
            visibleCount++;
        } else {
            card.style.display = 'none';
            card.classList.add('hidden');
        }
    });
    
    return visibleCount;
}

function matchesRangeFilter(value, filter) {
    switch(filter) {
        case '0': return value === 0;
        case '1-2': return value >= 1 && value <= 2;
        case '1-3': return value >= 1 && value <= 3;
        case '3-5': return value >= 3 && value <= 5;
        case '4-7': return value >= 4 && value <= 7;
        case '6-10': return value >= 6 && value <= 10;
        case '8-15': return value >= 8 && value <= 15;
        case '10+': return value >= 10;
        case '15+': return value >= 15;
        default: return true;
    }
}

function matchesScoreRange(score, filter) {
    switch(filter) {
        case '90-100': return score >= 90 && score <= 100;
        case '80-89': return score >= 80 && score < 90;
        case '70-79': return score >= 70 && score < 80;
        case '60-69': return score >= 60 && score < 70;
        case '0-59': return score >= 0 && score < 60;
        default: return true;
    }
}

function showFilterResultsSummary() {
    const summary = document.getElementById('filterResultsSummary');
    const summaryText = document.getElementById('filterSummaryText');
    const visibleCount = document.querySelectorAll('.talent-card:not([style*="display: none"]):not(.hidden)').length;
    const totalCount = document.querySelectorAll('.talent-card').length;
    
    if (hasActiveFilters()) {
        summaryText.textContent = `Showing ${visibleCount} of ${totalCount} talents based on your filter criteria`;
        summary.classList.remove('hidden');
    } else {
        summary.classList.add('hidden');
    }
}

function hasActiveFilters() {
    const filters = ['certificatesFilter', 'quizFilter', 'availabilityFilter', 'redflagsFilter'];
    return filters.some(filterId => {
        const element = document.getElementById(filterId);
        return element && element.value !== '';
    });
}

function resetAllFilters() {
    // Reset dropdowns
    const selects = document.querySelectorAll('#scoutingFilters select');
    selects.forEach(select => select.value = '');
    
    // Show all cards
    const talentCards = document.querySelectorAll('.talent-card');
    talentCards.forEach(card => {
        card.style.display = 'block';
        card.classList.remove('hidden');
    });
    
    // Update UI
    updateActiveFiltersCount();
    updateFilteredResultsCount();
    document.getElementById('filterResultsSummary').classList.add('hidden');
}

function clearFilterResults() {
    resetAllFilters();
}

function saveFilterPreset() {
    const presetName = prompt('Enter a name for this filter preset:');
    if (!presetName) return;
    
    const preset = {
        name: presetName,
        timestamp: new Date().toISOString(),
        filters: {
            certificates: document.getElementById('certificatesFilter').value,
            quiz: document.getElementById('quizFilter').value,
            availability: document.getElementById('availabilityFilter').value,
            redflags: document.getElementById('redflagsFilter').value
        }
    };
    
    filterPresets.push(preset);
    localStorage.setItem('talentFilterPresets', JSON.stringify(filterPresets));
    
    alert(`Filter preset "${presetName}" saved successfully!`);
}

// Add event listeners for real-time filtering
document.addEventListener('DOMContentLoaded', function() {
    const filterElements = ['certificatesFilter', 'quizFilter', 'availabilityFilter', 'redflagsFilter'];
    filterElements.forEach(id => {
        const element = document.getElementById(id);
        if (element) {
            element.addEventListener('change', updateActiveFiltersCount);
        }
    });
});

// Search Functions
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
        
        if (searchTerm === '' || nameMatch || skillsMatch) {
            card.style.display = 'block';
            card.classList.remove('hidden');
            visibleCount++;
        } else {
            card.style.display = 'none';
            card.classList.add('hidden');
        }
    });
    
    // Update search results info
    if (searchTerm.length > 0) {
        searchResultsInfo.classList.remove('hidden');
        if (visibleCount === 0) {
            searchResultsText.textContent = `No talents found matching "${searchInput.value}"`;
            searchResultsInfo.className = 'mb-4 p-3 bg-red-50 border border-red-200 rounded-lg';
            searchResultsText.className = 'text-red-800 text-sm';
        } else {
            searchResultsText.textContent = `Found ${visibleCount} talent${visibleCount !== 1 ? 's' : ''} matching "${searchInput.value}"`;
            searchResultsInfo.className = 'mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg';
            searchResultsText.className = 'text-blue-800 text-sm';
        }
    } else {
        searchResultsInfo.classList.add('hidden');
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
        card.style.display = 'block';
        card.classList.remove('hidden');
    });
    
    // Focus back to search input
    searchInput.focus();
}

function refreshTalents() {
    // Add smooth loading animation
    const refreshButton = document.querySelector('[onclick="refreshTalents()"]');
    const originalHTML = refreshButton.innerHTML;
    refreshButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Refreshing...';
    refreshButton.disabled = true;

    setTimeout(() => {
        location.reload();
    }, 500);
}

// Ensure modal close functionality works
$(document).ready(function() {
    // Handle modal close buttons
    $('.modal .close, .modal [data-dismiss="modal"]').on('click', function() {
        $(this).closest('.modal').modal('hide');
    });

    // Add smooth scroll behavior
    $('html').css('scroll-behavior', 'smooth');

    // Add hover effects for talent cards
    $('.talent-card').hover(
        function() {
            $(this).addClass('transform scale-105');
        },
        function() {
            $(this).removeClass('transform scale-105');
        }
    );

    // Add click handlers for talent cards in compare mode
    $('.talent-card').on('click', function(e) {
        // Only handle clicks when in compare mode
        if (!isCompareMode) return;

        // Don't trigger if clicking on buttons, links, or other interactive elements
        if ($(e.target).closest('button, a, input, .modal').length > 0) return;

        // Find the checkbox within this card
        const checkbox = $(this).find('.talent-compare-check')[0];
        if (checkbox) {
            checkbox.checked = !checkbox.checked;
            updateCompareSelection();

            // Visual feedback
            if (checkbox.checked) {
                $(this).addClass('ring-2 ring-emerald-500 bg-emerald-50');
            } else {
                $(this).removeClass('ring-2 ring-emerald-500 bg-emerald-50');
            }
        }
    });
});

// ===== TALENT COMPARISON FUNCTIONALITY =====
let isCompareMode = false;
let selectedTalents = [];

function toggleCompareMode() {
    isCompareMode = !isCompareMode;
    const checkboxes = document.querySelectorAll('.compare-checkbox');
    const compareBtn = document.getElementById('compareModeBtn');
    const comparisonPanel = document.getElementById('comparisonPanel');

    if (isCompareMode) {
        // Enable compare mode
        checkboxes.forEach(cb => cb.classList.remove('hidden'));
        if (compareBtn) {
            compareBtn.innerHTML = '<i class="fas fa-times mr-2"></i>Exit Compare';
            compareBtn.classList.add('bg-red-600', 'hover:bg-red-700', 'text-white');
            compareBtn.classList.remove('bg-white/20', 'hover:bg-white/30');
        }

        // Show comparison panel
        if (comparisonPanel) {
            comparisonPanel.style.display = 'block';
            setTimeout(() => {
                comparisonPanel.classList.remove('translate-y-full');
            }, 10);
        }

        // Add margin to body to account for panel
        document.body.style.marginBottom = '120px';

        // Add visual indicator that cards are clickable
        document.querySelectorAll('.talent-card').forEach(card => {
            card.style.cursor = 'pointer';
            card.classList.add('hover:ring-2', 'hover:ring-emerald-300', 'transition-all');
        });
    } else {
        // Disable compare mode
        checkboxes.forEach(cb => {
            cb.classList.add('hidden');
            const checkbox = cb.querySelector('input');
            if (checkbox) checkbox.checked = false;
        });

        if (compareBtn) {
            compareBtn.innerHTML = '<i class="fas fa-balance-scale mr-2"></i>Compare';
            compareBtn.classList.remove('bg-red-600', 'hover:bg-red-700', 'text-white');
            compareBtn.classList.add('bg-white/20', 'hover:bg-white/30');
        }

        // Hide comparison panel
        if (comparisonPanel) {
            comparisonPanel.classList.add('translate-y-full');
            setTimeout(() => {
                comparisonPanel.style.display = 'none';
            }, 300);
        }

        // Reset margin
        document.body.style.marginBottom = '0';

        // Remove visual indicators and selected states
        document.querySelectorAll('.talent-card').forEach(card => {
            card.style.cursor = 'default';
            card.classList.remove('hover:ring-2', 'hover:ring-emerald-300', 'transition-all', 'ring-2', 'ring-emerald-500', 'bg-emerald-50');
        });

        // Clear selection
        selectedTalents = [];
        updateCompareSelection();
    }

    // Make sure the comparison panel visibility is updated
    if (comparisonPanel) {
        if (isCompareMode && selectedTalents.length > 0) {
            comparisonPanel.style.display = 'block';
            comparisonPanel.classList.remove('translate-y-full');
        } else if (!isCompareMode) {
            comparisonPanel.classList.add('translate-y-full');
            setTimeout(() => {
                comparisonPanel.style.display = 'none';
            }, 300);
        }
    }
}

function updateCompareSelection() {
    const checkedBoxes = document.querySelectorAll('.talent-compare-check:checked');

    selectedTalents = Array.from(checkedBoxes).map(cb => {
        let skills = [];
        try {
            // Safely parse JSON with error handling
            const skillsData = cb.dataset.talentSkills;
            if (skillsData && skillsData.trim() !== '' && skillsData !== 'null' && skillsData !== 'undefined') {
                skills = JSON.parse(skillsData);
                // Ensure skills is an array
                if (!Array.isArray(skills)) {
                    console.warn('Skills data is not an array:', skills);
                    skills = [];
                }
            }
        } catch (error) {
            console.warn('Failed to parse talent skills JSON:', error);
            console.warn('Raw data:', cb.dataset.talentSkills);
            skills = [];
        }

        return {
            id: cb.dataset.talentId,
            name: cb.dataset.talentName,
            email: cb.dataset.talentEmail,
            position: cb.dataset.talentPosition,
            score: cb.dataset.talentScore,
            courses: cb.dataset.talentCourses,
            certificates: cb.dataset.talentCertificates,
            quizAvg: cb.dataset.talentQuizAvg,
            skills: skills,
            redflagged: cb.dataset.talentRedflagged === 'true',
            redflagReason: cb.dataset.talentRedflagReason
        };
    });

    // Update visual state of all talent cards
    document.querySelectorAll('.talent-card').forEach(card => {
        const checkbox = card.querySelector('.talent-compare-check');
        if (checkbox && checkbox.checked) {
            card.classList.add('ring-2', 'ring-emerald-500', 'bg-emerald-50');
        } else {
            card.classList.remove('ring-2', 'ring-emerald-500', 'bg-emerald-50');
        }
    });

    // Update counter
    const selectedCount = document.getElementById('selectedCount');
    if (selectedCount) {
        selectedCount.textContent = `${selectedTalents.length} selected`;
    }

    // Update compare button state
    const compareBtn = document.getElementById('compareBtn');
    if (compareBtn) {
        compareBtn.disabled = selectedTalents.length < 2;
    }

    // Update preview
    updateSelectedTalentsPreview();
}

function updateSelectedTalentsPreview() {
    const preview = document.getElementById('selectedTalentsPreview');
    preview.innerHTML = '';

    selectedTalents.forEach(talent => {
        const talentCard = document.createElement('div');
        talentCard.className = 'flex-shrink-0 bg-gray-50 rounded-lg p-3 min-w-48';
        talentCard.innerHTML = `
            <div class="flex items-center">
                <div class="w-10 h-10 bg-emerald-500 rounded-full flex items-center justify-center mr-3">
                    <i class="fas fa-user text-white"></i>
                </div>
                <div>
                    <div class="font-medium text-sm">${talent.name}</div>
                    <div class="text-xs text-gray-600">${talent.position}</div>
                </div>
            </div>
        `;
        preview.appendChild(talentCard);
    });
}

function clearComparison() {
    // Uncheck all checkboxes
    document.querySelectorAll('.talent-compare-check').forEach(cb => {
        cb.checked = false;
    });

    // Clear array and update UI
    selectedTalents = [];
    updateCompareSelection();
}

function viewComparison() {
    if (selectedTalents.length < 2) {
        alert('Please select at least 2 talents to compare.');
        return;
    }

    const modal = document.getElementById('talentComparisonModal');
    const content = document.getElementById('comparisonContent');

    // Generate comparison table
    content.innerHTML = generateComparisonTable();

    // Show modal
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeComparisonModal() {
    const modal = document.getElementById('talentComparisonModal');
    modal.style.display = 'none';
    document.body.style.overflow = 'auto';
}

function generateComparisonTable() {
    if (selectedTalents.length === 0) return '<p>Tidak ada talenta yang dipilih untuk dibandingkan.</p>';

    return `
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="border border-gray-200 p-4 text-left font-semibold">Kriteria</th>
                        ${selectedTalents.map(talent => `
                            <th class="border border-gray-200 p-4 text-center font-semibold min-w-48">
                                <div class="flex flex-col items-center">
                                    <div class="w-12 h-12 bg-emerald-500 rounded-full flex items-center justify-center mb-2">
                                        <i class="fas fa-user text-white"></i>
                                    </div>
                                    <div class="font-bold">${talent.name}</div>
                                    <div class="text-sm text-gray-600">${talent.position}</div>
                                </div>
                            </th>
                        `).join('')}
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="border border-gray-200 p-4 font-medium bg-gray-50">Skor Keseluruhan</td>
                        ${selectedTalents.map(talent => `
                            <td class="border border-gray-200 p-4 text-center">
                                <span class="inline-flex items-center px-3 py-1 bg-emerald-100 text-emerald-800 rounded-full font-bold">
                                    ${talent.score}/100
                                </span>
                            </td>
                        `).join('')}
                    </tr>
                    <tr>
                        <td class="border border-gray-200 p-4 font-medium bg-gray-50">Kursus Selesai</td>
                        ${selectedTalents.map(talent => `
                            <td class="border border-gray-200 p-4 text-center font-semibold">${talent.courses}</td>
                        `).join('')}
                    </tr>
                    <tr>
                        <td class="border border-gray-200 p-4 font-medium bg-gray-50">Sertifikat Diperoleh</td>
                        ${selectedTalents.map(talent => `
                            <td class="border border-gray-200 p-4 text-center font-semibold">${talent.certificates}</td>
                        `).join('')}
                    </tr>
                    <tr>
                        <td class="border border-gray-200 p-4 font-medium bg-gray-50">Rata-rata Kuis</td>
                        ${selectedTalents.map(talent => `
                            <td class="border border-gray-200 p-4 text-center font-semibold">${talent.quizAvg}%</td>
                        `).join('')}
                    </tr>
                    <tr>
                        <td class="border border-gray-200 p-4 font-medium bg-gray-50">Skills</td>
                        ${selectedTalents.map(talent => `
                            <td class="border border-gray-200 p-4">
                                <div class="space-y-1">
                                    ${talent.skills && talent.skills.length > 0 ?
                                        talent.skills.slice(0, 4).map(skill => `
                                            <div class="flex justify-between items-center text-xs">
                                                <span class="font-medium text-gray-700">${skill.skill_name || 'Unknown'}</span>
                                                <span class="px-2 py-1 rounded-full text-xs font-medium
                                                    ${skill.proficiency && skill.proficiency.toLowerCase() === 'advanced' ? 'bg-green-100 text-green-800' :
                                                      skill.proficiency && skill.proficiency.toLowerCase() === 'intermediate' ? 'bg-blue-100 text-blue-800' :
                                                      skill.proficiency && skill.proficiency.toLowerCase() === 'beginner' ? 'bg-yellow-100 text-yellow-800' :
                                                      'bg-gray-100 text-gray-800'}">
                                                    ${skill.proficiency ? skill.proficiency.charAt(0).toUpperCase() + skill.proficiency.slice(1) : 'Unknown'}
                                                </span>
                                            </div>
                                        `).join('') +
                                        (talent.skills.length > 4 ? `<div class="text-xs text-gray-500 text-center mt-1">+${talent.skills.length - 4} lainnya</div>` : '')
                                        : '<div class="text-xs text-gray-500 text-center">Belum ada keahlian yang diperoleh</div>'
                                    }
                                </div>
                            </td>
                        `).join('')}
                    </tr>
                    <tr>
                        <td class="border border-gray-200 p-4 font-medium bg-gray-50">Kontak</td>
                        ${selectedTalents.map(talent => `
                            <td class="border border-gray-200 p-4 text-center">
                                <a href="mailto:${talent.email}" class="inline-flex items-center px-3 py-1 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm">
                                    <i class="fas fa-envelope mr-1"></i>Email
                                </a>
                            </td>
                        `).join('')}
                    </tr>
                    <tr>
                        <td class="border border-gray-200 p-4 font-medium bg-gray-50">Aksi</td>
                        ${selectedTalents.map(talent => `
                            <td class="border border-gray-200 p-4 text-center">
                                <div class="flex flex-col gap-2">
                                    <button onclick="openRequestModal('${talent.id}', '${talent.name.replace(/'/g, "\\'")}', ${talent.redflagged}, '${(talent.redflagReason || '').replace(/'/g, "\\'")}')"
                                            class="px-3 py-1 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors text-sm">
                                        <i class="fas fa-handshake mr-1"></i>Minta
                                    </button>
                                    <button onclick="viewScoutingReport('${talent.id}', '${talent.name.replace(/'/g, "\\'")}', {
                                        progress_tracking: { completed_courses: ${talent.courses} },
                                        certifications: { total_certificates: ${talent.certificates} },
                                        quiz_performance: { average_score: ${talent.quizAvg} },
                                        completion_rate: { rate: 0 },
                                        learning_velocity: { score: 0 },
                                        consistency: { score: 0 },
                                        adaptability: { score: 0 }
                                    })"
                                            class="px-3 py-1 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm">
                                        <i class="fas fa-chart-line mr-1"></i>Report
                                    </button>
                                </div>
                            </td>
                        `).join('')}
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="mt-6 text-center">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 max-w-4xl mx-auto">
                <div class="bg-blue-50 p-4 rounded-lg">
                    <h4 class="font-semibold text-blue-900 mb-2">Best Overall Score</h4>
                    <p class="text-blue-700">${getBestTalent('score')}</p>
                </div>
                <div class="bg-green-50 p-4 rounded-lg">
                    <h4 class="font-semibold text-green-900 mb-2">Most Experienced</h4>
                    <p class="text-green-700">${getBestTalent('courses')}</p>
                </div>
                <div class="bg-purple-50 p-4 rounded-lg">
                    <h4 class="font-semibold text-purple-900 mb-2">Best Quiz Performance</h4>
                    <p class="text-purple-700">${getBestTalent('quizAvg')}</p>
                </div>
            </div>
        </div>
    `;
}

function getBestTalent(criteria) {
    if (selectedTalents.length === 0) return 'No data';

    let best = selectedTalents[0];
    let value = parseFloat(best[criteria]);

    selectedTalents.forEach(talent => {
        const talentValue = parseFloat(talent[criteria]);
        if (talentValue > value) {
            best = talent;
            value = talentValue;
        }
    });

    return best.name;
}

// Handle talent request form submission
document.addEventListener('DOMContentLoaded', function() {
    console.log('Dashboard JavaScript loaded successfully');

    // Initialize export dropdown
    initializeExportDropdown();

    // Add event delegation for talent card clicks in compare mode
    document.addEventListener('click', function(e) {
        // Handle talent card clicks for comparison
        if (isCompareMode && e.target.closest('.talent-card')) {
            const card = e.target.closest('.talent-card');
            const checkbox = card.querySelector('.talent-compare-check');

            // Don't trigger card click if clicking on the checkbox itself
            if (!e.target.matches('.talent-compare-check') && checkbox) {
                checkbox.checked = !checkbox.checked;
                // Trigger the change event to update comparison
                checkbox.dispatchEvent(new Event('change'));
            }
        }
    });

    // Handle checkbox changes for comparison
    document.addEventListener('change', function(e) {
        if (e.target.matches('.talent-compare-check')) {
            console.log('Checkbox changed, updating comparison');
            updateCompareSelection();
        }
    });

    // Verify that all required functions exist
    const requiredFunctions = ['toggleScoutingFilters', 'toggleCompareMode', 'refreshTalents', 'updateCompareSelection'];
    requiredFunctions.forEach(funcName => {
        if (typeof window[funcName] === 'function') {
            console.log(`✓ Function ${funcName} is available`);
        } else {
            console.error(`✗ Function ${funcName} is missing`);
        }
    });
});

document.getElementById('talentRequestForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const submitButton = this.querySelector('button[type="submit"]');
    const originalText = submitButton.innerHTML;

    // Show loading state
    submitButton.disabled = true;
    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Submitting Request...';

    fetch('{{ route("recruiter.submit_talent_request") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(data => {
                throw { status: response.status, data: data };
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Show success message with timeline info if available
            let successMessage = 'Success! Your talent request has been submitted.';
            if (data.project_timeline) {
                successMessage += ` Project scheduled: ${data.project_timeline.start_date} - ${data.project_timeline.end_date}`;
            }

            const successAlert = document.createElement('div');
            successAlert.className = 'fixed top-4 right-4 bg-green-100 border border-green-400 text-green-700 px-6 py-4 rounded-xl shadow-lg z-50 max-w-md';
            successAlert.innerHTML = '<i class="fas fa-check-circle mr-2"></i>' + successMessage;
            document.body.appendChild(successAlert);

            // Close modal and refresh page
            $('#talentRequestModal').modal('hide');
            setTimeout(() => {
                successAlert.remove();
                location.reload();
            }, 3000);
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
            // Regular error message
            const errorAlert = document.createElement('div');
            errorAlert.className = 'fixed top-4 right-4 bg-red-100 border border-red-400 text-red-700 px-6 py-4 rounded-xl shadow-lg z-50 max-w-md';
            errorAlert.innerHTML = '<i class="fas fa-exclamation-circle mr-2"></i>Error: ' +
                (error.data?.message || error.data?.error || 'Failed to submit request. Please try again.');
            document.body.appendChild(errorAlert);

            setTimeout(() => {
                errorAlert.remove();
            }, 5000);
        }
    })
    .finally(() => {
        // Reset button state
        submitButton.disabled = false;
        submitButton.innerHTML = originalText;
    });
});

// Project-based Redflag Modal Functions
function showTalentRedflagHistory(talentId, talentName) {
    // Show loading
    showNotification('Memuat riwayat proyek...', 'info');

    // Fetch redflag history via AJAX
    fetch(`/recruiter/talent/${talentId}/redflag-history`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayRedflagHistoryModal(talentName, data.redflag_summary, data.redflagged_projects);
            } else {
                showNotification('Gagal memuat riwayat proyek', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Terjadi kesalahan saat memuat riwayat proyek', 'error');
        });
}

function displayRedflagHistoryModal(talentName, summary, redflaggedProjects) {
    const modalHtml = `
    <div id="redflagHistoryModal" class="fixed inset-0 z-50 overflow-y-auto" style="background: rgba(0,0,0,0.5);">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="bg-white rounded-xl max-w-2xl w-full max-h-[90vh] overflow-hidden shadow-2xl">
                <div class="bg-gradient-to-r from-orange-500 to-red-500 px-6 py-4 text-white">
                    <h3 class="text-xl font-bold flex items-center">
                        <i class="fas fa-flag mr-2"></i>Project History for ${talentName}
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

// Legacy function - kept for backward compatibility but will be deprecated
function showRedFlagDetails(talentName, reason, flagDate) {
    showNotification('Fitur ini telah diperbarui. Silakan gunakan tombol "Lihat Riwayat" untuk informasi proyek yang detail.', 'info');
}

function contactTalentAdmin() {
    // Create contact information alert
    const message = `Untuk menanyakan status red flag talenta ini, silakan hubungi:\n\n` +
                   `Administrasi Talenta\n` +
                   `Email: talent-admin@webpelatihan.com\n` +
                   `Telepon: +62-XXX-XXXX-XXXX\n\n` +
                   `Mohon sertakan nama talenta dalam pertanyaan Anda.`;

    if (confirm(message + '\n\nApakah Anda ingin menyalin alamat email?')) {
        // Copy email to clipboard if supported
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText('talent-admin@webpelatihan.com').then(() => {
                showNotification('Alamat email berhasil disalin ke clipboard!', 'success');
            }).catch(() => {
                showNotification('Silakan salin manual: talent-admin@webpelatihan.com', 'info');
            });
        } else {
            showNotification('Silakan salin manual: talent-admin@webpelatihan.com', 'info');
        }
    }
}

// Enhanced notification function for red flag interactions
function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification fixed top-4 right-4 z-50 px-6 py-4 rounded-xl shadow-lg transition-all duration-300 ${
        type === 'success' ? 'bg-green-100 border border-green-400 text-green-700' :
        type === 'warning' ? 'bg-yellow-100 border border-yellow-400 text-yellow-700' :
        type === 'error' ? 'bg-red-100 border border-red-400 text-red-700' :
        'bg-blue-100 border border-blue-400 text-blue-700'
    }`;

    notification.innerHTML = `
        <div class="flex items-center">
            <i class="fas fa-${
                type === 'success' ? 'check-circle' :
                type === 'warning' ? 'exclamation-triangle' :
                type === 'error' ? 'exclamation-circle' : 'info-circle'
            } mr-2"></i>
            <span>${message}</span>
        </div>
    `;

    document.body.appendChild(notification);

    // Auto-remove after 5 seconds
    setTimeout(() => {
        notification.style.transform = 'translateX(100%)';
        notification.style.opacity = '0';
        setTimeout(() => notification.remove(), 300);
    }, 5000);
}

// Close modal on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const redFlagModal = document.getElementById('redFlagModal');
        if (redFlagModal && !redFlagModal.classList.contains('hidden')) {
            closeRedFlagModal();
        }
    }
});
</script>

<style>
/* Enhanced Premium Tailwind Styling */
.pagination-wrapper .pagination {
    @apply flex items-center justify-center space-x-3;
}

.pagination-wrapper .page-item {
    @apply block;
}

.pagination-wrapper .page-link {
    @apply px-5 py-3 text-sm font-semibold text-gray-700 bg-white border-2 border-gray-200 rounded-xl hover:bg-gray-50 hover:text-gray-900 hover:border-gray-300 transition-all duration-300 shadow-sm hover:shadow-md;
}

.pagination-wrapper .page-item.active .page-link {
    @apply bg-emerald-600 text-white border-emerald-600 hover:bg-emerald-700 shadow-lg;
}

.pagination-wrapper .page-item.disabled .page-link {
    @apply text-gray-400 cursor-not-allowed hover:bg-white hover:text-gray-400 hover:border-gray-200;
}

/* Premium Card Animations */
.talent-card {
    transition: all 0.7s cubic-bezier(0.4, 0, 0.2, 1);
    transform-origin: center;
}

.talent-card:hover {
    transform: translateY(-16px) scale(1.02);
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25), 0 0 0 1px rgba(16, 185, 129, 0.1);
}

/* Sophisticated Gradient Animations */
@keyframes gradientShift {
    0%, 100% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
}

@keyframes floatingPulse {
    0%, 100% {
        opacity: 0.6;
        transform: scale(1);
    }
    50% {
        opacity: 1;
        transform: scale(1.1);
    }
}

.animate-gradient {
    background-size: 200% 200%;
    animation: gradientShift 6s ease infinite;
}

.animate-floating {
    animation: floatingPulse 4s ease-in-out infinite;
}

/* Premium Hover Effects */
.group:hover .group-hover\:scale-105 {
    transform: scale(1.05);
}

.group:hover .group-hover\:scale-110 {
    transform: scale(1.1);
}

.group:hover .group-hover\:translate-x-1 {
    transform: translateX(0.25rem);
}

.group:hover .group-hover\:rotate-12 {
    transform: rotate(12deg);
}

.group:hover .group-hover\:rotate-180 {
    transform: rotate(180deg);
}

/* Advanced Shadow Effects */
.shadow-3xl {
    box-shadow: 0 35px 60px -12px rgba(0, 0, 0, 0.3);
}

.shadow-4xl {
    box-shadow: 0 50px 100px -20px rgba(0, 0, 0, 0.4);
}

/* Enhanced Loading States */
.loading-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: .3;
    }
}

/* Premium Scrollbar Styling */
.custom-scrollbar::-webkit-scrollbar {
    width: 8px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: linear-gradient(to bottom, #f1f5f9, #e2e8f0);
    border-radius: 10px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: linear-gradient(to bottom, #64748b, #475569);
    border-radius: 10px;
    border: 2px solid #f1f5f9;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(to bottom, #475569, #334155);
}

/* Enhanced Modal Styling */
.modal-backdrop {
    background: linear-gradient(135deg, rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.8));
    backdrop-filter: blur(8px);
}

.modal-content {
    border-radius: 1.5rem;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
}

/* Advanced Star Rating */
.star-rating {
    display: inline-flex;
    align-items: center;
    gap: 2px;
}

.star-rating .star {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    cursor: pointer;
    filter: drop-shadow(0 1px 2px rgba(0, 0, 0, 0.1));
}

.star-rating .star:hover {
    color: #fbbf24;
    transform: scale(1.2);
}

/* Premium Button Hover Effects */
.btn-premium {
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
}

.btn-premium::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    transform: translate(-50%, -50%);
    transition: all 0.5s ease;
}

.btn-premium:hover::before {
    width: 300px;
    height: 300px;
}

/* Enhanced Metric Cards */
.metric-card {
    position: relative;
    overflow: hidden;
}

.metric-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
    transition: left 0.5s;
}

.metric-card:hover::before {
    left: 100%;
}

/* Sophisticated Gradient Backgrounds */
.bg-premium-blue {
    background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 50%, #93c5fd 100%);
}

.bg-premium-green {
    background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 50%, #6ee7b7 100%);
}

.bg-premium-purple {
    background: linear-gradient(135deg, #e9d5ff 0%, #ddd6fe 50%, #c4b5fd 100%);
}

.bg-premium-orange {
    background: linear-gradient(135deg, #fed7aa 0%, #fdba74 50%, #fb923c 100%);
}

/* Advanced Animation Utilities */
@keyframes slideInUp {
    from {
        transform: translateY(30px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

@keyframes slideInLeft {
    from {
        transform: translateX(-30px);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

@keyframes scaleIn {
    from {
        transform: scale(0.9);
        opacity: 0;
    }
    to {
        transform: scale(1);
        opacity: 1;
    }
}

.animate-slideInUp {
    animation: slideInUp 0.6s ease-out;
}

.animate-slideInLeft {
    animation: slideInLeft 0.6s ease-out;
}

.animate-scaleIn {
    animation: scaleIn 0.5s ease-out;
}

/* Premium Glass Morphism */
.glass-effect {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

/* Enhanced Focus States */
.focus-ring {
    @apply focus:outline-none focus:ring-4 focus:ring-emerald-500 focus:ring-opacity-50 focus:border-emerald-500;
}

/* Responsive Design Enhancements */
@media (max-width: 768px) {
    .modal-dialog {
        margin: 1rem;
        max-width: calc(100vw - 2rem);
    }

    .talent-card {
        margin-bottom: 2rem;
    }

    .grid-responsive {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }

    /* Mobile responsive flex layout */
    .flex.flex-wrap.gap-\[2\%\] > .w-\[32\%\] {
        width: 100% !important;
        margin-bottom: 1rem;
    }

    .flex.flex-wrap.gap-\[2\%\] > .w-\[32\%\]:last-child {
        margin-bottom: 0;
    }
}

@media (min-width: 769px) and (max-width: 1024px) {
    /* Tablet responsive flex layout */
    .flex.flex-wrap.gap-\[2\%\] > .w-\[32\%\] {
        width: 48% !important;
        margin-bottom: 1rem;
    }

    .flex.flex-wrap.gap-\[2\%\] > .w-\[32\%\]:nth-child(3) {
        width: 100% !important;
        margin-bottom: 0;
    }
}

@media (min-width: 1536px) {
    .container-2xl {
        max-width: 1536px;
    }
}

/* Dark Mode Support (if needed) */
@media (prefers-color-scheme: dark) {
    .auto-dark {
        --tw-bg-opacity: 1;
        background-color: rgb(17 24 39 / var(--tw-bg-opacity));
        color: rgb(243 244 246 / var(--tw-text-opacity));
    }
}

/* Print Optimizations */
@media print {
    .no-print {
        display: none !important;
    }

    .talent-card {
        box-shadow: none;
        border: 1px solid #e5e7eb;
        page-break-inside: avoid;
    }
}

/* Performance Optimizations */
.talent-card,
.metric-card,
.btn-premium {
    will-change: transform;
}

/* Accessibility Enhancements */
.sr-only {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap;
    border: 0;
}

/* High Contrast Mode Support */
@media (prefers-contrast: high) {
    .talent-card {
        border-width: 2px;
        border-color: #000;
    }

    .btn-premium {
        border: 2px solid currentColor;
    }
}

/* Reduced Motion Support */
@media (prefers-reduced-motion: reduce) {
    .talent-card,
    .metric-card,
    .btn-premium,
    .animate-pulse,
    .animate-ping,
    .animate-gradient,
    .animate-floating {
        animation: none !important;
        transition: none !important;
    }
}

/* Talent Comparison Styles */
.talent-card {
    position: relative;
}

.compare-checkbox {
    transition: all 0.3s ease;
}

.talent-compare-check:checked + label,
.talent-card:has(.talent-compare-check:checked) {
    background-color: rgba(16, 185, 129, 0.1);
    border-color: #10b981;
}

#comparisonPanel {
    box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.1);
}

.comparison-highlight {
    background-color: #fef3c7;
    font-weight: bold;
}

/* Modal responsive adjustments */
@media (max-width: 768px) {
    #talentComparisonModal .p-6 {
        padding: 1rem;
    }

    #comparisonContent table {
        font-size: 0.875rem;
    }

    #comparisonContent th,
    #comparisonContent td {
        padding: 0.5rem;
    }
}

/* Red Flag UI Enhancements */
.red-flag-badge {
    @apply inline-flex items-center px-3 py-1 bg-red-100 text-red-800 text-xs font-medium rounded-full border border-red-200;
    box-shadow: 0 1px 3px rgba(220, 38, 38, 0.2);
}

.red-flag-details-btn {
    @apply inline-flex items-center text-xs text-red-600 hover:text-red-800 hover:bg-red-50 px-2 py-1 rounded transition-all duration-200;
    border: 1px solid transparent;
}

.red-flag-details-btn:hover {
    border-color: rgba(220, 38, 38, 0.2);
    box-shadow: 0 1px 2px rgba(220, 38, 38, 0.1);
}

/* Red Flag Modal Enhancements */
#redFlagModal .modal-panel {
    animation: modalSlideIn 0.3s ease-out;
}

@keyframes modalSlideIn {
    from {
        transform: translateY(-20px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

/* Notification styling */
.notification {
    animation: notificationSlideIn 0.3s ease-out;
}

@keyframes notificationSlideIn {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

/* Talent card red flag indicator */
.talent-card.red-flagged {
    @apply border-red-200;
}

.talent-card.red-flagged::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, #ef4444, #dc2626);
    border-radius: 12px 12px 0 0;
}

/* Enhanced Filter Styles */
.filter-panel {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border: 1px solid #e2e8f0;
    border-radius: 1rem;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

.filter-section {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 0.75rem;
    transition: all 0.3s ease;
}

.filter-section:hover {
    border-color: #10b981;
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.15);
}

/* Range Slider Styles */
.range-slider {
    -webkit-appearance: none;
    appearance: none;
    height: 6px;
    background: linear-gradient(to right, #e5e7eb 0%, #e5e7eb 100%);
    border-radius: 3px;
    outline: none;
    transition: all 0.3s ease;
}

.range-slider::-webkit-slider-thumb {
    -webkit-appearance: none;
    appearance: none;
    width: 20px;
    height: 20px;
    background: linear-gradient(135deg, #10b981, #059669);
    border-radius: 50%;
    cursor: pointer;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    transition: all 0.3s ease;
}

.range-slider::-webkit-slider-thumb:hover {
    transform: scale(1.1);
    box-shadow: 0 4px 8px rgba(16, 185, 129, 0.4);
}

.range-slider::-moz-range-thumb {
    width: 20px;
    height: 20px;
    background: linear-gradient(135deg, #10b981, #059669);
    border-radius: 50%;
    cursor: pointer;
    border: none;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    transition: all 0.3s ease;
}

.range-slider::-moz-range-thumb:hover {
    transform: scale(1.1);
    box-shadow: 0 4px 8px rgba(16, 185, 129, 0.4);
}

/* Skills Input Styles */
.skills-input {
    border: 2px solid #e5e7eb;
    border-radius: 0.5rem;
    transition: all 0.3s ease;
}

.skills-input:focus {
    border-color: #10b981;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
}

.skills-dropdown {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 0.5rem;
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    max-height: 200px;
    overflow-y: auto;
}

.skills-dropdown-item {
    padding: 0.75rem 1rem;
    cursor: pointer;
    transition: all 0.2s ease;
    border-bottom: 1px solid #f3f4f6;
}

.skills-dropdown-item:hover {
    background: #f0fdf4;
    color: #059669;
}

.skills-dropdown-item:last-child {
    border-bottom: none;
}

/* Selected Skills Styles */
.selected-skill {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 9999px;
    font-size: 0.875rem;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
}

.selected-skill:hover {
    background: linear-gradient(135deg, #059669, #047857);
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(16, 185, 129, 0.3);
}

.selected-skill .remove-skill {
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    width: 1.25rem;
    height: 1.25rem;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
}

.selected-skill .remove-skill:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: scale(1.1);
}

/* Filter Action Buttons */
.filter-btn {
    padding: 0.75rem 1.5rem;
    border-radius: 0.5rem;
    font-weight: 600;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.filter-btn-primary {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
    border: none;
}

.filter-btn-primary:hover {
    background: linear-gradient(135deg, #059669, #047857);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
}

.filter-btn-secondary {
    background: white;
    color: #374151;
    border: 2px solid #e5e7eb;
}

.filter-btn-secondary:hover {
    background: #f9fafb;
    border-color: #d1d5db;
    transform: translateY(-1px);
}

.filter-btn-danger {
    background: linear-gradient(135deg, #ef4444, #dc2626);
    color: white;
    border: none;
}

.filter-btn-danger:hover {
    background: linear-gradient(135deg, #dc2626, #b91c1c);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(239, 68, 68, 0.4);
}

/* Filter Count Badge */
.filter-count {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: white;
    font-size: 0.75rem;
    font-weight: 700;
    padding: 0.25rem 0.5rem;
    border-radius: 9999px;
    min-width: 1.5rem;
    text-align: center;
    animation: pulse 2s infinite;
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .filter-panel {
        margin: 0.5rem;
        padding: 1rem;
    }
    
    .filter-section {
        margin-bottom: 1rem;
    }
    
    .filter-btn {
        width: 100%;
        justify-content: center;
        margin-bottom: 0.5rem;
    }
    
    .selected-skill {
        font-size: 0.75rem;
        padding: 0.375rem 0.75rem;
    }
}

/* Animation for filter application */
@keyframes filterApply {
    0% { opacity: 0.5; transform: scale(0.95); }
    50% { opacity: 0.8; transform: scale(1.02); }
    100% { opacity: 1; transform: scale(1); }
}

.filter-applying {
    animation: filterApply 0.6s ease-out;
}
</style>

<!-- Comparison Panel (Fixed at Bottom) -->
<div id="comparisonPanel" class="fixed bottom-0 left-0 right-0 bg-white border-t shadow-lg transform translate-y-full transition-transform duration-300 ease-in-out z-40" style="display: none;">
    <div class="max-w-7xl mx-auto px-6 py-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <h3 class="text-lg font-semibold text-gray-900 mr-4">Compare Talents</h3>
                <span id="selectedCount" class="bg-emerald-100 text-emerald-800 px-3 py-1 rounded-full text-sm font-medium">0 selected</span>
            </div>
            <div class="flex gap-3">
                <button onclick="viewComparison()"
                        class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                        id="compareBtn" disabled>
                    <i class="fas fa-chart-bar mr-2"></i>Compare Details
                </button>
                <button onclick="clearComparison()"
                        class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    <i class="fas fa-times mr-2"></i>Clear
                </button>
                <button onclick="toggleCompareMode()"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                    <i class="fas fa-times mr-2"></i>Exit Compare
                </button>
            </div>
        </div>

        <!-- Selected Talents Preview -->
        <div id="selectedTalentsPreview" class="mt-4 flex gap-4 overflow-x-auto pb-2">
            <!-- Selected talents will be populated here by JavaScript -->
        </div>
    </div>
</div>

<!-- Talent Comparison Modal -->
<div id="talentComparisonModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" style="display: none;">
    <div class="bg-white rounded-2xl max-w-7xl w-full max-h-[90vh] overflow-hidden">
        <!-- Modal Header -->
        <div class="bg-emerald-600 text-white p-6">
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold flex items-center">
                    <i class="fas fa-balance-scale mr-3"></i>
                    Talent Comparison
                </h2>
                <button onclick="closeComparisonModal()" class="text-white hover:text-gray-200">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>

        <!-- Modal Content -->
        <div class="p-6 overflow-y-auto max-h-[calc(90vh-120px)]">
            <div id="comparisonContent">
                <!-- Comparison content will be populated here -->
            </div>
        </div>
    </div>
</div>

<!-- Red Flag Details Modal -->
<div id="redFlagModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="redFlagModalTitle" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" onclick="closeRedFlagModal()"></div>

        <!-- Modal panel -->
        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full modal-panel">
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-red-600 to-red-700 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="bg-red-500/30 rounded-full p-2">
                            <i class="fas fa-flag text-red-200 text-lg"></i>
                        </div>
                        <h3 class="text-lg font-medium text-white" id="redFlagModalTitle">Red Flag Details</h3>
                    </div>
                    <button type="button" class="text-red-200 hover:text-white transition-colors" onclick="closeRedFlagModal()">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>

            <!-- Modal Body -->
            <div class="px-6 py-6">
                <div class="space-y-4">
                    <!-- Talent Info -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center space-x-3">
                            <div class="bg-red-100 rounded-full p-2">
                                <i class="fas fa-user text-red-600"></i>
                            </div>
                            <div>
                                <div class="text-sm text-gray-600">Talent Name</div>
                                <div class="font-semibold text-gray-900" id="redFlagTalentName">-</div>
                            </div>
                        </div>
                    </div>

                    <!-- Flag Status -->
                    <div class="border-l-4 border-red-500 pl-4">
                        <div class="text-sm text-gray-600 mb-1">Account Status</div>
                        <div class="flex items-center space-x-2">
                            <span class="inline-flex items-center px-3 py-1 bg-red-500 text-white text-sm font-medium rounded-full shadow-sm">
                                <i class="fas fa-exclamation-triangle mr-1"></i>Red Flagged
                            </span>
                            <span class="text-xs text-gray-500" id="redFlagDate">-</span>
                        </div>
                    </div>

                    <!-- Reason -->
                    <div>
                        <div class="text-sm font-medium text-gray-700 mb-2">Reason for Red Flag</div>
                        <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                            <p class="text-red-800" id="redFlagReason">-</p>
                        </div>
                    </div>

                    <!-- Warning Notice -->
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <div class="flex items-start space-x-3">
                            <i class="fas fa-exclamation-triangle text-yellow-600 mt-0.5"></i>
                            <div class="text-sm text-yellow-800">
                                <div class="font-medium mb-1">Important Notice</div>
                                <p>This talent has been flagged by the administration. Please consider this information when making collaboration decisions. Contact the talent admin for more details if needed.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="bg-gray-50 px-6 py-4">
                <div class="flex items-center justify-between">
                    <button onclick="contactTalentAdmin()" class="text-blue-600 hover:text-blue-700 text-sm font-medium underline">
                        <i class="fas fa-envelope mr-1"></i>Contact Talent Admin
                    </button>
                    <button type="button" onclick="closeRedFlagModal()" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
