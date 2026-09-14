<div class="card mb-4">
    <div class="card-header"><h6 class="mb-0 fw-semibold"><i class="bi bi-file-earmark-medical me-2"></i>{{ __('Medical Documents') }}</h6></div>
    <div class="card-body">
        @forelse($pet->medicalDocuments as $document)
            <div class="d-flex justify-content-between align-items-center gap-2 py-2 {{ $loop->last ? '' : 'border-bottom' }}">
                <div class="text-break"><strong class="small">{{ $document->file_name }}</strong><p class="text-muted small mb-0">{{ $document->description }}</p></div>
                <a class="btn btn-sm btn-outline-success" href="{{ route('vet.patients.document', [$pet, $document]) }}">{{ __('Download') }}</a>
            </div>
        @empty
            <p class="text-muted small mb-0">{{ __('No medical documents uploaded.') }}</p>
        @endforelse
    </div>
</div>
