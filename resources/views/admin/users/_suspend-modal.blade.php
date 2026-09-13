@php
    $suspendUser = $suspendUser ?? $user;
@endphp

<div class="modal fade" id="suspendModal{{ $suspendUser->id }}" tabindex="-1" aria-labelledby="suspendModalLabel{{ $suspendUser->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.users.suspend', $suspendUser) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="suspendModalLabel{{ $suspendUser->id }}">
                        <i class="bi bi-slash-circle text-danger me-1"></i>{{ __('Suspend User') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
                </div>
                <div class="modal-body">
                    <p>{{ __('Are you sure you want to suspend') }} <strong>{{ $suspendUser->name }}</strong>?</p>
                    <p class="text-muted" style="font-size: 0.875rem;">{{ __('Suspended users cannot log in to their account until reinstated.') }}</p>
                    <div class="mb-0">
                        <label for="reason{{ $suspendUser->id }}" class="form-label fw-semibold">{{ __('Suspension Reason') }} <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="reason{{ $suspendUser->id }}" name="reason" rows="4" placeholder="{{ __('Enter the reason for suspension...') }}" required>{{ old('reason') }}</textarea>
                        @error('reason')
                            <div class="text-danger mt-1" style="font-size: 0.875rem;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-slash-circle me-1"></i>{{ __('Suspend User') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
