<?php

namespace App\Http\Controllers\Vet;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Pet;
use App\Models\MedicalDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PatientController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:200'],
            'species_id' => ['nullable', 'integer', 'exists:species,id'],
        ]);
        $vetId = Auth::id();

        $petIds = Appointment::where('vet_id', $vetId)
            ->distinct()
            ->pluck('pet_id');

        // Subquery to get last visit date per pet for this vet
        $lastVisitSubquery = Appointment::select(
            DB::raw('MAX(appointment_date) as last_visit_date'),
            'pet_id'
        )
            ->where('vet_id', $vetId)
            ->where('status', 'completed')
            ->whereDate('appointment_date', '<=', today())
            ->groupBy('pet_id');

        $query = Pet::whereIn('id', $petIds)
            ->with(['owner', 'species', 'breed', 'images'])
            ->leftJoinSub($lastVisitSubquery, 'last_visits', 'pets.id', '=', 'last_visits.pet_id')
            ->select('pets.*', 'last_visits.last_visit_date');

        $search = trim($filters['search'] ?? '');
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('pets.name', 'like', "%{$search}%")
                    ->orWhereHas('owner', fn ($q) => $q->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('breed', fn ($q) => $q->where('name', 'like', "%{$search}%"));
            });
        }

        if ($speciesId = $filters['species_id'] ?? null) {
            $query->where('pets.species_id', $speciesId);
        }

        $patients = $query->orderBy('pets.name')->paginate(15)->withQueryString();

        $species = \App\Models\Species::whereIn('id',
            Pet::whereIn('id', $petIds)->pluck('species_id')
        )->orderBy('name')->get();

        return view('vet.patients.index', compact('patients', 'species'));
    }

    public function show(Pet $pet): View
    {
        $vetId = Auth::id();

        $hasAppointment = Appointment::where('vet_id', $vetId)
            ->where('pet_id', $pet->id)
            ->exists();

        abort_unless($hasAppointment, 403);

        $pet->load([
            'owner', 'species', 'breed', 'images',
            'healthRecords.vet', 'vaccinations', 'medicalDocuments',
        ]);

        $appointments = Appointment::where('vet_id', $vetId)
            ->where('pet_id', $pet->id)
            ->with('treatment.prescriptions')
            ->orderByDesc('appointment_date')
            ->get();

        return view('vet.patients.show', compact('pet', 'appointments'));
    }

    public function document(Pet $pet, MedicalDocument $document)
    {
        abort_unless((int) $document->pet_id === (int) $pet->id, 404);
        abort_unless(Appointment::where('vet_id', Auth::id())->where('pet_id', $pet->id)->exists(), 403);
        abort_unless(Storage::disk('public')->exists($document->file_path), 404, 'This document is no longer available.');

        return Storage::disk('public')->download($document->file_path, basename($document->file_name));
    }
}
