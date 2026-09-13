<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Species;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SpeciesController extends Controller
{
    public function index(Request $request): View
    {
        $query = Species::withCount('breeds');

        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        $species = $query->latest()->paginate(20);

        return view('admin.species.index', compact('species'));
    }

    public function create(): View
    {
        return view('admin.species.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:species,name'],
            'description' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', 'string', 'in:active,inactive'],
        ]);

        Species::create($validated);

        return redirect()->route('admin.species.index')
            ->with('success', 'Species created successfully.');
    }

    public function edit(Species $species): View
    {
        return view('admin.species.edit', compact('species'));
    }

    public function update(Request $request, Species $species): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:species,name,' . $species->id],
            'description' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', 'string', 'in:active,inactive'],
        ]);

        $species->update($validated);

        return redirect()->route('admin.species.index')
            ->with('success', 'Species updated successfully.');
    }

    public function destroy(Species $species): RedirectResponse
    {
        if ($species->breeds()->count() > 0) {
            return back()->with('error', 'Cannot delete species with assigned breeds.');
        }

        if ($species->pets()->count() > 0) {
            return back()->with('error', 'Cannot delete species with assigned pets.');
        }

        $species->delete();

        return redirect()->route('admin.species.index')
            ->with('success', 'Species deleted successfully.');
    }
}
