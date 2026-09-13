@extends('layouts.shelter.app')

@section('content')
<div class="mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <h2 class="h4 mb-0 fw-semibold">{{ __('Adoption Applications') }}</h2>
    </div>
</div>

{{-- Stats --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0" style="border-left:4px solid var(--fs-primary);">
            <div class="card-body py-2">
                <div class="text-muted" style="font-size:0.75rem;">{{ __('Total') }}</div>
                <div class="fw-bold fs-5">{{ $stats['total'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0" style="border-left:4px solid var(--fs-accent);">
            <div class="card-body py-2">
                <div class="text-muted" style="font-size:0.75rem;">{{ __('Pending') }}</div>
                <div class="fw-bold fs-5">{{ $stats['pending'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0" style="border-left:4px solid var(--fs-success);">
            <div class="card-body py-2">
                <div class="text-muted" style="font-size:0.75rem;">{{ __('Approved') }}</div>
                <div class="fw-bold fs-5">{{ $stats['approved'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0" style="border-left:4px solid var(--fs-danger);">
            <div class="card-body py-2">
                <div class="text-muted" style="font-size:0.75rem;">{{ __('Rejected') }}</div>
                <div class="fw-bold fs-5">{{ $stats['rejected'] }}</div>
            </div>
        </div>
    </div>
</div>

{{-- Filter --}}
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('shelter.applications.index') }}" class="row g-3">
            <div class="col-md-5">
                <input type="text" class="form-control" name="search" placeholder="{{ __('Search by name, pet...') }}" value="{{ request('search') }}">
            </div>
            <div class="col-md-4">
                <select class="form-select" name="status">
                    <option value="">{{ __('All Statuses') }}</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>{{ __('Approved') }}</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>{{ __('Rejected') }}</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>{{ __('Completed') }}</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-outline-secondary flex-grow-1">
                    <i class="bi bi-search me-1"></i>{{ __('Filter') }}
                </button>
                <a href="{{ route('shelter.applications.index') }}" class="btn btn-outline-danger"><i class="bi bi-x"></i></a>
            </div>
        </form>
    </div>
</div>

{{-- Applications Table --}}
<div class="card">
    <div class="card-body p-0">
        @if($applications->count())
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="font-size:0.8rem;">{{ __('Applicant') }}</th>
                            <th style="font-size:0.8rem;">{{ __('Pet') }}</th>
                            <th style="font-size:0.8rem;">{{ __('Date') }}</th>
                            <th style="font-size:0.8rem;">{{ __('Status') }}</th>
                            <th style="font-size:0.8rem;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($applications as $app)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center me-2" style="width:32px;height:32px;background:linear-gradient(135deg,#1a6b3c,#2e9e5a);">
                                            <span class="text-white fw-semibold" style="font-size:0.7rem;">{{ substr($app->applicant->name ?? '?', 0, 1) }}</span>
                                        </div>
                                        <div>
                                            <div class="fw-semibold" style="font-size:0.85rem;">{{ $app->applicant->name ?? '-' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td style="font-size:0.85rem;">{{ $app->listing->pet_name ?? '-' }}</td>
                                <td style="font-size:0.85rem;">{{ $app->created_at->format('M d, Y') }}</td>
                                <td>
                                    @if($app->status === 'pending')
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @elseif($app->status === 'approved')
                                        <span class="badge bg-success">Approved</span>
                                    @elseif($app->status === 'rejected')
                                        <span class="badge bg-danger">Rejected</span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst($app->status) }}</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('shelter.applications.show', $app) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-inbox" style="font-size:3rem;opacity:0.3;"></i>
                <p class="mt-2 text-muted">{{ __('No applications found.') }}</p>
            </div>
        @endif
    </div>
    @if($applications->hasPages())
        <div class="card-footer">{{ $applications->links() }}</div>
    @endif
</div>
@endsection
