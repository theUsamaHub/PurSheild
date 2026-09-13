<x-guest-layout>
    <p class="text-center mb-4" style="font-size: 0.875rem; color: var(--fs-text-muted);">
        {{ __('Enter your new password below.') }}
    </p>

    <form method="POST" action="{{ route('password.store') }}" novalidate>
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address -->
        <div class="mb-3">
            <x-input-label for="email" :value="__('Email Address')" />
            <input
                id="email"
                type="email"
                name="email"
                class="form-control @error('email') is-invalid @enderror"
                value="{{ old('email', $request->email) }}"
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
            <x-input-label for="password" :value="__('New Password')" />
            <div class="password-wrapper">
                <input
                    id="password"
                    type="password"
                    name="password"
                    class="form-control @error('password') is-invalid @enderror"
                    required
                    autocomplete="new-password"
                    placeholder="Min. 8 characters"
                />
                <button type="button" class="password-toggle" onclick="togglePassword('password', this)">
                    <i class="bi bi-eye"></i>
                </button>
            </div>
            @error('password')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div class="mb-4">
            <x-input-label for="password_confirmation" :value="__('Confirm New Password')" />
            <div class="password-wrapper">
                <input
                    id="password_confirmation"
                    type="password"
                    name="password_confirmation"
                    class="form-control"
                    required
                    autocomplete="new-password"
                    placeholder="Repeat your password"
                />
                <button type="button" class="password-toggle" onclick="togglePassword('password_confirmation', this)">
                    <i class="bi bi-eye"></i>
                </button>
            </div>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-primary w-100">
            <i class="bi bi-key me-1"></i>
            {{ __('Reset Password') }}
        </button>
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
