<x-guest-layout>
    <p class="text-center mb-4" style="font-size: 0.875rem; color: var(--fs-text-muted);">
        {{ __('Enter your email and we\'ll send you a reset link.') }}
    </p>

    <!-- Session Status -->
    <x-auth-session-status class="mb-3" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" novalidate>
        @csrf

        <!-- Email Address -->
        <div class="mb-3">
            <x-input-label for="email" :value="__('Email Address')" />
            <input
                id="email"
                type="email"
                name="email"
                class="form-control @error('email') is-invalid @enderror"
                value="{{ old('email') }}"
                required
                autofocus
                autocomplete="username"
                placeholder="Enter your email"
            />
            @error('email')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-primary w-100">
            <i class="bi bi-envelope me-1"></i>
            {{ __('Send Reset Link') }}
        </button>

        <!-- Back to Login -->
        <div class="text-center mt-4 pt-3" style="border-top: 1px solid var(--fs-border-light);">
            <a href="{{ route('login') }}" class="auth-link" style="font-size: 0.875rem;">
                <i class="bi bi-arrow-left me-1"></i>{{ __('Back to login') }}
            </a>
        </div>
    </form>
</x-guest-layout>
