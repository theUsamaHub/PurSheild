@extends('layouts.app')

@section('content')
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 mb-0 fw-semibold">{{ __('Edit Care Content') }} — {{ $careContent->title }}</h2>
            <a href="{{ route('admin.care-content.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>{{ __('Back') }}</a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body p-4">
                    <form action="{{ route('admin.care-content.update', $careContent) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="title" class="form-label fw-semibold">{{ __('Title') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $careContent->title) }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="category" class="form-label fw-semibold">{{ __('Category') }} <span class="text-danger">*</span></label>
                                <select class="form-select @error('category') is-invalid @enderror" id="category" name="category" required>
                                    <option value="">{{ __('Select category') }}</option>
                                    <option value="feeding" {{ old('category', $careContent->category) === 'feeding' ? 'selected' : '' }}>{{ __('Feeding') }}</option>
                                    <option value="hygiene" {{ old('category', $careContent->category) === 'hygiene' ? 'selected' : '' }}>{{ __('Hygiene') }}</option>
                                    <option value="exercise" {{ old('category', $careContent->category) === 'exercise' ? 'selected' : '' }}>{{ __('Exercise') }}</option>
                                    <option value="health" {{ old('category', $careContent->category) === 'health' ? 'selected' : '' }}>{{ __('Health') }}</option>
                                    <option value="training" {{ old('category', $careContent->category) === 'training' ? 'selected' : '' }}>{{ __('Training') }}</option>
                                </select>
                                @error('category')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="content_type" class="form-label fw-semibold">{{ __('Content Type') }} <span class="text-danger">*</span></label>
                                <select class="form-select @error('content_type') is-invalid @enderror" id="content_type" name="content_type" required>
                                    <option value="">{{ __('Select type') }}</option>
                                    <option value="article" {{ old('content_type', $careContent->content_type) === 'article' ? 'selected' : '' }}>{{ __('Article') }}</option>
                                    <option value="video" {{ old('content_type', $careContent->content_type) === 'video' ? 'selected' : '' }}>{{ __('Video') }}</option>
                                    <option value="faq" {{ old('content_type', $careContent->content_type) === 'faq' ? 'selected' : '' }}>{{ __('FAQ') }}</option>
                                </select>
                                @error('content_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="content" class="form-label fw-semibold">{{ __('Content') }} <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('content') is-invalid @enderror" id="content" name="content" rows="8" required>{{ old('content', $careContent->content) }}</textarea>
                            @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-8">
                                <label for="media_url" class="form-label fw-semibold">{{ __('Media URL') }}</label>
                                <input type="url" class="form-control @error('media_url') is-invalid @enderror" id="media_url" name="media_url" value="{{ old('media_url', $careContent->media_url) }}" placeholder="https://...">
                                @error('media_url')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="thumbnail" class="form-label fw-semibold">{{ __('Thumbnail') }}</label>
                                <input type="text" class="form-control @error('thumbnail') is-invalid @enderror" id="thumbnail" name="thumbnail" value="{{ old('thumbnail', $careContent->thumbnail) }}" placeholder="{{ __('Path or URL') }}">
                                @error('thumbnail')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="status" class="form-label fw-semibold">{{ __('Status') }} <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="active" {{ old('status', $careContent->status) === 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                                <option value="inactive" {{ old('status', $careContent->status) === 'inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.care-content.index') }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
                            <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-1"></i>{{ __('Update Content') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
