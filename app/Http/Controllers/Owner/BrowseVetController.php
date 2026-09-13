<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Specialization;
use App\Models\User;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BrowseVetController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::whereHas('roles', function ($q) {
            $q->where('slug', 'vet');
        })->whereHas('vetProfile', function ($q) {
            $q->where('is_verified', true);
        })->with(['vetProfile', 'specializations']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($specializationId = $request->input('specialization_id')) {
            $query->whereHas('specializations', function ($q) use ($specializationId) {
                $q->where('specializations.id', $specializationId);
            });
        }

        $vets = $query->latest()->paginate(15);
        $specializations = Specialization::where('status', 'active')->orderBy('name')->get();

        $vetIds = $vets->pluck('id')->toArray();
        $reviewStats = Review::whereIn('reviewable_id', $vetIds)
            ->where('reviewable_type', User::class)
            ->selectRaw('reviewable_id, avg(rating) as avg_rating, count(*) as review_count')
            ->groupBy('reviewable_id')
            ->get()
            ->keyBy('reviewable_id');

        $userReviewedVets = Review::where('user_id', auth()->id())
            ->where('reviewable_type', User::class)
            ->whereIn('reviewable_id', $vetIds)
            ->pluck('reviewable_id')
            ->toArray();

        return view('owner.browse-vets', compact('vets', 'specializations', 'reviewStats', 'userReviewedVets'));
    }

    public function show(User $vet): View
    {
        if (!$vet->hasRole('vet')) {
            abort(404);
        }

        $vet->load(['vetProfile', 'specializations']);

        $reviews = Review::where('reviewable_type', User::class)
            ->where('reviewable_id', $vet->id)
            ->with('user')
            ->latest()
            ->paginate(10);

        $avgRating = Review::where('reviewable_type', User::class)
            ->where('reviewable_id', $vet->id)
            ->avg('rating');

        $reviewCount = Review::where('reviewable_type', User::class)
            ->where('reviewable_id', $vet->id)
            ->count();

        $hasReviewed = Review::where('user_id', auth()->id())
            ->where('reviewable_type', User::class)
            ->where('reviewable_id', $vet->id)
            ->exists();

        return view('owner.vet-detail', compact('vet', 'reviews', 'avgRating', 'reviewCount', 'hasReviewed'));
    }
}
