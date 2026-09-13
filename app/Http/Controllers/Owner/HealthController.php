<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\HealthRecord;
use App\Models\MedicalDocument;
use App\Models\Pet;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class HealthController extends Controller
{
    public function index(Pet $pet): View
    {
        if ($pet->owner_id !== Auth::id()) {
            abort(403, 'You are not authorized to view health records for this pet.');
        }

        $pet->load(['species', 'breed', 'healthRecords' => function ($q) {
            $q->with('vet')->latest('record_date');
        }, 'vaccinations' => function ($q) {
            $q->latest('vaccination_date');
        }, 'medicalDocuments' => function ($q) {
            $q->latest();
        }, 'insurancePolicies' => function ($q) {
            $q->latest('start_date');
        }]);

        return view('owner.health.index', compact('pet'));
    }

    public function store(Request $request, Pet $pet): RedirectResponse
    {
        if ($pet->owner_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'record_date' => ['required', 'date'],
            'record_type' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ], [
            'record_date.required' => 'Date is required.',
            'record_date.date' => 'Please enter a valid date.',
            'record_type.required' => 'Please select a record type.',
        ]);

        $validated['pet_id'] = $pet->id;

        HealthRecord::create($validated);

        return redirect()->route('owner.pets.show', $pet)
            ->with('success', 'Health record added successfully.');
    }

    public function storeDocument(Request $request, Pet $pet): RedirectResponse
    {
        if ($pet->owner_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'file' => ['required', 'file', 'max:10240', 'mimes:pdf,jpg,jpeg,png,doc,docx'],
            'document_type' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:255'],
        ], [
            'file.required' => 'Please select a file to upload.',
            'file.max' => 'File must not exceed 10MB.',
            'file.mimes' => 'File must be PDF, JPG, PNG, DOC, or DOCX.',
        ]);

        $file = $request->file('file');
        $path = $file->store('uploads/medical-documents', 'public');

        MedicalDocument::create([
            'pet_id' => $pet->id,
            'document_type' => $validated['document_type'] ?? null,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'mime_type' => $file->getMimeType(),
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()->route('owner.pets.show', $pet)
            ->with('success', 'Document uploaded successfully.');
    }

    public function storeVaccination(Request $request, Pet $pet): RedirectResponse
    {
        if ($pet->owner_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'vaccine_name' => ['required', 'string', 'max:255'],
            'vaccination_date' => ['required', 'date'],
            'next_due_date' => ['nullable', 'date', 'after:vaccination_date'],
            'batch_number' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:500'],
        ], [
            'vaccine_name.required' => 'Vaccine name is required.',
            'vaccination_date.required' => 'Vaccination date is required.',
            'vaccination_date.date' => 'Please enter a valid date.',
            'next_due_date.after' => 'Next due date must be after the vaccination date.',
        ]);

        $validated['pet_id'] = $pet->id;

        \App\Models\Vaccination::create($validated);

        return redirect()->route('owner.pets.show', $pet)
            ->with('success', 'Vaccination record added successfully.');
    }
}
