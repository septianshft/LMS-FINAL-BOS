<section class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6" id="skills-dashboard">
    <header class="mb-6">
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 flex items-center">
            <i class="fas fa-chart-line mr-2 text-blue-600"></i>
            {{ __('Skills & Learning Analytics') }}
        </h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Track your skill development and learning progress.') }}
        </p>
    </header>

    @php
        $skillAnalytics = auth()->user()->getSkillAnalytics();
        $skillsByProficiency = auth()->user()->getSkillsByProficiency();
        $totalSkills = $skillAnalytics['total_skills'] ?? 0;
        $skillLevels = $skillAnalytics['skill_levels'] ?? ['beginner' => 0, 'intermediate' => 0, 'advanced' => 0];
        $recentSkills = $skillAnalytics['recent_skills'] ?? 0;
    @endphp

    <!-- Skills Overview Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm">Total Skills</p>
                    <p class="text-2xl font-bold">{{ $totalSkills }}</p>
                </div>
                <i class="fas fa-cog text-2xl text-blue-200"></i>
            </div>
        </div>

        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm">Advanced Skills</p>
                    <p class="text-2xl font-bold">{{ $skillLevels['advanced'] }}</p>
                </div>
                <i class="fas fa-star text-2xl text-green-200"></i>
            </div>
        </div>

        <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm">Intermediate</p>
                    <p class="text-2xl font-bold">{{ $skillLevels['intermediate'] }}</p>
                </div>
                <i class="fas fa-signal text-2xl text-purple-200"></i>
            </div>
        </div>

        <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-lg p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-sm">Recent (30d)</p>
                    <p class="text-2xl font-bold">{{ $recentSkills }}</p>
                </div>
                <i class="fas fa-calendar text-2xl text-orange-200"></i>
            </div>
        </div>
    </div>

    @if($totalSkills > 0)
        <!-- Skills by Proficiency Level -->
        <div class="mb-6">
            <h3 class="text-md font-semibold text-gray-900 dark:text-gray-100 mb-4">
                <i class="fas fa-signal mr-2"></i>Skills by Proficiency Level
            </h3>
            <div class="space-y-4">
                @foreach($skillsByProficiency as $proficiency => $skills)
                    <div class="border rounded-lg p-4 bg-gray-50 dark:bg-gray-700">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="font-medium text-gray-900 dark:text-gray-100 flex items-center">
                                @switch($proficiency)
                                    @case('advanced')
                                        <i class="fas fa-star mr-2 text-yellow-500"></i>
                                        @break
                                    @case('intermediate')
                                        <i class="fas fa-signal mr-2 text-blue-500"></i>
                                        @break
                                    @case('beginner')
                                        <i class="fas fa-seedling mr-2 text-green-500"></i>
                                        @break
                                    @default
                                        <i class="fas fa-code mr-2 text-gray-500"></i>
                                @endswitch
                                {{ ucfirst($proficiency) }} Level
                                <span class="ml-2 bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded">
                                    {{ count($skills) }} skill{{ count($skills) > 1 ? 's' : '' }}
                                </span>
                            </h4>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            @foreach($skills as $skill)
                                <div class="bg-white dark:bg-gray-800 rounded-lg p-3 border border-gray-200 dark:border-gray-600">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="font-medium text-gray-900 dark:text-gray-100">{{ $skill['skill_name'] }}</span>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($skill['proficiency'] === 'advanced') bg-yellow-100 text-yellow-800
                                            @elseif($skill['proficiency'] === 'intermediate') bg-blue-100 text-blue-800
                                            @else bg-green-100 text-green-800 @endif">
                                            <i class="fas fa-medal mr-1"></i>
                                            {{ ucfirst($skill['proficiency']) }}
                                        </span>
                                    </div>
                                    <div class="flex items-center justify-between text-sm text-gray-600 dark:text-gray-400">
                                        <span class="flex items-center">
                                            <i class="fas fa-graduation-cap mr-1"></i>
                                            Course Completed
                                        </span>
                                        <span class="flex items-center">
                                            <i class="fas fa-calendar mr-1"></i>
                                            {{ isset($skill['completed_date']) ? \Carbon\Carbon::parse($skill['completed_date'])->format('M Y') : 'Recent' }}
                                        </span>
                                    </div>
                                    <div class="mt-2 flex items-center text-xs text-green-600">
                                        <i class="fas fa-check-circle mr-1"></i>
                                        Verified through Course Completion
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Skill Level Distribution -->
        <div class="mb-6">
            <h3 class="text-md font-semibold text-gray-900 dark:text-gray-100 mb-4">
                <i class="fas fa-chart-pie mr-2"></i>Skill Level Distribution
            </h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach($skillLevels as $level => $count)
                    <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $count }}</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">{{ ucfirst($level) }}</div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Learning Progress Insight -->
        <div class="bg-gradient-to-r from-indigo-50 to-blue-50 dark:from-indigo-900 dark:to-blue-900 rounded-lg p-4">
            <h3 class="text-md font-semibold text-indigo-900 dark:text-indigo-100 mb-2">
                <i class="fas fa-lightbulb mr-2"></i>Learning Insights
            </h3>
            <div class="text-sm text-indigo-800 dark:text-indigo-200">
                @if($recentSkills > 0)
                    <p class="mb-2">🎉 You've gained {{ $recentSkills }} new skill{{ $recentSkills > 1 ? 's' : '' }} in the last 30 hari!</p>
                @endif

                @if($skillLevels['advanced'] > 0)
                    <p class="mb-2">⭐ You have {{ $skillLevels['advanced'] }} advanced-level skill{{ $skillLevels['advanced'] > 1 ? 's' : '' }} - excellent expertise!</p>
                @endif

                @if($totalSkills >= 5 && !auth()->user()->available_for_scouting)
                    <p class="mb-2">💼 With {{ $totalSkills }} verified skills, you're ready to attract recruiters! Consider enabling talent scouting below.</p>
                @endif

                @if($totalSkills >= 10)
                    <p>🌟 Your extensive skill portfolio with {{ $totalSkills }} skills makes you a highly capable candidate!</p>
                @elseif($totalSkills >= 5)
                    <p>🚀 You're building a solid skill foundation with {{ $totalSkills }} skills. Keep learning!</p>
                @endif
            </div>
        </div>
    @else
        <!-- No Skills Yet -->
        <div class="text-center py-8">
            <i class="fas fa-graduation-cap text-6xl text-gray-300 dark:text-gray-600 mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">No Skills Tracked Yet</h3>
            <p class="text-gray-600 dark:text-gray-400 mb-4">Complete courses and pass quizzes to automatically build your skill profile.</p>
            <a href="{{ route('front.index') }}"
               class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <i class="fas fa-play mr-2"></i>Start Learning
            </a>
        </div>
    @endif
</section>

@push('scripts')
<script>
// Add any interactive features for the skills dashboard
document.addEventListener('DOMContentLoaded', function() {
    // Animate skill cards on page load
    const skillCards = document.querySelectorAll('#skills-dashboard .bg-white, #skills-dashboard .bg-gray-50');
    skillCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        setTimeout(() => {
            card.style.transition = 'all 0.3s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
});
</script>
@endpush
