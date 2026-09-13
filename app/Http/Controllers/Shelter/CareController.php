<?php

namespace App\Http\Controllers\Shelter;

use App\Http\Controllers\Controller;
use App\Models\AdoptionListing;
use App\Models\CareStatusLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CareController extends Controller
{
    public function index(Request $request): View
    {
        $shelterId = Auth::id();

        $query = CareStatusLog::where('shelter_id', $shelterId);

        if ($type = $request->input('type')) {
            $query->where('type', $type);
        }

        if ($animal = $request->input('animal')) {
            $query->where('animal_name', 'like', "%{$animal}%");
        }

        if ($dateFrom = $request->input('date_from')) {
            $query->where('log_date', '>=', $dateFrom);
        }

        if ($dateTo = $request->input('date_to')) {
            $query->where('log_date', '<=', $dateTo);
        }

        $logs = $query->latest('log_date')->latest('id')->paginate(15);

        $stats = [
            'total' => CareStatusLog::where('shelter_id', $shelterId)->count(),
            'feeding' => CareStatusLog::where('shelter_id', $shelterId)->where('type', 'feeding')->count(),
            'grooming' => CareStatusLog::where('shelter_id', $shelterId)->where('type', 'grooming')->count(),
            'medical' => CareStatusLog::where('shelter_id', $shelterId)->where('type', 'medical')->count(),
        ];

        $listings = AdoptionListing::where('shelter_id', $shelterId)
            ->where('status', '!=', 'inactive')
            ->orderBy('pet_name')
            ->get(['id', 'pet_name']);

        return view('shelter.care.index', compact('logs', 'stats', 'listings'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'animal_name' => 'required|string|max:255',
            'type' => 'required|in:feeding,grooming,medical,other',
            'notes' => 'required|string|max:2000',
            'log_date' => 'required|date',
            'listing_id' => 'nullable|exists:adoption_listings,id',
        ]);

        $validated['shelter_id'] = Auth::id();

        CareStatusLog::create($validated);

        return redirect()->route('shelter.care-status.index')
            ->with('success', 'Care log added successfully.');
    }

    public function destroy(CareStatusLog $log)
    {
        abort_unless($log->shelter_id === Auth::id(), 403);

        $log->delete();

        return redirect()->route('shelter.care-status.index')
            ->with('success', 'Care log deleted successfully.');
    }
}
