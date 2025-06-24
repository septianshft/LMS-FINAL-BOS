<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();

        // Update last login timestamp
        User::where('id', $user->id)->update(['last_login_at' => now()]);

        $platform = $request->input('platform', 'lms');

        // If talent platform login, check for talent roles
        if ($platform === 'talent') {
            return $this->handleTalentLogin($user);
        }

        // Default LMS login redirect
        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Handle talent platform login routing
     */
    private function handleTalentLogin($user): RedirectResponse
    {
        if ($user->hasAnyRole(['talent_admin', 'talent', 'recruiter'])) {
            // Redirect based on role
            if ($user->hasRole('talent_admin')) {
                return redirect()->route('talent_admin.dashboard');
            } elseif ($user->hasRole('talent')) {
                return redirect()->route('talent.dashboard');
            } elseif ($user->hasRole('recruiter')) {
                return redirect()->route('recruiter.dashboard');
            }
        }

        // User doesn't have talent roles, logout and redirect back
        Auth::logout();
        return back()->withErrors([
            'email' => 'You do not have access to the talent platform.',
        ]);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
