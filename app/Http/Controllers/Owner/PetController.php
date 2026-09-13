<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Breed;
use App\Models\Pet;
use App\Models\PetImage;
use App\Models\Species;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PetController extends Controller
{
    public function index(): View
    {
        $pets = Pet::with(['species', 'breed', 'images'])
            ->where('owner_id', Auth::id())
            ->latest()
            ->paginate(15);

        return view('owner.pets.index', compact('pets'));
    }

    public function create(): View
    {
        $species = Species::where('status', 'active')->orderBy('name')->get();

        return view('owner.pets.create', compact('species'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'species_id' => ['required', 'exists:species,id'],
            'breed_id' => ['nullable', 'exists:breeds,id'],
            'gender' => ['required', 'in:male,female'],
            'date_of_birth' => ['required', 'date', 'before:today'],
            'weight' => ['nullable', 'numeric', 'min:0'],
            'color' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'is_neutered' => ['boolean'],
            'microchip_number' => ['nullable', 'string', 'max:100', 'unique:pets,microchip_number'],
            'profile_image' => ['nullable', 'image', 'max:2048'],
            'images' => ['nullable', 'array', 'max:6'],
            'images.*' => ['file', 'max:5120', 'mimes:jpg,jpeg,png,gif,webp'],
        ], [
            'name.required' => 'Pet name is required.',
            'name.max' => 'Pet name must not exceed 255 characters.',
            'species_id.required' => 'Please select a species.',
            'species_id.exists' => 'Selected species does not exist.',
            'breed_id.exists' => 'Selected breed does not exist.',
            'gender.required' => 'Please select a gender.',
            'gender.in' => 'Gender must be male or female.',
            'date_of_birth.required' => 'Date of birth is required.',
            'date_of_birth.date' => 'Please enter a valid date.',
            'date_of_birth.before' => 'Date of birth must be in the past.',
            'weight.numeric' => 'Weight must be a number.',
            'weight.min' => 'Weight must be at least 0.',
            'color.max' => 'Color must not exceed 100 characters.',
            'microchip_number.unique' => 'This microchip number is already registered.',
            'profile_image.image' => 'Profile image must be an image file.',
            'profile_image.max' => 'Profile image must not exceed 2MB.',
            'images.max' => 'You can upload a maximum of 6 images.',
            'images.*.max' => 'Each image must not exceed 5MB.',
            'images.*.mimes' => 'Each image must be a JPG, PNG, GIF, or WebP file.',
        ]);

        $data = collect($validated)->except(['profile_image', 'images'])->toArray();
        $data['owner_id'] = Auth::id();

        if (!isset($data['is_neutered'])) {
            $data['is_neutered'] = false;
        }

        $pet = Pet::create($data);

        if ($request->hasFile('profile_image')) {
            $path = $request->file('profile_image')->store('uploads/pets', 'public');
            $pet->update(['profile_image' => $path]);
        }

        if ($request->hasFile('images')) {
            $files = $request->file('images');
            foreach ($files as $index => $file) {
                $path = $file->store('uploads/pets', 'public');
                PetImage::create([
                    'pet_id' => $pet->id,
                    'image_path' => $path,
                    'is_primary' => $index === 0,
                    'sort_order' => $index,
                ]);
            }
        }

        return redirect()->route('owner.pets.show', $pet)
            ->with('success', 'Pet added successfully.');
    }

    public function show(Pet $pet): View
    {
        if ($pet->owner_id !== Auth::id()) {
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
        if ($pet->owner_id !== Auth::id()) {
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
        if ($pet->owner_id !== Auth::id()) {
            abort(403, 'You are not authorized to update this pet.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'species_id' => ['required', 'exists:species,id'],
            'breed_id' => ['nullable', 'exists:breeds,id'],
            'gender' => ['required', 'in:male,female'],
            'date_of_birth' => ['required', 'date', 'before:today'],
            'weight' => ['nullable', 'numeric', 'min:0'],
            'color' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'is_neutered' => ['boolean'],
            'microchip_number' => ['nullable', 'string', 'max:100', 'unique:pets,microchip_number,' . $pet->id],
            'profile_image' => ['nullable', 'image', 'max:2048'],
            'images' => ['nullable', 'array', 'max:6'],
            'images.*' => ['file', 'max:5120', 'mimes:jpg,jpeg,png,gif,webp'],
            'remove_images' => ['nullable', 'array'],
            'remove_images.*' => ['integer', 'exists:pet_images,id'],
            'primary_image_id' => ['nullable', 'integer', 'exists:pet_images,id'],
        ], [
            'name.required' => 'Pet name is required.',
            'name.max' => 'Pet name must not exceed 255 characters.',
            'species_id.required' => 'Please select a species.',
            'species_id.exists' => 'Selected species does not exist.',
            'breed_id.exists' => 'Selected breed does not exist.',
            'gender.required' => 'Please select a gender.',
            'gender.in' => 'Gender must be male or female.',
            'date_of_birth.required' => 'Date of birth is required.',
            'date_of_birth.date' => 'Please enter a valid date.',
            'date_of_birth.before' => 'Date of birth must be in the past.',
            'weight.numeric' => 'Weight must be a number.',
            'weight.min' => 'Weight must be at least 0.',
            'color.max' => 'Color must not exceed 100 characters.',
            'microchip_number.unique' => 'This microchip number is already registered.',
            'profile_image.image' => 'Profile image must be an image file.',
            'profile_image.max' => 'Profile image must not exceed 2MB.',
            'images.max' => 'You can upload a maximum of 6 images total.',
            'images.*.max' => 'Each image must not exceed 5MB.',
            'images.*.mimes' => 'Each image must be a JPG, PNG, GIF, or WebP file.',
            'primary_image_id.exists' => 'Selected primary image does not exist.',
        ]);

        $data = collect($validated)->except(['profile_image', 'images', 'remove_images', 'primary_image_id'])->toArray();

        if (!isset($data['is_neutered'])) {
            $data['is_neutered'] = false;
        }

        $pet->update($data);

        if ($request->hasFile('profile_image')) {
            if ($pet->profile_image) {
                Storage::disk('public')->delete($pet->profile_image);
            }
            $path = $request->file('profile_image')->store('uploads/pets', 'public');
            $pet->update(['profile_image' => $path]);
        }

        if (!empty($validated['remove_images'])) {
            foreach ($validated['remove_images'] as $imageId) {
                $image = PetImage::where('id', $imageId)
                    ->where('pet_id', $pet->id)
                    ->first();

                if ($image) {
                    Storage::disk('public')->delete($image->image_path);
                    $image->delete();
                }
            }
        }

        if ($request->hasFile('images')) {
            $existingCount = $pet->images()->count();
            $files = $request->file('images');

            foreach ($files as $index => $file) {
                $path = $file->store('uploads/pets', 'public');
                PetImage::create([
                    'pet_id' => $pet->id,
                    'image_path' => $path,
                    'is_primary' => false,
                    'sort_order' => $existingCount + $index,
                ]);
            }
        }

        if (!empty($validated['primary_image_id'])) {
            PetImage::where('pet_id', $pet->id)->update(['is_primary' => false]);
            PetImage::where('id', $validated['primary_image_id'])
                ->where('pet_id', $pet->id)
                ->update(['is_primary' => true]);
        }

        return redirect()->route('owner.pets.show', $pet)
            ->with('success', 'Pet updated successfully.');
    }

    public function destroy(Pet $pet): RedirectResponse
    {
        if ($pet->owner_id !== Auth::id()) {
            abort(403, 'You are not authorized to delete this pet.');
        }

        foreach ($pet->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }

        $pet->delete();

        return redirect()->route('owner.pets.index')
            ->with('success', 'Pet deleted successfully.');
    }
}
