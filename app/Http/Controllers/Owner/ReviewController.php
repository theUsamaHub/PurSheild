<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request, User $vet): RedirectResponse
    {
        if (!$vet->hasRole('vet')) {
            return back()->with('error', 'You can only review veterinarians.');
        }

        $existingReview = Review::where('user_id', Auth::id())
            ->where('reviewable_type', User::class)
            ->where('reviewable_id', $vet->id)
            ->first();

        if ($existingReview) {
            return back()->with('error', 'You have already reviewed this veterinarian.');
        }

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:2000'],
        ], [
            'rating.required' => 'Please select a rating.',
            'rating.integer' => 'Rating must be a whole number.',
            'rating.min' => 'Rating must be at least 1.',
            'rating.max' => 'Rating must not exceed 5.',
            'comment.max' => 'Comment must not exceed 2000 characters.',
        ]);

        Review::create([
            'user_id' => Auth::id(),
            'reviewable_type' => User::class,
            'reviewable_id' => $vet->id,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? null,
        ]);

        return redirect()->back()
            ->with('success', 'Review submitted successfully.');
    }
}
