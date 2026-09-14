@extends('layouts.vet.app')

@section('content')
<div class="fade-in">
    <div class="mb-4">
        <h4 class="mb-1 fw-bold">{{ __('My Treatments') }}</h4>
        <p class="text-muted mb-0" style="font-size:0.875rem;">{{ __('Review, manage and track treatment records for your patients.') }}</p>
    </div>

    {{-- Stats --}}
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card border-0" style="border-left:4px solid var(--fs-primary)!important;">
                <div class="card-body py-2">
                    <div class="text-muted" style="font-size:0.75rem;">{{ __('Total Treatments') }}</div>
                    <div class="fw-bold fs-4">{{ $stats['total'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0" style="border-left:4px solid var(--fs-info)!important;">
                <div class="card-body py-2">
                    <div class="text-muted" style="font-size:0.75rem;">{{ __('This Month') }}</div>
                    <div class="fw-bold fs-4">{{ $stats['this_month'] }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-center">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" class="form-control border-start-0" name="search"
                               placeholder="{{ __('Search by pet, owner, diagnosis, symptoms...') }}"
                               value="{{ request('search') }}" maxlength="200">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-calendar3 text-muted"></i></span>
                        <input type="date" class="form-control border-start-0" name="date"
                               value="{{ request('date') }}" title="{{ __('Filter by treatment date') }}">
                    </div>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">{{ __('Search') }}</button>
                    <a href="{{ route('vet.treatments.index') }}" class="btn btn-outline-secondary" title="{{ __('Reset') }}">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h6 class="mb-0 fw-semibold"><i class="bi bi-clipboard2-pulse me-2" style="color:var(--fs-primary);"></i>{{ __('Treatment Records') }}</h6>
            <div class="d-flex align-items-center gap-3">
                <span class="text-muted" style="font-size:0.85rem;">
                    {{ __('Showing :from of :total records', ['from' => $treatments->count(), 'total' => method_exists($treatments, 'total') ? $treatments->total() : $treatments->count()]) }}
                </span>
                {{-- Treatments are always recorded from an approved appointment, so "Add" starts there --}}
                <a href="{{ route('vet.appointments.index') }}" class="btn btn-sm text-white" style="background:var(--fs-primary);">
                    <i class="bi bi-plus-lg me-1"></i>{{ __('Record Treatment') }}
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            @if($treatments->count())
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="font-size:0.8rem;">{{ __('Date') }}</th>
                                <th style="font-size:0.8rem;">{{ __('Pet') }}</th>
                                <th style="font-size:0.8rem;">{{ __('Owner') }}</th>
                                <th style="font-size:0.8rem;">{{ __('Diagnosis') }}</th>
                                <th style="font-size:0.8rem;">{{ __('Treatment') }}</th>
                                <th style="font-size:0.8rem;">{{ __('Follow-up') }}</th>
                                <th style="font-size:0.8rem;">{{ __('Rx') }}</th>
                                <th style="font-size:0.8rem;">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($treatments as $treatment)
                                <tr>
                                    <td style="font-size:0.85rem;white-space:nowrap;">
                                        {{ $treatment->created_at->format('M d, Y') }}<br>
                                        <small class="text-muted">{{ $treatment->created_at->format('D') }}</small>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            @include('vet.partials.pet-avatar', ['pet' => $treatment->appointment->pet])
                                            <div>
                                                <div class="fw-semibold" style="font-size:0.85rem;">{{ $treatment->appointment->pet->name }}</div>
                                                <small class="text-muted">{{ $treatment->appointment->pet->species?->name ?? '' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="font-size:0.85rem;">{{ $treatment->appointment->owner->name }}</td>
                                    <td style="font-size:0.85rem;max-width:160px;">
                                        <span style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;" title="{{ $treatment->diagnosis }}">{{ $treatment->diagnosis }}</span>
                                    </td>
                                    <td style="font-size:0.85rem;max-width:160px;">
                                        <span style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;" title="{{ $treatment->treatment }}">{{ \Illuminate\Support\Str::limit($treatment->treatment, 60) }}</span>
                                    </td>
                                    <td style="font-size:0.85rem;">
                                        @if($treatment->follow_up_date)
                                            <span class="{{ $treatment->follow_up_date->isPast() ? 'text-danger' : 'text-success' }}" title="{{ $treatment->follow_up_date->isPast() ? __('Overdue') : __('Upcoming') }}">
                                                <i class="bi bi-{{ $treatment->follow_up_date->isPast() ? 'exclamation-circle' : 'calendar-check' }} me-1"></i>{{ $treatment->follow_up_date->format('d M Y') }}
                                            </span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td style="font-size:0.85rem;">
                                        @if($treatment->prescriptions->count())
                                            <span class="badge bg-success bg-opacity-10 text-success">{{ $treatment->prescriptions->count() }} {{ __('Rx') }}</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('vet.treatments.show', $treatment) }}" class="btn btn-sm btn-outline-secondary" title="{{ __('View') }}">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-state">
                    <i class="bi bi-clipboard2-pulse d-block"></i>
                    <p>{{ __('No treatment records found.') }}</p>
                    @if(request('search') || request('date'))
                        <a href="{{ route('vet.treatments.index') }}" class="btn btn-sm btn-outline-secondary mt-2">{{ __('Clear Filters') }}</a>
                    @endif
                </div>
            @endif
        </div>
        @if($treatments->hasPages())
            <div class="card-footer">{{ $treatments->links() }}</div>
        @endif
    </div>
</div>
@endsection