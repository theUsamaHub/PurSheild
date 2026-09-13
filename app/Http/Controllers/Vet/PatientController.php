<?php

namespace App\Http\Controllers\Vet;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Pet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PatientController extends Controller
{
    public function index(Request $request): View
    {
        $vetId = Auth::id();

        $petIds = Appointment::where('vet_id', $vetId)
            ->where('status', 'completed')
            ->distinct()
            ->pluck('pet_id');

        $query = Pet::whereIn('id', $petIds)
            ->with(['owner', 'species', 'breed']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhereHas('owner', fn($q) => $q->where('name', 'like', "%{$search}%"));
            });
        }

        if ($speciesId = $request->input('species_id')) {
            $query->where('species_id', $speciesId);
        }

        $patients = $query->orderBy('name')->paginate(15);
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
}
