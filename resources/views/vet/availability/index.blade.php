@extends('layouts.vet.app')

@section('content')
<div class="fade-in">
    <div class="mb-4">
        <h4 class="mb-1 fw-bold">{{ __('Availability Schedule') }}</h4>
        <p class="text-muted mb-0" style="font-size:0.875rem;">{{ __('Set your weekly availability for appointments') }}</p>
    </div>

    <form action="{{ route('vet.availability.update') }}" method="POST" id="availabilityForm">
        @csrf
        @method('PUT')

        <div class="card">
            <div class="card-body">
                @foreach($days as $index => $day)
                    @php $slot = $availability->get($day); @endphp
                    <div class="row align-items-center py-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                        <div class="col-md-2">
                            <div class="form-check">
                                <input class="form-check-input day-toggle" type="checkbox"
                                       name="availability[{{ $index }}][is_available]" value="1"
                                       id="day_{{ $index }}" {{ $slot && $slot->is_available ? 'checked' : '' }}
                                       data-index="{{ $index }}">
                                <label class="form-check-label fw-semibold" for="day_{{ $index }}">{{ ucfirst(__($day)) }}</label>
                            </div>
                        </div>
                        <div class="col-md-8 time-inputs" id="time_inputs_{{ $index }}" style="{{ $slot && $slot->is_available ? '' : 'opacity:0.4;pointer-events:none;' }}">
                            <div class="d-flex align-items-center gap-2">
                                <input type="time" class="form-control form-control-sm time-field" style="width:auto;"
                                       name="availability[{{ $index }}][start_time]"
                                       id="start_{{ $index }}"
                                       value="{{ $slot->start_time ?? '09:00' }}"
                                       {{ !($slot && $slot->is_available) ? 'disabled' : '' }}>
                                <span class="text-muted">to</span>
                                <input type="time" class="form-control form-control-sm time-field" style="width:auto;"
                                       name="availability[{{ $index }}][end_time]"
                                       id="end_{{ $index }}"
                                       value="{{ $slot->end_time ?? '17:00' }}"
                                       {{ !($slot && $slot->is_available) ? 'disabled' : '' }}>
                            </div>
                        </div>
                        <div class="col-md-2 text-end" id="status_{{ $index }}">
                            @if($slot && $slot->is_available)
                                <span class="badge bg-success">{{ __('Available') }}</span>
                            @else
                                <span class="badge bg-secondary">{{ __('Unavailable') }}</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="bi bi-check-lg me-1"></i>{{ __('Save Schedule') }}
                </button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.querySelectorAll('.day-toggle').forEach(function(checkbox) {
    checkbox.addEventListener('change', function() {
        const index = this.dataset.index;
        const timeInputs = document.getElementById('time_inputs_' + index);
        const startInput = document.getElementById('start_' + index);
        const endInput = document.getElementById('end_' + index);
        const status = document.getElementById('status_' + index);

        if (this.checked) {
            timeInputs.style.opacity = '1';
            timeInputs.style.pointerEvents = 'auto';
            startInput.disabled = false;
            endInput.disabled = false;
            status.innerHTML = '<span class="badge bg-success">{{ __("Available") }}</span>';
        } else {
            timeInputs.style.opacity = '0.4';
            timeInputs.style.pointerEvents = 'none';
            startInput.disabled = true;
            endInput.disabled = true;
            status.innerHTML = '<span class="badge bg-secondary">{{ __("Unavailable") }}</span>';
        }
    });
});
</script>
@endpush
@endsection
