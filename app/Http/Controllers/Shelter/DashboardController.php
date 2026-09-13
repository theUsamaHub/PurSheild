<?php

namespace App\Http\Controllers\Shelter;

use App\Http\Controllers\Controller;
use App\Models\AdoptionApplication;
use App\Models\AdoptionListing;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $shelterId = Auth::id();

        $totalListings = AdoptionListing::where('shelter_id', $shelterId)->count();
        $availableListings = AdoptionListing::where('shelter_id', $shelterId)->where('status', 'available')->count();
        $adoptedListings = AdoptionListing::where('shelter_id', $shelterId)->where('status', 'adopted')->count();

        $pendingApplications = AdoptionApplication::whereHas('listing', function ($q) use ($shelterId) {
            $q->where('shelter_id', $shelterId);
        })->where('status', 'pending')->count();

        $approvedApplications = AdoptionApplication::whereHas('listing', function ($q) use ($shelterId) {
            $q->where('shelter_id', $shelterId);
        })->where('status', 'approved')->count();

        $totalApplications = AdoptionApplication::whereHas('listing', function ($q) use ($shelterId) {
            $q->where('shelter_id', $shelterId);
        })->count();

        $avgRating = Review::where('reviewable_type', \App\Models\AdoptionListing::class)
            ->whereIn('reviewable_id', function ($q) use ($shelterId) {
                $q->select('id')->from('adoption_listings')->where('shelter_id', $shelterId);
            })
            ->avg('rating');

        $recentApplications = AdoptionApplication::whereHas('listing', function ($q) use ($shelterId) {
            $q->where('shelter_id', $shelterId);
        })->with(['listing', 'applicant'])
          ->latest()
          ->limit(5)
          ->get();

        $recentListings = AdoptionListing::where('shelter_id', $shelterId)
            ->withCount('applications')
            ->latest()
            ->limit(5)
            ->get();

        return view('shelter.dashboard', compact(
            'totalListings', 'availableListings', 'adoptedListings',
            'pendingApplications', 'approvedApplications', 'totalApplications',
            'avgRating', 'recentApplications', 'recentListings'
        ));
    }
}
