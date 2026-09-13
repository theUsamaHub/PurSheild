<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Specialization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SpecializationController extends Controller
{
    public function index(Request $request): View
    {
        $query = Specialization::withCount('vets');

        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        $specializations = $query->latest()->paginate(20);

        return view('admin.specializations.index', compact('specializations'));
    }

    public function create(): View
    {
        return view('admin.specializations.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:specializations,name'],
            'description' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', 'string', 'in:active,inactive'],
        ]);

        Specialization::create($validated);

        return redirect()->route('admin.specializations.index')
            ->with('success', 'Specialization created successfully.');
    }

    public function edit(Specialization $specialization): View
    {
        return view('admin.specializations.edit', compact('specialization'));
    }

    public function update(Request $request, Specialization $specialization): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:specializations,name,' . $specialization->id],
            'description' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', 'string', 'in:active,inactive'],
        ]);

        $specialization->update($validated);

        return redirect()->route('admin.specializations.index')
            ->with('success', 'Specialization updated successfully.');
    }

    public function destroy(Specialization $specialization): RedirectResponse
    {
        if ($specialization->vets()->count() > 0) {
            return back()->with('error', 'Cannot delete specialization with assigned vets.');
        }

        $specialization->delete();

        return redirect()->route('admin.specializations.index')
            ->with('success', 'Specialization deleted successfully.');
    }
}
