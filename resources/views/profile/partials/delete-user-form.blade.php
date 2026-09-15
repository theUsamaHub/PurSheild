<section>
    <div class="pc-section-header pc-section-danger">
        <i class="bi bi-exclamation-triangle"></i>
        <div>
            <h6>{{ __('Delete Account') }}</h6>
            <p>{{ __('Once your account is deleted, all of its resources and data will be permanently deleted.') }}</p>
        </div>
    </div>

    <button
        type="button"
        class="pc-btn-danger mt-3"
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
    >
        <i class="bi bi-trash"></i> {{ __('Delete Account') }}
    </button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <div class="modal-header border-0 pb-0">
            <h5 class="modal-title fw-bold">{{ __('Are you sure you want to delete your account?') }}</h5>
        </div>
        <div class="modal-body">
            <p class="text-muted" style="font-size: 0.875rem;">
                {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
            </p>

            <div class="mt-3">
                <x-input-label for="password" value="{{ __('Password') }}" class="visually-hidden" />
                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="form-control"
                    placeholder="{{ __('Password') }}"
                />
                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-1" />
            </div>
        </div>
        <div class="modal-footer border-0 pt-0">
            <x-secondary-button x-on:click="$dispatch('close')">
                {{ __('Cancel') }}
            </x-secondary-button>
            <x-danger-button class="ms-2">
                {{ __('Delete Account') }}
            </x-danger-button>
        </div>
    </x-modal>
</section>
