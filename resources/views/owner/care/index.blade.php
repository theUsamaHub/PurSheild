@extends('layouts.owner.app')

@section('content')
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 mb-0 fw-semibold">{{ __('Care Content') }}</h2>
        </div>
    </div>

    <!-- Search & Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('owner.care.index') }}" class="row g-3">
                <div class="col-md-4">
                    <input type="text" class="form-control" name="search" placeholder="{{ __('Search articles, videos...') }}" value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="content_type">
                        <option value="">{{ __('All Types') }}</option>
                        <option value="article" {{ request('content_type') === 'article' ? 'selected' : '' }}>{{ __('Article') }}</option>
                        <option value="video" {{ request('content_type') === 'video' ? 'selected' : '' }}>{{ __('Video') }}</option>
                        <option value="faq" {{ request('content_type') === 'faq' ? 'selected' : '' }}>{{ __('FAQ') }}</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="category">
                        <option value="">{{ __('All Categories') }}</option>
                        <option value="feeding" {{ request('category') === 'feeding' ? 'selected' : '' }}>{{ __('Feeding') }}</option>
                        <option value="hygiene" {{ request('category') === 'hygiene' ? 'selected' : '' }}>{{ __('Hygiene') }}</option>
                        <option value="exercise" {{ request('category') === 'exercise' ? 'selected' : '' }}>{{ __('Exercise') }}</option>
                        <option value="health" {{ request('category') === 'health' ? 'selected' : '' }}>{{ __('Health') }}</option>
                        <option value="training" {{ request('category') === 'training' ? 'selected' : '' }}>{{ __('Training') }}</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-outline-secondary flex-grow-1">
                        <i class="bi bi-search me-1"></i>{{ __('Filter') }}
                    </button>
                    <a href="{{ route('owner.care.index') }}" class="btn btn-outline-danger"><i class="bi bi-x"></i></a>
                </div>
            </form>
        </div>
    </div>

    <!-- Care Content Cards -->
    <div class="row g-4">
        @forelse ($careContents as $item)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex align-items-center justify-content-between mb-3">
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
                            <span class="badge bg-{{ $typeColors[$item->content_type] ?? 'secondary' }}">
                                @if ($item->content_type === 'article')
                                    <i class="bi bi-file-text me-1"></i>
                                @elseif ($item->content_type === 'video')
                                    <i class="bi bi-play-circle me-1"></i>
                                @else
                                    <i class="bi bi-question-circle me-1"></i>
                                @endif
                                {{ ucfirst($item->content_type) }}
                            </span>
                            <span class="badge bg-{{ $categoryColors[$item->category] ?? 'secondary' }} bg-opacity-10 text-{{ $categoryColors[$item->category] ?? 'secondary' }}">
                                {{ ucfirst($item->category) }}
                            </span>
                        </div>

                        <h6 class="card-title fw-semibold mb-2">{{ $item->title }}</h6>
                        <p class="text-muted mb-3 flex-grow-1" style="font-size:0.85rem;">
                            {{ Str::limit(strip_tags($item->excerpt ?? $item->body), 120) }}
                        </p>

                        <div class="mt-auto">
                            <a href="{{ route('owner.care.show', $item) }}" class="btn btn-sm btn-primary w-100">
                                <i class="bi bi-book me-1"></i>{{ __('Read') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <div class="empty-state">
                            <i class="bi bi-journal-bookmark" style="font-size:3rem;opacity:0.3;"></i>
                            <p class="mt-2 text-muted">{{ __('No care content found.') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    @if ($careContents->hasPages())
        <div class="mt-4">
            {{ $careContents->links() }}
        </div>
    @endif
@endsection
