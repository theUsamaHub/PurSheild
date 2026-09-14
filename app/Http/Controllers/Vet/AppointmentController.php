<?php

namespace App\Http\Controllers\Vet;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\User;
use App\Models\VetAvailability;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AppointmentController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:200'],
            'date'   => ['nullable', 'date_format:Y-m-d'],
            'status' => ['nullable', 'in:pending,approved,rescheduled,completed,cancelled,rejected'],
            'period' => ['nullable', 'in:week,today,month,upcoming,all'],
        ]);

        $period = $filters['period'] ?? 'all';
        $query = Appointment::where('vet_id', Auth::id())
            ->with(['pet.species', 'pet.breed', 'pet.images', 'owner'])
            ->withExists('treatment');

        if ($status = $filters['status'] ?? null) {
            $query->where('status', $status);
        }

        $search = trim($filters['search'] ?? '');
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('reason', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%")
                    ->orWhereHas('pet', fn ($pq) => $pq->where('name', 'like', "%{$search}%")
                        ->orWhereHas('species', fn ($sq) => $sq->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('breed', fn ($bq) => $bq->where('name', 'like', "%{$search}%")))
                    ->orWhereHas('owner', fn ($oq) => $oq->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%"));
            });
        }

        // An exact date takes precedence over the broader period filter.
        if ($date = $filters['date'] ?? null) {
            $query->whereDate('appointment_date', $date);
            $period = 'all';
        } else {
            match ($period) {
                'today'    => $query->whereDate('appointment_date', today()),
                'week'     => $query->whereDate('appointment_date', '>=', now()->startOfWeek(Carbon::MONDAY))->whereDate('appointment_date', '<=', now()->endOfWeek(Carbon::SUNDAY)),
                'month'    => $query->whereDate('appointment_date', '>=', now()->startOfMonth())->whereDate('appointment_date', '<=', now()->endOfMonth()),
                'upcoming' => $query->whereIn('status', ['pending', 'approved', 'rescheduled'])
                    ->where(function ($q) {
                        $q->whereDate('appointment_date', '>', today())
                            ->orWhere(fn ($today) => $today->whereDate('appointment_date', today())->whereTime('appointment_time', '>=', now()->format('H:i:s')));
                    }),
                default    => null,
            };
        }
        $appointments = $query->orderBy('appointment_date')->orderBy('appointment_time')->orderBy('id')
            ->paginate(10)->withQueryString();

        return view('vet.appointments.index', compact('appointments', 'period'));
    }

    public function show(Appointment $appointment): View
    {
        abort_unless($appointment->vet_id === Auth::id(), 403);

        $appointment->load([
            'pet.species', 'pet.breed', 'pet.images', 'pet.healthRecords',
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

        $appointment->update(['status' => 'rejected']);

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

        if (! in_array($appointment->status, ['approved', 'rescheduled'])) {
            return back()->with('error', 'Only approved or rescheduled appointments can have their status updated.');
        }

        if ($newStatus === 'completed' && ! $appointment->treatment) {
            return back()->with('error', 'Please record a treatment before marking as completed.');
        }

        $appointment->update(['status' => $newStatus]);

        $label = $newStatus === 'completed' ? 'completed' : 'cancelled';

        return back()->with('success', "Appointment marked as {$label}.");
    }

    public function reschedule(Request $request, Appointment $appointment): RedirectResponse
    {
        abort_unless((int) $appointment->vet_id === (int) Auth::id(), 403);
        $validated = $request->validate([
            'appointment_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
            'appointment_time' => ['required', 'date_format:H:i'],
        ]);
        $when = Carbon::parse($validated['appointment_date'].' '.$validated['appointment_time']);
        if ($when->isPast()) {
            throw ValidationException::withMessages(['appointment_time' => 'Please choose a future appointment time.']);
        }

        DB::transaction(function () use ($appointment, $validated, $when) {
            // Serialize rescheduling requests for this vet, including different appointments.
            User::whereKey(Auth::id())->lockForUpdate()->firstOrFail();
            $appointment = Appointment::whereKey($appointment->id)->lockForUpdate()->firstOrFail();
            if (! in_array($appointment->status, ['pending', 'approved', 'rescheduled']) || $appointment->treatment()->exists()) {
                throw ValidationException::withMessages(['appointment_date' => 'Only active appointments without a treatment can be rescheduled.']);
            }
            $time = $when->format('H:i:s');
            $slots = VetAvailability::where('vet_id', Auth::id())
                ->where('day_of_week', strtolower($when->format('l')))->get();
            $contains = fn ($slot) => $slot->start_time && $slot->end_time
                && $slot->start_time <= $time && $slot->end_time > $time;
            if (! $slots->where('is_available', true)->contains($contains)
                || $slots->where('is_available', false)->contains($contains)) {
                throw ValidationException::withMessages(['appointment_time' => 'Choose a time within your available weekly slots.']);
            }
            $conflict = Appointment::where('vet_id', Auth::id())
                ->whereKeyNot($appointment->id)
                ->whereDate('appointment_date', $validated['appointment_date'])
                ->whereTime('appointment_time', $time)
                ->whereIn('status', ['pending', 'approved', 'rescheduled'])->exists();
            if ($conflict) {
                throw ValidationException::withMessages(['appointment_time' => 'This time is already booked. Please choose another time.']);
            }
            $appointment->update([
                'appointment_date' => $validated['appointment_date'],
                'appointment_time' => $time,
                'status' => 'rescheduled',
            ]);
        });

        return back()->with('success', 'Appointment rescheduled successfully.');
    }
}
