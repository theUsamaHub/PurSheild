@extends('layouts.vet.app')

@section('content')
<div class="fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 fw-bold">{{ __('Veterinarian Dashboard') }}</h4>
            <p class="text-muted mb-0" style="font-size:0.875rem;">{{ __('Welcome back, ') }}{{ Auth::user()->name }}!</p>
        </div>
    </div>

    @if(Auth::user()->status === 'pending_verification')
        <div class="alert alert-warning d-flex align-items-center" role="alert">
            <i class="bi bi-clock-history me-2 fs-5"></i>
            <div>
                <strong>{{ __('Pending Verification') }}</strong> - {{ __('Your account is being reviewed by admin. You can edit your profile but cannot appear in public listings until verified.') }}
            </div>
        </div>
    @endif

    {{-- Stats --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0" style="border-left:4px solid var(--fs-primary);">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:var(--fs-primary-bg);">
                            <i class="bi bi-calendar-check text-primary fs-5"></i>
                        </div>
                        <div class="ms-3">
                            <div class="text-muted" style="font-size:0.75rem;">{{ __("Today's Appointments") }}</div>
                            <div class="fw-bold fs-4" style="color:var(--fs-text-heading);">{{ $todayAppointments->count() }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0" style="border-left:4px solid var(--fs-info);">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:#e0f2fe;">
                            <i class="bi bi-clock text-info fs-5"></i>
                        </div>
                        <div class="ms-3">
                            <div class="text-muted" style="font-size:0.75rem;">{{ __('Pending Requests') }}</div>
                            <div class="fw-bold fs-4" style="color:var(--fs-text-heading);">{{ $pendingCount }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0" style="border-left:4px solid var(--fs-success);">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:#dcfce7;">
                            <i class="bi bi-check-circle text-success fs-5"></i>
                        </div>
                        <div class="ms-3">
                            <div class="text-muted" style="font-size:0.75rem;">{{ __('Completed') }}</div>
                            <div class="fw-bold fs-4" style="color:var(--fs-text-heading);">{{ $completedCount }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0" style="border-left:4px solid var(--fs-accent);">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:#fef3c7;">
                            <i class="bi bi-star text-warning fs-5"></i>
                        </div>
                        <div class="ms-3">
                            <div class="text-muted" style="font-size:0.75rem;">{{ __('Avg Rating') }}</div>
                            <div class="fw-bold fs-4" style="color:var(--fs-text-heading);">{{ $avgRating ? number_format($avgRating, 1) : '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Upcoming Appointments --}}
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-semibold">{{ __('Upcoming Appointments') }}</h6>
                    <a href="{{ route('vet.appointments.index') }}" class="btn btn-sm btn-outline-primary">{{ __('View All') }}</a>
                </div>
                <div class="card-body p-0">
                    @if($upcomingAppointments->count())
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="font-size:0.8rem;">{{ __('Pet') }}</th>
                                        <th style="font-size:0.8rem;">{{ __('Owner') }}</th>
                                        <th style="font-size:0.8rem;">{{ __('Date') }}</th>
                                        <th style="font-size:0.8rem;">{{ __('Time') }}</th>
                                        <th style="font-size:0.8rem;">{{ __('Status') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($upcomingAppointments as $apt)
                                        <tr>
                                            <td>
                                                <a href="{{ route('vet.appointments.show', $apt) }}" class="text-decoration-none fw-semibold" style="font-size:0.85rem;">
                                                    {{ $apt->pet->name }}
                                                </a>
                                                <div class="text-muted" style="font-size:0.75rem;">{{ $apt->pet->species?->name ?? '' }}</div>
                                            </td>
                                            <td style="font-size:0.85rem;">{{ $apt->owner->name }}</td>
                                            <td style="font-size:0.85rem;">{{ $apt->appointment_date->format('M d, Y') }}</td>
                                            <td style="font-size:0.85rem;">{{ $apt->appointment_time }}</td>
                                            <td>
                                                @if($apt->status === 'pending')
                                                    <span class="badge bg-warning text-dark">{{ __('Pending') }}</span>
                                                @elseif($apt->status === 'approved')
                                                    <span class="badge bg-success">{{ __('Approved') }}</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ ucfirst($apt->status) }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="bi bi-calendar-x d-block"></i>
                            <p>{{ __('No upcoming appointments.') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Right Column --}}
        <div class="col-lg-4">
            {{-- Verification Status --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0 fw-semibold">{{ __('Verification Status') }}</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:#e0f2fe;">
                            <i class="bi bi-person-badge text-info fs-5"></i>
                        </div>
                        <div class="ms-3">
                            <div class="fw-semibold">{{ Auth::user()->name }}</div>
                            <div class="text-muted" style="font-size:0.8rem;">{{ __('Veterinarian') }}</div>
                        </div>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted" style="font-size:0.8rem;">{{ __('Status') }}</span>
                        @if(Auth::user()->status === 'active')
                            <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>{{ __('Verified') }}</span>
                        @else
                            <span class="badge bg-warning text-dark"><i class="bi bi-clock me-1"></i>{{ __('Pending') }}</span>
                        @endif
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted" style="font-size:0.8rem;">{{ __('Total Patients') }}</span>
                        <span style="font-size:0.8rem;">{{ $totalPatients }}</span>
                    </div>
                    <a href="{{ route('profile.edit') }}" class="btn btn-sm btn-outline-primary mt-2 w-100">
                        <i class="bi bi-pencil me-1"></i>{{ __('Edit Profile') }}
                    </a>
                </div>
            </div>

            {{-- Recent Reviews --}}
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-semibold">{{ __('Recent Reviews') }}</h6>
                    <a href="{{ route('vet.reviews.index') }}" class="btn btn-sm btn-outline-primary">{{ __('View All') }}</a>
                </div>
                <div class="card-body p-0">
                    @if($recentReviews->count())
                        @foreach($recentReviews as $review)
                            <div class="px-3 py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <small class="fw-semibold">{{ $review->user->name ?? 'Anonymous' }}</small>
                                    <small class="text-warning">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $review->rating)
                                                <i class="bi bi-star-fill"></i>
                                            @else
                                                <i class="bi bi-star"></i>
                                            @endif
                                        @endfor
                                    </small>
                                </div>
                                @if($review->comment)
                                    <p class="text-muted mb-0" style="font-size:0.8rem;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">{{ $review->comment }}</p>
                                @endif
                                <small class="text-muted" style="font-size:0.7rem;">{{ $review->created_at->diffForHumans() }}</small>
                            </div>
                        @endforeach
                    @else
                        <div class="empty-state py-3">
                            <p class="mb-0">{{ __('No reviews yet.') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
