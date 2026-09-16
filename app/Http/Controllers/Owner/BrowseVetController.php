<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Review;
use App\Models\Specialization;
use App\Models\User;
use App\Models\VetAvailability;
use App\Models\VetProfile;
use App\Support\OwnerDiscovery;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;

class BrowseVetController extends Controller
{
    public function index(Request $request): View
    {
        $f = $request->validate(['search' => ['nullable', 'string', 'max:200'], 'specialization_id' => ['nullable', 'integer', 'exists:specializations,id'], 'location' => ['nullable', 'string', 'max:100'], 'availability' => ['nullable', 'in:today,tomorrow,week'], 'rating' => ['nullable', 'numeric', 'between:0,5'], 'online' => ['nullable', 'boolean'], 'emergency' => ['nullable', 'boolean'], 'sort' => ['nullable', 'in:rating,name,nearest,fee'], 'view' => ['nullable', 'in:grid,list'], 'latitude' => ['nullable', 'required_with:longitude,distance', 'numeric', 'between:-90,90'], 'longitude' => ['nullable', 'required_with:latitude,distance', 'numeric', 'between:-180,180'], 'distance' => ['nullable', 'in:5,10,25,50,100'], 'favorites' => ['nullable', 'boolean'], 'compare' => ['nullable', 'array', 'max:3'], 'compare.*' => ['integer', 'distinct']]);
        $query = OwnerDiscovery::vets()->with(['vetProfile', 'specializations']);
        $search = trim($f['search'] ?? '');
        if ($search !== '') {
            $query->where(fn ($q) => $q->where('name', 'like', "%{$search}%")->orWhereHas('vetProfile', fn ($p) => $p->where('clinic_name', 'like', "%{$search}%")->orWhere('clinic_address', 'like', "%{$search}%")->orWhere('city', 'like', "%{$search}%")));
        }
        if ($f['specialization_id'] ?? null) {
            $query->whereHas('specializations', fn ($q) => $q->where('specializations.id', $f['specialization_id']));
        }
        if ($f['location'] ?? null) {
            $query->whereHas('vetProfile', fn ($q) => $q->where('city', 'like', '%'.$f['location'].'%')->orWhere('clinic_address', 'like', '%'.$f['location'].'%'));
        }
        foreach (['online' => 'online_consultation', 'emergency' => 'emergency_services'] as $filter => $column) {
            if ($f[$filter] ?? false) {
                $query->whereHas('vetProfile', fn ($q) => $q->where($column, true));
            }
        }
        $favorites = OwnerDiscovery::favorites('vet');
        if ($f['favorites'] ?? false) {
            $query->whereIn('id', $favorites);
        }
        $all = $query->get();
        $ids = $all->pluck('id');
        $reviewStats = Review::where('reviewable_type', User::class)->whereIn('reviewable_id', $ids)->selectRaw('reviewable_id,AVG(rating) AS avg_rating,COUNT(*) AS review_count')->groupBy('reviewable_id')->get()->keyBy('reviewable_id');
        $slots = VetAvailability::whereIn('vet_id', $ids)->get()->groupBy('vet_id');
        $bookings = Appointment::whereIn('vet_id', $ids)->whereBetween('appointment_date', [today()->toDateString(), today()->addDays(6)->toDateString()])->whereIn('status', ['pending', 'approved', 'rescheduled'])->get()->groupBy('vet_id');
        foreach ($all as $vet) {
            $vet->rating = (float) ($reviewStats[$vet->id]->avg_rating ?? 0);
            $vet->review_count = (int) ($reviewStats[$vet->id]->review_count ?? 0);
            $vet->distance = null;
            if (isset($f['latitude'],$f['longitude']) && $vet->vetProfile->latitude !== null && $vet->vetProfile->longitude !== null) {
                $vet->distance = OwnerDiscovery::distance($f['latitude'], $f['longitude'], $vet->vetProfile->latitude, $vet->vetProfile->longitude);
            }
            $vet->next_available = null;
            $vet->available_days = [];
            for ($i = 0; $i < 7; $i++) {
                $date = today()->addDays($i);
                $times = OwnerDiscovery::availableTimes($vet->id, $date, $slots->get($vet->id, collect()), $bookings->get($vet->id, collect())->filter(fn ($b) => $b->appointment_date->isSameDay($date))->pluck('appointment_time')->all());
                if ($times) {
                    $vet->available_days = array_merge($vet->available_days, [$i]);
                    if (! $vet->next_available) {
                        $vet->next_available = ['day' => $i, 'date' => $date, 'time' => $times[0], 'end' => end($times)];
                    }
                }
            }
        }
        $all = $all->filter(fn ($v) => $v->rating >= (float) ($f['rating'] ?? 0));
        if ($f['distance'] ?? null) {
            $all = $all->filter(fn ($v) => $v->distance !== null && $v->distance <= (float) $f['distance']);
        }
        if ($f['availability'] ?? null) {
            $all = $all->filter(fn ($v) => match ($f['availability']) {
                'today' => in_array(0, $v->available_days),'tomorrow' => in_array(1, $v->available_days),default => count($v->available_days) > 0
            });
        }
        $all = match ($f['sort'] ?? 'rating') {
            'name' => $all->sortBy('name'),'fee' => $all->sortBy(fn ($v) => $v->vetProfile->consultation_fee ?? INF),'nearest' => $all->sortBy(fn ($v) => $v->distance ?? INF),default => $all->sortByDesc('rating')
        };
        $page = LengthAwarePaginator::resolveCurrentPage();
        $vets = new LengthAwarePaginator($all->forPage($page, 6)->values(), $all->count(), 6, $page, ['path' => $request->url(), 'query' => $request->query()]);
        $specializations = Specialization::where('status', 'active')->orderBy('name')->get();
        $locations = VetProfile::whereHas('user', fn ($q) => $q->where('status', 'active'))->where('is_verified', true)->whereNotNull('city')->where('city', '!=', '')->distinct()->orderBy('city')->pluck('city');
        $comparison = OwnerDiscovery::vets()->with(['vetProfile', 'specializations'])->whereIn('id', $f['compare'] ?? [])->get();

        return view('owner.browse-vets', compact('vets', 'specializations', 'locations', 'favorites', 'comparison'));
    }

    public function show(User $vet): View
    {
        if (! OwnerDiscovery::vets()->whereKey($vet->id)->exists()) {
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
