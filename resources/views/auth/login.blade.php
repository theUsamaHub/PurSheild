<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-3" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" novalidate>
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

        <!-- Password -->
        <div class="mb-3">
            <x-input-label for="password" :value="__('Password')" />
            <div class="password-wrapper">
                <input
                    id="password"
                    type="password"
                    name="password"
                    class="form-control @error('password') is-invalid @enderror"
                    required
                    autocomplete="current-password"
                    placeholder="Enter your password"
                />
                <button type="button" class="password-toggle" onclick="togglePassword('password', this)">
                    <i class="bi bi-eye"></i>
                </button>
            </div>
            @error('password')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <!-- Remember Me & Forgot Password -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="remember" id="remember_me">
                <label class="form-check-label" for="remember_me" style="font-size: 0.875rem;">
                    {{ __('Remember me') }}
                </label>
            </div>

            @if (Route::has('password.request'))
                <a class="auth-link" href="{{ route('password.request') }}">
                    {{ __('Forgot password?') }}
                </a>
            @endif
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-primary w-100">
            <i class="bi bi-box-arrow-in-right me-1"></i>
            {{ __('Log in') }}
        </button>

        <!-- Register Link -->
        @if (Route::has('register'))
            <div class="text-center mt-4 pt-3" style="border-top: 1px solid var(--fs-border-light);">
                <span style="font-size: 0.875rem; color: var(--fs-text-muted);">{{ __("Don't have an account?") }}</span>
                <a href="{{ route('register') }}" class="auth-link">{{ __('Create account') }}</a>
            </div>
        @endif
    </form>

    @push('scripts')
    <script>
        function togglePassword(fieldId, btn) {
            const field = document.getElementById(fieldId);
            const icon = btn.querySelector('i');
            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                field.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        }
    </script>
    @endpush
</x-guest-layout>
