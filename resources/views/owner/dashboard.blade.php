@extends('layouts.owner.app')

@section('content')
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-1 fw-bold">{{ __('My Dashboard') }}</h4>
                <p class="text-muted mb-0" style="font-size:0.875rem;">{{ __('Welcome back, ') }}{{ Auth::user()->name }}!</p>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <a href="{{ route('owner.pets.index') }}" class="text-decoration-none">
                <div class="card border-start border-4 h-100" style="border-left-color: #1a6b3c !important;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-muted" style="font-size:0.75rem;">{{ __('My Pets') }}</div>
                                <div class="fs-4 fw-bold">{{ $stats['pets'] ?? 0 }}</div>
                            </div>
                            <div class="rounded-3 d-flex align-items-center justify-content-center" style="width:40px;height:40px;background:rgba(26,107,60,0.1);">
                                <i class="bi bi-heart" style="color:#1a6b3c;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="{{ route('owner.appointments.index') }}" class="text-decoration-none">
                <div class="card border-start border-4 h-100" style="border-left-color: #0d6efd !important;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-muted" style="font-size:0.75rem;">{{ __('Upcoming Appointments') }}</div>
                                <div class="fs-4 fw-bold">{{ $stats['upcoming_appointments'] ?? 0 }}</div>
                            </div>
                            <div class="rounded-3 d-flex align-items-center justify-content-center" style="width:40px;height:40px;background:rgba(13,110,253,0.1);">
                                <i class="bi bi-calendar-check text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="{{ route('owner.appointments.index') }}" class="text-decoration-none">
                <div class="card border-start border-4 h-100" style="border-left-color: #ffc107 !important;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-muted" style="font-size:0.75rem;">{{ __('Pending Appointments') }}</div>
                                <div class="fs-4 fw-bold">{{ $stats['pending_appointments'] ?? 0 }}</div>
                            </div>
                            <div class="rounded-3 d-flex align-items-center justify-content-center" style="width:40px;height:40px;background:rgba(255,193,7,0.1);">
                                <i class="bi bi-clock-history text-warning"></i>
                            </div>
                        </div>
                        @if (($stats['pending_appointments'] ?? 0) > 0)
                            <span class="badge bg-warning mt-1">{{ __('needs action') }}</span>
                        @endif
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="{{ route('owner.orders.index') }}" class="text-decoration-none">
                <div class="card border-start border-4 h-100" style="border-left-color: #198754 !important;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-muted" style="font-size:0.75rem;">{{ __('Total Orders') }}</div>
                                <div class="fs-4 fw-bold">{{ $stats['total_orders'] ?? 0 }}</div>
                            </div>
                            <div class="rounded-3 d-flex align-items-center justify-content-center" style="width:40px;height:40px;background:rgba(25,135,84,0.1);">
                                <i class="bi bi-receipt text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="card mb-4">
        <div class="card-header">
            <h6 class="mb-0 fw-semibold">{{ __('Quick Actions') }}</h6>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <a href="{{ route('owner.pets.create') }}" class="btn btn-outline-primary w-100 py-3">
                        <i class="bi bi-plus-circle d-block mb-1 fs-4"></i>
                        <span style="font-size:0.8rem;">{{ __('Add Pet') }}</span>
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('owner.appointments.create') }}" class="btn btn-outline-success w-100 py-3">
                        <i class="bi bi-calendar-plus d-block mb-1 fs-4"></i>
                        <span style="font-size:0.8rem;">{{ __('Book Appointment') }}</span>
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('owner.products.index') }}" class="btn btn-outline-info w-100 py-3">
                        <i class="bi bi-shop d-block mb-1 fs-4"></i>
                        <span style="font-size:0.8rem;">{{ __('Browse Products') }}</span>
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('owner.browse-vets') }}" class="btn btn-outline-warning w-100 py-3">
                        <i class="bi bi-heartbeat d-block mb-1 fs-4"></i>
                        <span style="font-size:0.8rem;">{{ __('Browse Vets') }}</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Appointments & Orders -->
    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-semibold">{{ __('Recent Appointments') }}</h6>
                    <a href="{{ route('owner.appointments.index') }}" class="btn btn-sm btn-outline-primary">{{ __('View All') }}</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>{{ __('Pet') }}</th>
                                    <th>{{ __('Vet') }}</th>
                                    <th>{{ __('Date') }}</th>
                                    <th>{{ __('Status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentAppointments ?? [] as $appointment)
                                    <tr>
                                        <td class="fw-medium">{{ $appointment->pet->name ?? '-' }}</td>
                                        <td class="text-muted">{{ $appointment->vet->name ?? '-' }}</td>
                                        <td class="text-muted">{{ $appointment->appointment_date ? \Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y') : '-' }}</td>
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
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4">
                                            <div class="empty-state">
                                                <i class="bi bi-calendar-x" style="font-size:2rem;opacity:0.3;"></i>
                                                <p class="mt-2 text-muted" style="font-size:0.875rem;">{{ __('No appointments yet.') }}</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-semibold">{{ __('Recent Orders') }}</h6>
                    <a href="{{ route('owner.orders.index') }}" class="btn btn-sm btn-outline-primary">{{ __('View All') }}</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>{{ __('Order') }}</th>
                                    <th>{{ __('Total') }}</th>
                                    <th>{{ __('Status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentOrders ?? [] as $order)
                                    <tr>
                                        <td class="fw-medium">
                                            <a href="{{ route('owner.orders.show', $order) }}" class="text-decoration-none">#{{ $order->id }}</a>
                                        </td>
                                        <td class="text-muted">{{ number_format($order->total_amount ?? 0, 2) }}</td>
                                        <td>
                                            @if ($order->status === 'placed')
                                                <span class="badge bg-warning text-dark">{{ __('Placed') }}</span>
                                            @elseif ($order->status === 'processing')
                                                <span class="badge bg-info">{{ __('Processing') }}</span>
                                            @elseif ($order->status === 'completed')
                                                <span class="badge bg-success">{{ __('Completed') }}</span>
                                            @elseif ($order->status === 'cancelled')
                                                <span class="badge bg-danger">{{ __('Cancelled') }}</span>
                                            @else
                                                <span class="badge bg-secondary">{{ ucfirst($order->status) }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-4">
                                            <div class="empty-state">
                                                <i class="bi bi-receipt" style="font-size:2rem;opacity:0.3;"></i>
                                                <p class="mt-2 text-muted" style="font-size:0.875rem;">{{ __('No orders yet.') }}</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
