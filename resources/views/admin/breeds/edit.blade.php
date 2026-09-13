@extends('layouts.app')

@section('content')
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 mb-0 fw-semibold">{{ __('Edit Breed') }} — {{ $breed->name }}</h2>
            <a href="{{ route('admin.breeds.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>{{ __('Back') }}</a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body p-4">
                    <form action="{{ route('admin.breeds.update', $breed) }}" method="POST">
                        @csrf @method('PUT')
                        <div class="mb-3">
                            <x-input-label for="species_id" :value="__('Species')" />
                            <select id="species_id" name="species_id" class="form-select" required>
                                <option value="">{{ __('Select Species') }}</option>
                                @foreach ($speciesList as $species)
                                    <option value="{{ $species->id }}" {{ old('species_id', $breed->species_id) == $species->id ? 'selected' : '' }}>{{ $species->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('species_id')" class="mt-1" />
                        </div>
                        <div class="mb-3">
                            <x-input-label for="name" :value="__('Breed Name')" />
                            <x-text-input id="name" name="name" type="text" class="form-control" :value="old('name', $breed->name)" required />
                            <x-input-error :messages="$errors->get('name')" class="mt-1" />
                        </div>
                        <div class="mb-3">
                            <x-input-label for="description" :value="__('Description')" />
                            <textarea id="description" name="description" class="form-control" rows="3">{{ old('description', $breed->description) }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-1" />
                        </div>
                        <div class="mb-4">
                            <x-input-label for="status" :value="__('Status')" />
                            <select id="status" name="status" class="form-select" required>
                                <option value="active" {{ old('status', $breed->status) === 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                                <option value="inactive" {{ old('status', $breed->status) === 'inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-1" />
                        </div>
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.breeds.index') }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
                            <x-primary-button>{{ __('Update Breed') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
