@extends('layouts.owner.app')

@section('content')
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 mb-0 fw-semibold">{{ __('Book Appointment') }}</h2>
            <a href="{{ route('owner.appointments.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i>{{ __('Back') }}
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body p-4">
                    <form action="{{ route('owner.appointments.store') }}" method="POST">
                        @csrf

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <x-input-label for="pet_id" :value="__('Select Pet')" />
                                <select class="form-select @error('pet_id') is-invalid @enderror" id="pet_id" name="pet_id" required>
                                    <option value="">{{ __('Choose a pet') }}</option>
                                    @foreach($pets as $pet)
                                        <option value="{{ $pet->id }}" {{ old('pet_id') == $pet->id ? 'selected' : '' }}>
                                            {{ $pet->name }} - {{ $pet->species->name ?? '' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('pet_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <x-input-label for="vet_id" :value="__('Select Veterinarian')" />
                                <div class="position-relative" x-data="{ search: '', open: false }" @click.outside="open = false">
                                    <input type="text" class="form-control" placeholder="{{ __('Search by name or clinic...') }}"
                                        x-model="search" @focus="open = true" @input="open = true"
                                        style="border-bottom-left-radius:0;border-bottom-right-radius:0;">
                                    <div class="position-absolute w-100 bg-white border border-top-0 rounded-bottom shadow-sm" style="z-index:1050;max-height:250px;overflow-y:auto;display:none;" x-show="open && search.length > 0" x-transition>
                                        @foreach($vets as $vet)
                                            <div class="px-3 py-2 cursor-pointer hover-bg" style="font-size:0.875rem;"
                                                x-show="'{{ strtolower($vet->name) }}'.includes(search.toLowerCase()) || '{{ strtolower($vet->vetProfile->clinic_name ?? '') }}'.includes(search.toLowerCase())"
                                                @click="$refs.vetId.value = '{{ $vet->id }}'; search = ''; open = false; $refs.vetDisplay.value = '{{ $vet->name }} - {{ $vet->vetProfile->clinic_name ?? $vet->vetProfile->qualification ?? '' }}'">
                                                <strong>{{ $vet->name }}</strong>
                                                <small class="text-muted">- {{ $vet->vetProfile->clinic_name ?? $vet->vetProfile->qualification ?? '' }}</small>
                                            </div>
                                        @endforeach
                                    </div>
                                    <input type="hidden" name="vet_id" x-ref="vetId" value="{{ old('vet_id', $selectedVetId) }}">
                                    <input type="text" class="form-control @error('vet_id') is-invalid @enderror" readonly x-ref="vetDisplay"
                                        value="{{ old('vet_id', $selectedVetId) ? $vets->firstWhere('id', old('vet_id', $selectedVetId))?->name . ' - ' . ($vets->firstWhere('id', old('vet_id', $selectedVetId))?->vetProfile->clinic_name ?? '') : '' }}"
                                        placeholder="{{ __('Choose a vet') }}" required
                                        style="border-top-left-radius:0;border-top-right-radius:0;">
                                    @error('vet_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <x-input-label for="appointment_date" :value="__('Appointment Date')" />
                                <x-text-input id="appointment_date" name="appointment_date" type="date" class="form-control" :value="old('appointment_date')" min="{{ date('Y-m-d') }}" required />
                                <x-input-error :messages="$errors->get('appointment_date')" class="mt-1" />
                            </div>
                            <div class="col-md-6">
                                <x-input-label for="appointment_time" :value="__('Appointment Time')" />
                                <x-text-input id="appointment_time" name="appointment_time" type="time" class="form-control" :value="old('appointment_time')" required />
                                <x-input-error :messages="$errors->get('appointment_time')" class="mt-1" />
                            </div>
                        </div>

                        <div class="mb-3">
                            <x-input-label for="reason" :value="__('Reason for Visit')" />
                            <textarea class="form-control @error('reason') is-invalid @enderror" id="reason" name="reason" rows="4" required>{{ old('reason') }}</textarea>
                            @error('reason')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">{{ __('Please describe the reason for the appointment (symptoms, checkup, vaccination, etc.)') }}</small>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('owner.appointments.index') }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
                            <x-primary-button>{{ __('Book Appointment') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0 fw-semibold">{{ __('Booking Tips') }}</h6>
                </div>
                <div class="card-body">
                    <ul class="mb-0" style="font-size: 0.875rem;">
                        <li class="mb-2">{{ __('Select the pet that needs medical attention.') }}</li>
                        <li class="mb-2">{{ __('Choose a veterinarian based on their specialization.') }}</li>
                        <li class="mb-2">{{ __('Provide a detailed reason to help the vet prepare.') }}</li>
                        <li class="mb-2">{{ __('You can cancel pending appointments from the appointments list.') }}</li>
                        <li class="mb-0">{{ __('Please arrive 10 minutes before your scheduled time.') }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <style>
        .hover-bg:hover { background: #f0f7f2; cursor: pointer; }
    </style>
@endsection
