@extends('layouts.vet.app')

@section('content')
<div class="fade-in">
    <div class="mb-4">
        <a href="{{ route('vet.appointments.index') }}" class="text-decoration-none text-muted" style="font-size:0.85rem;">
            <i class="bi bi-arrow-left me-1"></i>{{ __('Back to Appointments') }}
        </a>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            {{-- Appointment Info --}}
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-semibold">{{ __('Appointment Details') }}</h6>
                    <span class="badge bg-{{ $appointment->status === 'pending' ? 'warning text-dark' : ($appointment->status === 'approved' ? 'success' : ($appointment->status === 'completed' ? 'info' : 'secondary')) }} fs-6">
                        {{ ucfirst($appointment->status) }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="text-muted" style="font-size:0.8rem;">{{ __('Pet') }}</label>
                            <div class="fw-semibold">{{ $appointment->pet->name }}</div>
                            <small class="text-muted">{{ $appointment->pet->species?->name ?? '' }} {{ $appointment->pet->breed ? '- ' . $appointment->pet->breed->name : '' }}</small>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted" style="font-size:0.8rem;">{{ __('Owner') }}</label>
                            <div class="fw-semibold">{{ $appointment->owner->name }}</div>
                            <small class="text-muted">{{ $appointment->owner->email }}</small>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted" style="font-size:0.8rem;">{{ __('Date & Time') }}</label>
                            <div class="fw-semibold">{{ $appointment->appointment_date->format('l, M d, Y') }} at {{ $appointment->appointment_time }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted" style="font-size:0.8rem;">{{ __('Reason') }}</label>
                            <div>{{ $appointment->reason ?? 'No reason provided' }}</div>
                        </div>
                        @if($appointment->notes)
                            <div class="col-12">
                                <label class="text-muted" style="font-size:0.8rem;">{{ __('Notes') }}</label>
                                <div>{{ $appointment->notes }}</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Status Actions --}}
            @if(in_array($appointment->status, ['pending', 'approved']))
                <div class="card mb-4 border-0" style="border-left:4px solid {{ $appointment->status === 'pending' ? 'var(--fs-warning)' : 'var(--fs-success)' }};">
                    <div class="card-header bg-transparent">
                        <h6 class="mb-0 fw-semibold"><i class="bi bi-arrow-repeat me-2"></i>{{ __('Update Status') }}</h6>
                    </div>
                    <div class="card-body">
                        @if($appointment->status === 'pending')
                            <p class="text-muted mb-3" style="font-size:0.85rem;">{{ __('This appointment is waiting for your approval.') }}</p>
                            <div class="d-flex gap-2">
                                <form action="{{ route('vet.appointments.approve', $appointment) }}" method="POST">
                                    @csrf @method('PUT')
                                    <button type="submit" class="btn btn-success btn-sm">
                                        <i class="bi bi-check-circle me-1"></i>{{ __('Approve') }}
                                    </button>
                                </form>
                                <form action="{{ route('vet.appointments.reject', $appointment) }}" method="POST">
                                    @csrf @method('PUT')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('{{ __('Are you sure you want to reject this appointment?') }}')">
                                        <i class="bi bi-x-circle me-1"></i>{{ __('Reject') }}
                                    </button>
                                </form>
                            </div>
                        @endif

                        @if($appointment->status === 'approved')
                            <p class="text-muted mb-3" style="font-size:0.85rem;">{{ __('This appointment is approved. Choose the next action:') }}</p>
                            <div class="d-flex flex-wrap gap-2">
                                @if(!$hasTreatment)
                                    <a href="{{ route('vet.treatments.create', $appointment) }}" class="btn btn-primary btn-sm">
                                        <i class="bi bi-clipboard2-pulse me-1"></i>{{ __('Record Treatment') }}
                                    </a>
                                @endif
                                <form action="{{ route('vet.appointments.updateStatus', $appointment) }}" method="POST" class="d-inline">
                                    @csrf @method('PUT')
                                    <input type="hidden" name="status" value="completed">
                                    <button type="submit" class="btn btn-success btn-sm" {{ !$hasTreatment ? 'disabled' : '' }}>
                                        <i class="bi bi-check2-all me-1"></i>{{ __('Mark Completed') }}
                                    </button>
                                </form>
                                <form action="{{ route('vet.appointments.updateStatus', $appointment) }}" method="POST" class="d-inline">
                                    @csrf @method('PUT')
                                    <input type="hidden" name="status" value="cancelled">
                                    <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('{{ __('Mark as no-show / cancelled?') }}')">
                                        <i class="bi bi-x-circle me-1"></i>{{ __('No-Show / Cancel') }}
                                    </button>
                                </form>
                            </div>
                            @if(!$hasTreatment)
                                <div class="alert alert-info mt-3 mb-0" style="font-size:0.8rem;">
                                    <i class="bi bi-info-circle me-1"></i>{{ __('You must record a treatment before marking as completed.') }}
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            @endif

            @if(in_array($appointment->status, ['completed', 'cancelled']))
                <div class="alert alert-secondary d-flex align-items-center mb-4" role="alert">
                    <i class="bi bi-info-circle me-2 fs-5"></i>
                    <div>
                        @if($appointment->status === 'completed')
                            {{ __('This appointment has been completed.') }}
                        @else
                            {{ __('This appointment has been cancelled.') }}
                        @endif
                    </div>
                </div>
            @endif

            {{-- Existing Treatment --}}
            @if($appointment->treatment)
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-semibold"><i class="bi bi-clipboard2-pulse me-2"></i>{{ __('Treatment Record') }}</h6>
                        <a href="{{ route('vet.treatments.show', $appointment->treatment) }}" class="btn btn-sm btn-outline-primary">{{ __('View Full') }}</a>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="text-muted" style="font-size:0.8rem;">{{ __('Symptoms') }}</label>
                                <div style="font-size:0.9rem;">{{ $appointment->treatment->symptoms }}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted" style="font-size:0.8rem;">{{ __('Diagnosis') }}</label>
                                <div style="font-size:0.9rem;">{{ $appointment->treatment->diagnosis }}</div>
                            </div>
                            <div class="col-12">
                                <label class="text-muted" style="font-size:0.8rem;">{{ __('Treatment Given') }}</label>
                                <div style="font-size:0.9rem;">{{ $appointment->treatment->treatment }}</div>
                            </div>
                            @if($appointment->treatment->follow_up_date)
                                <div class="col-md-6">
                                    <label class="text-muted" style="font-size:0.8rem;">{{ __('Follow-up Date') }}</label>
                                    <div style="font-size:0.9rem;">{{ $appointment->treatment->follow_up_date->format('M d, Y') }}</div>
                                </div>
                            @endif
                        </div>
                        @if($appointment->treatment->prescriptions->count())
                            <hr>
                            <label class="text-muted mb-2" style="font-size:0.8rem;">{{ __('Prescriptions') }}</label>
                            <div class="table-responsive">
                                <table class="table table-sm mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="font-size:0.75rem;">{{ __('Medicine') }}</th>
                                            <th style="font-size:0.75rem;">{{ __('Dosage') }}</th>
                                            <th style="font-size:0.75rem;">{{ __('Frequency') }}</th>
                                            <th style="font-size:0.75rem;">{{ __('Duration') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($appointment->treatment->prescriptions as $rx)
                                            <tr>
                                                <td style="font-size:0.8rem;">{{ $rx->medicine_name }}</td>
                                                <td style="font-size:0.8rem;">{{ $rx->dosage }}</td>
                                                <td style="font-size:0.8rem;">{{ $rx->frequency }}</td>
                                                <td style="font-size:0.8rem;">{{ $rx->duration }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        {{-- Pet Medical History --}}
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-heart-pulse me-2"></i>{{ __('Pet Info') }}</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        @if($appointment->pet->profile_image)
                            <img src="{{ asset('uploads/pets/' . $appointment->pet->profile_image) }}" alt="{{ $appointment->pet->name }}" class="rounded-circle" style="width:80px;height:80px;object-fit:cover;">
                        @else
                            <div class="rounded-circle d-inline-flex align-items-center justify-content-center" style="width:80px;height:80px;background:linear-gradient(135deg,#1a6b3c,#2e9e5a);">
                                <span class="text-white fw-bold fs-4">{{ substr($appointment->pet->name, 0, 1) }}</span>
                            </div>
                        @endif
                        <h6 class="mt-2 mb-0">{{ $appointment->pet->name }}</h6>
                        <small class="text-muted">{{ $appointment->pet->species?->name ?? '' }} {{ $appointment->pet->breed ? '- ' . $appointment->pet->breed->name : '' }}</small>
                    </div>
                    <div class="d-flex justify-content-between mb-1" style="font-size:0.8rem;">
                        <span class="text-muted">{{ __('Gender') }}</span>
                        <span>{{ ucfirst($appointment->pet->gender ?? '-') }}</span>
                    </div>
                    @if($appointment->pet->weight)
                        <div class="d-flex justify-content-between mb-1" style="font-size:0.8rem;">
                            <span class="text-muted">{{ __('Weight') }}</span>
                            <span>{{ $appointment->pet->weight }} kg</span>
                        </div>
                    @endif
                    @if($appointment->pet->color)
                        <div class="d-flex justify-content-between mb-1" style="font-size:0.8rem;">
                            <span class="text-muted">{{ __('Color') }}</span>
                            <span>{{ $appointment->pet->color }}</span>
                        </div>
                    @endif
                    <a href="{{ route('vet.patients.show', $appointment->pet) }}" class="btn btn-sm btn-outline-primary w-100 mt-3">
                        <i class="bi bi-folder2-open me-1"></i>{{ __('Full Medical History') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
