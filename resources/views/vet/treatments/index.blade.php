@extends('layouts.vet.app')

@section('content')
<div class="fade-in">
    <div class="mb-4">
        <h4 class="mb-1 fw-bold">{{ __('Treatments') }}</h4>
        <p class="text-muted mb-0" style="font-size:0.875rem;">{{ __('Your treatment records') }}</p>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card border-0" style="border-left:4px solid var(--fs-primary);">
                <div class="card-body py-2">
                    <div class="text-muted" style="font-size:0.75rem;">{{ __('Total Treatments') }}</div>
                    <div class="fw-bold fs-4">{{ $stats['total'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0" style="border-left:4px solid var(--fs-info);">
                <div class="card-body py-2">
                    <div class="text-muted" style="font-size:0.75rem;">{{ __('This Month') }}</div>
                    <div class="fw-bold fs-4">{{ $stats['this_month'] }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-10">
                    <input type="text" class="form-control" name="search" placeholder="{{ __('Search by diagnosis, pet name...') }}" value="{{ request('search') }}">
                </div>
                <div class="col-md-2 d-flex gap-1">
                    <button type="submit" class="btn btn-primary flex-grow-1"><i class="bi bi-search"></i></button>
                    <a href="{{ route('vet.treatments.index') }}" class="btn btn-outline-secondary"><i class="bi bi-x"></i></a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            @if($treatments->count())
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="font-size:0.8rem;">{{ __('Date') }}</th>
                                <th style="font-size:0.8rem;">{{ __('Pet') }}</th>
                                <th style="font-size:0.8rem;">{{ __('Owner') }}</th>
                                <th style="font-size:0.8rem;">{{ __('Diagnosis') }}</th>
                                <th style="font-size:0.8rem;">{{ __('Prescriptions') }}</th>
                                <th style="font-size:0.8rem;">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($treatments as $treatment)
                                <tr>
                                    <td style="font-size:0.85rem;">{{ $treatment->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <div class="fw-semibold" style="font-size:0.85rem;">{{ $treatment->appointment->pet->name }}</div>
                                        <small class="text-muted">{{ $treatment->appointment->pet->species?->name ?? '' }}</small>
                                    </td>
                                    <td style="font-size:0.85rem;">{{ $treatment->appointment->owner->name }}</td>
                                    <td>
                                        <span style="font-size:0.85rem;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;max-width:200px;">
                                            {{ $treatment->diagnosis }}
                                        </span>
                                    </td>
                                    <td><span class="badge bg-secondary">{{ $treatment->prescriptions->count() }}</span></td>
                                    <td>
                                        <a href="{{ route('vet.treatments.show', $treatment) }}" class="btn btn-sm btn-outline-primary">
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
                </div>
            @endif
        </div>
        @if($treatments->hasPages())
            <div class="card-footer">{{ $treatments->links() }}</div>
        @endif
    </div>
</div>
@endsection
