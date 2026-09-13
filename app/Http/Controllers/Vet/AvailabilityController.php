<?php

namespace App\Http\Controllers\Vet;

use App\Http\Controllers\Controller;
use App\Models\VetAvailability;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AvailabilityController extends Controller
{
    public function index(): View
    {
        $vetId = Auth::id();
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

        $availability = VetAvailability::where('vet_id', $vetId)
            ->get()
            ->keyBy('day_of_week');

        return view('vet.availability.index', compact('days', 'availability'));
    }

    public function update(Request $request): \Illuminate\Http\RedirectResponse
    {
        $vetId = Auth::id();
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        $rawAvailability = $request->input('availability', []);

        foreach ($days as $index => $day) {
            $data = $rawAvailability[$index] ?? [];
            $isAvailable = isset($data['is_available']) && $data['is_available'] == '1';

            $start = null;
            $end = null;

            if ($isAvailable) {
                $start = $data['start_time'] ?? '09:00';
                $end = $data['end_time'] ?? '17:00';

                if ($start >= $end) {
                    return back()->withInput()
                        ->with('error', "End time must be after start time for {$day}.");
                }
            }

            VetAvailability::updateOrCreate(
                ['vet_id' => $vetId, 'day_of_week' => $day],
                [
                    'is_available' => $isAvailable,
                    'start_time' => $start,
                    'end_time' => $end,
                ]
            );
        }

        return redirect()->route('vet.availability.index')
            ->with('success', 'Availability schedule updated successfully.');
    }
}
