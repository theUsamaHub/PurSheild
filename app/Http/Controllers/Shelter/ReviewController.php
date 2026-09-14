<?php

namespace App\Http\Controllers\Shelter;

use App\Http\Controllers\Controller;
use App\Models\AdoptionListing;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $f = $request->validate(['search' => ['nullable', 'string', 'max:200'], 'rating' => ['nullable', 'integer', 'between:1,5'], 'sort' => ['nullable', 'in:newest,oldest,highest,lowest']]);
        $base = Review::where('reviewable_type', AdoptionListing::class)->whereIn('reviewable_id', AdoptionListing::where('shelter_id', Auth::id())->select('id'));
        $q = (clone $base)->with(['user', 'reviewable']);
        $search = trim($f['search'] ?? '');
        if ($search !== '') {
            $q->where(fn ($q) => $q->where('comment', 'like', "%{$search}%")->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%"))->orWhereHasMorph('reviewable', [AdoptionListing::class], fn ($l) => $l->where('pet_name', 'like', "%{$search}%")));
        }
        if ($f['rating'] ?? null) {
            $q->where('rating', $f['rating']);
        }
        match ($f['sort'] ?? 'newest') {
            'oldest' => $q->oldest(),'highest' => $q->orderByDesc('rating'),'lowest' => $q->orderBy('rating'),default => $q->latest()
        };
        $reviews = $q->orderByDesc('id')->paginate(10)->withQueryString();
        $avgRating = (clone $base)->avg('rating');
        $totalReviews = (clone $base)->count();

        return view('shelter.reviews.index', compact('reviews','avgRating','totalReviews'));
    }
}
