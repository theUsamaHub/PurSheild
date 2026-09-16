<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Specialization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        $user = $request->user();
        $vetProfile = $user->vetProfile;
        $shelterProfile = $user->shelterProfile;
        $specializations = Specialization::where('status', 'active')->get();
        $userSpecializationIds = $user->specializations()->pluck('specializations.id')->toArray();

        return view('profile.edit', [
            'user' => $user,
            'vetProfile' => $vetProfile,
            'shelterProfile' => $shelterProfile,
            'specializations' => $specializations,
            'userSpecializationIds' => $userSpecializationIds,
        ]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $user = $request->user();

        if ($request->hasFile('profile_image')) {
            if ($user->profile_image) {
                Storage::disk('public')->delete($user->profile_image);
            }
            $validated['profile_image'] = $request->file('profile_image')->store('profile', 'public');
        }

        unset($validated['email']);
        $user->fill(collect($validated)->only(['name', 'phone', 'address', 'profile_image'])->toArray());
        $user->save();

        if ($user->hasRole('vet')) {
            $vetData = collect($validated)->only([
                'qualification', 'experience_years', 'clinic_name',
                'clinic_address', 'consultation_fee', 'bio', 'city', 'latitude', 'longitude', 'online_consultation', 'emergency_services',
            ])->toArray();

            if ($user->vetProfile) {
                $user->vetProfile->update($vetData);
            } else {
                $user->vetProfile()->create($vetData);
            }

            if ($request->has('specializations')) {
                $user->specializations()->sync($request->input('specializations', []));
            } else {
                $user->specializations()->detach();
            }
        }

        if ($user->hasRole('shelter')) {
            $shelterData = collect($validated)->only([
                'shelter_name', 'description', 'shelter_address',
                'city', 'contact_number', 'website', 'capacity',
                'latitude', 'longitude',
            ])->toArray();

            if ($user->shelterProfile) {
                $user->shelterProfile->update($shelterData);
            } else {
                $user->shelterProfile()->create($shelterData);
            }
        }

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
