<?php

namespace App\Http\Controllers\Vet;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Treatment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TreatmentController extends Controller
{
    public function index(Request $request): View
    {
        $vetId = Auth::id();

        $query = Treatment::where('vet_id', $vetId)
            ->with(['appointment.pet.species', 'appointment.pet.breed', 'appointment.owner', 'prescriptions']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('diagnosis', 'like', "%{$search}%")
                    ->orWhereHas('appointment.pet', fn ($q) => $q->where('name', 'like', "%{$search}%"));
            });
        }

        $treatments = $query->latest()->paginate(15);

        $stats = [
            'total' => Treatment::where('vet_id', $vetId)->count(),
            'this_month' => Treatment::where('vet_id', $vetId)
                ->whereMonth('created_at', now()->month)
                ->count(),
        ];

        return view('vet.treatments.index', compact('treatments', 'stats'));
    }

    public function create(Appointment $appointment): View|RedirectResponse
    {
        abort_unless($appointment->vet_id === Auth::id(), 403);
        abort_unless(in_array($appointment->status, ['approved', 'rescheduled']), 400, 'Only approved or rescheduled appointments can have treatments recorded.');

        $hasTreatment = $appointment->treatment()->exists();
        if ($hasTreatment) {
            return redirect()->route('vet.treatments.show', $appointment->treatment);
        }

        $appointment->load(['pet.species', 'pet.breed', 'pet.healthRecords', 'pet.vaccinations', 'owner']);

        return view('vet.treatments.create', compact('appointment'));
    }

    public function store(Request $request, Appointment $appointment): RedirectResponse
    {
        abort_unless($appointment->vet_id === Auth::id(), 403);
        abort_unless(in_array($appointment->status, ['approved', 'rescheduled']), 400, 'Only approved or rescheduled appointments can have treatments recorded.');
        abort_if($appointment->treatment()->exists(), 409, 'A treatment already exists for this appointment.');

        $validated = $request->validate([
            'symptoms' => ['required', 'string', 'max:2000'],
            'diagnosis' => ['required', 'string', 'max:2000'],
            'treatment' => ['required', 'string', 'max:2000'],
            'follow_up_date' => ['nullable', 'date', 'after:today'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'prescriptions' => ['nullable', 'array'],
            'prescriptions.*.medicine_name' => ['required_with:prescriptions', 'string', 'max:255'],
            'prescriptions.*.dosage' => ['required_with:prescriptions', 'string', 'max:255'],
            'prescriptions.*.frequency' => ['required_with:prescriptions', 'string', 'max:255'],
            'prescriptions.*.duration' => ['required_with:prescriptions', 'string', 'max:255'],
            'prescriptions.*.instructions' => ['nullable', 'string', 'max:500'],
        ]);

        $treatment = Treatment::create([
            'appointment_id' => $appointment->id,
            'pet_id' => $appointment->pet_id,
            'vet_id' => Auth::id(),
            'symptoms' => $validated['symptoms'],
            'diagnosis' => $validated['diagnosis'],
            'treatment' => $validated['treatment'],
            'follow_up_date' => $validated['follow_up_date'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        if (! empty($validated['prescriptions'])) {
            foreach ($validated['prescriptions'] as $prescription) {
                $treatment->prescriptions()->create($prescription);
            }
        }

        $appointment->update(['status' => 'completed']);

        return redirect()->route('vet.treatments.show', $treatment)
            ->with('success', 'Treatment recorded successfully. Appointment marked as completed.');
    }

    public function show(Treatment $treatment): View
    {
        abort_unless($treatment->vet_id === Auth::id(), 403);

        $treatment->load([
            'appointment.pet.species', 'appointment.pet.breed', 'appointment.owner',
            'prescriptions',
        ]);

        return view('vet.treatments.show', compact('treatment'));
    }
}
