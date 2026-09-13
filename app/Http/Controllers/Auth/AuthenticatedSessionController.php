<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
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

        $user = $request->user();

        // Update last login timestamp
        $user->update(['last_login_at' => now()]);

        // Role-based redirect
        return redirect()->intended($this->getRedirectPath($user));
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

    /**
     * Get the redirect path based on user role.
     */
    private function getRedirectPath($user): string
    {
        if ($user->hasRole('admin')) {
            return route('admin.dashboard', absolute: false);
        }

        if ($user->hasRole('vet')) {
            return route('vet.dashboard', absolute: false);
        }

        if ($user->hasRole('shelter')) {
            return route('shelter.dashboard', absolute: false);
        }

        return route('owner.dashboard', absolute: false);
    }
}
