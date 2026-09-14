<?php

namespace App\Http\Controllers\Shelter;

use App\Http\Controllers\Controller;
use App\Models\AdoptionApplication;
use App\Models\AdoptionListing;
use App\Models\Species;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ApplicationController extends Controller
{
    private function query()
    {
        return AdoptionApplication::whereHas('listing', fn ($q) => $q->where('shelter_id', Auth::id()));
    }

    public function index(Request $request)
    {
        return $this->browse($request, false);
    }

    public function history(Request $request)
    {
        return $this->browse($request, true);
    }

    private function browse(Request $request, bool $history)
    {
        $f = $request->validate(['search' => ['nullable', 'string', 'max:200'], 'status' => ['nullable', $history ? 'in:completed,rejected' : 'in:pending,reviewing,approved,rejected,completed'],
            'species_id' => ['nullable', 'integer', 'exists:species,id'], 'date_from' => ['nullable', 'date_format:Y-m-d'],
            'date_to' => ['nullable', 'date_format:Y-m-d', ...($request->filled('date_from') ? ['after_or_equal:date_from'] : [])],
            'selected' => ['nullable', 'integer'], 'panel' => ['nullable', 'in:closed']]);
        $base = $this->query();
        if ($history) {
            $base->where(fn ($q) => $q->whereIn('status', ['completed', 'rejected'])->orWhere(fn ($q) => $q->where('status', 'approved')->whereHas('listing', fn ($l) => $l->where('status', 'adopted'))));
        }
        $stats = ['total' => (clone $base)->count()];
        foreach (['pending', 'reviewing', 'approved', 'rejected', 'completed'] as $status) {
            $stats[$status] = (clone $base)->where('status', $status)->count();
        }
        if ($history) {
            $stats['completed'] = (clone $base)->whereIn('status', ['approved', 'completed'])->count();
            $stats['month'] = (clone $base)->whereRaw('COALESCE(completed_at, decided_at, updated_at) >= ?', [now()->startOfMonth()])->whereRaw('COALESCE(completed_at, decided_at, updated_at) <= ?', [now()->endOfMonth()])->count();
        }
        $q = (clone $base)->with(['listing.species', 'listing.breed', 'listing.images', 'applicant']);
        $search = trim($f['search'] ?? '');
        if ($search !== '') {
            $q->where(fn ($q) => $q->where('message', 'like', "%{$search}%")->orWhereHas('applicant', fn ($a) => $a->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"))->orWhereHas('listing', fn ($l) => $l->where('pet_name', 'like', "%{$search}%")));
        }
        if ($f['status'] ?? null) {
            if ($history && $f['status'] === 'completed') {
                $q->whereIn('status', ['approved', 'completed']);
            } else {
                $q->where('status', $f['status']);
            }
        }
        if ($f['species_id'] ?? null) {
            $q->whereHas('listing', fn ($l) => $l->where('species_id', $f['species_id']));
        }
        $column = $history ? 'COALESCE(completed_at, decided_at, updated_at)' : 'created_at';
        if ($f['date_from'] ?? null) {
            $q->whereRaw("DATE($column) >= ?", [$f['date_from']]);
        }
        if ($f['date_to'] ?? null) {
            $q->whereRaw("DATE($column) <= ?", [$f['date_to']]);
        }
        $applications = $q->orderByRaw("$column DESC")->orderByDesc('id')->paginate(8)->withQueryString();
        $selected = null;
        if (! $history && ($f['panel'] ?? '') !== 'closed') {
            $selected = ($f['selected'] ?? null) ? $this->query()->with(['listing.species', 'listing.breed', 'listing.images', 'applicant'])->findOrFail($f['selected']) : $applications->first();
        }
        $species = Species::orderBy('name')->get();

        return view($history ? 'shelter.applications.history' : 'shelter.applications.index', compact('applications', 'stats', 'selected', 'species'));
    }

    public function show(AdoptionApplication $application)
    {
        $this->authorizeApplication($application);
        $application->load(['listing.species', 'listing.breed', 'listing.images', 'applicant']);

        return view('shelter.applications.show', compact('application'));
    }

    public function updateStatus(Request $request, AdoptionApplication $application)
    {
        $this->authorizeApplication($application);
        $data = $request->validate(['status' => ['required', 'in:reviewing,approved,rejected'], 'shelter_response' => ['nullable', 'string', 'max:1000']]);
        DB::transaction(function () use ($application, $data) {
            $listing = AdoptionListing::whereKey($application->listing_id)->lockForUpdate()->firstOrFail();
            $application = AdoptionApplication::whereKey($application->id)->lockForUpdate()->firstOrFail();
            if (in_array($application->status, ['rejected', 'completed']) || $listing->status === 'adopted') {
                throw ValidationException::withMessages(['status' => 'This request is already closed.']);
            }
            if ($application->status === 'approved' && $data['status'] !== 'rejected') {
                throw ValidationException::withMessages(['status' => 'An approved request can be finalized or rejected.']);
            }
            if ($data['status'] === 'approved') {
                if (! in_array($listing->status, ['available', 'pending']) || $listing->applications()->whereKeyNot($application->id)->whereIn('status', ['approved', 'completed'])->exists()) {
                    throw ValidationException::withMessages(['status' => 'This animal is not available for approval.']);
                }
                $listing->update(['status' => 'pending']);
            }
            $wasApproved = $application->status === 'approved';
            $application->update($data + ['decided_at' => in_array($data['status'], ['approved', 'rejected']) ? now() : null]);
            if ($wasApproved && $data['status'] === 'rejected') {
                $listing->update(['status' => 'available']);
            }
        });

        return back()->with('success', 'Application status updated successfully.');
    }

    public function finalizeAdoption(AdoptionApplication $application)
    {
        $this->authorizeApplication($application);
        DB::transaction(function () use ($application) {
            $listing = AdoptionListing::whereKey($application->listing_id)->lockForUpdate()->firstOrFail();
            $application = AdoptionApplication::whereKey($application->id)->lockForUpdate()->firstOrFail();
            if ($application->status !== 'approved' || $listing->status === 'adopted') {
                throw ValidationException::withMessages(['status' => 'Only an approved request for an unadopted animal can be finalized.']);
            }
            $application->update(['status' => 'completed', 'completed_at' => now(), 'decided_at' => now()]);
            $listing->update(['status' => 'adopted']);
            $listing->applications()->whereKeyNot($application->id)->whereIn('status', ['pending', 'reviewing', 'approved'])->update(['status' => 'rejected', 'decided_at' => now(), 'shelter_response' => 'The animal has been adopted by another applicant.']);
        });

        return redirect()->route('shelter.applications.show', $application)->with('success', 'Adoption finalized. The animal and its completed request are now in adoption history.');
    }

    private function authorizeApplication(AdoptionApplication $application): void
    {
        abort_unless((int) $application->listing->shelter_id === (int) Auth::id(),403);
    }
}
