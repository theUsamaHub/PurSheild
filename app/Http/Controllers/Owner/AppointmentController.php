<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Pet;
use App\Models\User;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AppointmentController extends Controller
{
    public function index(Request $request): View
    {
        $query = Appointment::with(['pet', 'vet'])
            ->where('owner_id', Auth::id());

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $appointments = $query->latest('appointment_date')->latest('appointment_time')->paginate(15);

        $vetIds = $appointments->pluck('vet_id')->filter()->unique()->toArray();
        $reviewedVetIds = Review::where('user_id', Auth::id())
            ->where('reviewable_type', User::class)
            ->whereIn('reviewable_id', $vetIds)
            ->pluck('reviewable_id')
            ->toArray();

        $userId = Auth::id();
        $stats = [
            'total' => Appointment::where('owner_id', $userId)->count(),
            'pending' => Appointment::where('owner_id', $userId)->where('status', 'pending')->count(),
            'approved' => Appointment::where('owner_id', $userId)->where('status', 'approved')->count(),
            'completed' => Appointment::where('owner_id', $userId)->where('status', 'completed')->count(),
        ];

        return view('owner.appointments.index', compact('appointments', 'stats', 'reviewedVetIds'));
    }

    public function create(Request $request): View
    {
        $pets = Pet::where('owner_id', Auth::id())->orderBy('name')->get();
        $vets = User::whereHas('roles', function ($q) {
            $q->where('slug', 'vet');
        })->whereHas('vetProfile', function ($q) {
            $q->where('is_verified', true);
        })->with('vetProfile')->orderBy('name')->get();

        $selectedVetId = $request->input('vet_id');

        return view('owner.appointments.create', compact('pets', 'vets', 'selectedVetId'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'pet_id' => ['required', 'exists:pets,id'],
            'vet_id' => ['required', 'exists:users,id'],
            'appointment_date' => ['required', 'date', 'after_or_equal:today'],
            'appointment_time' => ['required', 'date_format:H:i'],
            'reason' => ['required', 'string', 'max:1000'],
        ], [
            'pet_id.required' => 'Please select a pet.',
            'pet_id.exists' => 'Selected pet does not exist.',
            'vet_id.required' => 'Please select a veterinarian.',
            'vet_id.exists' => 'Selected veterinarian does not exist.',
            'appointment_date.required' => 'Appointment date is required.',
            'appointment_date.date' => 'Please enter a valid date.',
            'appointment_date.after_or_equal' => 'Appointment date must be today or in the future.',
            'appointment_time.required' => 'Appointment time is required.',
            'appointment_time.date_format' => 'Please enter a valid time (HH:MM).',
            'reason.required' => 'Please provide a reason for the appointment.',
            'reason.max' => 'Reason must not exceed 1000 characters.',
        ]);

        $pet = Pet::where('id', $validated['pet_id'])->where('owner_id', Auth::id())->first();
        if (!$pet) {
            return back()->withErrors(['pet_id' => 'You can only book appointments for your own pets.'])->withInput();
        }

        $vet = User::whereHas('roles', function ($q) {
            $q->where('slug', 'vet');
        })->whereHas('vetProfile', function ($q) {
            $q->where('is_verified', true);
        })->find($validated['vet_id']);

        if (!$vet) {
            return back()->withErrors(['vet_id' => 'Selected veterinarian is not available.'])->withInput();
        }

        $data['owner_id'] = Auth::id();

        Appointment::create(array_merge($validated, $data));

        return redirect()->route('owner.appointments.index')
            ->with('success', 'Appointment booked successfully.');
    }

    public function destroy(Appointment $appointment): RedirectResponse
    {
        if ($appointment->owner_id !== Auth::id()) {
            abort(403, 'You are not authorized to cancel this appointment.');
        }

        if ($appointment->status !== 'pending') {
            return back()->with('error', 'Only pending appointments can be cancelled.');
        }

        $appointment->update(['status' => 'cancelled']);

        return redirect()->route('owner.appointments.index')
            ->with('success', 'Appointment cancelled successfully.');
    }
}
