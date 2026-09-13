<?php

namespace App\Http\Controllers\Vet;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AppointmentController extends Controller
{
    public function index(Request $request): View
    {
        $query = Appointment::where('vet_id', Auth::id())
            ->with(['pet.species', 'pet.breed', 'owner']);

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('pet', fn($q) => $q->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('owner', fn($q) => $q->where('name', 'like', "%{$search}%"));
            });
        }

        if ($date = $request->input('date')) {
            $query->whereDate('appointment_date', $date);
        }

        $appointments = $query->latest('appointment_date')
            ->latest('appointment_time')
            ->paginate(15);

        $stats = [
            'total' => Appointment::where('vet_id', Auth::id())->count(),
            'pending' => Appointment::where('vet_id', Auth::id())->where('status', 'pending')->count(),
            'approved' => Appointment::where('vet_id', Auth::id())->where('status', 'approved')->count(),
            'completed' => Appointment::where('vet_id', Auth::id())->where('status', 'completed')->count(),
            'cancelled' => Appointment::where('vet_id', Auth::id())->where('status', 'cancelled')->count(),
        ];

        return view('vet.appointments.index', compact('appointments', 'stats'));
    }

    public function show(Appointment $appointment): View
    {
        abort_unless($appointment->vet_id === Auth::id(), 403);

        $appointment->load([
            'pet.species', 'pet.breed', 'pet.healthRecords',
            'pet.vaccinations', 'pet.medicalDocuments', 'owner',
            'treatment.prescriptions',
        ]);

        $hasTreatment = $appointment->treatment()->exists();

        return view('vet.appointments.show', compact('appointment', 'hasTreatment'));
    }

    public function approve(Appointment $appointment): RedirectResponse
    {
        abort_unless($appointment->vet_id === Auth::id(), 403);

        if ($appointment->status !== 'pending') {
            return back()->with('error', 'Only pending appointments can be approved.');
        }

        $appointment->update(['status' => 'approved']);

        return back()->with('success', 'Appointment approved successfully.');
    }

    public function reject(Appointment $appointment): RedirectResponse
    {
        abort_unless($appointment->vet_id === Auth::id(), 403);

        if ($appointment->status !== 'pending') {
            return back()->with('error', 'Only pending appointments can be rejected.');
        }

        $appointment->update(['status' => 'cancelled']);

        return back()->with('success', 'Appointment rejected.');
    }

    public function updateStatus(Request $request, Appointment $appointment): RedirectResponse
    {
        abort_unless($appointment->vet_id === Auth::id(), 403);

        $validated = $request->validate([
            'status' => ['required', 'in:completed,cancelled'],
        ], [
            'status.required' => 'Please select a status.',
            'status.in' => 'Invalid status selected.',
        ]);

        $newStatus = $validated['status'];

        if (!in_array($appointment->status, ['approved'])) {
            return back()->with('error', 'Only approved appointments can have their status updated.');
        }

        if ($newStatus === 'completed' && !$appointment->treatment) {
            return back()->with('error', 'Please record a treatment before marking as completed.');
        }

        $appointment->update(['status' => $newStatus]);

        $label = $newStatus === 'completed' ? 'completed' : 'cancelled';

        return back()->with('success', "Appointment marked as {$label}.");
    }
}
