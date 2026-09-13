<?php

namespace App\Http\Controllers\Shelter;

use App\Http\Controllers\Controller;
use App\Models\AdoptionListing;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function index(): View
    {
        $shelterId = Auth::id();

        $reviews = Review::where('reviewable_type', AdoptionListing::class)
            ->whereIn('reviewable_id', function ($q) use ($shelterId) {
                $q->select('id')->from('adoption_listings')->where('shelter_id', $shelterId);
            })
            ->with('user')
            ->latest()
            ->paginate(15);

        $avgRating = Review::where('reviewable_type', AdoptionListing::class)
            ->whereIn('reviewable_id', function ($q) use ($shelterId) {
                $q->select('id')->from('adoption_listings')->where('shelter_id', $shelterId);
            })
            ->avg('rating');

        $distribution = Review::where('reviewable_type', AdoptionListing::class)
            ->whereIn('reviewable_id', function ($q) use ($shelterId) {
                $q->select('id')->from('adoption_listings')->where('shelter_id', $shelterId);
            })
            ->selectRaw('rating, count(*) as count')
            ->groupBy('rating')
            ->pluck('count', 'rating');

        return view('shelter.reviews.index', compact('reviews', 'avgRating', 'distribution'));
    }
}
