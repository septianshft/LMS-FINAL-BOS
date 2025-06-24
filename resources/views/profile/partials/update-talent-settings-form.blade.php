<section id="talent-settings">
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Talent Scouting Settings') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Make yourself available to recruiters and showcase your skills acquired from completed courses.') }}
        </p>
    </header>

    @if(session('status') === 'talent-updated')
        <div class="mb-4 rounded-md bg-green-50 p-4 shadow-sm border border-green-200">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">
                        {{ __('Talent settings updated successfully!') }}
                        @if(session('opted_in_talent'))
                            <span class="block sm:inline">{{ __('You are now discoverable by recruiters.') }}</span>
                        @elseif(session('opted_out_talent'))
                            <span class="block sm:inline">{{ __('You are no longer discoverable by recruiters.') }}</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>
    @endif

    <form method="post" action="{{ route('profile.update-talent') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <!-- Available for Scouting Toggle -->
        <div>
            <div class="flex items-center">
                <input id="available_for_scouting" name="available_for_scouting" type="checkbox"
                       value="1" {{ old('available_for_scouting', $user->available_for_scouting) ? 'checked' : '' }}
                       class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                <label for="available_for_scouting" class="ml-3 block text-sm font-medium text-gray-700">
                    {{ __('Make me available for talent scouting') }}
                </label>
            </div>
            <p class="mt-2 text-sm text-gray-500">
                {{ __('Recruiters will be able to find and contact you for opportunities.') }}
            </p>
            <x-input-error class="mt-2" :messages="$errors->get('available_for_scouting')" />
        </div>

        <!-- Current Skills Display -->
        @php
            $userSkills = $user->getTalentSkillsArray();
        @endphp
        @if($userSkills && is_array($userSkills) && count($userSkills) > 0)
        <div>
            <x-input-label for="current_skills" :value="__('Skills from Completed Courses')" />
            <div class="mt-2 flex flex-wrap gap-2">
                @foreach($userSkills as $skill)
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    {{ is_array($skill) ? ($skill['skill_name'] ?? $skill['name'] ?? 'Unknown') : $skill }}
                    @if(is_array($skill) && isset($skill['proficiency']))
                        <span class="ml-1 text-green-600">({{ ucfirst($skill['proficiency']) }})</span>
                    @elseif(is_array($skill) && isset($skill['level']))
                        <span class="ml-1 text-green-600">({{ ucfirst($skill['level']) }})</span>
                    @endif
                </span>
                @endforeach
            </div>
            <p class="mt-2 text-sm text-gray-500">
                {{ __('Skills are automatically added when you complete courses and pass their final quizzes.') }}
            </p>
        </div>
        @else
        <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">
                        {{ __('No Skills Yet') }}
                    </h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <p>{{ __('Complete courses and pass their final quizzes to automatically add skills to your profile. These skills will help recruiters find you for relevant opportunities.') }}</p>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Additional Talent Information (only show if opting in) -->
        <div id="talent-details" class="{{ $user->available_for_scouting ? '' : 'hidden' }}">
            <!-- Bio -->
            <div>
                <x-input-label for="talent_bio" :value="__('Professional Bio')" />
                <textarea id="talent_bio" name="talent_bio" rows="4"
                         class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                         placeholder="Tell recruiters about your experience, expertise, and what kind of projects you're interested in...">{{ old('talent_bio', $user->talent_bio) }}</textarea>
                <x-input-error class="mt-2" :messages="$errors->get('talent_bio')" />
            </div>

            <!-- Portfolio URL -->
            <div>
                <x-input-label for="portfolio_url" :value="__('Portfolio/Website URL')" />
                <x-text-input id="portfolio_url" name="portfolio_url" type="url"
                             class="mt-1 block w-full" :value="old('portfolio_url', $user->portfolio_url)"
                             placeholder="https://your-portfolio.com" />
                <x-input-error class="mt-2" :messages="$errors->get('portfolio_url')" />
            </div>

            <!-- Location -->
            <div>
                <x-input-label for="location" :value="__('Location')" />
                <x-text-input id="location" name="location" type="text"
                             class="mt-1 block w-full" :value="old('location', $user->location)"
                             placeholder="e.g., New York, NY or Remote" />
                <x-input-error class="mt-2" :messages="$errors->get('location')" />
            </div>

            <!-- Phone -->
            <div>
                <x-input-label for="phone" :value="__('Phone Number')" />
                <x-text-input id="phone" name="phone" type="tel"
                             class="mt-1 block w-full" :value="old('phone', $user->phone)"
                             placeholder="+1 (555) 123-4567" />
                <x-input-error class="mt-2" :messages="$errors->get('phone')" />
            </div>
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save Talent Settings') }}</x-primary-button>

            {{-- Original "Saved." message removed as it is replaced by the new notification block above --}}
        </div>
    </form>

    <script>
        // Show/hide talent details based on checkbox
        document.getElementById('available_for_scouting').addEventListener('change', function() {
            const talentDetails = document.getElementById('talent-details');
            if (this.checked) {
                talentDetails.classList.remove('hidden');
            } else {
                talentDetails.classList.add('hidden');
            }
        });
    </script>
</section>
