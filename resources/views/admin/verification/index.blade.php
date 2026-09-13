@extends('layouts.app')

@section('content')
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 mb-0 fw-semibold">Verify Vets & Shelters</h2>
        </div>
    </div>

    <!-- Stats -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-start border-warning border-4 h-100">
                <div class="card-body">
                    <div class="text-muted" style="font-size:0.75rem;">Total Pending</div>
                    <div class="fs-4 fw-bold">{{ $stats['total_pending'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-start border-info border-4 h-100">
                <div class="card-body">
                    <div class="text-muted" style="font-size:0.75rem;">Pending Vets</div>
                    <div class="fs-4 fw-bold">{{ $stats['pending_vets'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-start border-success border-4 h-100">
                <div class="card-body">
                    <div class="text-muted" style="font-size:0.75rem;">Verified</div>
                    <div class="fs-4 fw-bold">{{ $stats['verified'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-start border-danger border-4 h-100">
                <div class="card-body">
                    <div class="text-muted" style="font-size:0.75rem;">Rejected</div>
                    <div class="fs-4 fw-bold">{{ $stats['rejected'] }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="card mb-4">
        <div class="card-body py-2">
            <nav class="nav nav-pills">
                <a class="nav-link {{ $filter === 'all' ? 'active' : '' }}" href="{{ route('admin.verification.index', ['filter' => 'all']) }}">All</a>
                <a class="nav-link {{ $filter === 'pending' ? 'active' : '' }}" href="{{ route('admin.verification.index', ['filter' => 'pending']) }}">Pending</a>
                <a class="nav-link {{ $filter === 'verified' ? 'active' : '' }}" href="{{ route('admin.verification.index', ['filter' => 'verified']) }}">Verified</a>
                <a class="nav-link {{ $filter === 'rejected' ? 'active' : '' }}" href="{{ route('admin.verification.index', ['filter' => 'rejected']) }}">Rejected</a>
            </nav>
        </div>
    </div>

    <!-- Vet Applications Section -->
    <div class="mb-4">
        <h5 class="fw-semibold mb-3">
            <i class="bi bi-heart-pulse me-1"></i> Vet Applications
            <span class="badge bg-info ms-2">{{ $vets->count() }}</span>
        </h5>
        @forelse ($vets as $vet)
            <div class="card mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="d-flex">
                            <div class="rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width:48px;height:48px;background:linear-gradient(135deg,#1a6b3c,#2e9e5a);">
                                <span class="text-white fw-semibold">{{ substr($vet->user->name, 0, 1) }}</span>
                            </div>
                            <div>
                                <div class="fw-semibold">{{ $vet->user->name }}</div>
                                <div class="text-muted small">{{ $vet->user->email }}</div>
                                <div class="mt-2">
                                    <span class="badge bg-light text-dark"><i class="bi bi-mortarboard me-1"></i>{{ $vet->qualification ?: 'N/A' }}</span>
                                    <span class="badge bg-light text-dark"><i class="bi bi-briefcase me-1"></i>{{ $vet->experience_years ?: 0 }} yrs exp</span>
                                    <span class="badge bg-light text-dark"><i class="bi bi-hospital me-1"></i>{{ $vet->clinic_name ?: 'N/A' }}</span>
                                    @if ($vet->consultation_fee)
                                        <span class="badge bg-light text-dark"><i class="bi bi-currency-dollar me-1"></i>{{ number_format($vet->consultation_fee, 2) }}</span>
                                    @endif
                                </div>
                                @if ($vet->clinic_address)
                                    <div class="text-muted small mt-1"><i class="bi bi-geo-alt me-1"></i>{{ $vet->clinic_address }}</div>
                                @endif
                            </div>
                        </div>
                        <div class="text-end">
                            @if ($vet->is_verified)
                                <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Verified</span>
                                <div class="text-muted small mt-1">{{ $vet->verified_at?->format('M d, Y') }}</div>
                                @if ($vet->verifiedBy)
                                    <div class="text-muted small">by {{ $vet->verifiedBy->name }}</div>
                                @endif
                            @elseif ($vet->rejected_at)
                                <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Rejected</span>
                                <div class="text-muted small mt-1">{{ $vet->rejected_at->format('M d, Y') }}</div>
                                @if ($vet->rejection_reason)
                                    <div class="text-danger small mt-1" style="max-width:250px;">{{ $vet->rejection_reason }}</div>
                                @endif
                                <div class="mt-2">
                                    <form action="{{ route('admin.verification.vet.approve', $vet) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-success" title="Re-approve">
                                            <i class="bi bi-check-lg me-1"></i>Re-approve
                                        </button>
                                    </form>
                                </div>
                            @else
                                <div class="text-muted small mb-2">Applied {{ $vet->created_at->diffForHumans() }}</div>
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-outline-info"
                                        onclick="openDetailModal('{{ route('admin.verification.vet.approve', $vet) }}', 'vet-detail-{{ $vet->id }}')">
                                        <i class="bi bi-eye me-1"></i>Detail
                                    </button>
                                    <form action="{{ route('admin.verification.vet.approve', $vet) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-success ms-1" title="Approve">
                                            <i class="bi bi-check-lg me-1"></i>Approve
                                        </button>
                                    </form>
                                    <button type="button" class="btn btn-danger ms-1" title="Reject"
                                        onclick="openRejectModal('{{ route('admin.verification.vet.reject', $vet) }}', '{{ addslashes($vet->user->name) }}')">
                                        <i class="bi bi-x-lg me-1"></i>Reject
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Re-approval warning for rejected -->
                    @if ($vet->rejected_at && !$vet->is_verified)
                        <div class="mt-2 p-2 rounded" style="background:#fff3cd;font-size:0.8rem;">
                            <i class="bi bi-exclamation-triangle me-1"></i>
                            <strong>Previously rejected</strong> on {{ $vet->rejected_at->format('M d, Y') }}
                            @if ($vet->rejection_reason)
                                — "{{ $vet->rejection_reason }}"
                            @endif
                        </div>
                    @endif

                    <!-- Hidden detail content for modal -->
                    <div id="vet-detail-{{ $vet->id }}" style="display:none;">
                        <table class="table table-sm mb-0">
                            <tr><td class="fw-semibold" style="width:160px;">Name</td><td>{{ $vet->user->name }}</td></tr>
                            <tr><td class="fw-semibold">Email</td><td>{{ $vet->user->email }}</td></tr>
                            <tr><td class="fw-semibold">Phone</td><td>{{ $vet->user->phone ?: 'N/A' }}</td></tr>
                            <tr><td class="fw-semibold">Address</td><td>{{ $vet->user->address ?: 'N/A' }}</td></tr>
                            <tr><td class="fw-semibold">Qualification</td><td>{{ $vet->qualification ?: 'N/A' }}</td></tr>
                            <tr><td class="fw-semibold">Experience</td><td>{{ $vet->experience_years ?: 0 }} years</td></tr>
                            <tr><td class="fw-semibold">Clinic Name</td><td>{{ $vet->clinic_name ?: 'N/A' }}</td></tr>
                            <tr><td class="fw-semibold">Clinic Address</td><td>{{ $vet->clinic_address ?: 'N/A' }}</td></tr>
                            <tr><td class="fw-semibold">Consultation Fee</td><td>${{ number_format($vet->consultation_fee ?? 0, 2) }}</td></tr>
                            <tr><td class="fw-semibold">Bio</td><td>{{ $vet->bio ?: 'N/A' }}</td></tr>
                            <tr><td class="fw-semibold">Applied</td><td>{{ $vet->created_at->format('M d, Y H:i') }}</td></tr>
                        </table>
                    </div>
                </div>
            </div>
        @empty
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="bi bi-check-circle text-muted" style="font-size:2rem;"></i>
                    <p class="text-muted mt-2 mb-0">No vet applications found.</p>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Shelter Applications Section -->
    <div class="mb-4">
        <h5 class="fw-semibold mb-3">
            <i class="bi bi-building me-1"></i> Shelter Applications
            <span class="badge bg-primary ms-2">{{ $shelters->count() }}</span>
        </h5>
        @forelse ($shelters as $shelter)
            <div class="card mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="d-flex">
                            <div class="rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width:48px;height:48px;background:linear-gradient(135deg,#6b3a1a,#9e6b2e);">
                                <span class="text-white fw-semibold">{{ substr($shelter->shelter_name, 0, 1) }}</span>
                            </div>
                            <div>
                                <div class="fw-semibold">{{ $shelter->shelter_name }}</div>
                                <div class="text-muted small">{{ $shelter->user->name }} &middot; {{ $shelter->user->email }}</div>
                                <div class="mt-2">
                                    @if ($shelter->city)
                                        <span class="badge bg-light text-dark"><i class="bi bi-geo-alt me-1"></i>{{ $shelter->city }}</span>
                                    @endif
                                    @if ($shelter->capacity)
                                        <span class="badge bg-light text-dark"><i class="bi bi-people me-1"></i>Capacity: {{ $shelter->capacity }}</span>
                                    @endif
                                    @if ($shelter->contact_number)
                                        <span class="badge bg-light text-dark"><i class="bi bi-telephone me-1"></i>{{ $shelter->contact_number }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="text-end">
                            @if ($shelter->is_verified)
                                <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Verified</span>
                                <div class="text-muted small mt-1">{{ $shelter->verified_at?->format('M d, Y') }}</div>
                                @if ($shelter->verifiedBy)
                                    <div class="text-muted small">by {{ $shelter->verifiedBy->name }}</div>
                                @endif
                            @elseif ($shelter->rejected_at)
                                <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Rejected</span>
                                <div class="text-muted small mt-1">{{ $shelter->rejected_at->format('M d, Y') }}</div>
                                @if ($shelter->rejection_reason)
                                    <div class="text-danger small mt-1" style="max-width:250px;">{{ $shelter->rejection_reason }}</div>
                                @endif
                                <div class="mt-2">
                                    <form action="{{ route('admin.verification.shelter.approve', $shelter) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-success" title="Re-approve">
                                            <i class="bi bi-check-lg me-1"></i>Re-approve
                                        </button>
                                    </form>
                                </div>
                            @else
                                <div class="text-muted small mb-2">Applied {{ $shelter->created_at->diffForHumans() }}</div>
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-outline-info"
                                        onclick="openDetailModal('{{ route('admin.verification.shelter.approve', $shelter) }}', 'shelter-detail-{{ $shelter->id }}')">
                                        <i class="bi bi-eye me-1"></i>Detail
                                    </button>
                                    <form action="{{ route('admin.verification.shelter.approve', $shelter) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-success ms-1" title="Approve">
                                            <i class="bi bi-check-lg me-1"></i>Approve
                                        </button>
                                    </form>
                                    <button type="button" class="btn btn-danger ms-1" title="Reject"
                                        onclick="openRejectModal('{{ route('admin.verification.shelter.reject', $shelter) }}', '{{ addslashes($shelter->shelter_name) }}')">
                                        <i class="bi bi-x-lg me-1"></i>Reject
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Re-approval warning for rejected -->
                    @if ($shelter->rejected_at && !$shelter->is_verified)
                        <div class="mt-2 p-2 rounded" style="background:#fff3cd;font-size:0.8rem;">
                            <i class="bi bi-exclamation-triangle me-1"></i>
                            <strong>Previously rejected</strong> on {{ $shelter->rejected_at->format('M d, Y') }}
                            @if ($shelter->rejection_reason)
                                — "{{ $shelter->rejection_reason }}"
                            @endif
                        </div>
                    @endif

                    <!-- Hidden detail content for modal -->
                    <div id="shelter-detail-{{ $shelter->id }}" style="display:none;">
                        <table class="table table-sm mb-0">
                            <tr><td class="fw-semibold" style="width:160px;">Shelter Name</td><td>{{ $shelter->shelter_name }}</td></tr>
                            <tr><td class="fw-semibold">Contact Person</td><td>{{ $shelter->user->name }}</td></tr>
                            <tr><td class="fw-semibold">Email</td><td>{{ $shelter->user->email }}</td></tr>
                            <tr><td class="fw-semibold">Phone</td><td>{{ $shelter->contact_number ?: 'N/A' }}</td></tr>
                            <tr><td class="fw-semibold">Address</td><td>{{ $shelter->address ?: 'N/A' }}</td></tr>
                            <tr><td class="fw-semibold">City</td><td>{{ $shelter->city ?: 'N/A' }}</td></tr>
                            <tr><td class="fw-semibold">Capacity</td><td>{{ $shelter->capacity ?: 'N/A' }}</td></tr>
                            <tr><td class="fw-semibold">Website</td><td>{{ $shelter->website ?: 'N/A' }}</td></tr>
                            <tr><td class="fw-semibold">Description</td><td>{{ $shelter->description ?: 'N/A' }}</td></tr>
                            <tr><td class="fw-semibold">Applied</td><td>{{ $shelter->created_at->format('M d, Y H:i') }}</td></tr>
                        </table>
                    </div>
                </div>
            </div>
        @empty
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="bi bi-check-circle text-muted" style="font-size:2rem;"></i>
                    <p class="text-muted mt-2 mb-0">No shelter applications found.</p>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Reject Modal -->
    @include('admin.verification._reject-modal')

    <!-- Detail Modal -->
    <div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailModalLabel">Application Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="detailModalBody"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function openRejectModal(url, name) {
            var form = document.getElementById('rejectForm');
            form.action = url;
            document.getElementById('rejectModalLabel').textContent = 'Reject ' + name;
            document.getElementById('reason').value = '';
            var modal = new bootstrap.Modal(document.getElementById('rejectModal'));
            modal.show();
        }

        function openDetailModal(approveUrl, contentId) {
            var content = document.getElementById(contentId);
            document.getElementById('detailModalBody').innerHTML = content.innerHTML;
            var modal = new bootstrap.Modal(document.getElementById('detailModal'));
            modal.show();
        }
    </script>
    @endpush
@endsection
