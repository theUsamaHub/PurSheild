<x-guest-layout>
    <form method="POST" action="{{ route('register') }}" novalidate x-data="{ terms: false }">
        @csrf

        <!-- Full Name -->
        <div class="mb-3">
            <x-input-label for="name" :value="__('Full Name')" />
            <input
                id="name"
                type="text"
                name="name"
                class="form-control @error('name') is-invalid @enderror"
                value="{{ old('name') }}"
                required
                autofocus
                autocomplete="name"
                placeholder="Enter your full name"
            />
            @error('name')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

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
                autocomplete="username"
                placeholder="Enter your email"
            />
            @error('email')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <!-- Phone Number -->
        <div class="mb-3">
            <x-input-label for="phone" :value="__('Phone Number')" />
            <input
                id="phone"
                type="tel"
                name="phone"
                class="form-control @error('phone') is-invalid @enderror"
                value="{{ old('phone') }}"
                autocomplete="tel"
                placeholder="e.g. +1234567890"
            />
            @error('phone')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <!-- I Am (Role Selection) -->
        <div class="mb-3">
            <x-input-label for="role" :value="__('I am a...')" />
            <select
                id="role"
                name="role"
                class="form-select @error('role') is-invalid @enderror"
                required
            >
                <option value="" disabled {{ old('role') ? '' : 'selected' }}>Select your role</option>
                <option value="owner" {{ old('role') == 'owner' ? 'selected' : '' }}>
                    Pet Owner - I have pets and need care services
                </option>
                <option value="vet" {{ old('role') == 'vet' ? 'selected' : '' }}>
                    Veterinarian - I provide veterinary services
                </option>
                <option value="shelter" {{ old('role') == 'shelter' ? 'selected' : '' }}>
                    Animal Shelter - I run an animal shelter
                </option>
            </select>
            @error('role')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
            <div class="form-text">
                <i class="bi bi-info-circle me-1"></i>
                {{ __('Vet and Shelter accounts require admin verification before going live.') }}
            </div>
        </div>

        <!-- Address -->
        <div class="mb-3">
            <x-input-label for="address" :value="__('Address')" />
            <textarea
                id="address"
                name="address"
                class="form-control @error('address') is-invalid @enderror"
                rows="2"
                autocomplete="street-address"
                placeholder="Enter your address"
            >{{ old('address') }}</textarea>
            @error('address')
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
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
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

        <!-- Terms -->
        <div class="form-check mb-4">
            <input class="form-check-input @error('terms') is-invalid @enderror" type="checkbox" name="terms" id="terms" required x-model="terms">
            <label class="form-check-label" for="terms" style="font-size: 0.85rem;">
                {{ __('I agree to the') }}
                <a href="#" class="auth-link">{{ __('Terms of Service') }}</a>
                {{ __('and') }}
                <a href="#" class="auth-link">{{ __('Privacy Policy') }}</a>
            </label>
            @error('terms')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn w-100" :class="terms ? 'btn-primary' : 'btn-secondary'" :disabled="!terms">
            <i class="bi bi-person-plus me-1"></i>
            {{ __('Create Account') }}
        </button>

        <!-- Login Link -->
        <div class="text-center mt-4 pt-3" style="border-top: 1px solid var(--fs-border-light);">
            <span style="font-size: 0.875rem; color: var(--fs-text-muted);">{{ __('Already have an account?') }}</span>
            <a href="{{ route('login') }}" class="auth-link">{{ __('Log in') }}</a>
        </div>
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
