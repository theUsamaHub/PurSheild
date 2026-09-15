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
        $f = $request->validate(['search' => ['nullable', 'string', 'max:200'], 'rating' => ['nullable', 'integer', 'between:1,5'], 'sort' => ['nullable', 'in:latest,newest,oldest,highest,lowest'], 'response' => ['nullable', 'in:all,unanswered,replied']]);
        $base = Review::where('reviewable_type', AdoptionListing::class)->whereIn('reviewable_id', AdoptionListing::where('shelter_id', Auth::id())->select('id'));
        $q = (clone $base)->with(['user', 'reviewable']);
        $search = trim($f['search'] ?? '');
        if ($search !== '') {
            $q->where(fn ($q) => $q->where('comment', 'like', "%{$search}%")->orWhere('reply', 'like', "%{$search}%")->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%"))->orWhereHasMorph('reviewable', [AdoptionListing::class], fn ($l) => $l->where('pet_name', 'like', "%{$search}%")));
        }
        if ($f['rating'] ?? null) {
            $q->where('rating', $f['rating']);
        }
        if (($f['response'] ?? '') === 'unanswered') {
            $q->whereNull('replied_at');
        } elseif (($f['response'] ?? '') === 'replied') {
            $q->whereNotNull('replied_at');
        }
        match ($f['sort'] ?? 'latest') {
            'oldest' => $q->oldest(),'highest' => $q->orderByDesc('rating')->latest(),'lowest' => $q->orderBy('rating')->latest(),default => $q->latest()
        };
        $reviews = $q->orderByDesc('id')->paginate(10)->withQueryString();
        $avgRating = (clone $base)->avg('rating');
        $totalReviews = (clone $base)->count();

        return view('shelter.reviews.index', compact('reviews', 'avgRating', 'totalReviews'));
    }

    public function reply(Request $request, Review $review)
    {
        abort_unless($review->reviewable_type === AdoptionListing::class
            && AdoptionListing::whereKey($review->reviewable_id)->where('shelter_id', Auth::id())->exists(), 403);

        $validated = $request->validate(['reply' => ['required', 'string', 'max:2000']], [
            'reply.required' => 'Please enter a reply.',
        ]);
        $review->update(['reply' => $validated['reply'], 'replied_at' => now(), 'status' => 'resolved']);

        return back()->with('success', 'Response saved successfully.');
    }
}
