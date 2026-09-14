<?php

namespace App\Http\Controllers\Shelter;

use App\Http\Controllers\Controller;
use App\Models\AdoptionApplication;
use App\Models\AdoptionListing;
use App\Models\CareStatusLog;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $base = AdoptionListing::where('shelter_id', Auth::id());
        $apps = AdoptionApplication::whereHas('listing', fn ($q) => $q->where('shelter_id', Auth::id()));
        $stats = ['total' => (clone $base)->count(), 'available' => (clone $base)->where('status', 'available')->count(), 'pending' => (clone $apps)->where('status', 'pending')->count(), 'under_treatment' => (clone $base)->where('health_state', 'under_treatment')->count(), 'adopted' => (clone $base)->where('status', 'adopted')->count()];
        $recentApplications = (clone $apps)->with(['listing.breed', 'listing.images', 'applicant'])->latest()->orderByDesc('id')->limit(4)->get();
        $attention = CareStatusLog::where('shelter_id', Auth::id())->whereIn('status', ['pending', 'follow_up'])->with(['listing.images', 'listing.breed'])->orderByRaw('COALESCE(due_date, log_date)')->limit(3)->get();
        $attentionAnimals = (clone $base)->whereIn('health_state', ['under_treatment', 'vaccination_due'])->whereNotIn('id', $attention->pluck('listing_id')->filter())->where('status', '!=', 'adopted')->with(['images', 'breed'])->limit(max(0, 3 - $attention->count()))->get();
        $activity = CareStatusLog::where('shelter_id', Auth::id())->latest('updated_at')->limit(4)->get()->map(fn ($log) => (object) ['text' => ucfirst($log->type).' log updated for '.$log->animal_name, 'date' => $log->updated_at, 'icon' => 'care', 'url' => route('shelter.care-status.show', $log)]);
        $activity = $activity->concat((clone $base)->latest()->limit(4)->get()->map(fn ($l) => (object) ['text' => 'New animal added – '.$l->pet_name, 'date' => $l->created_at, 'icon' => 'paw', 'url' => route('shelter.listings.show', $l)]))
            ->concat((clone $apps)->with('applicant')->latest('updated_at')->limit(4)->get()->map(fn ($a) => (object) ['text' => ucfirst($a->status).' adoption request from '.($a->applicant->name ?? 'Applicant'), 'date' => $a->updated_at, 'icon' => 'requests', 'url' => route('shelter.applications.show', $a)]))->sortByDesc('date')->take(4);

        return view('shelter.dashboard', compact('stats','recentApplications','attention','attentionAnimals','activity'));
    }
}
