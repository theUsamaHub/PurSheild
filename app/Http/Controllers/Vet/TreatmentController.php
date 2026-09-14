<?php

namespace App\Http\Controllers\Vet;

use App\Http\Controllers\Controller;
use App\Models\Treatment;
use App\Models\Appointment;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TreatmentController extends Controller
{
    public function index(Request $request): View
    {
        $vetId = Auth::id();

        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:200'],
            'date'   => ['nullable', 'date_format:Y-m-d'],
        ], [
            'date.date_format' => 'Please enter a valid date.',
        ]);

        $query = Treatment::where('vet_id', $vetId)
            ->with(['appointment.pet.species', 'appointment.pet.breed', 'appointment.pet.images', 'appointment.owner', 'prescriptions']);

        $search = trim($filters['search'] ?? '');
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('diagnosis', 'like', "%{$search}%")
                    ->orWhere('symptoms', 'like', "%{$search}%")
                    ->orWhere('treatment', 'like', "%{$search}%")
                    ->orWhereHas('appointment.pet', fn ($q) => $q->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('appointment.owner', fn ($q) => $q->where('name', 'like', "%{$search}%"));
            });
        }

        if ($date = $filters['date'] ?? null) {
            $query->whereDate('created_at', $date);
        }

        $treatments = $query->latest()->paginate(15)->withQueryString();

        $stats = [
            'total'      => Treatment::where('vet_id', $vetId)->count(),
            'this_month' => Treatment::where('vet_id', $vetId)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
        ];

        return view('vet.treatments.index', compact('treatments', 'stats'));
    }

    public function create(\App\Models\Appointment $appointment): View|\Illuminate\Http\RedirectResponse
    {
        abort_unless($appointment->vet_id === Auth::id(), 403);
        $hasTreatment = $appointment->treatment()->exists();
        if ($hasTreatment) {
            return redirect()->route('vet.treatments.show', $appointment->treatment);
        }

        abort_unless(in_array($appointment->status, ['approved', 'rescheduled']), 400, 'Only approved or rescheduled appointments can have treatments recorded.');

        $appointment->load(['pet.species', 'pet.breed', 'pet.images', 'pet.healthRecords', 'pet.vaccinations', 'owner']);

        return view('vet.treatments.create', compact('appointment'));
    }

    public function store(Request $request, \App\Models\Appointment $appointment): \Illuminate\Http\RedirectResponse
    {
        abort_unless($appointment->vet_id === Auth::id(), 403);
        abort_unless(in_array($appointment->status, ['approved', 'rescheduled']), 400, 'Only approved or rescheduled appointments can have treatments recorded.');
        abort_if($appointment->treatment()->exists(), 409, 'A treatment already exists for this appointment.');

        $validated = $request->validate([
            'symptoms'    => ['required', 'string', 'max:2000'],
            'diagnosis'   => ['required', 'string', 'max:2000'],
            'treatment'   => ['required', 'string', 'max:2000'],
            'follow_up_date' => ['nullable', 'date', 'after_or_equal:today'],
            'notes'       => ['nullable', 'string', 'max:2000'],
            'prescriptions'                         => ['nullable', 'array', 'max:30'],
            'prescriptions.*.medicine_name'         => ['required_with:prescriptions', 'string', 'max:255'],
            'prescriptions.*.dosage'                => ['required_with:prescriptions', 'string', 'max:255'],
            'prescriptions.*.frequency'             => ['required_with:prescriptions', 'string', 'max:255'],
            'prescriptions.*.duration'              => ['required_with:prescriptions', 'string', 'max:255'],
            'prescriptions.*.instructions'          => ['nullable', 'string', 'max:500'],
        ], [
            'symptoms.required'  => 'Please describe the symptoms observed.',
            'diagnosis.required' => 'Please provide a diagnosis.',
            'treatment.required' => 'Please describe the treatment given.',
            'follow_up_date.after_or_equal' => 'Follow-up date must be today or later.',
            'prescriptions.*.medicine_name.required_with' => 'Medicine name is required for each prescription.',
            'prescriptions.*.dosage.required_with'        => 'Dosage is required for each prescription.',
            'prescriptions.*.frequency.required_with'     => 'Frequency is required for each prescription.',
            'prescriptions.*.duration.required_with'      => 'Duration is required for each prescription.',
        ]);

        $treatment = DB::transaction(function () use ($appointment, $validated) {
            $appointment = Appointment::whereKey($appointment->id)->lockForUpdate()->firstOrFail();
            abort_if($appointment->treatment()->exists(), 409, 'A treatment already exists for this appointment.');
            abort_unless(in_array($appointment->status, ['approved', 'rescheduled']), 400, 'This appointment is no longer active.');
            $treatment = Treatment::create([
                'appointment_id' => $appointment->id,
                'pet_id'         => $appointment->pet_id,
                'vet_id'         => Auth::id(),
                'symptoms'       => $validated['symptoms'],
                'diagnosis'      => $validated['diagnosis'],
                'treatment'      => $validated['treatment'],
                'follow_up_date' => $validated['follow_up_date'] ?? null,
                'notes'          => $validated['notes'] ?? null,
            ]);

            if (! empty($validated['prescriptions'])) {
                foreach ($validated['prescriptions'] as $prescription) {
                    $treatment->prescriptions()->create($prescription);
                }
            }

            $appointment->update(['status' => 'completed']);
            return $treatment;
        });

        return redirect()->route('vet.treatments.show', $treatment)
            ->with('success', 'Treatment recorded successfully. Appointment marked as completed.');
    }

    public function show(Treatment $treatment): View
    {
        abort_unless($treatment->vet_id === Auth::id(), 403);

        $treatment->load([
            'appointment.pet.species', 'appointment.pet.breed', 'appointment.pet.images', 'appointment.owner',
            'prescriptions',
        ]);

        return view('vet.treatments.show', compact('treatment'));
    }
}
