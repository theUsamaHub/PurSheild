<?php

namespace App\Http\Controllers\Vet;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:200'],
            'rating' => ['nullable', 'integer', 'between:1,5'],
            'sort' => ['nullable', 'in:latest,oldest,highest,lowest'],
            'response' => ['nullable', 'in:all,unanswered,replied'],
        ]);
        $vetId = Auth::id();

        $query = Review::where('reviewable_type', \App\Models\User::class)
            ->where('reviewable_id', $vetId)
            ->with('user');

        $search = trim($filters['search'] ?? '');
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('comment', 'like', "%{$search}%")
                  ->orWhere('reply', 'like', "%{$search}%")
                  ->orWhereHas('user', fn ($uq) => $uq->where('name', 'like', "%{$search}%"));
            });
        }

        if ($rating = $filters['rating'] ?? null) {
            $query->where('rating', $rating);
        }

        match ($filters['sort'] ?? 'latest') {
            'oldest'  => $query->oldest(),
            'highest' => $query->orderByDesc('rating')->latest(),
            'lowest'  => $query->orderBy('rating')->latest(),
            default   => $query->latest(),
        };

        if (($filters['response'] ?? '') === 'unanswered') {
            $query->whereNull('replied_at');
        } elseif (($filters['response'] ?? '') === 'replied') {
            $query->whereNotNull('replied_at');
        }

        $reviews = $query->orderByDesc('id')->paginate(15)->withQueryString();

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

    public function reply(Request $request, Review $review): RedirectResponse
    {
        abort_unless($review->reviewable_type === \App\Models\User::class && (int) $review->reviewable_id === (int) Auth::id(), 403);

        $validated = $request->validate([
            'reply' => ['required', 'string', 'max:2000'],
        ], [
            'reply.required' => 'Please enter a reply.',
        ]);

        $review->update([
            'reply'      => $validated['reply'],
            'replied_at' => now(),
            'status'     => 'resolved',
        ]);

        return back()->with('success', 'Response saved successfully.');
    }
}
