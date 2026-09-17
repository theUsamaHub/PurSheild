<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
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
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:500'],
            'role' => ['nullable', 'string', 'in:owner,vet,shelter'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ];

        if ($request->has('terms')) {
            $rules['terms'] = ['accepted'];
        }

        $validated = $request->validate($rules, [
            'name.required' => 'Please enter your full name.',
            'name.max' => 'Name must not exceed 255 characters.',
            'email.required' => 'Please enter your email address.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email is already registered. Please use a different one.',
            'phone.max' => 'Phone number must not exceed 30 characters.',
            'address.max' => 'Address must not exceed 500 characters.',
            'role.in' => 'Invalid role selected. Please choose Pet Owner, Veterinarian, or Animal Shelter.',
            'password.required' => 'Please enter a password.',
            'password.confirmed' => 'Passwords do not match.',
            'terms.accepted' => 'You must accept the Terms of Service and Privacy Policy.',
        ]);

        $selectedRole = $validated['role'] ?? 'owner';

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'status' => $selectedRole === 'owner' ? 'active' : 'pending_verification',
            'password' => Hash::make($validated['password']),
        ]);

        $user->assignRole($selectedRole);

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('dashboard');
    }
}
