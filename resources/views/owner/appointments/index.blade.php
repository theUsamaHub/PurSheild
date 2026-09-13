@extends('layouts.owner.app')

@section('content')
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 mb-0 fw-semibold">{{ __('My Appointments') }}</h2>
            <a href="{{ route('owner.appointments.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-circle me-1"></i>{{ __('Book Appointment') }}
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-start border-4 h-100" style="border-left-color: #1a6b3c !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted" style="font-size:0.75rem;">{{ __('Total') }}</div>
                            <div class="fs-4 fw-bold">{{ $stats['total'] ?? 0 }}</div>
                        </div>
                        <div class="rounded-3 d-flex align-items-center justify-content-center" style="width:40px;height:40px;background:rgba(26,107,60,0.1);">
                            <i class="bi bi-calendar-check" style="color:#1a6b3c;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-start border-4 h-100" style="border-left-color: #ffc107 !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted" style="font-size:0.75rem;">{{ __('Pending') }}</div>
                            <div class="fs-4 fw-bold">{{ $stats['pending'] ?? 0 }}</div>
                        </div>
                        <div class="rounded-3 d-flex align-items-center justify-content-center" style="width:40px;height:40px;background:rgba(255,193,7,0.1);">
                            <i class="bi bi-clock-history text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-start border-4 h-100" style="border-left-color: #0d6efd !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted" style="font-size:0.75rem;">{{ __('Approved') }}</div>
                            <div class="fs-4 fw-bold">{{ $stats['approved'] ?? 0 }}</div>
                        </div>
                        <div class="rounded-3 d-flex align-items-center justify-content-center" style="width:40px;height:40px;background:rgba(13,110,253,0.1);">
                            <i class="bi bi-check-circle text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-start border-4 h-100" style="border-left-color: #198754 !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted" style="font-size:0.75rem;">{{ __('Completed') }}</div>
                            <div class="fs-4 fw-bold">{{ $stats['completed'] ?? 0 }}</div>
                        </div>
                        <div class="rounded-3 d-flex align-items-center justify-content-center" style="width:40px;height:40px;background:rgba(25,135,84,0.1);">
                            <i class="bi bi-check2-all text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('owner.appointments.index') }}" class="row g-3">
                <div class="col-md-4">
                    <select class="form-select" name="status">
                        <option value="">{{ __('All Status') }}</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>{{ __('Approved') }}</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>{{ __('Completed') }}</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>{{ __('Cancelled') }}</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-outline-secondary flex-grow-1">
                        <i class="bi bi-search me-1"></i>{{ __('Filter') }}
                    </button>
                    <a href="{{ route('owner.appointments.index') }}" class="btn btn-outline-danger"><i class="bi bi-x"></i></a>
                </div>
            </form>
        </div>
    </div>

    <!-- Appointments Table -->
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>{{ __('Pet') }}</th>
                            <th>{{ __('Vet') }}</th>
                            <th>{{ __('Date') }}</th>
                            <th>{{ __('Time') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Reason') }}</th>
                            <th class="text-end">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($appointments as $appointment)
                            <tr>
                                <td class="fw-medium">{{ $appointment->pet->name ?? '-' }}</td>
                                <td class="text-muted">{{ $appointment->vet->name ?? '-' }}</td>
                                <td class="text-muted">{{ $appointment->appointment_date ? \Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y') : '-' }}</td>
                                <td class="text-muted">{{ $appointment->appointment_time ?? '-' }}</td>
                                <td>
                                    @if ($appointment->status === 'pending')
                                        <span class="badge bg-warning">{{ __('Pending') }}</span>
                                    @elseif ($appointment->status === 'approved')
                                        <span class="badge bg-info">{{ __('Approved') }}</span>
                                    @elseif ($appointment->status === 'completed')
                                        <span class="badge bg-success">{{ __('Completed') }}</span>
                                    @elseif ($appointment->status === 'cancelled')
                                        <span class="badge bg-danger">{{ __('Cancelled') }}</span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst($appointment->status) }}</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="text-truncate d-inline-block" style="max-width:200px;" title="{{ $appointment->reason ?? '' }}">
                                        {{ $appointment->reason ?: '-' }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        @if ($appointment->status === 'pending')
                                            <form action="{{ route('owner.appointments.destroy', $appointment) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Are you sure you want to cancel this appointment?') }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger" title="{{ __('Cancel') }}">
                                                    <i class="bi bi-x-circle"></i>
                                                </button>
                                            </form>
                                        @endif
                                        @if ($appointment->vet_id && !in_array($appointment->vet_id, $reviewedVetIds))
                                            @if ($appointment->status === 'completed')
                                                <button type="button" class="btn btn-outline-success btn-sm" title="{{ __('Review Vet') }}" data-bs-toggle="modal" data-bs-target="#reviewModal{{ $appointment->vet_id }}">
                                                    <i class="bi bi-star me-1"></i>{{ __('Review') }}
                                                </button>
                                            @else
                                                <button type="button" class="btn btn-outline-secondary btn-sm" title="{{ __('Review available after appointment is completed') }}" disabled style="opacity:0.45;cursor:not-allowed;">
                                                    <i class="bi bi-star me-1"></i>{{ __('Review') }}
                                                </button>
                                            @endif
                                        @endif
                                        @if ($appointment->vet_id && in_array($appointment->vet_id, $reviewedVetIds))
                                            <span class="btn btn-outline-success btn-sm" title="{{ __('Already Reviewed') }}" style="opacity:0.6;cursor:default;">
                                                <i class="bi bi-star-fill me-1"></i>{{ __('Reviewed') }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="empty-state">
                                        <i class="bi bi-calendar-x" style="font-size:3rem;opacity:0.3;"></i>
                                        <p class="mt-2 text-muted">{{ __('No appointments found.') }}</p>
                                        <a href="{{ route('owner.appointments.create') }}" class="btn btn-primary btn-sm mt-2">
                                            <i class="bi bi-plus-circle me-1"></i>{{ __('Book Your First Appointment') }}
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($appointments->hasPages())
            <div class="card-footer bg-white">
                {{ $appointments->links() }}
            </div>
        @endif
    </div>

    <!-- Review Modals for completed appointments -->
    @foreach($appointments->where('status', 'completed')->whereNotNull('vet_id')->unique('vet_id') as $appointment)
        @if(!in_array($appointment->vet_id, $reviewedVetIds))
            <div class="modal fade" id="reviewModal{{ $appointment->vet_id }}" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form action="{{ route('owner.reviews.store', $appointment->vet_id) }}" method="POST">
                            @csrf
                            <div class="modal-header">
                                <h5 class="modal-title fw-semibold">{{ __('Review') }} {{ $appointment->vet->name ?? '' }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">{{ __('Rating') }} *</label>
                                    <div class="d-flex gap-1" x-data="{ rating: 0 }">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <button type="button" class="btn btn-link p-0 text-warning" style="font-size:1.5rem;text-decoration:none;" x-on:click="rating = {{ $i }}">
                                                <i class="bi" :class="rating >= {{ $i }} ? 'bi-star-fill' : 'bi-star'"></i>
                                            </button>
                                        @endfor                                        <input type="hidden" name="rating" :value="rating" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">{{ __('Comment') }}</label>
                                    <textarea class="form-control" name="comment" rows="4" placeholder="{{ __('Share your experience...') }}">{{ old('comment') }}</textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="bi bi-send me-1"></i>{{ __('Submit Review') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
@endsection
