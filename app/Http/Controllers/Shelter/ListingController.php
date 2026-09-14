<?php

namespace App\Http\Controllers\Shelter;

use App\Http\Controllers\Controller;
use App\Models\AdoptionListing;
use App\Models\Breed;
use App\Models\Species;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ListingController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:200'], 'species_id' => ['nullable', 'integer', 'exists:species,id'],
            'breed_id' => ['nullable', 'integer', Rule::exists('breeds', 'id')->when($request->filled('species_id'), fn ($rule) => $rule->where('species_id', $request->integer('species_id')))],
            'health_state' => ['nullable', Rule::in(['healthy', 'under_treatment', 'vaccination_due'])],
            'status' => ['nullable', Rule::in(['available', 'pending', 'adopted', 'inactive'])], 'sort' => ['nullable', Rule::in(['newest', 'oldest', 'name'])],
        ]);
        $base = AdoptionListing::where('shelter_id', Auth::id());
        $query = (clone $base)->with(['species', 'breed', 'images'])->withCount('applications');
        $search = trim($filters['search'] ?? '');
        if ($search !== '') {
            $query->where(fn ($q) => $q->where('pet_name', 'like', "%{$search}%")->orWhere('description', 'like', "%{$search}%")->orWhereHas('breed', fn ($b) => $b->where('name', 'like', "%{$search}%")));
        }
        foreach (['species_id', 'breed_id', 'health_state', 'status'] as $field) {
            if ($filters[$field] ?? null) {
                $query->where($field, $filters[$field]);
            }
        }
        match ($filters['sort'] ?? 'newest') {
            'oldest' => $query->oldest(),'name' => $query->orderBy('pet_name'),default => $query->latest()
        };
        $listings = $query->orderByDesc('id')->paginate(8)->withQueryString();
        $stats = ['total' => (clone $base)->count(), 'available' => (clone $base)->where('status', 'available')->count(), 'under_treatment' => (clone $base)->where('health_state', 'under_treatment')->count(), 'adopted' => (clone $base)->where('status', 'adopted')->count()];
        $species = Species::orderBy('name')->get();
        $breeds = Breed::when($filters['species_id'] ?? null, fn ($q, $id) => $q->where('species_id', $id))->orderBy('name')->get();
        $recentListings = (clone $base)->with(['images', 'breed'])->latest()->orderByDesc('id')->limit(3)->get();
        $speciesCounts = (clone $base)->with('species')->selectRaw('species_id, COUNT(*) as total')->groupBy('species_id')->get();

        return view('shelter.listings.index', compact('listings', 'stats', 'species', 'breeds', 'recentListings', 'speciesCounts'));
    }

    public function create()
    {
        return $this->form(new AdoptionListing(['status' => 'available', 'health_state' => 'healthy']));
    }

    public function edit(AdoptionListing $listing)
    {
        $this->authorizeListing($listing);

        return $this->form($listing);
    }

    private function form(AdoptionListing $listing)
    {
        $species = Species::where('status', 'active')->orderBy('name')->get();
        $breeds = Breed::orderBy('name')->get();
        $listing->load('images');

        return view('shelter.listings.form', compact('listing', 'species', 'breeds'));
    }

    public function show(AdoptionListing $listing)
    {
        $this->authorizeListing($listing);
        $listing->load(['species', 'breed', 'images', 'applications.applicant']);

        return view('shelter.listings.show', compact('listing'));
    }

    public function store(Request $request)
    {
        return $this->save($request, new AdoptionListing(['shelter_id' => Auth::id()]));
    }

    public function update(Request $request, AdoptionListing $listing)
    {
        $this->authorizeListing($listing);

        return $this->save($request, $listing);
    }

    private function save(Request $request, AdoptionListing $listing)
    {
        $data = $request->validate([
            'pet_name' => ['required', 'string', 'max:255'], 'species_id' => ['required', 'integer', 'exists:species,id'],
            'breed_id' => ['nullable', 'integer', Rule::exists('breeds', 'id')->where('species_id', $request->integer('species_id'))],
            'age' => ['nullable', 'string', 'max:50'], 'gender' => ['nullable', 'in:male,female'],
            'health_state' => ['required', 'in:healthy,under_treatment,vaccination_due'], 'health_status' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'], 'status' => ['required', 'in:available,pending,adopted,inactive'],
            'images' => ['nullable', 'array', 'max:6'], 'images.*' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'remove_images' => ['nullable', 'array', 'max:6'], 'remove_images.*' => ['integer', 'distinct', Rule::exists('adoption_images', 'id')->where('listing_id', $listing->id ?? 0)],
        ]);
        $paths = [];
        $removedPaths = [];
        try {
            DB::transaction(function () use ($listing, $data, $request, &$paths, &$removedPaths) {
                if ($listing->exists) {
                    $listing = AdoptionListing::whereKey($listing->id)->lockForUpdate()->firstOrFail();
                }
                if (($data['status'] === 'adopted' && $listing->status !== 'adopted') || ($listing->status === 'adopted' && $data['status'] !== 'adopted')) {
                    throw ValidationException::withMessages(['status' => 'Finalize an approved adoption request to mark an animal adopted. Completed adoptions cannot be reopened here.']);
                }
                if ($listing->exists && $listing->applications()->where('status', 'approved')->exists() && $data['status'] !== 'pending') {
                    throw ValidationException::withMessages(['status' => 'This animal is reserved for an approved applicant. Complete or reject that request first.']);
                }
                $remove = $data['remove_images'] ?? [];
                if (($listing->exists ? $listing->images()->count() : 0) - count($remove) + count($request->file('images', [])) > 6) {
                    throw ValidationException::withMessages(['images' => 'An animal can have at most six photos, including existing photos.']);
                }
                $listing->fill(collect($data)->except(['images', 'remove_images'])->all())->save();
                foreach ($listing->images()->whereIn('id', $remove)->get() as $image) {
                    $removedPaths[] = $image->image_path;
                    $image->delete();
                }
                foreach ($request->file('images', []) as $image) {
                    $path = $image->store('adoption-listings', 'public');
                    if (! $path) {
                        throw ValidationException::withMessages(['images' => 'The photo could not be saved. Please try again.']);
                    }$paths[] = $path;
                    $listing->images()->create(['image_path' => $path]);
                }
            });
        } catch (\Throwable $error) {
            foreach ($paths as $path) {
                Storage::disk('public')->delete($path);
            }throw $error;
        }
        foreach ($removedPaths as $path) {
            Storage::disk('public')->delete($path);
        }

        return redirect()->route('shelter.listings.index')->with('success', 'Animal saved successfully.');
    }

    public function destroy(AdoptionListing $listing)
    {
        $this->authorizeListing($listing);
        DB::transaction(function () use ($listing) {
            $listing = AdoptionListing::whereKey($listing->id)->lockForUpdate()->firstOrFail();
            if ($listing->status === 'adopted' || $listing->applications()->whereIn('status', ['pending', 'reviewing', 'approved'])->exists()) {
                throw ValidationException::withMessages(['listing' => 'Resolve active adoption requests before archiving. Adopted animals remain in your history.']);
            }
            $listing->update(['status' => 'inactive']);
        });

        return back()->with('success','Animal archived. Its care and adoption history have been preserved.');
    }

    private function authorizeListing(AdoptionListing $listing): void
    {
        abort_unless((int) $listing->shelter_id === (int) Auth::id(),403);
    }
}
