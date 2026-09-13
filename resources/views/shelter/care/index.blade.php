@extends('layouts.shelter.app')

@section('content')
<div class="mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <h2 class="h4 mb-0 fw-semibold">{{ __('Care Status') }}</h2>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addLogModal">
            <i class="bi bi-plus me-1"></i>{{ __('Add Log') }}
        </button>
    </div>
</div>

{{-- Stats --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0" style="border-left:4px solid var(--fs-primary);">
            <div class="card-body py-2">
                <div class="text-muted" style="font-size:0.75rem;">{{ __('Total Logs') }}</div>
                <div class="fw-bold fs-5">{{ $stats['total'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0" style="border-left:4px solid #f59e0b;">
            <div class="card-body py-2">
                <div class="text-muted" style="font-size:0.75rem;">{{ __('Feeding') }}</div>
                <div class="fw-bold fs-5">{{ $stats['feeding'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0" style="border-left:4px solid #8b5cf6;">
            <div class="card-body py-2">
                <div class="text-muted" style="font-size:0.75rem;">{{ __('Grooming') }}</div>
                <div class="fw-bold fs-5">{{ $stats['grooming'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0" style="border-left:4px solid #ef4444;">
            <div class="card-body py-2">
                <div class="text-muted" style="font-size:0.75rem;">{{ __('Medical') }}</div>
                <div class="fw-bold fs-5">{{ $stats['medical'] }}</div>
            </div>
        </div>
    </div>
</div>

{{-- Filter --}}
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('shelter.care-status.index') }}" class="row g-3">
            <div class="col-md-3">
                <input type="text" class="form-control" name="animal" placeholder="{{ __('Animal name...') }}" value="{{ request('animal') }}">
            </div>
            <div class="col-md-2">
                <select class="form-select" name="type">
                    <option value="">{{ __('All Types') }}</option>
                    <option value="feeding" {{ request('type') === 'feeding' ? 'selected' : '' }}>{{ __('Feeding') }}</option>
                    <option value="grooming" {{ request('type') === 'grooming' ? 'selected' : '' }}>{{ __('Grooming') }}</option>
                    <option value="medical" {{ request('type') === 'medical' ? 'selected' : '' }}>{{ __('Medical') }}</option>
                    <option value="other" {{ request('type') === 'other' ? 'selected' : '' }}>{{ __('Other') }}</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" class="form-control" name="date_from" value="{{ request('date_from') }}" placeholder="{{ __('From') }}">
            </div>
            <div class="col-md-2">
                <input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}" placeholder="{{ __('To') }}">
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-outline-secondary flex-grow-1">
                    <i class="bi bi-search me-1"></i>{{ __('Filter') }}
                </button>
                <a href="{{ route('shelter.care-status.index') }}" class="btn btn-outline-danger"><i class="bi bi-x"></i></a>
            </div>
        </form>
    </div>
</div>

{{-- Logs Table --}}
<div class="card">
    <div class="card-body p-0">
        @if($logs->count())
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="font-size:0.8rem;">{{ __('Date') }}</th>
                            <th style="font-size:0.8rem;">{{ __('Animal') }}</th>
                            <th style="font-size:0.8rem;">{{ __('Type') }}</th>
                            <th style="font-size:0.8rem;">{{ __('Notes') }}</th>
                            <th style="font-size:0.8rem;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logs as $log)
                            <tr>
                                <td style="font-size:0.85rem;">{{ $log->log_date->format('M d, Y') }}</td>
                                <td>
                                    <span class="fw-semibold" style="font-size:0.85rem;">{{ $log->animal_name }}</span>
                                    @if($log->listing)
                                        <div class="text-muted" style="font-size:0.7rem;">{{ __('Listed') }}</div>
                                    @endif
                                </td>
                                <td>
                                    @if($log->type === 'feeding')
                                        <span class="badge bg-warning text-dark"><i class="bi bi-cup me-1"></i>{{ __('Feeding') }}</span>
                                    @elseif($log->type === 'grooming')
                                        <span class="badge" style="background:#8b5cf6;color:#fff;"><i class="bi bi-scissors me-1"></i>{{ __('Grooming') }}</span>
                                    @elseif($log->type === 'medical')
                                        <span class="badge bg-danger"><i class="bi bi-heart-pulse me-1"></i>{{ __('Medical') }}</span>
                                    @else
                                        <span class="badge bg-secondary">{{ __('Other') }}</span>
                                    @endif
                                </td>
                                <td style="font-size:0.85rem;max-width:400px;">
                                    <span style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">{{ $log->notes }}</span>
                                </td>
                                <td>
                                    <form action="{{ route('shelter.care-status.destroy', $log) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Delete this log?') }}')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-clipboard2-pulse" style="font-size:3rem;opacity:0.3;"></i>
                <p class="mt-2 text-muted">{{ __('No care logs yet.') }}</p>
            </div>
        @endif
    </div>
    @if($logs->hasPages())
        <div class="card-footer">{{ $logs->links() }}</div>
    @endif
</div>

{{-- Add Log Modal --}}
<div class="modal fade" id="addLogModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('shelter.care-status.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-semibold">{{ __('Add Care Log') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ __('Animal Name') }} *</label>
                        <input type="text" class="form-control @error('animal_name') is-invalid @enderror" name="animal_name" list="animals-list" value="{{ old('animal_name') }}" required>
                        <datalist id="animals-list">
                            @foreach($listings as $l)
                                <option value="{{ $l->pet_name }}">
                            @endforeach
                        </datalist>
                        @error('animal_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ __('Linked Listing (optional)') }}</label>
                        <select class="form-select" name="listing_id">
                            <option value="">{{ __('None') }}</option>
                            @foreach($listings as $l)
                                <option value="{{ $l->id }}" {{ old('listing_id') == $l->id ? 'selected' : '' }}>{{ $l->pet_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">{{ __('Type') }} *</label>
                            <select class="form-select @error('type') is-invalid @enderror" name="type" required>
                                <option value="feeding" {{ old('type') === 'feeding' ? 'selected' : '' }}>{{ __('Feeding') }}</option>
                                <option value="grooming" {{ old('type') === 'grooming' ? 'selected' : '' }}>{{ __('Grooming') }}</option>
                                <option value="medical" {{ old('type') === 'medical' ? 'selected' : '' }}>{{ __('Medical') }}</option>
                                <option value="other" {{ old('type') === 'other' ? 'selected' : '' }}>{{ __('Other') }}</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">{{ __('Date') }} *</label>
                            <input type="date" class="form-control @error('log_date') is-invalid @enderror" name="log_date" value="{{ old('log_date', date('Y-m-d')) }}" required>
                            @error('log_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ __('Notes') }} *</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" name="notes" rows="3" required placeholder="{{ __('Describe what was done...') }}">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-plus me-1"></i>{{ __('Add Log') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
