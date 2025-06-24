<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Services\TalentScoutingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    protected $scoutingService;

    public function __construct(TalentScoutingService $scoutingService)
    {
        $this->scoutingService = $scoutingService;
    }

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();

        // Don't show conversion suggestions if user already has talent role
        if (!$user->hasRole('talent')) {
            // Check for cached conversion suggestion
            $conversionSuggestion = Cache::get("conversion_suggestion_{$user->id}");

            // If there's a cached suggestion, add it to the session
            if ($conversionSuggestion && !session()->has('smart_talent_suggestion')) {
                session()->flash('smart_talent_suggestion', $conversionSuggestion);
            }
        }

        return view('profile.edit', [
            'user' => $user,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Update the user's talent scouting settings.
     */
    public function updateTalent(Request $request): RedirectResponse
    {
        $request->validate([
            'available_for_scouting' => 'boolean',
            'talent_bio' => 'nullable|string|max:1000',
            'portfolio_url' => 'nullable|url|max:255',
            'location' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'experience_level' => 'nullable|in:beginner,intermediate,advanced,expert',
        ]);

        $user = $request->user();
        $isOptingIn = $request->boolean('available_for_scouting');

        // Update talent fields
        $user->update([
            'available_for_scouting' => $isOptingIn,
            'talent_bio' => $isOptingIn ? $request->talent_bio : null,
            'portfolio_url' => $isOptingIn ? $request->portfolio_url : null,
            'location' => $isOptingIn ? $request->location : null,
            'phone' => $isOptingIn ? $request->phone : null,
            'experience_level' => $isOptingIn ? $request->experience_level : null,
            'is_active_talent' => $isOptingIn,
        ]);

        // Handle role assignment and Talent record
        if ($isOptingIn) {
            // Assign talent role if not already assigned
            if (!$user->hasRole('talent')) {
                $user->assignRole('talent');
            }

            // Create Talent record if it doesn't exist
            $talent = null;
            if (!$user->talent) {
                $talent = \App\Models\Talent::create([
                    'user_id' => $user->id,
                    'is_active' => true,
                ]);
            } else {
                $talent = $user->talent;
                $talent->update(['is_active' => true]);
            }

            // Calculate and cache scouting metrics for the new/reactivated talent
            // This ensures metrics are immediately available in dashboard views
            try {
                $metrics = $this->scoutingService->getTalentScoutingMetrics($talent);

                // Cache the metrics for immediate access
                $cacheKey = "talent_metrics_{$talent->id}";
                cache()->put($cacheKey, $metrics, now()->addHours(24));

                // Also store basic metrics in session for immediate feedback
                session()->flash('talent_metrics', [
                    'completed_courses' => $metrics['progress_tracking']['completed_courses'] ?? 0,
                    'certificates' => $metrics['certifications']['total_certificates'] ?? 0,
                    'quiz_average' => $metrics['quiz_performance']['average_score'] ?? 0,
                ]);
            } catch (\Exception $e) {
                // Log error but don't prevent opt-in
                Log::warning('Failed to calculate talent metrics during opt-in', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage()
                ]);
            }
        } else {
            // Deactivate talent but keep the role for potential future re-enabling
            if ($user->talent) {
                $user->talent->update(['is_active' => false]);

                // Clear cached metrics when deactivating
                $cacheKey = "talent_metrics_{$user->talent->id}";
                cache()->forget($cacheKey);
            }
        }

        $redirect = Redirect::route('profile.edit')->with('status', 'talent-updated');

        if ($isOptingIn) {
            $redirect->with('opted_in_talent', true);
        } else {
            $redirect->with('opted_out_talent', true);
        }

        return $redirect;
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
