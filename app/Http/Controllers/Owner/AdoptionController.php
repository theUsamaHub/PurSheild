<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\AdoptionApplication;
use App\Models\AdoptionListing;
use App\Models\Breed;
use App\Models\FurshieldNotification;
use App\Models\ShelterProfile;
use App\Models\Species;
use App\Support\OwnerDiscovery;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AdoptionController extends Controller
{
    public function index(Request $request): View
    {
        $f = $request->validate(['search' => ['nullable', 'string', 'max:200'], 'species_id' => ['nullable', 'integer', 'exists:species,id'], 'breed_id' => ['nullable', 'integer', 'exists:breeds,id'], 'gender' => ['nullable', 'in:male,female'], 'health_status' => ['nullable', 'in:healthy,under_treatment,vaccination_due'], 'age' => ['nullable', 'in:baby,young,adult,senior'], 'location' => ['nullable', 'string', 'max:100'], 'favorites' => ['nullable', 'boolean']]);
        $base = OwnerDiscovery::listings();
        $counts = (clone $base)->with('species')->selectRaw('species_id,COUNT(*) AS total')->groupBy('species_id')->get();
        $total = $counts->sum('total');
        $query = (clone $base)->with(['species', 'breed', 'images', 'shelter.shelterProfile']);
        $search = trim($f['search'] ?? '');
        if ($search !== '') {
            $query->where(fn ($q) => $q->where('pet_name', 'like', "%{$search}%")->orWhere('description', 'like', "%{$search}%")->orWhereHas('breed', fn ($b) => $b->where('name', 'like', "%{$search}%"))->orWhereHas('species', fn ($b) => $b->where('name', 'like', "%{$search}%")));
        }
        foreach (['species_id', 'breed_id', 'gender'] as $field) {
            if ($f[$field] ?? null) {
                $query->where($field, $f[$field]);
            }
        }
        if ($f['health_status'] ?? null) {
            $query->where('health_state', $f['health_status']);
        }
        if ($f['location'] ?? null) {
            $query->whereHas('shelter.shelterProfile', fn ($q) => $q->where('city', $f['location']));
        }
        $favorites = OwnerDiscovery::favorites('pet');
        if ($f['favorites'] ?? false) {
            $query->whereIn('id', $favorites);
        }
        if ($f['age'] ?? null) {
            $matching = (clone $query)->get(['id', 'age'])->filter(function ($pet) use ($f) {
                $m = OwnerDiscovery::ageMonths($pet->age);

                return $m !== null && match ($f['age']) {
                    'baby' => $m < 12,'young' => $m >= 12 && $m < 36,'adult' => $m >= 36 && $m < 84,'senior' => $m >= 84
                };
            })->pluck('id');
            $query->whereIn('id', $matching);
        }
        $listings = $query->latest()->orderByDesc('id')->paginate(8)->withQueryString();
        $species = Species::where('status', 'active')->orderBy('name')->get();
        $breeds = Breed::orderBy('name')->get();
        $locations = ShelterProfile::whereHas('user', fn ($q) => $q->where('status', 'active'))->whereNotNull('city')->where('city', '!=', '')->distinct()->orderBy('city')->pluck('city');

        return view('owner.adoption.index', compact('listings', 'species', 'breeds', 'locations', 'counts', 'total', 'favorites'));
    }

    public function show(AdoptionListing $listing): View
    {
        abort_unless(OwnerDiscovery::listings()->whereKey($listing->id)->exists() || AdoptionApplication::where('listing_id', $listing->id)->where('applicant_id', Auth::id())->exists(), 404);
        $listing->load(['species', 'breed', 'shelter.shelterProfile', 'images', 'reviews.user']);

        $hasApplied = AdoptionApplication::where('listing_id', $listing->id)
            ->where('applicant_id', Auth::id())
            ->exists();

        $avgRating = $listing->reviews->avg('rating');
        $reviewCount = $listing->reviews->count();

        return view('owner.adoption.show', compact('listing', 'hasApplied', 'avgRating', 'reviewCount'));
    }

    public function apply(Request $request, AdoptionListing $listing): RedirectResponse
    {
        if ($listing->status !== 'available' || ! $listing->shelter || $listing->shelter->status !== 'active') {
            return back()->with('error', 'This listing is no longer available.');
        }

        $existingApplication = AdoptionApplication::where('listing_id', $listing->id)
            ->where('applicant_id', Auth::id())
            ->first();

        if ($existingApplication) {
            return back()->with('error', 'You have already applied for this listing.');
        }

        $validated = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:255'],
            'home_type' => ['nullable', 'string', 'in:house,apartment,condo,farm,other'],
            'has_yard' => ['nullable', 'boolean'],
            'living_situation' => ['nullable', 'string', 'in:alone,family,roommates,other'],
            'has_other_pets' => ['nullable', 'boolean'],
            'other_pets_details' => ['nullable', 'string', 'max:500'],
            'has_children' => ['nullable', 'boolean'],
            'children_ages' => ['nullable', 'string', 'max:100'],
            'work_schedule' => ['nullable', 'string', 'max:255'],
            'pet_experience' => ['nullable', 'string', 'max:1000'],
            'why_adopt' => ['nullable', 'string', 'max:1000'],
        ], [
            'message.required' => 'Please provide a message with your application.',
            'message.max' => 'Message must not exceed 2000 characters.',
        ]);

        DB::transaction(function () use ($listing, $validated) {
            $listing = AdoptionListing::whereKey($listing->id)->lockForUpdate()->firstOrFail();
            if ($listing->status !== 'available' || $listing->shelter?->status !== 'active' || AdoptionApplication::where('listing_id', $listing->id)->where('applicant_id', Auth::id())->exists()) {
                throw ValidationException::withMessages(['message' => 'This animal is no longer available or you have already applied.']);
            }
            $application = AdoptionApplication::create([
                'listing_id' => $listing->id,
                'applicant_id' => Auth::id(),
                'message' => $validated['message'],
                'phone' => $validated['phone'] ?? Auth::user()->phone,
                'address' => $validated['address'] ?? Auth::user()->address,
                'home_type' => $validated['home_type'] ?? null,
                'has_yard' => $validated['has_yard'] ?? null,
                'living_situation' => $validated['living_situation'] ?? null,
                'has_other_pets' => $validated['has_other_pets'] ?? null,
                'other_pets_details' => $validated['other_pets_details'] ?? null,
                'has_children' => $validated['has_children'] ?? null,
                'children_ages' => $validated['children_ages'] ?? null,
                'work_schedule' => $validated['work_schedule'] ?? null,
                'pet_experience' => $validated['pet_experience'] ?? null,
                'why_adopt' => $validated['why_adopt'] ?? null,
                'status' => 'pending',
            ]);

            FurshieldNotification::create([
                'user_id' => $listing->shelter_id,
                'title' => 'New adoption request',
                'message' => Auth::user()->name.' applied to adopt '.$listing->pet_name.'.',
                'type' => 'adoption',
                'link' => route('shelter.applications.show', $application, false),
                'is_read' => false,
            ]);

        });

        return redirect()->route('owner.browse-adoption')
            ->with('success', 'Application submitted successfully. The shelter will review it shortly.');
    }
}
