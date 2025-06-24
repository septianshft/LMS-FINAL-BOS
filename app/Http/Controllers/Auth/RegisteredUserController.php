<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Talent;
use App\Models\Recruiter;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'pekerjaan' => ['required', 'string', 'max:255'],
            'avatar' => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:2048'], // Made optional and added size limit
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'string', 'in:trainee,talent,recruiter'],
        ]);

        // Proses upload file photo or use default
        if($request->hasFile('avatar')){
            $avatarPath = $request->file('avatar')->store('avatars','public');
        } else {
            $avatarPath = 'images/default-avatar.svg'; // Provide a default avatar
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'pekerjaan' => $request->pekerjaan,
            'avatar' => $avatarPath,
            'password' => Hash::make($request->password),
        ]);

        // Assign role to user
        $role = Role::findByName($request->role);
        $user->assignRole($role);

        // Create related record based on role
        switch ($request->role) {
            case 'talent':
                Talent::create([
                    'user_id' => $user->id,
                    'is_active' => true,
                ]);
                break;
            case 'recruiter':
                Recruiter::create([
                    'user_id' => $user->id,
                    'company_name' => $request->company_name ?: $request->pekerjaan, // Use job as fallback
                    'industry' => $request->industry ?: 'Other',
                    'company_size' => $request->company_size,
                    'company_description' => $request->company_description,
                    'phone' => $request->phone,
                    'address' => $request->address,
                    'is_active' => true,
                ]);
                break;
            case 'trainee':
                // Trainee doesn't need a separate record as it's handled by existing system
                break;
        }

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
