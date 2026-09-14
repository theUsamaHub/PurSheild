@extends('layouts.vet.app')

@section('content')
<div class="fade-in">
    <div class="mb-4">
        <a href="{{ route('vet.appointments.show', $appointment) }}" class="text-decoration-none text-muted" style="font-size:0.85rem;">
            <i class="bi bi-arrow-left me-1"></i>{{ __('Back to Appointment') }}
        </a>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-clipboard2-pulse me-2"></i>{{ __('Record Treatment') }}</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('vet.treatments.store', $appointment) }}" method="POST" id="treatmentForm">
                        @csrf

                        <h6 class="fw-semibold mb-3"><i class="bi bi-search me-2"></i>{{ __('Examination') }}</h6>
                        <div class="mb-3">
                            <label for="symptoms" class="form-label fw-semibold">{{ __('Symptoms') }} <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('symptoms') is-invalid @enderror" id="symptoms" name="symptoms" rows="3" required
                                      placeholder="{{ __('Describe the symptoms observed...') }}">{{ old('symptoms') }}</textarea>
                            @error('symptoms') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label for="diagnosis" class="form-label fw-semibold">{{ __('Diagnosis') }} <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('diagnosis') is-invalid @enderror" id="diagnosis" name="diagnosis" rows="3" required
                                      placeholder="{{ __('What is your diagnosis?') }}">{{ old('diagnosis') }}</textarea>
                            @error('diagnosis') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <hr>

                        <h6 class="fw-semibold mb-3"><i class="bi bi-capsule me-2"></i>{{ __('Treatment') }}</h6>
                        <div class="mb-3">
                            <label for="treatment" class="form-label fw-semibold">{{ __('Treatment Given') }} <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('treatment') is-invalid @enderror" id="treatment" name="treatment" rows="3" required
                                      placeholder="{{ __('Describe the treatment performed...') }}">{{ old('treatment') }}</textarea>
                            @error('treatment') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="follow_up_date" class="form-label">{{ __('Follow-up Date') }}</label>
                                <input type="date" class="form-control @error('follow_up_date') is-invalid @enderror" id="follow_up_date" name="follow_up_date"
                                       value="{{ old('follow_up_date') }}" min="{{ now()->toDateString() }}">
                                @error('follow_up_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="notes" class="form-label">{{ __('Additional Notes') }}</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes"
                                          rows="2" maxlength="2000"
                                          placeholder="{{ __('Any additional notes or observations...') }}">{{ old('notes') }}</textarea>
                                @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <hr>

                        <h6 class="fw-semibold mb-3">
                            <i class="bi bi-capsule me-2"></i>{{ __('Prescriptions') }} <small class="text-muted fw-normal">({{ __('Optional') }})</small>
                            <button type="button" class="btn btn-sm btn-outline-success ms-2" onclick="addPrescription()">
                                <i class="bi bi-plus"></i> {{ __('Add Prescription') }}
                            </button>
                        </h6>
                        <div id="prescriptions-container"></div>

                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i>{{ __('Save Treatment & Complete Appointment') }}
                            </button>
                            <a href="{{ route('vet.appointments.show', $appointment) }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            {{-- Pet Summary --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-heart-pulse me-2"></i>{{ __('Patient') }}</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        @include('vet.partials.pet-avatar', ['pet' => $appointment->pet])
                        <div class="ms-3">
                            <div class="fw-semibold">{{ $appointment->pet->name }}</div>
                            <small class="text-muted">{{ $appointment->pet->species?->name ?? '' }}</small>
                        </div>
                    </div>
                    <div class="text-muted mb-1" style="font-size:0.8rem;"><strong>{{ __('Owner') }}:</strong> {{ $appointment->owner->name }}</div>
                    <div class="text-muted mb-1" style="font-size:0.8rem;"><strong>{{ __('Reason') }}:</strong> {{ $appointment->reason ?? '-' }}</div>
                </div>
            </div>

            {{-- Past Health Records --}}
            @if($appointment->pet->healthRecords->count())
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0 fw-semibold">{{ __('Past Health Records') }}</h6>
                    </div>
                    <div class="card-body p-0">
                        @foreach($appointment->pet->healthRecords->take(5) as $record)
                            <div class="px-3 py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                                <span class="badge bg-info bg-opacity-10 text-info mb-1">{{ ucfirst($record->record_type) }}</span>
                                <div style="font-size:0.8rem;">{{ Str::limit($record->description, 80) }}</div>
                                <small class="text-muted">{{ $record->record_date->format('M d, Y') }}</small>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
let prescriptionIndex = 1;

function addPrescription(values = {}) {
    const container = document.getElementById('prescriptions-container');
    const html = `
        <div class="prescription-entry card mb-3" data-index="${prescriptionIndex}">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label" style="font-size:0.8rem;">Medicine Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm" name="prescriptions[${prescriptionIndex}][medicine_name]" required placeholder="e.g. Amoxicillin">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" style="font-size:0.8rem;">Dosage <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm" name="prescriptions[${prescriptionIndex}][dosage]" required placeholder="e.g. 250mg">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" style="font-size:0.8rem;">Frequency <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm" name="prescriptions[${prescriptionIndex}][frequency]" required placeholder="e.g. Twice daily">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" style="font-size:0.8rem;">Duration <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm" name="prescriptions[${prescriptionIndex}][duration]" required placeholder="e.g. 7 days">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" style="font-size:0.8rem;">Instructions</label>
                        <input type="text" class="form-control form-control-sm" name="prescriptions[${prescriptionIndex}][instructions]" placeholder="e.g. Take with food">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="button" class="btn btn-sm btn-outline-danger w-100" onclick="removePrescription(this)">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
    const entry = container.lastElementChild;
    Object.entries(values).forEach(([field, value]) => {
        const input = entry.querySelector(`[name="prescriptions[${prescriptionIndex}][${field}]"]`);
        if (input) input.value = value ?? '';
    });
    prescriptionIndex++;
}

function removePrescription(btn) {
    btn.closest('.prescription-entry').remove();
}
Object.values(@json(old('prescriptions', []))).forEach(values => addPrescription(values));
</script>
@endpush
@endsection
