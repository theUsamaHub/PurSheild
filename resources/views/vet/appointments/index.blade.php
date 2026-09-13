@extends('layouts.vet.app')

@section('content')
<div class="fade-in">
    <div class="mb-4">
        <h4 class="mb-1 fw-bold">{{ __('My Appointments') }}</h4>
        <p class="text-muted mb-0" style="font-size:0.875rem;">{{ __('Manage your appointment schedule') }}</p>
    </div>

    {{-- Stats --}}
    <div class="row g-3 mb-4">
        @foreach(['total' => ['Total', 'primary', 'bi-calendar', 'var(--fs-primary-bg)'], 'pending' => ['Pending', 'info', 'bi-clock', '#e0f2fe'], 'approved' => ['Approved', 'success', 'bi-check-circle', '#dcfce7'], 'completed' => ['Completed', 'secondary', 'bi-check2-circle', '#f3f4f6']] as $key => [$label, $color, $icon, $bg])
            <div class="col-md-3">
                <div class="card border-0" style="border-left:4px solid var(--fs-{{ $color }});">
                    <div class="card-body py-2">
                        <div class="d-flex align-items-center">
                            <div class="rounded d-flex align-items-center justify-content-center" style="width:40px;height:40px;background:{{ $bg }};">
                                <i class="bi {{ $icon }} text-{{ $color }} fs-6"></i>
                            </div>
                            <div class="ms-2">
                                <div class="text-muted" style="font-size:0.7rem;">{{ __($label) }}</div>
                                <div class="fw-bold fs-5">{{ $stats[$key] }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Filters --}}
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <input type="text" class="form-control" name="search" placeholder="{{ __('Search by pet or owner name...') }}" value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="status">
                        <option value="">{{ __('All Status') }}</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>{{ __('Approved') }}</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>{{ __('Completed') }}</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>{{ __('Cancelled') }}</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="date" class="form-control" name="date" value="{{ request('date') }}">
                </div>
                <div class="col-md-2 d-flex gap-1">
                    <button type="submit" class="btn btn-primary flex-grow-1"><i class="bi bi-search"></i></button>
                    <a href="{{ route('vet.appointments.index') }}" class="btn btn-outline-secondary"><i class="bi bi-x"></i></a>
                </div>
            </form>
        </div>
    </div>

    {{-- Appointments Table --}}
    <div class="card">
        <div class="card-body p-0">
            @if($appointments->count())
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="font-size:0.8rem;">{{ __('Pet') }}</th>
                                <th style="font-size:0.8rem;">{{ __('Owner') }}</th>
                                <th style="font-size:0.8rem;">{{ __('Date & Time') }}</th>
                                <th style="font-size:0.8rem;">{{ __('Reason') }}</th>
                                <th style="font-size:0.8rem;">{{ __('Status') }}</th>
                                <th style="font-size:0.8rem;">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($appointments as $apt)
                                <tr>
                                    <td>
                                        <div class="fw-semibold" style="font-size:0.85rem;">{{ $apt->pet->name }}</div>
                                        <small class="text-muted">{{ $apt->pet->species?->name ?? '' }} {{ $apt->pet->breed ? '- ' . $apt->pet->breed->name : '' }}</small>
                                    </td>
                                    <td style="font-size:0.85rem;">{{ $apt->owner->name }}</td>
                                    <td>
                                        <div style="font-size:0.85rem;">{{ $apt->appointment_date->format('M d, Y') }}</div>
                                        <small class="text-muted">{{ $apt->appointment_time }}</small>
                                    </td>
                                    <td>
                                        <span style="font-size:0.85rem;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;max-width:200px;">
                                            {{ $apt->reason ?? '-' }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($apt->status === 'pending')
                                            <span class="badge bg-warning text-dark">{{ __('Pending') }}</span>
                                        @elseif($apt->status === 'approved')
                                            <span class="badge bg-success">{{ __('Approved') }}</span>
                                        @elseif($apt->status === 'completed')
                                            <span class="badge bg-info">{{ __('Completed') }}</span>
                                        @elseif($apt->status === 'cancelled')
                                            <span class="badge bg-secondary">{{ __('Cancelled') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('vet.appointments.show', $apt) }}" class="btn btn-sm btn-outline-primary" title="{{ __('View') }}">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            @if($apt->status === 'pending')
                                                <form action="{{ route('vet.appointments.approve', $apt) }}" method="POST" class="d-inline">
                                                    @csrf @method('PUT')
                                                    <button type="submit" class="btn btn-sm btn-success" title="{{ __('Approve') }}"><i class="bi bi-check-lg"></i></button>
                                                </form>
                                                <form action="{{ route('vet.appointments.reject', $apt) }}" method="POST" class="d-inline">
                                                    @csrf @method('PUT')
                                                    <button type="submit" class="btn btn-sm btn-danger" title="{{ __('Reject') }}"><i class="bi bi-x-lg"></i></button>
                                                </form>
                                            @endif
                                            @if($apt->status === 'approved')
                                                <a href="{{ route('vet.treatments.create', $apt) }}" class="btn btn-sm btn-primary" title="{{ __('Record Treatment') }}"><i class="bi bi-clipboard2-pulse"></i></a>
                                                <form action="{{ route('vet.appointments.updateStatus', $apt) }}" method="POST" class="d-inline">
                                                    @csrf @method('PUT')
                                                    <input type="hidden" name="status" value="cancelled">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="{{ __('No-Show / Cancel') }}" onclick="return confirm('{{ __('Mark as no-show?') }}')"><i class="bi bi-x-lg"></i></button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-state">
                    <i class="bi bi-calendar-x d-block"></i>
                    <p>{{ __('No appointments found.') }}</p>
                </div>
            @endif
        </div>
        @if($appointments->hasPages())
            <div class="card-footer">
                {{ $appointments->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
