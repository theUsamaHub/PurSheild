<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\AdoptionApplication;
use App\Models\AdoptionListing;
use App\Models\FurshieldNotification;
use App\Models\Species;
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
        $query = AdoptionListing::with(['species', 'breed', 'shelter.shelterProfile', 'images'])
            ->where('status', 'available')
            ->whereHas('shelter', function ($q) {
                $q->where('status', 'active');
            });

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('pet_name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($speciesId = $request->input('species_id')) {
            $query->where('species_id', $speciesId);
        }

        if ($gender = $request->input('gender')) {
            $query->where('gender', $gender);
        }

        if ($health = $request->input('health_status')) {
            $query->where('health_status', $health);
        }

        $listings = $query->latest()->paginate(15);
        $species = Species::where('status', 'active')->orderBy('name')->get();

        return view('owner.adoption.index', compact('listings', 'species'));
    }

    public function show(AdoptionListing $listing): View
    {
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
        if ($listing->status !== 'available') {
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
            if ($listing->status !== 'available' || AdoptionApplication::where('listing_id', $listing->id)->where('applicant_id', Auth::id())->exists()) {
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
