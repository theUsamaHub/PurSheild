@extends('layouts.vet.app')

@section('content')
<div class="fade-in">
    <div class="mb-4">
        <a href="{{ route('vet.treatments.index') }}" class="text-decoration-none text-muted" style="font-size:0.85rem;">
            <i class="bi bi-arrow-left me-1"></i>{{ __('Back to Treatments') }}
        </a>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-clipboard2-pulse me-2"></i>{{ __('Treatment Record') }}</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="text-muted" style="font-size:0.8rem;">{{ __('Date') }}</label>
                            <div class="fw-semibold">{{ $treatment->created_at->format('M d, Y \a\t g:i A') }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted" style="font-size:0.8rem;">{{ __('Appointment') }}</label>
                            <div class="fw-semibold">{{ $treatment->appointment->appointment_date->format('M d, Y') }} at {{ $treatment->appointment->appointment_time }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted" style="font-size:0.8rem;">{{ __('Pet') }}</label>
                            <div class="fw-semibold">{{ $treatment->appointment->pet->name }}</div>
                            <small class="text-muted">{{ $treatment->appointment->pet->species?->name ?? '' }}</small>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted" style="font-size:0.8rem;">{{ __('Owner') }}</label>
                            <div class="fw-semibold">{{ $treatment->appointment->owner->name }}</div>
                            <small class="text-muted">{{ $treatment->appointment->owner->email }}</small>
                        </div>
                        <div class="col-12">
                            <label class="text-muted" style="font-size:0.8rem;">{{ __('Symptoms') }}</label>
                            <div style="white-space:pre-line;">{{ $treatment->symptoms }}</div>
                        </div>
                        <div class="col-12">
                            <label class="text-muted" style="font-size:0.8rem;">{{ __('Diagnosis') }}</label>
                            <div style="white-space:pre-line;">{{ $treatment->diagnosis }}</div>
                        </div>
                        <div class="col-12">
                            <label class="text-muted" style="font-size:0.8rem;">{{ __('Treatment Given') }}</label>
                            <div style="white-space:pre-line;">{{ $treatment->treatment }}</div>
                        </div>
                        @if($treatment->follow_up_date)
                            <div class="col-md-6">
                                <label class="text-muted" style="font-size:0.8rem;">{{ __('Follow-up Date') }}</label>
                                <div class="fw-semibold"><i class="bi bi-calendar me-1"></i>{{ $treatment->follow_up_date->format('M d, Y') }}</div>
                            </div>
                        @endif
                        @if($treatment->notes)
                            <div class="col-12">
                                <label class="text-muted" style="font-size:0.8rem;">{{ __('Notes') }}</label>
                                <div style="white-space:pre-line;">{{ $treatment->notes }}</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Prescriptions --}}
            @if($treatment->prescriptions->count())
                <div class="card mt-4">
                    <div class="card-header">
                        <h6 class="mb-0 fw-semibold"><i class="bi bi-capsule me-2"></i>{{ __('Prescriptions') }} ({{ $treatment->prescriptions->count() }})</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="font-size:0.8rem;">{{ __('Medicine') }}</th>
                                        <th style="font-size:0.8rem;">{{ __('Dosage') }}</th>
                                        <th style="font-size:0.8rem;">{{ __('Frequency') }}</th>
                                        <th style="font-size:0.8rem;">{{ __('Duration') }}</th>
                                        <th style="font-size:0.8rem;">{{ __('Instructions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($treatment->prescriptions as $rx)
                                        <tr>
                                            <td class="fw-semibold" style="font-size:0.85rem;">{{ $rx->medicine_name }}</td>
                                            <td style="font-size:0.85rem;">{{ $rx->dosage }}</td>
                                            <td style="font-size:0.85rem;">{{ $rx->frequency }}</td>
                                            <td style="font-size:0.85rem;">{{ $rx->duration }}</td>
                                            <td style="font-size:0.85rem;">{{ $rx->instructions ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0 fw-semibold">{{ __('Patient') }}</h6>
                </div>
                <div class="card-body text-center">
                    @if($treatment->appointment->pet->profile_image)
                        <img src="{{ asset('uploads/pets/' . $treatment->appointment->pet->profile_image) }}" class="rounded-circle mb-2" style="width:60px;height:60px;object-fit:cover;">
                    @else
                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width:60px;height:60px;background:linear-gradient(135deg,#1a6b3c,#2e9e5a);">
                            <span class="text-white fw-bold fs-5">{{ substr($treatment->appointment->pet->name, 0, 1) }}</span>
                        </div>
                    @endif
                    <div class="fw-semibold">{{ $treatment->appointment->pet->name }}</div>
                    <small class="text-muted">{{ $treatment->appointment->pet->species?->name ?? '' }}</small>
                    <hr>
                    <a href="{{ route('vet.patients.show', $treatment->appointment->pet) }}" class="btn btn-sm btn-outline-primary w-100">
                        <i class="bi bi-folder2-open me-1"></i>{{ __('View Full History') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
