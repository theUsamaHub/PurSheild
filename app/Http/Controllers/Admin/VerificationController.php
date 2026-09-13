<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShelterProfile;
use App\Models\VetProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class VerificationController extends Controller
{
    public function index(Request $request): View
    {
        $filter = $request->input('filter', 'all');

        $vetQuery = VetProfile::with(['user', 'verifiedBy']);
        $shelterQuery = ShelterProfile::with(['user', 'verifiedBy']);

        if ($filter === 'pending') {
            $vetQuery->where('is_verified', false)->whereNull('rejected_at');
            $shelterQuery->where('is_verified', false)->whereNull('rejected_at');
        } elseif ($filter === 'verified') {
            $vetQuery->where('is_verified', true);
            $shelterQuery->where('is_verified', true);
        } elseif ($filter === 'rejected') {
            $vetQuery->whereNotNull('rejected_at');
            $shelterQuery->whereNotNull('rejected_at');
        }

        $vets = $vetQuery->latest()->get();
        $shelters = $shelterQuery->latest()->get();

        $stats = [
            'total_pending' => VetProfile::where('is_verified', false)->whereNull('rejected_at')->count()
                + ShelterProfile::where('is_verified', false)->whereNull('rejected_at')->count(),
            'pending_vets' => VetProfile::where('is_verified', false)->whereNull('rejected_at')->count(),
            'pending_shelters' => ShelterProfile::where('is_verified', false)->whereNull('rejected_at')->count(),
            'verified' => VetProfile::where('is_verified', true)->count()
                + ShelterProfile::where('is_verified', true)->count(),
            'rejected' => VetProfile::whereNotNull('rejected_at')->count()
                + ShelterProfile::whereNotNull('rejected_at')->count(),
        ];

        return view('admin.verification.index', compact('vets', 'shelters', 'stats', 'filter'));
    }

    public function approveVet(VetProfile $profile): RedirectResponse
    {
        $profile->update([
            'is_verified' => true,
            'verified_at' => now(),
            'verified_by' => Auth::id(),
            'rejection_reason' => null,
            'rejected_at' => null,
        ]);

        $profile->user->update(['status' => 'active']);

        return redirect()->route('admin.verification.index')
            ->with('success', "Vet \"{$profile->user->name}\" has been verified successfully.");
    }

    public function rejectVet(VetProfile $profile, Request $request): RedirectResponse
    {
        $request->validate([
            'reason' => 'required|string|max:1000',
        ], [
            'reason.required' => 'Please provide a reason for rejection.',
            'reason.max' => 'Rejection reason must not exceed 1000 characters.',
        ]);

        $profile->update([
            'is_verified' => false,
            'rejection_reason' => $request->reason,
            'rejected_at' => now(),
            'verified_at' => null,
            'verified_by' => null,
        ]);

        return redirect()->route('admin.verification.index')
            ->with('success', "Vet \"{$profile->user->name}\" has been rejected.");
    }

    public function approveShelter(ShelterProfile $profile): RedirectResponse
    {
        $profile->update([
            'is_verified' => true,
            'verified_at' => now(),
            'verified_by' => Auth::id(),
            'rejection_reason' => null,
            'rejected_at' => null,
        ]);

        $profile->user->update(['status' => 'active']);

        return redirect()->route('admin.verification.index')
            ->with('success', "Shelter \"{$profile->shelter_name}\" has been verified successfully.");
    }

    public function rejectShelter(ShelterProfile $profile, Request $request): RedirectResponse
    {
        $request->validate([
            'reason' => 'required|string|max:1000',
        ], [
            'reason.required' => 'Please provide a reason for rejection.',
            'reason.max' => 'Rejection reason must not exceed 1000 characters.',
        ]);

        $profile->update([
            'is_verified' => false,
            'rejection_reason' => $request->reason,
            'rejected_at' => now(),
            'verified_at' => null,
            'verified_by' => null,
        ]);

        return redirect()->route('admin.verification.index')
            ->with('success', "Shelter \"{$profile->shelter_name}\" has been rejected.");
    }
}
