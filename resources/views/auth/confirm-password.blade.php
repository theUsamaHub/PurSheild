<x-guest-layout>
    <p class="text-center mb-4" style="font-size: 0.875rem; color: var(--fs-text-muted);">
        {{ __('Please confirm your password to continue.') }}
    </p>

    <form method="POST" action="{{ route('password.confirm') }}" novalidate>
        @csrf

        <!-- Password -->
        <div class="mb-4">
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

        <!-- Submit Button -->
        <button type="submit" class="btn btn-primary w-100">
            <i class="bi bi-shield-lock me-1"></i>
            {{ __('Confirm Password') }}
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
