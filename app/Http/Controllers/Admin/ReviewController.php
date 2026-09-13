<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function index(Request $request): View
    {
        $query = Review::with(['user', 'reviewable']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($q2) use ($search) {
                    $q2->where('name', 'like', "%{$search}%");
                })->orWhere('comment', 'like', "%{$search}%");
            });
        }

        if ($rating = $request->input('rating')) {
            $query->where('rating', $rating);
        }

        if ($type = $request->input('type')) {
            $typeMap = [
                'product' => 'App\\Models\\Product',
                'vet' => 'App\\Models\\User',
                'shelter' => 'App\\Models\\AdoptionListing',
            ];
            if (isset($typeMap[$type])) {
                $query->where('reviewable_type', $typeMap[$type]);
            }
        }

        $reviews = $query->latest()->paginate(15);

        $statsRow = Review::query()
            ->selectRaw("count(*) as total")
            ->selectRaw("round(avg(rating), 1) as avg_rating")
            ->selectRaw("count(case when rating = 5 then 1 end) as five_star")
            ->selectRaw("count(case when rating = 1 then 1 end) as one_star")
            ->first();

        $typeCounts = Review::selectRaw("reviewable_type, count(*) as count")
            ->groupBy('reviewable_type')
            ->pluck('count', 'reviewable_type');

        $stats = [
            'total' => $statsRow->total ?? 0,
            'average' => $statsRow->avg_rating ?? 0,
            'five_star' => $statsRow->five_star ?? 0,
            'one_star' => $statsRow->one_star ?? 0,
            'vet_count' => $typeCounts->get('App\\Models\\User', 0),
            'product_count' => $typeCounts->get('App\\Models\\Product', 0),
            'shelter_count' => $typeCounts->get('App\\Models\\AdoptionListing', 0),
        ];

        return view('admin.reviews.index', compact('reviews', 'stats'));
    }

    public function destroy(Review $review): RedirectResponse
    {
        $review->delete();

        return redirect()->route('admin.reviews.index')
            ->with('success', 'Review deleted successfully.');
    }
}
