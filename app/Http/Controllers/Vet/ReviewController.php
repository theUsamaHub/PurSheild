<?php

namespace App\Http\Controllers\Vet;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function index(): View
    {
        $vetId = Auth::id();

        $reviews = Review::where('reviewable_type', \App\Models\User::class)
            ->where('reviewable_id', $vetId)
            ->with('user')
            ->latest()
            ->paginate(15);

        $avgRating = Review::where('reviewable_type', \App\Models\User::class)
            ->where('reviewable_id', $vetId)
            ->avg('rating');

        $totalReviews = Review::where('reviewable_type', \App\Models\User::class)
            ->where('reviewable_id', $vetId)
            ->count();

        $ratingDistribution = Review::where('reviewable_type', \App\Models\User::class)
            ->where('reviewable_id', $vetId)
            ->selectRaw('rating, count(*) as count')
            ->groupBy('rating')
            ->pluck('count', 'rating')
            ->toArray();

        return view('vet.reviews.index', compact('reviews', 'avgRating', 'totalReviews', 'ratingDistribution'));
    }
}
