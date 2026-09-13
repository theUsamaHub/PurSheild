<?php

namespace App\Http\Controllers\Shelter;

use App\Http\Controllers\Controller;
use App\Models\AdoptionApplication;
use App\Models\AdoptionListing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ApplicationController extends Controller
{
    public function index(Request $request): View
    {
        $shelterId = Auth::id();

        $query = AdoptionApplication::whereHas('listing', function ($q) use ($shelterId) {
            $q->where('shelter_id', $shelterId);
        })->with(['listing', 'applicant']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('message', 'like', "%{$search}%")
                  ->orWhereHas('applicant', function ($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('listing', function ($q2) use ($search) {
                      $q2->where('pet_name', 'like', "%{$search}%");
                  });
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $applications = $query->latest()->paginate(12);

        $stats = [
            'total' => AdoptionApplication::whereHas('listing', function ($q) use ($shelterId) {
                $q->where('shelter_id', $shelterId);
            })->count(),
            'pending' => AdoptionApplication::whereHas('listing', function ($q) use ($shelterId) {
                $q->where('shelter_id', $shelterId);
            })->where('status', 'pending')->count(),
            'approved' => AdoptionApplication::whereHas('listing', function ($q) use ($shelterId) {
                $q->where('shelter_id', $shelterId);
            })->where('status', 'approved')->count(),
            'rejected' => AdoptionApplication::whereHas('listing', function ($q) use ($shelterId) {
                $q->where('shelter_id', $shelterId);
            })->where('status', 'rejected')->count(),
        ];

        return view('shelter.applications.index', compact('applications', 'stats'));
    }

    public function show(AdoptionApplication $application): View
    {
        $listing = $application->listing;
        abort_unless($listing->shelter_id === Auth::id(), 403);

        $application->load(['listing', 'applicant']);

        return view('shelter.applications.show', compact('application'));
    }

    public function updateStatus(Request $request, AdoptionApplication $application)
    {
        $listing = $application->listing;
        abort_unless($listing->shelter_id === Auth::id(), 403);

        $validated = $request->validate([
            'status' => 'required|in:approved,rejected',
            'shelter_response' => 'nullable|string|max:1000',
        ]);

        $application->update($validated);

        return redirect()->route('shelter.applications.show', $application)
            ->with('success', "Application {$validated['status']} successfully.");
    }

    public function finalizeAdoption(AdoptionApplication $application)
    {
        $listing = $application->listing;
        abort_unless($listing->shelter_id === Auth::id(), 403);

        $application->update(['status' => 'approved']);

        $listing->update(['status' => 'adopted']);

        AdoptionApplication::where('listing_id', $listing->id)
            ->where('id', '!=', $application->id)
            ->whereIn('status', ['pending'])
            ->update(['status' => 'rejected']);

        return redirect()->route('shelter.applications.show', $application)
            ->with('success', 'Adoption finalized! Listing marked as adopted and other pending applications have been rejected.');
    }
}
