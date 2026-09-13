<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Breed;
use App\Models\Species;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BreedController extends Controller
{
    public function index(Request $request): View
    {
        $query = Breed::with('species');

        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($speciesId = $request->input('species_id')) {
            $query->where('species_id', $speciesId);
        }

        $breeds = $query->latest()->paginate(20);
        $speciesList = Species::orderBy('name')->get();

        return view('admin.breeds.index', compact('breeds', 'speciesList'));
    }

    public function create(): View
    {
        $speciesList = Species::orderBy('name')->get();

        return view('admin.breeds.create', compact('speciesList'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'species_id' => ['required', 'exists:species,id'],
            'name' => ['required', 'string', 'max:255', 'unique:breeds,name'],
            'description' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', 'string', 'in:active,inactive'],
        ]);

        Breed::create($validated);

        return redirect()->route('admin.breeds.index')
            ->with('success', 'Breed created successfully.');
    }

    public function edit(Breed $breed): View
    {
        $speciesList = Species::orderBy('name')->get();

        return view('admin.breeds.edit', compact('breed', 'speciesList'));
    }

    public function update(Request $request, Breed $breed): RedirectResponse
    {
        $validated = $request->validate([
            'species_id' => ['required', 'exists:species,id'],
            'name' => ['required', 'string', 'max:255', 'unique:breeds,name,' . $breed->id],
            'description' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', 'string', 'in:active,inactive'],
        ]);

        $breed->update($validated);

        return redirect()->route('admin.breeds.index')
            ->with('success', 'Breed updated successfully.');
    }

    public function destroy(Breed $breed): RedirectResponse
    {
        if ($breed->pets()->count() > 0) {
            return back()->with('error', 'Cannot delete breed with assigned pets.');
        }

        $breed->delete();

        return redirect()->route('admin.breeds.index')
            ->with('success', 'Breed deleted successfully.');
    }
}
