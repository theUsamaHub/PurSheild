@extends('layouts.vet.app')

@section('content')
<div class="fade-in">
    <div class="mb-4">
        <a href="{{ route('vet.patients.index') }}" class="text-decoration-none text-muted" style="font-size:0.85rem;">
            <i class="bi bi-arrow-left me-1"></i>{{ __('Back to Patients') }}
        </a>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            {{-- Pet Info Card --}}
            <div class="card mb-4">
                <div class="card-body text-center">
                    @if($pet->profile_image)
                        <img src="{{ asset('uploads/pets/' . $pet->profile_image) }}" alt="{{ $pet->name }}" class="rounded-circle mb-3" style="width:100px;height:100px;object-fit:cover;">
                    @else
                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:100px;height:100px;background:linear-gradient(135deg,#1a6b3c,#2e9e5a);">
                            <span class="text-white fw-bold fs-2">{{ substr($pet->name, 0, 1) }}</span>
                        </div>
                    @endif
                    <h5 class="fw-bold mb-1">{{ $pet->name }}</h5>
                    <p class="text-muted mb-3" style="font-size:0.85rem;">{{ $pet->species?->name ?? '' }} {{ $pet->breed ? '- ' . $pet->breed->name : '' }}</p>

                    <div class="row g-2 text-start">
                        <div class="col-6">
                            <div class="p-2 rounded bg-light">
                                <small class="text-muted d-block" style="font-size:0.7rem;">{{ __('Owner') }}</small>
                                <span style="font-size:0.8rem;">{{ $pet->owner->name }}</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 rounded bg-light">
                                <small class="text-muted d-block" style="font-size:0.7rem;">{{ __('Gender') }}</small>
                                <span style="font-size:0.8rem;">{{ ucfirst($pet->gender ?? '-') }}</span>
                            </div>
                        </div>
                        @if($pet->weight)
                            <div class="col-6">
                                <div class="p-2 rounded bg-light">
                                    <small class="text-muted d-block" style="font-size:0.7rem;">{{ __('Weight') }}</small>
                                    <span style="font-size:0.8rem;">{{ $pet->weight }} kg</span>
                                </div>
                            </div>
                        @endif
                        @if($pet->color)
                            <div class="col-6">
                                <div class="p-2 rounded bg-light">
                                    <small class="text-muted d-block" style="font-size:0.7rem;">{{ __('Color') }}</small>
                                    <span style="font-size:0.8rem;">{{ $pet->color }}</span>
                                </div>
                            </div>
                        @endif
                        @if($pet->date_of_birth)
                            <div class="col-6">
                                <div class="p-2 rounded bg-light">
                                    <small class="text-muted d-block" style="font-size:0.7rem;">{{ __('Date of Birth') }}</small>
                                    <span style="font-size:0.8rem;">{{ $pet->date_of_birth->format('M d, Y') }}</span>
                                </div>
                            </div>
                        @endif
                        @if($pet->microchip_number)
                            <div class="col-6">
                                <div class="p-2 rounded bg-light">
                                    <small class="text-muted d-block" style="font-size:0.7rem;">{{ __('Microchip') }}</small>
                                    <span style="font-size:0.8rem;">{{ $pet->microchip_number }}</span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Vaccinations --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-shield-check me-2"></i>{{ __('Vaccinations') }} ({{ $pet->vaccinations->count() }})</h6>
                </div>
                <div class="card-body p-0">
                    @if($pet->vaccinations->count())
                        @foreach($pet->vaccinations as $vax)
                            <div class="px-3 py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                                <div class="fw-semibold" style="font-size:0.85rem;">{{ $vax->vaccine_name }}</div>
                                <small class="text-muted">{{ $vax->vaccination_date->format('M d, Y') }}</small>
                                @if($vax->next_due_date)
                                    <br><small class="text-info"><i class="bi bi-clock me-1"></i>{{ __('Next due') }}: {{ $vax->next_due_date->format('M d, Y') }}</small>
                                @endif
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-3 text-muted" style="font-size:0.85rem;">{{ __('No vaccination records.') }}</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            {{-- Appointment History with Treatments --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-clock-history me-2"></i>{{ __('Appointment & Treatment History') }}</h6>
                </div>
                <div class="card-body p-0">
                    @if($appointments->count())
                        @foreach($appointments as $apt)
                            <div class="px-3 py-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <div class="fw-semibold" style="font-size:0.9rem;">
                                            {{ $apt->appointment_date->format('M d, Y') }} at {{ $apt->appointment_time }}
                                        </div>
                                        <small class="text-muted">{{ $apt->reason ?? 'No reason' }}</small>
                                    </div>
                                    <span class="badge bg-{{ $apt->status === 'completed' ? 'success' : ($apt->status === 'approved' ? 'info' : ($apt->status === 'pending' ? 'warning text-dark' : 'secondary')) }}">
                                        {{ ucfirst($apt->status) }}
                                    </span>
                                </div>

                                @if($apt->treatment)
                                    <div class="ms-3 p-3 rounded bg-light mt-2">
                                        <div class="row g-2">
                                            <div class="col-md-6">
                                                <small class="text-muted d-block" style="font-size:0.7rem;">{{ __('Diagnosis') }}</small>
                                                <span style="font-size:0.85rem;">{{ $apt->treatment->diagnosis }}</span>
                                            </div>
                                            <div class="col-md-6">
                                                <small class="text-muted d-block" style="font-size:0.7rem;">{{ __('Treatment') }}</small>
                                                <span style="font-size:0.85rem;">{{ Str::limit($apt->treatment->treatment, 100) }}</span>
                                            </div>
                                            @if($apt->treatment->prescriptions->count())
                                                <div class="col-12">
                                                    <small class="text-muted d-block mb-1" style="font-size:0.7rem;">{{ __('Prescriptions') }}:</small>
                                                    @foreach($apt->treatment->prescriptions as $rx)
                                                        <span class="badge bg-light text-dark me-1 mb-1" style="font-size:0.75rem;">
                                                            {{ $rx->medicine_name }} - {{ $rx->dosage }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                        @if($apt->treatment->follow_up_date)
                                            <small class="text-info mt-1 d-block"><i class="bi bi-calendar me-1"></i>{{ __('Follow-up') }}: {{ $apt->treatment->follow_up_date->format('M d, Y') }}</small>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-calendar-x d-block mb-2" style="font-size:2rem;opacity:0.3;"></i>
                            <p>{{ __('No appointment history with this pet.') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Health Records --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-folder2-open me-2"></i>{{ __('Health Records') }}</h6>
                </div>
                <div class="card-body p-0">
                    @if($pet->healthRecords->count())
                        @foreach($pet->healthRecords as $record)
                            <div class="px-3 py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <span class="badge bg-info bg-opacity-10 text-info mb-1">{{ ucfirst($record->record_type) }}</span>
                                        <div style="font-size:0.85rem;">{{ $record->description }}</div>
                                        @if($record->notes)
                                            <small class="text-muted">{{ $record->notes }}</small>
                                        @endif
                                    </div>
                                    <small class="text-muted">{{ $record->record_date->format('M d, Y') }}</small>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-3 text-muted" style="font-size:0.85rem;">{{ __('No health records.') }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
