<?php

namespace App\Http\Controllers\Shelter;

use App\Http\Controllers\Controller;
use App\Models\AdoptionImage;
use App\Models\AdoptionListing;
use App\Models\Breed;
use App\Models\Species;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ListingController extends Controller
{
    public function index(Request $request): View
    {
        $query = AdoptionListing::where('shelter_id', Auth::id())
            ->withCount('applications');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('pet_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $listings = $query->latest()->paginate(12);

        $stats = [
            'total' => AdoptionListing::where('shelter_id', Auth::id())->count(),
            'available' => AdoptionListing::where('shelter_id', Auth::id())->where('status', 'available')->count(),
            'pending' => AdoptionListing::where('shelter_id', Auth::id())->where('status', 'pending')->count(),
            'adopted' => AdoptionListing::where('shelter_id', Auth::id())->where('status', 'adopted')->count(),
        ];

        return view('shelter.listings.index', compact('listings', 'stats'));
    }

    public function create(): View
    {
        $species = Species::where('status', 'active')->orderBy('name')->get();

        return view('shelter.listings.create', compact('species'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pet_name' => 'required|string|max:255',
            'species_id' => 'required|exists:species,id',
            'breed_id' => 'nullable|exists:breeds,id',
            'age' => 'nullable|string|max:50',
            'gender' => 'nullable|in:male,female',
            'health_status' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'images' => 'nullable|array|max:6',
            'images.*' => 'image|mimes:jpg,jpeg,png,gif,webp|max:5120',
        ]);

        $validated['shelter_id'] = Auth::id();
        $validated['status'] = 'available';

        $listing = AdoptionListing::create($validated);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('adoption-listings', 'public');
                AdoptionImage::create([
                    'listing_id' => $listing->id,
                    'image_path' => $path,
                    'caption' => null,
                ]);
            }
        }

        return redirect()->route('shelter.listings.index')
            ->with('success', 'Adoption listing created successfully.');
    }

    public function show(AdoptionListing $listing): View
    {
        abort_unless($listing->shelter_id === Auth::id(), 403);

        $listing->load(['species', 'breed', 'images', 'applications' => function ($q) {
            $q->with('applicant')->latest();
        }]);

        return view('shelter.listings.show', compact('listing'));
    }

    public function edit(AdoptionListing $listing): View
    {
        abort_unless($listing->shelter_id === Auth::id(), 403);

        $species = Species::where('status', 'active')->orderBy('name')->get();
        $listing->load('images');

        return view('shelter.listings.edit', compact('listing', 'species'));
    }

    public function update(Request $request, AdoptionListing $listing)
    {
        abort_unless($listing->shelter_id === Auth::id(), 403);

        $validated = $request->validate([
            'pet_name' => 'required|string|max:255',
            'species_id' => 'required|exists:species,id',
            'breed_id' => 'nullable|exists:breeds,id',
            'age' => 'nullable|string|max:50',
            'gender' => 'nullable|in:male,female',
            'health_status' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:available,pending,adopted,inactive',
            'images' => 'nullable|array|max:6',
            'images.*' => 'image|mimes:jpg,jpeg,png,gif,webp|max:5120',
            'remove_images' => 'nullable|array',
            'remove_images.*' => 'exists:adoption_images,id',
        ]);

        $listing->update(collect($validated)->only([
            'pet_name', 'species_id', 'breed_id', 'age', 'gender',
            'health_status', 'description', 'status',
        ])->toArray());

        if (!empty($validated['remove_images'])) {
            foreach ($validated['remove_images'] as $imageId) {
                $image = AdoptionImage::where('id', $imageId)
                    ->where('listing_id', $listing->id)
                    ->first();
                if ($image) {
                    Storage::disk('public')->delete($image->image_path);
                    $image->delete();
                }
            }
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('adoption-listings', 'public');
                AdoptionImage::create([
                    'listing_id' => $listing->id,
                    'image_path' => $path,
                    'caption' => null,
                ]);
            }
        }

        return redirect()->route('shelter.listings.index')
            ->with('success', 'Adoption listing updated successfully.');
    }

    public function destroy(AdoptionListing $listing)
    {
        abort_unless($listing->shelter_id === Auth::id(), 403);

        foreach ($listing->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }

        $listing->delete();

        return redirect()->route('shelter.listings.index')
            ->with('success', 'Adoption listing deleted successfully.');
    }
}
