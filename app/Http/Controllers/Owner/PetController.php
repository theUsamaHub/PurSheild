<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Breed;
use App\Models\Pet;
use App\Models\Species;
use App\Support\OwnerPetHealth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PetController extends Controller
{
    public function index(Request $request): View
    {
        $f = $request->validate(['search' => ['nullable', 'string', 'max:200'], 'species_id' => ['nullable', 'integer', 'exists:species,id'],
            'status' => ['nullable', 'in:healthy,vaccination_due'], 'sort' => ['nullable', 'in:newest,oldest,name'],
            'panel' => ['nullable', 'in:closed,add'], 'view' => ['nullable', 'in:health']]);
        $base = Pet::where('owner_id', Auth::id());
        $q = (clone $base)->with(['species', 'breed', 'images'])->withExists(['vaccinations as vaccination_due' => fn ($q) => OwnerPetHealth::due($q)]);
        $search = trim($f['search'] ?? '');
        if ($search !== '') {
            $q->where(fn ($q) => $q->where('name', 'like', "%{$search}%")->orWhereHas('species', fn ($q) => $q->where('name', 'like', "%{$search}%"))->orWhereHas('breed', fn ($q) => $q->where('name', 'like', "%{$search}%")));
        }
        if ($f['species_id'] ?? null) {
            $q->where('species_id', $f['species_id']);
        }
        if (($f['status'] ?? '') === 'vaccination_due') {
            $q->whereHas('vaccinations', fn ($q) => OwnerPetHealth::due($q));
        }
        if (($f['status'] ?? '') === 'healthy') {
            $q->whereDoesntHave('vaccinations', fn ($q) => OwnerPetHealth::due($q));
        }
        match ($f['sort'] ?? 'newest') {
            'oldest' => $q->oldest(),'name' => $q->orderBy('name'),default => $q->latest()
        };
        $pets = $q->orderByDesc('id')->paginate(6)->withQueryString();
        $species = Species::where('status', 'active')->orderBy('name')->get();
        $breeds = Breed::orderBy('name')->get();
        $speciesCounts = (clone $base)->with('species')->selectRaw('species_id,COUNT(*) as total')->groupBy('species_id')->get();
        $totalPets = (clone $base)->count();
        $showAdd = ($f['panel'] ?? 'add') !== 'closed' && ($f['view'] ?? '') !== 'health';

        return view('owner.pets.index', compact('pets', 'species', 'breeds', 'speciesCounts', 'totalPets', 'showAdd'));
    }

    public function create(): RedirectResponse
    {
        return redirect()->route('owner.pets.index', ['panel' => 'add']);
    }

    public function store(Request $request): RedirectResponse
    {
        return $this->save($request, new Pet(['owner_id' => Auth::id()]));
    }

    public function show(Pet $pet): View
    {
        if ((int) $pet->owner_id !== (int) Auth::id()) {
            abort(403, 'You are not authorized to view this pet.');
        }

        $pet->load(['species', 'breed', 'images', 'healthRecords' => function ($q) {
            $q->with('vet')->latest('record_date');
        }, 'vaccinations' => function ($q) {
            $q->latest('vaccination_date');
        }]);

        return view('owner.pets.show', compact('pet'));
    }

    public function edit(Pet $pet): View
    {
        if ((int) $pet->owner_id !== (int) Auth::id()) {
            abort(403, 'You are not authorized to edit this pet.');
        }

        $pet->load(['images', 'healthRecords' => function ($q) {
            $q->with('vet')->latest('record_date');
        }, 'vaccinations' => function ($q) {
            $q->latest('vaccination_date');
        }, 'medicalDocuments']);
        $species = Species::where('status', 'active')->orderBy('name')->get();
        $breeds = Breed::where('species_id', $pet->species_id)->orderBy('name')->get();

        return view('owner.pets.edit', compact('pet', 'species', 'breeds'));
    }

    public function update(Request $request, Pet $pet): RedirectResponse
    {
        abort_unless((int) $pet->owner_id === (int) Auth::id(), 403);

        return $this->save($request, $pet);
    }

    private function save(Request $request, Pet $pet): RedirectResponse
    {
        $v = $request->validate([
            'name' => ['required', 'string', 'max:255'], 'species_id' => ['required', 'integer', 'exists:species,id'],
            'breed_id' => [Rule::requiredIf(fn () => Breed::where('species_id', $request->integer('species_id'))->exists()), 'nullable', 'integer', Rule::exists('breeds', 'id')->where('species_id', $request->integer('species_id'))],
            'gender' => ['required', 'in:male,female'], 'date_of_birth' => ['required', 'date_format:Y-m-d', 'before_or_equal:today'],
            'weight' => ['nullable', 'numeric', 'min:0.01', 'max:999999.99'], 'color' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:5000'], 'is_neutered' => ['sometimes', 'boolean'],
            'microchip_number' => ['nullable', 'string', 'max:100', Rule::unique('pets', 'microchip_number')->ignore($pet->id)],
            'profile_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'images' => ['nullable', 'array', 'max:6'], 'images.*' => ['required', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:5120'],
            'remove_images' => ['nullable', 'array', 'max:6'], 'remove_images.*' => ['integer', 'distinct', Rule::exists('pet_images', 'id')->where('pet_id', $pet->id ?? 0)],
            'primary_image_id' => ['nullable', 'integer', Rule::exists('pet_images', 'id')->where('pet_id', $pet->id ?? 0)],
        ]);
        $paths = [];
        $removed = [];
        try {
            DB::transaction(function () use ($request, $v, $pet, &$paths, &$removed) {
                if ($pet->exists) {
                    $pet = Pet::whereKey($pet->id)->lockForUpdate()->firstOrFail();
                }
                $remove = $v['remove_images'] ?? [];
                if (($pet->exists ? $pet->images()->count() : 0) - count($remove) + count($request->file('images', [])) > 6) {
                    throw ValidationException::withMessages(['images' => 'Keep at most six gallery photos, including existing photos.']);
                }
                if (in_array($v['primary_image_id'] ?? null, $remove)) {
                    throw ValidationException::withMessages(['primary_image_id' => 'The primary photo cannot also be removed.']);
                }
                $pet->fill(collect($v)->except(['profile_image', 'images', 'remove_images', 'primary_image_id'])->all());
                if ($request->hasFile('profile_image')) {
                    $path = $request->file('profile_image')->store('uploads/pets', 'public');
                    if (! $path) {
                        throw ValidationException::withMessages(['profile_image' => 'Photo could not be saved. Please try again.']);
                    }
                    $paths[] = $path;
                    if ($pet->profile_image) {
                        $removed[] = $pet->profile_image;
                    }$pet->profile_image = $path;
                }
                $pet->save();
                foreach ($pet->images()->whereIn('id', $remove)->get() as $image) {
                    $removed[] = $image->image_path;
                    $image->delete();
                }
                $sort = (int) $pet->images()->max('sort_order');
                foreach ($request->file('images', []) as $file) {
                    $path = $file->store('uploads/pets', 'public');
                    if (! $path) {
                        throw ValidationException::withMessages(['images' => 'Photo could not be saved. Please try again.']);
                    }$paths[] = $path;
                    $pet->images()->create(['image_path' => $path, 'sort_order' => ++$sort, 'is_primary' => false]);
                }
                if ($v['primary_image_id'] ?? null) {
                    $pet->images()->update(['is_primary' => false]);
                    $pet->images()->whereKey($v['primary_image_id'])->update(['is_primary' => true]);
                }
                if (! $pet->images()->where('is_primary', true)->exists()) {
                    $pet->images()->orderBy('sort_order')->first()?->update(['is_primary' => true]);
                }
                    });
        } catch (\Throwable $e) {
            foreach ($paths as $p) {
                Storage::disk('public')->delete($p);
            }throw $e;
        }
        foreach ($removed as $p) {
            Storage::disk('public')->delete($p);
        }

        return redirect()->route('owner.pets.index')->with('success', 'Pet saved successfully.');
    }

    public function destroy(Pet $pet): RedirectResponse
    {
        abort_unless((int) $pet->owner_id === (int) Auth::id(), 403);
        $paths = [];
        DB::transaction(function () use ($pet, &$paths) {
            $pet = Pet::whereKey($pet->id)->lockForUpdate()->firstOrFail();
            if (Appointment::where('pet_id', $pet->id)->exists() || $pet->healthRecords()->exists() || $pet->vaccinations()->exists() || $pet->medicalDocuments()->exists() || $pet->insurancePolicies()->exists()) {
                throw ValidationException::withMessages(['pet' => 'This pet has appointments or health records. Keep its profile to preserve that history.']);
            }
            $paths = $pet->images()->pluck('image_path')->all();
            if ($pet->profile_image) {
                $paths[] = $pet->profile_image;
            }$pet->delete();
        });
        foreach ($paths as $p) {
            Storage::disk('public')->delete($p);
        }

        return redirect()->route('owner.pets.index')->with('success','Pet deleted successfully.');
    }
}
