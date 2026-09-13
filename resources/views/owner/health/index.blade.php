@extends('layouts.owner.app')

@section('content')
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="h4 mb-0 fw-semibold">{{ __('Health Records') }}</h2>
                <div class="text-muted" style="font-size:0.875rem;">
                    {{ $pet->name }} - {{ $pet->species->name ?? '' }}{{ $pet->breed ? ' - ' . $pet->breed->name : '' }}
                </div>
            </div>
            <a href="{{ route('owner.pets.show', $pet) }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i>{{ __('Back to Pet') }}
            </a>
        </div>
    </div>

    <!-- Health Records Section -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-semibold"><i class="bi bi-clipboard2-pulse me-1"></i>{{ __('Health Records') }}</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>{{ __('Date') }}</th>
                            <th>{{ __('Type') }}</th>
                            <th>{{ __('Description') }}</th>
                            <th>{{ __('Vet') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pet->healthRecords ?? [] as $record)
                            <tr>
                                <td class="text-muted">{{ $record->record_date ? \Carbon\Carbon::parse($record->record_date)->format('M d, Y') : '-' }}</td>
                                <td>
                                    <span class="badge bg-primary">{{ ucfirst($record->record_type ?? '-') }}</span>
                                </td>
                                <td>{{ $record->description ?: '-' }}</td>
                                <td class="text-muted">{{ $record->vet->name ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4">
                                    <div class="empty-state">
                                        <i class="bi bi-clipboard2-pulse" style="font-size:2rem;opacity:0.3;"></i>
                                        <p class="mt-2 text-muted" style="font-size:0.875rem;">{{ __('No health records yet.') }}</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Vaccinations Section -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-semibold"><i class="bi bi-shield-check me-1"></i>{{ __('Vaccinations') }}</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>{{ __('Vaccine') }}</th>
                            <th>{{ __('Date') }}</th>
                            <th>{{ __('Next Due') }}</th>
                            <th>{{ __('Batch No.') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pet->vaccinations ?? [] as $vaccination)
                            <tr>
                                <td class="fw-medium">{{ $vaccination->vaccine_name ?: '-' }}</td>
                                <td class="text-muted">{{ $vaccination->vaccination_date ? \Carbon\Carbon::parse($vaccination->vaccination_date)->format('M d, Y') : '-' }}</td>
                                <td>
                                    @if ($vaccination->next_due_date)
                                        @php $isOverdue = \Carbon\Carbon::parse($vaccination->next_due_date)->isPast(); @endphp
                                        <span class="{{ $isOverdue ? 'text-danger fw-semibold' : 'text-muted' }}">
                                            {{ \Carbon\Carbon::parse($vaccination->next_due_date)->format('M d, Y') }}
                                            @if ($isOverdue)
                                                <i class="bi bi-exclamation-triangle ms-1"></i>
                                            @endif
                                        </span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td><code>{{ $vaccination->batch_number ?: '-' }}</code></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4">
                                    <div class="empty-state">
                                        <i class="bi bi-shield-check" style="font-size:2rem;opacity:0.3;"></i>
                                        <p class="mt-2 text-muted" style="font-size:0.875rem;">{{ __('No vaccination records yet.') }}</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Medical Documents Section -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-semibold"><i class="bi bi-file-earmark-medical me-1"></i>{{ __('Medical Documents') }}</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>{{ __('Document') }}</th>
                            <th>{{ __('Type') }}</th>
                            <th>{{ __('Date') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pet->media ?? [] as $document)
                            @if(str_contains($document->mime_type ?? '', 'pdf') || str_contains($document->mime_type ?? '', 'image') || str_contains($document->name ?? '', 'medical') || str_contains($document->name ?? '', 'health'))
                                <tr>
                                    <td>
                                        <a href="{{ $document->url }}" target="_blank" class="text-decoration-none">
                                            <i class="bi bi-file-earmark me-1"></i>{{ $document->original_name }}
                                        </a>
                                    </td>
                                    <td><code>{{ $document->mime_type }}</code></td>
                                    <td class="text-muted">{{ $document->created_at->format('M d, Y') }}</td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-4">
                                    <div class="empty-state">
                                        <i class="bi bi-file-earmark-medical" style="font-size:2rem;opacity:0.3;"></i>
                                        <p class="mt-2 text-muted" style="font-size:0.875rem;">{{ __('No medical documents uploaded yet.') }}</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Insurance Policies Section -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-semibold"><i class="bi bi-shield-lock me-1"></i>{{ __('Insurance Policies') }}</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>{{ __('Provider') }}</th>
                            <th>{{ __('Policy Number') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Expiry') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $insurancePolicies = $pet->insurancePolicies ?? collect(); @endphp
                        @forelse($insurancePolicies as $policy)
                            <tr>
                                <td class="fw-medium">{{ $policy->provider ?? '-' }}</td>
                                <td><code>{{ $policy->policy_number ?? '-' }}</code></td>
                                <td>
                                    @if ($policy->status === 'active')
                                        <span class="badge bg-success">{{ __('Active') }}</span>
                                    @elseif ($policy->status === 'expired')
                                        <span class="badge bg-danger">{{ __('Expired') }}</span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst($policy->status ?? '-') }}</span>
                                    @endif
                                </td>
                                <td class="text-muted">{{ $policy->expiry_date ? \Carbon\Carbon::parse($policy->expiry_date)->format('M d, Y') : '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4">
                                    <div class="empty-state">
                                        <i class="bi bi-shield-lock" style="font-size:2rem;opacity:0.3;"></i>
                                        <p class="mt-2 text-muted" style="font-size:0.875rem;">{{ __('No insurance policies found.') }}</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
