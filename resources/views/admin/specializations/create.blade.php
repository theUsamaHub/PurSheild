@extends('layouts.app')

@section('content')
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 mb-0 fw-semibold">{{ __('Create Specialization') }}</h2>
            <a href="{{ route('admin.specializations.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>{{ __('Back') }}</a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body p-4">
                    <form action="{{ route('admin.specializations.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <x-input-label for="name" :value="__('Specialization Name')" />
                            <x-text-input id="name" name="name" type="text" class="form-control" :value="old('name')" required />
                            <x-input-error :messages="$errors->get('name')" class="mt-1" />
                        </div>
                        <div class="mb-3">
                            <x-input-label for="description" :value="__('Description')" />
                            <textarea id="description" name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-1" />
                        </div>
                        <div class="mb-4">
                            <x-input-label for="status" :value="__('Status')" />
                            <select id="status" name="status" class="form-select" required>
                                <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                                <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-1" />
                        </div>
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.specializations.index') }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
                            <x-primary-button>{{ __('Create Specialization') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
