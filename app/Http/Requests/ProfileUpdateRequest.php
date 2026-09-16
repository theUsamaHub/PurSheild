<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        $user = $this->user();
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:500'],
            'profile_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];

        if ($user->hasRole('vet')) {
            $rules = array_merge($rules, [
                'qualification' => ['nullable', 'string', 'max:255'],
                'experience_years' => ['nullable', 'integer', 'min:0', 'max:50'],
                'clinic_name' => ['nullable', 'string', 'max:255'],
                'clinic_address' => ['nullable', 'string', 'max:500'],
                'city' => ['nullable', 'string', 'max:100'],
                'latitude' => ['nullable', 'required_with:longitude', 'numeric', 'between:-90,90'],
                'longitude' => ['nullable', 'required_with:latitude', 'numeric', 'between:-180,180'],
                'online_consultation' => ['sometimes', 'boolean'],
                'emergency_services' => ['sometimes', 'boolean'],
                'consultation_fee' => ['nullable', 'numeric', 'min:0'],
                'bio' => ['nullable', 'string', 'max:1000'],
                'specializations' => ['nullable', 'array'],
                'specializations.*' => ['exists:specializations,id'],
            ]);
        }

        if ($user->hasRole('shelter')) {
            $rules = array_merge($rules, [
                'shelter_name' => ['nullable', 'string', 'max:255'],
                'description' => ['nullable', 'string', 'max:1000'],
                'shelter_address' => ['nullable', 'string', 'max:500'],
                'city' => ['nullable', 'string', 'max:255'],
                'contact_number' => ['nullable', 'string', 'max:30'],
                'website' => ['nullable', 'url', 'max:255'],
                'capacity' => ['nullable', 'integer', 'min:1'],
            ]);
        }

        return $rules;
    }
}
