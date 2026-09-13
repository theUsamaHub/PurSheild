@extends('layouts.vet.app')

@section('content')
<div class="fade-in">

    @if(Auth::user()->status === 'pending_verification')
        <div class="alert alert-warning d-flex align-items-center mb-4" role="alert">
            <i class="bi bi-clock-history me-2 fs-5"></i>
            <div>
                <strong>{{ __('Pending Verification') }}</strong> - {{ __('Your account is being reviewed by admin. You can edit your profile but cannot appear in public listings until verified.') }}
            </div>
        </div>
    @endif

    {{-- Welcome header --}}
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1">{{ __('Welcome, ') }}{{ Auth::user()->name }} <span>&#128075;</span></h3>
            <p class="text-muted mb-0">{{ __("Here's what's happening with your practice today.") }}</p>
        </div>
        <div class="text-md-end">
            <div class="fw-semibold" style="color:var(--pc-text-heading);">{{ now()->format('l, d F Y') }}</div>
            <div class="text-muted" style="font-size:.85rem;">
                <i class="bi bi-sun text-warning me-1"></i>{{ __('A healthier tomorrow for every pet') }}
            </div>
        </div>
    </div>

    {{-- Stat cards --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-xl-3">
            <div class="pc-card p-3">
                <div class="d-flex align-items-start justify-content-between">
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:var(--pc-success-bg);">
                        <i class="bi bi-calendar-check fs-5" style="color:var(--pc-success);"></i>
                    </div>
                    <a href="{{ route('vet.appointments.index', ['filter' => 'today']) }}" class="text-muted"><i class="bi bi-chevron-right"></i></a>
                </div>
                <div class="fw-bold fs-3 mt-3">{{ $todayAppointments->count() }}</div>
                <div class="text-muted mb-1" style="font-size:.85rem;">{{ __("Today's Appointments") }}</div>
                <a href="{{ route('vet.appointments.index', ['filter' => 'today']) }}" class="text-decoration-none fw-semibold" style="font-size:.8rem;color:var(--pc-success);">{{ __('View all') }} <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="pc-card p-3">
                <div class="d-flex align-items-start justify-content-between">
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:var(--pc-warning-bg);">
                        <i class="bi bi-clock fs-5" style="color:var(--pc-warning);"></i>
                    </div>
                    <a href="{{ route('vet.appointments.index', ['status' => 'pending']) }}" class="text-muted"><i class="bi bi-chevron-right"></i></a>
                </div>
                <div class="fw-bold fs-3 mt-3">{{ $pendingCount }}</div>
                <div class="text-muted mb-1" style="font-size:.85rem;">{{ __('Pending Requests') }}</div>
                <a href="{{ route('vet.appointments.index', ['status' => 'pending']) }}" class="text-decoration-none fw-semibold" style="font-size:.8rem;color:var(--pc-warning);">{{ __('View all') }} <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="pc-card p-3">
                <div class="d-flex align-items-start justify-content-between">
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:var(--pc-info-bg);">
                        <i class="bi bi-calendar3 fs-5" style="color:var(--pc-info);"></i>
                    </div>
                    <a href="{{ route('vet.appointments.index', ['filter' => 'upcoming']) }}" class="text-muted"><i class="bi bi-chevron-right"></i></a>
                </div>
                <div class="fw-bold fs-3 mt-3">{{ $upcomingAppointments->count() }}</div>
                <div class="text-muted mb-1" style="font-size:.85rem;">{{ __('Upcoming Appointments') }}</div>
                <a href="{{ route('vet.appointments.index', ['filter' => 'upcoming']) }}" class="text-decoration-none fw-semibold" style="font-size:.8rem;color:var(--pc-info);">{{ __('View all') }} <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="pc-card p-3">
                <div class="d-flex align-items-start justify-content-between">
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:var(--pc-purple-bg);">
                        <i class="bi bi-check2-circle fs-5" style="color:var(--pc-purple);"></i>
                    </div>
                    <a href="{{ route('vet.appointments.index', ['status' => 'completed']) }}" class="text-muted"><i class="bi bi-chevron-right"></i></a>
                </div>
                <div class="fw-bold fs-3 mt-3">{{ $completedCount }}</div>
                <div class="text-muted mb-1" style="font-size:.85rem;">{{ __('Completed Appointments') }}</div>
                <a href="{{ route('vet.appointments.index', ['status' => 'completed']) }}" class="text-decoration-none fw-semibold" style="font-size:.8rem;color:var(--pc-purple);">{{ __('View all') }} <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Today's Appointments table --}}
        <div class="col-lg-8">
            <div class="pc-card">
                <div class="pc-card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-calendar-check me-2" style="color:var(--pc-success);"></i>{{ __("Today's Appointments") }}</h6>
                    <a href="{{ route('vet.appointments.index', ['filter' => 'today']) }}" class="btn btn-sm" style="background:var(--pc-success-bg);color:var(--pc-success);border-radius:.6rem;">
                        {{ __('View All') }} <i class="bi bi-chevron-right"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    @if($todayAppointments->count())
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 align-middle">
                                <thead>
                                    <tr class="text-muted text-uppercase" style="font-size:.7rem;letter-spacing:.04em;">
                                        <th class="ps-3">{{ __('Time') }}</th>
                                        <th>{{ __('Pet') }}</th>
                                        <th>{{ __('Owner') }}</th>
                                        <th>{{ __('Reason') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th class="text-end pe-3">{{ __('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($todayAppointments as $apt)
                                        <tr>
                                            <td class="ps-3 fw-semibold" style="font-size:.85rem;">{{ $apt->appointment_time }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:36px;height:36px;background:var(--pc-info-bg);color:var(--pc-info);font-weight:600;font-size:.8rem;">
                                                        {{ substr($apt->pet->name, 0, 1) }}
                                                    </div>
                                                    <div class="ms-2">
                                                        <div class="fw-semibold" style="font-size:.85rem;">{{ $apt->pet->name }}</div>
                                                        <div class="text-muted" style="font-size:.72rem;">{{ $apt->pet->species?->name ?? '' }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td style="font-size:.85rem;">
                                                {{ $apt->owner->name }}
                                                @if($apt->owner->phone ?? false)
                                                    <div class="text-muted" style="font-size:.72rem;">{{ $apt->owner->phone }}</div>
                                                @endif
                                            </td>
                                            <td style="font-size:.85rem;">{{ $apt->reason ?? $apt->notes ?? '-' }}</td>
                                            <td>
                                                @if($apt->status === 'pending')
                                                    <span class="badge rounded-pill" style="background:var(--pc-warning-bg);color:var(--pc-warning);">{{ __('Pending') }}</span>
                                                @elseif($apt->status === 'approved')
                                                    <span class="badge rounded-pill" style="background:var(--pc-success-bg);color:var(--pc-success);">{{ __('Approved') }}</span>
                                                @elseif($apt->status === 'cancelled')
                                                    <span class="badge rounded-pill" style="background:var(--pc-danger-bg);color:var(--pc-danger);">{{ __('Cancelled') }}</span>
                                                @else
                                                    <span class="badge rounded-pill bg-secondary">{{ ucfirst($apt->status) }}</span>
                                                @endif
                                            </td>
                                            <td class="text-end pe-3">
                                                <a href="{{ route('vet.appointments.show', $apt) }}" class="btn btn-sm btn-outline-success" style="border-radius:.6rem;">{{ __('View') }}</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-state py-5 text-center">
                            <i class="bi bi-calendar-x d-block fs-1 text-muted mb-2"></i>
                            <p class="text-muted mb-0">{{ __('No appointments scheduled for today.') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Right column --}}
        <div class="col-lg-4" id="notifications">
            {{-- Recent Notifications --}}
            <div class="pc-card mb-4">
                <div class="pc-card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-bell me-2" style="color:var(--pc-purple);"></i>{{ __('Recent Notifications') }}</h6>
                    <a href="{{ route('vet.notifications.index') ?? '#' }}" class="text-decoration-none fw-semibold" style="font-size:.8rem;color:var(--pc-success);">{{ __('View All') }} <i class="bi bi-chevron-right"></i></a>
                </div>
                <div class="card-body p-0">
                    @forelse(($recentNotifications ?? collect()) as $notification)
                        @php
                            $data = $notification->data ?? [];
                            $ntype = $data['type'] ?? 'default';
                            $iconMap = [
                                'request'   => ['bi-calendar-plus', 'var(--pc-purple)', 'var(--pc-purple-bg)'],
                                'approved'  => ['bi-check2-circle', 'var(--pc-success)', 'var(--pc-success-bg)'],
                                'review'    => ['bi-star-fill', 'var(--pc-warning)', 'var(--pc-warning-bg)'],
                                'cancelled' => ['bi-x-circle', 'var(--pc-danger)', 'var(--pc-danger-bg)'],
                                'default'   => ['bi-bell', 'var(--pc-info)', 'var(--pc-info-bg)'],
                            ];
                            [$icon, $iconColor, $iconBg] = $iconMap[$ntype] ?? $iconMap['default'];
                        @endphp
                        <div class="d-flex align-items-start px-3 py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:36px;height:36px;background:{{ $iconBg }};">
                                <i class="bi {{ $icon }}" style="color:{{ $iconColor }};font-size:.9rem;"></i>
                            </div>
                            <div class="ms-2 flex-grow-1">
                                <div class="fw-semibold" style="font-size:.83rem;">{{ $data['title'] ?? __('Notification') }}</div>
                                <div class="text-muted" style="font-size:.78rem;">{{ $data['message'] ?? '' }}</div>
                            </div>
                            <div class="text-muted flex-shrink-0" style="font-size:.7rem;white-space:nowrap;">{{ $notification->created_at->diffForHumans() }}</div>
                        </div>
                    @empty
                        <div class="empty-state py-4 text-center">
                            <p class="text-muted mb-0">{{ __('No notifications yet.') }}</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Quote card --}}
            <div class="pc-card p-4" style="background:var(--pc-success-bg);border-color:transparent;">
                <i class="bi bi-quote fs-2 d-block mb-2" style="color:var(--pc-success);"></i>
                <p class="fw-semibold mb-2" style="color:var(--pc-text-heading);">
                    {{ __('Caring for animals is not just a profession, it\'s a purpose.') }}
                </p>
                <div class="text-muted text-end" style="font-size:.8rem;">&mdash; {{ __('Unknown') }}</div>
            </div>
        </div>
    </div>
</div>
@endsection