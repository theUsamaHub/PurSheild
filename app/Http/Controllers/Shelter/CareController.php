<?php

namespace App\Http\Controllers\Shelter;

use App\Http\Controllers\Controller;
use App\Models\AdoptionListing;
use App\Models\CareStatusLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CareController extends Controller
{
    public function index(Request $request)
    {
        $f = $request->validate(['search' => ['nullable', 'string', 'max:200'], 'animal' => ['nullable', 'string', 'max:200'],
            'listing_id' => ['nullable', 'integer', Rule::exists('adoption_listings', 'id')->where('shelter_id', Auth::id())],
            'type' => ['nullable', 'in:feeding,grooming,medical,vaccination,other'], 'status' => ['nullable', 'in:completed,pending,follow_up'],
            'period' => ['nullable', 'in:today,week,month,all,custom'], 'sort' => ['nullable', 'in:newest,oldest'],
            'date_from' => ['nullable', 'date_format:Y-m-d'], 'date_to' => ['nullable', 'date_format:Y-m-d', ...($request->filled('date_from') ? ['after_or_equal:date_from'] : [])]]);
        $q = CareStatusLog::where('shelter_id', Auth::id())->with(['listing.images', 'listing.breed']);
        $search = trim($f['search'] ?? $f['animal'] ?? '');
        if ($search !== '') {
            $q->where(fn ($q) => $q->where('animal_name', 'like', "%{$search}%")->orWhere('notes', 'like', "%{$search}%")->orWhere('staff_name', 'like', "%{$search}%")->orWhereHas('listing', fn ($l) => $l->where('pet_name', 'like', "%{$search}%")));
        }
        foreach (['listing_id', 'type', 'status'] as $field) {
            if ($f[$field] ?? null) {
                $q->where($field, $f[$field]);
            }
        }
        if (($f['date_from'] ?? null) || ($f['date_to'] ?? null)) {
            if ($f['date_from'] ?? null) {
                $q->whereDate('log_date', '>=', $f['date_from']);
            }
            if ($f['date_to'] ?? null) {
                $q->whereDate('log_date', '<=', $f['date_to']);
            }
        } else {
            match ($f['period'] ?? 'all') {
                'today' => $q->whereDate('log_date', today()),'week' => $q->whereBetween('log_date', [now()->startOfWeek()->toDateString(), now()->endOfWeek()->toDateString()]),'month' => $q->whereBetween('log_date', [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()]),default => null
            };
        }
        $direction = ($f['sort'] ?? 'newest') === 'oldest' ? 'asc' : 'desc';
        $logs = $q->orderBy('log_date', $direction)->orderBy('log_time', $direction)->orderBy('id', $direction)->paginate(10)->withQueryString();
        $listings = AdoptionListing::where('shelter_id', Auth::id())->orderBy('pet_name')->get();

        return view('shelter.care.index', compact('logs', 'listings'));
    }

    public function create(Request $request)
    {
        $request->validate(['listing_id' => ['nullable', 'integer', Rule::exists('adoption_listings', 'id')->where('shelter_id', Auth::id())]]);

        return $this->form(new CareStatusLog(['listing_id' => $request->listing_id, 'log_date' => today(), 'status' => 'completed', 'log_time' => now()->format('H:i'), 'staff_name' => Auth::user()->name]));
    }

    public function edit(CareStatusLog $log)
    {
        $this->authorizeLog($log);

        return $this->form($log);
    }

    private function form(CareStatusLog $log)
    {
        $listings = AdoptionListing::where('shelter_id', Auth::id())->orderBy('pet_name')->get();

        return view('shelter.care.form', compact('log', 'listings'));
    }

    public function show(CareStatusLog $log)
    {
        $this->authorizeLog($log);
        $log->load(['listing.images', 'listing.breed']);

        return view('shelter.care.show', compact('log'));
    }

    public function store(Request $request)
    {
        return $this->save($request, new CareStatusLog(['shelter_id' => Auth::id()]));
    }

    public function update(Request $request, CareStatusLog $log)
    {
        $this->authorizeLog($log);

        return $this->save($request, $log);
    }

    private function save(Request $request, CareStatusLog $log)
    {
        $data = $request->validate([
            'listing_id' => ['required', 'integer', Rule::exists('adoption_listings', 'id')->where('shelter_id', Auth::id())],
            'type' => ['required', 'in:feeding,grooming,medical,vaccination,other'], 'notes' => ['required', 'string', 'max:2000'],
            'log_date' => ['required', 'date_format:Y-m-d', ...($request->status === 'completed' ? ['before_or_equal:today'] : [])],
            'log_time' => ['required', 'date_format:H:i'], 'staff_name' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:completed,pending,follow_up'], 'due_date' => ['nullable', 'required_if:status,follow_up', 'date_format:Y-m-d', 'after_or_equal:log_date'],
        ]);
        if ($data['status'] === 'completed' && Carbon::parse($data['log_date'].' '.$data['log_time'])->isFuture()) {
            throw ValidationException::withMessages(['log_time' => 'Completed care cannot be recorded for a future time. Use Pending for scheduled care.']);
        }
        $listing = AdoptionListing::where('shelter_id', Auth::id())->findOrFail($data['listing_id']);
        $data['animal_name'] = $listing->pet_name;
        $data['log_time'] .= ':00';
        $log->fill($data)->save();

        return redirect()->route('shelter.care-status.index')->with('success', 'Care record saved successfully.');
    }

    public function destroy(CareStatusLog $log)
    {
        $this->authorizeLog($log);
        $log->delete();

        return redirect()->route('shelter.care-status.index')->with('success', 'Care record deleted.');
    }

    private function authorizeLog(CareStatusLog $log): void
    {
        abort_unless((int) $log->shelter_id === (int) Auth::id(),403);
    }
}
