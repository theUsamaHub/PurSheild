<?php

namespace App\Http\Controllers\Vet;

use App\Http\Controllers\Controller;
use App\Models\VetAvailability;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AvailabilityController extends Controller
{
    /** Canonical day order used across the feature. */
    protected array $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

    public function index(): View
    {
        $vetId = Auth::id();
        $days = $this->days;

        $slots = VetAvailability::where('vet_id', $vetId)
            ->orderBy('start_time')
            ->get();

        // Group all slots by day so a day can have 0, 1, or many time ranges.
        $availability = $slots->groupBy('day_of_week');

        // A day counts as "active" if it has at least one available slot.
        $activeDays = collect($days)->filter(function ($day) use ($availability) {
            $daySlots = $availability->get($day);

            return $daySlots && $daySlots->contains('is_available', true);
        })->count();

        $availableSlotsThisWeek = $slots->where('is_available', true)->count();
        $blockedDates = $slots->where('is_available', false)->unique('day_of_week')->count();

        return view('vet.availability.index', compact(
            'days',
            'availability',
            'activeDays',
            'availableSlotsThisWeek',
            'blockedDates'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateSlot($request);

        if ($overlapError = $this->findOverlapError($validated, Auth::id())) {
            return back()->withInput()->with('error', $overlapError);
        }

        VetAvailability::create([
            'vet_id' => Auth::id(),
            'day_of_week' => $validated['day_of_week'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'is_available' => $validated['is_available'],
        ]);

        return back()->with('success', 'New availability slot added successfully.');
    }

    public function update(Request $request, VetAvailability $availability): RedirectResponse
    {
        abort_if($availability->vet_id !== Auth::id(), 403);

        $validated = $this->validateSlot($request);

        if ($overlapError = $this->findOverlapError($validated, Auth::id(), $availability->id)) {
            return back()->withInput()->with('error', $overlapError);
        }

        $availability->update([
            'day_of_week' => $validated['day_of_week'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'is_available' => $validated['is_available'],
        ]);

        return back()->with('success', 'Availability slot updated successfully.');
    }

    public function destroy(VetAvailability $availability): RedirectResponse
    {
        abort_if($availability->vet_id !== Auth::id(), 403);

        $availability->delete();

        return back()->with('success', 'Availability slot removed.');
    }

    /**
     * Copy one day's slots onto every day that currently has no slots at all.
     * Existing days are left untouched so nothing gets silently overwritten.
     */
    public function copyWeek(Request $request): RedirectResponse
    {
        $vetId = Auth::id();

        $validated = $request->validate([
            'source_day' => ['required', Rule::in($this->days)],
        ]);

        $sourceSlots = VetAvailability::where('vet_id', $vetId)
            ->where('day_of_week', $validated['source_day'])
            ->get();

        if ($sourceSlots->isEmpty()) {
            return back()->with('error', 'The selected day has no slots to copy.');
        }

        $existingDays = VetAvailability::where('vet_id', $vetId)
            ->pluck('day_of_week')
            ->unique();

        $copied = DB::transaction(function () use ($validated, $existingDays, $sourceSlots, $vetId) {
            $copied = 0;

            foreach ($this->days as $day) {
                if ($day === $validated['source_day'] || $existingDays->contains($day)) {
                    continue;
                }

                foreach ($sourceSlots as $slot) {
                    VetAvailability::create([
                        'vet_id' => $vetId,
                        'day_of_week' => $day,
                        'start_time' => $slot->start_time,
                        'end_time' => $slot->end_time,
                        'is_available' => $slot->is_available,
                    ]);
                }
                $copied++;
            }

            return $copied;
        });

        if ($copied === 0) {
            return back()->with('error', 'Every other day already has its own schedule.');
        }

        return back()->with('success', "Copied {$validated['source_day']}'s schedule to {$copied} day(s).");
    }

    protected function validateSlot(Request $request): array
    {
        $validated = $request->validate([
            'day_of_week' => ['required', Rule::in($this->days)],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'is_available' => ['required', 'boolean'],
        ], [
            'day_of_week.required' => 'Please select a day.',
            'day_of_week.in' => 'Please select a valid day.',
            'start_time.required' => 'Start time is required.',
            'start_time.date_format' => 'Start time must be a valid time (HH:MM).',
            'end_time.required' => 'End time is required.',
            'end_time.date_format' => 'End time must be a valid time (HH:MM).',
            'end_time.after' => 'End time must be after start time.',
        ]);

        // Parse with an explicit format so a malformed value throws a clean
        // validation error here instead of blowing up later when the view
        // tries to display it.
        $start = Carbon::createFromFormat('!H:i', $validated['start_time']);
        $end = Carbon::createFromFormat('!H:i', $validated['end_time']);

        if ($start->diffInMinutes($end) < 15) {
            throw ValidationException::withMessages([
                'end_time' => 'A slot must be at least 15 minutes long.',
            ]);
        }

        // Normalize to H:i:s so every row is stored in a consistent format,
        // regardless of what the browser sent. This is what fixed the
        // "two digit second could not be found" crash — the view was
        // receiving inconsistent H:i / H:i:s strings from the database.
        $validated['start_time'] = $start->format('H:i:s');
        $validated['end_time'] = $end->format('H:i:s');

        return $validated;
    }

    /**
     * Returns a human-readable error string if the given slot overlaps an
     * existing slot for the same vet/day, or null if there's no conflict.
     */
    protected function findOverlapError(array $validated, int $vetId, ?int $ignoreId = null): ?string
    {
        $query = VetAvailability::where('vet_id', $vetId)
            ->where('day_of_week', $validated['day_of_week'])
            ->where(function ($q) use ($validated) {
                $q->where('start_time', '<', $validated['end_time'])
                    ->where('end_time', '>', $validated['start_time']);
            });

        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        if ($query->exists()) {
            return 'This time slot overlaps with an existing slot for '.ucfirst($validated['day_of_week']).'.';
        }

        return null;
    }
}
