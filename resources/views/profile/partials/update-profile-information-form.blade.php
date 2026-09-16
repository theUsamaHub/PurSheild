@php
    $isVet = $user->hasRole('vet');
    $isShelter = $user->hasRole('shelter');
    $isOwner = $user->hasRole('owner');
@endphp

<section>
    <div class="pc-section-header pc-section-primary">
        <i class="bi bi-person"></i>
        <div>
            <h6>{{ __('Profile Information') }}</h6>
            <p>{{ __("Update your account's profile information.") }}</p>
        </div>
    </div>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-3" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <!-- Profile Photo -->
        <div class="mb-4 pc-photo-upload">
            <div>
                <img id="photo-preview"
                    @if ($user->profile_image)
                        src="{{ asset('storage/' . $user->profile_image) }}"
                    @else
                        src="data:image/svg+xml;base64,{{ base64_encode('<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'120\' height=\'120\'><rect width=\'120\' height=\'120\' rx=\'60\' fill=\'%231a6b3c\'/><text x=\'60\' y=\'72\' text-anchor=\'middle\' fill=\'white\' font-size=\'44\' font-weight=\'bold\'>' . substr($user->name, 0, 1) . '</text></svg>') }}"
                    @endif
                    alt="{{ $user->name }}"
                    class="rounded-circle pc-profile-avatar" style="width:120px;height:120px;object-fit:cover;">
            </div>
            <div>
                <label for="profile_image" class="pc-photo-upload-btn">
                    <i class="bi bi-camera"></i>{{ __('Change Photo') }}
                </label>
                <input type="file" id="profile_image" name="profile_image" accept="image/*" class="d-none"
                    onchange="if(this.files[0]) document.getElementById('photo-preview').src = window.URL.createObjectURL(this.files[0])">
                @error('profile_image')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <h6 class="fw-semibold mb-3 mt-4"><i class="bi bi-person me-1"></i>{{ __('Basic Information') }}</h6>

        <div class="row g-3">
            <div class="col-md-6">
                <label for="name" class="form-label fw-semibold">{{ __('Name') }}</label>
                <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                    value="{{ old('name', $user->name) }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label for="email" class="form-label fw-semibold">{{ __('Email') }}</label>
                <input type="email" id="email" class="form-control" value="{{ $user->email }}" disabled readonly>
                <div class="form-text">{{ __('Email cannot be changed.') }}</div>

                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                    <div class="mt-1">
                        <p class="text-muted mb-1" style="font-size: 0.8rem;">
                            {{ __('Your email address is unverified.') }}
                            <button form="send-verification" class="btn btn-link btn-sm p-0 text-decoration-none" style="font-size: 0.8rem;">
                                {{ __('Click here to re-send the verification email.') }}
                            </button>
                        </p>
                        @if (session('status') === 'verification-link-sent')
                            <div class="text-success small">{{ __('A new verification link has been sent to your email address.') }}</div>
                        @endif
                    </div>
                @endif
            </div>

            <div class="col-md-6">
                <label for="phone" class="form-label fw-semibold">{{ __('Phone') }}</label>
                <input type="text" id="phone" name="phone" class="form-control @error('phone') is-invalid @enderror"
                    value="{{ old('phone', $user->phone) }}" placeholder="{{ __('Enter phone number') }}">
                @error('phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label for="address" class="form-label fw-semibold">{{ __('Address') }}</label>
                <input type="text" id="address" name="address" class="form-control @error('address') is-invalid @enderror"
                    value="{{ old('address', $user->address) }}" placeholder="{{ __('Enter your address') }}">
                @error('address')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- ======================== VET FIELDS ======================== --}}
        @if ($isVet)
            <div class="pc-section-header pc-section-vet mt-4 mb-3">
                <i class="bi bi-heart-pulse"></i>
                <div>
                    <h6>{{ __('Veterinary Information') }}</h6>
                    <p>{{ __('Your professional details visible to pet owners.') }}</p>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="qualification" class="form-label fw-semibold">{{ __('Qualification') }}</label>
                    <input type="text" id="qualification" name="qualification"
                        class="form-control @error('qualification') is-invalid @enderror"
                        value="{{ old('qualification', $vetProfile->qualification ?? '') }}"
                        placeholder="{{ __('e.g. DVM, BVSc') }}">
                    @error('qualification')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="experience_years" class="form-label fw-semibold">{{ __('Years of Experience') }}</label>
                    <input type="number" id="experience_years" name="experience_years"
                        class="form-control @error('experience_years') is-invalid @enderror"
                        value="{{ old('experience_years', $vetProfile->experience_years ?? 0) }}"
                        min="0" max="50">
                    @error('experience_years')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="clinic_name" class="form-label fw-semibold">{{ __('Clinic Name') }}</label>
                    <input type="text" id="clinic_name" name="clinic_name"
                        class="form-control @error('clinic_name') is-invalid @enderror"
                        value="{{ old('clinic_name', $vetProfile->clinic_name ?? '') }}"
                        placeholder="{{ __('Enter clinic name') }}">
                    @error('clinic_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="consultation_fee" class="form-label fw-semibold">{{ __('Consultation Fee') }}</label>
                    <div class="input-group">
                        <span class="input-group-text">{{ __('$') }}</span>
                        <input type="number" id="consultation_fee" name="consultation_fee"
                            class="form-control @error('consultation_fee') is-invalid @enderror"
                            value="{{ old('consultation_fee', $vetProfile->consultation_fee ?? '') }}"
                            min="0" step="0.01">
                    </div>
                    @error('consultation_fee')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12">
                    <label for="clinic_address" class="form-label fw-semibold">{{ __('Clinic Address') }}</label>
                    <input type="text" id="clinic_address" name="clinic_address"
                        class="form-control @error('clinic_address') is-invalid @enderror"
                        value="{{ old('clinic_address', $vetProfile->clinic_address ?? '') }}"
                        placeholder="{{ __('Enter clinic address') }}">
                    @error('clinic_address')
                        <div class="invalid-feedback">{{ $message }}</div>
                <div class="col-md-6"><label for="vetCity" class="form-label">Clinic City</label><input class="form-control" id="vetCity" name="city" maxlength="100" value="{{ old('city',$vetProfile->city??'') }}"></div>
                <div class="col-md-3"><label for="vetLatitude" class="form-label">Clinic Latitude</label><input class="form-control" type="number" step="any" min="-90" max="90" id="vetLatitude" name="latitude" value="{{ old('latitude',$vetProfile->latitude??'') }}"></div>
                <div class="col-md-3"><label for="vetLongitude" class="form-label">Clinic Longitude</label><input class="form-control" type="number" step="any" min="-180" max="180" id="vetLongitude" name="longitude" value="{{ old('longitude',$vetProfile->longitude??'') }}"></div>
                <div class="col-12"><small class="text-muted">Add your clinic's map coordinates so pet owners can find nearby care.</small></div>
                <div class="col-md-6"><input type="hidden" name="online_consultation" value="0"><label><input type="checkbox" name="online_consultation" value="1" @checked(old('online_consultation',$vetProfile->online_consultation??false))> Online Consultation Available</label></div>
                <div class="col-md-6"><input type="hidden" name="emergency_services" value="0"><label><input type="checkbox" name="emergency_services" value="1" @checked(old('emergency_services',$vetProfile->emergency_services??false))> Emergency Services</label></div>

                    @enderror
                </div>

                <div class="col-12">
                    <label for="bio" class="form-label fw-semibold">{{ __('Bio') }}</label>
                    <textarea id="bio" name="bio" rows="3"
                        class="form-control @error('bio') is-invalid @enderror"
                        placeholder="{{ __('Tell pet owners about yourself...') }}">{{ old('bio', $vetProfile->bio ?? '') }}</textarea>
                    @error('bio')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">{{ __('Specializations') }}</label>
                    <div class="row g-2">
                        @forelse ($specializations as $spec)
                            <div class="col-auto">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="specializations[]"
                                        value="{{ $spec->id }}" id="spec_{{ $spec->id }}"
                                        {{ in_array($spec->id, old('specializations', $userSpecializationIds)) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="spec_{{ $spec->id }}">{{ $spec->name }}</label>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <span class="text-muted">{{ __('No specializations available.') }}</span>
                            </div>
                        @endforelse
                    </div>
                    @error('specializations')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        @endif

        {{-- ======================== SHELTER FIELDS ======================== --}}
        @if ($isShelter)
            <div class="pc-section-header pc-section-shelter mt-4 mb-3">
                <i class="bi bi-house-heart"></i>
                <div>
                    <h6>{{ __('Shelter Information') }}</h6>
                    <p>{{ __('Your shelter details visible to potential adopters.') }}</p>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="shelter_name" class="form-label fw-semibold">{{ __('Shelter Name') }}</label>
                    <input type="text" id="shelter_name" name="shelter_name"
                        class="form-control @error('shelter_name') is-invalid @enderror"
                        value="{{ old('shelter_name', $shelterProfile->shelter_name ?? '') }}"
                        placeholder="{{ __('Enter shelter name') }}">
                    @error('shelter_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="city" class="form-label fw-semibold">{{ __('City') }}</label>
                    <input type="text" id="city" name="city"
                        class="form-control @error('city') is-invalid @enderror"
                        value="{{ old('city', $shelterProfile->city ?? '') }}"
                        placeholder="{{ __('Enter city') }}">
                    @error('city')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12">
                    <label for="shelter_address" class="form-label fw-semibold">{{ __('Shelter Address') }}</label>
                    <input type="text" id="shelter_address" name="shelter_address"
                        class="form-control @error('shelter_address') is-invalid @enderror"
                        value="{{ old('shelter_address', $shelterProfile->address ?? '') }}"
                        placeholder="{{ __('Enter full shelter address') }}">
                    @error('shelter_address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="contact_number" class="form-label fw-semibold">{{ __('Contact Number') }}</label>
                    <input type="text" id="contact_number" name="contact_number"
                        class="form-control @error('contact_number') is-invalid @enderror"
                        value="{{ old('contact_number', $shelterProfile->contact_number ?? '') }}"
                        placeholder="{{ __('Enter contact number') }}">
                    @error('contact_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="capacity" class="form-label fw-semibold">{{ __('Animal Capacity') }}</label>
                    <input type="number" id="capacity" name="capacity"
                        class="form-control @error('capacity') is-invalid @enderror"
                        value="{{ old('capacity', $shelterProfile->capacity ?? '') }}"
                        min="1" placeholder="{{ __('Max animals') }}">
                    @error('capacity')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12">
                    <label for="website" class="form-label fw-semibold">{{ __('Website') }}</label>
                    <input type="url" id="website" name="website"
                        class="form-control @error('website') is-invalid @enderror"
                        value="{{ old('website', $shelterProfile->website ?? '') }}"
                        placeholder="{{ __('https://example.com') }}">
                    @error('website')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12">
                    <label for="description" class="form-label fw-semibold">{{ __('About the Shelter') }}</label>
                    <textarea id="description" name="description" rows="3"
                        class="form-control @error('description') is-invalid @enderror"
                        placeholder="{{ __('Describe your shelter mission and what you do...') }}">{{ old('description', $shelterProfile->description ?? '') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="latitude" class="form-label fw-semibold">{{ __('Google Maps Link') }}</label>
                    <input type="url" id="latitude" name="latitude"
                        class="form-control @error('latitude') is-invalid @enderror"
                        value="{{ old('latitude', $shelterProfile->latitude ?? '') }}"
                        placeholder="{{ __('https://maps.google.com/...') }}">
                    <div class="form-text">{{ __('Paste your Google Maps share link for map-based search.') }}</div>
                    @error('latitude')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        @endif

        <div class="d-flex align-items-center gap-3 mt-4">
            <button type="submit" class="pc-btn-primary">
                <i class="bi bi-check-lg"></i> {{ __('Save Changes') }}
            </button>

            @if (session('status') === 'profile-updated')
                <span
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="pc-saved-msg"
                ><i class="bi bi-check-circle"></i> {{ __('Saved.') }}</span>
            @endif
        </div>
    </form>
</section>
