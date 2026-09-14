@extends('layouts.vet.app')

@section('content')
<div class="fade-in">
    <div class="mb-4">
        <h4 class="mb-1 fw-bold">{{ __('My Patients') }}</h4>
        <p class="text-muted mb-0" style="font-size:0.875rem;">{{ __('View, search and manage all your patient records.') }}</p>
    </div>

    {{-- Filters --}}
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-center">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" class="form-control border-start-0" name="search"
                               placeholder="{{ __('Search by pet name, owner, or breed...') }}"
                               value="{{ request('search') }}" maxlength="200">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-paw text-muted"></i></span>
                        <select class="form-select border-start-0" name="species_id">
                            <option value="">{{ __('All Species') }}</option>
                            @foreach($species as $sp)
                                <option value="{{ $sp->id }}" {{ request('species_id') == $sp->id ? 'selected' : '' }}>{{ $sp->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">{{ __('Search') }}</button>
                    <a href="{{ route('vet.patients.index') }}" class="btn btn-outline-secondary" title="{{ __('Reset') }}">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h6 class="mb-0 fw-semibold"><i class="bi bi-clipboard2-check me-2" style="color:var(--fs-primary);"></i>{{ __('Patient Records') }}</h6>
            <span class="text-muted" style="font-size:0.85rem;">
                {{ __('Showing :from of :total patients', ['from' => $patients->count(), 'total' => method_exists($patients, 'total') ? $patients->total() : $patients->count()]) }}
            </span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="font-size:0.8rem;">{{ __('Pet') }}</th>
                            <th style="font-size:0.8rem;">{{ __('Owner') }}</th>
                            <th style="font-size:0.8rem;">{{ __('Species / Breed') }}</th>
                            <th style="font-size:0.8rem;">{{ __('Age') }}</th>
                            <th style="font-size:0.8rem;">{{ __('Last Visit') }}</th>
                            <th style="font-size:0.8rem;">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($patients as $pet)
                            @php
                                $age = $pet->date_of_birth
                                    ? \Carbon\Carbon::parse($pet->date_of_birth)->age . ' ' . __('yrs')
                                    : '-';
                                $lastVisit = $pet->last_visit_date ?? null;
                            @endphp
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        @include('vet.partials.pet-avatar', ['pet' => $pet])
                                        <span class="fw-semibold" style="font-size:0.85rem;">{{ $pet->name }}</span>
                                    </div>
                                </td>
                                <td style="font-size:0.85rem;">{{ $pet->owner->name }}</td>
                                <td style="font-size:0.85rem;">
                                    {{ $pet->species?->name ?? '-' }}@if($pet->breed) &bull; {{ $pet->breed->name }}@endif
                                </td>
                                <td style="font-size:0.85rem;">{{ $age }}</td>
                                <td style="font-size:0.85rem;">
                                    {{ $lastVisit ? \Carbon\Carbon::parse($lastVisit)->format('d M Y') : '-' }}
                                </td>
                                <td>
                                    <a href="{{ route('vet.patients.show', $pet) }}" class="btn btn-sm btn-outline-secondary" title="{{ __('View Medical History') }}">
                                        <i class="bi bi-folder2-open"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <div class="empty-state">
                                        <i class="bi bi-heart-pulse d-block"></i>
                                        <p>{{ __('No patients found.') }}</p>
                                        @if(request('search') || request('species_id'))
                                            <a href="{{ route('vet.patients.index') }}" class="btn btn-sm btn-outline-secondary mt-2">{{ __('Clear Filters') }}</a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($patients->hasPages())
            <div class="card-footer">{{ $patients->links() }}</div>
        @endif
    </div>
</div>
@endsection