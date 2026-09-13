@extends('layouts.owner.app')

@section('content')
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 mb-0 fw-semibold">{{ $careContent->title }}</h2>
            <a href="{{ route('owner.care.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i>{{ __('Back') }}
            </a>
        </div>
    </div>

    <!-- Content Header -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="d-flex align-items-center gap-2">
                    @php
                        $typeColors = [
                            'article' => 'primary',
                            'video' => 'info',
                            'faq' => 'warning',
                        ];
                        $categoryColors = [
                            'feeding' => 'success',
                            'hygiene' => 'info',
                            'exercise' => 'warning',
                            'health' => 'danger',
                            'training' => 'primary',
                        ];
                    @endphp
                    <span class="badge bg-{{ $typeColors[$careContent->content_type] ?? 'secondary' }}">
                        @if ($careContent->content_type === 'article')
                            <i class="bi bi-file-text me-1"></i>
                        @elseif ($careContent->content_type === 'video')
                            <i class="bi bi-play-circle me-1"></i>
                        @else
                            <i class="bi bi-question-circle me-1"></i>
                        @endif
                        {{ ucfirst($careContent->content_type) }}
                    </span>
                    <span class="badge bg-{{ $categoryColors[$careContent->category] ?? 'secondary' }} bg-opacity-10 text-{{ $categoryColors[$careContent->category] ?? 'secondary' }}">
                        {{ ucfirst($careContent->category) }}
                    </span>
                </div>
                <div class="text-muted" style="font-size:0.85rem;">
                    <i class="bi bi-calendar me-1"></i>
                    {{ $careContent->published_at ? $careContent->published_at->format('M d, Y') : $careContent->created_at->format('M d, Y') }}
                </div>
            </div>
        </div>
    </div>

    <!-- Content Body -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="content-body" style="line-height:1.8;">
                {!! $careContent->body !!}
            </div>
        </div>
    </div>

    <!-- Back Link -->
    <div class="mb-4">
        <a href="{{ route('owner.care.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>{{ __('Back to Care Content') }}
        </a>
    </div>
@endsection
