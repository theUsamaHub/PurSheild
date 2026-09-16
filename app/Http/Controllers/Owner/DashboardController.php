<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\CareContent;
use App\Models\Cart;
use App\Models\HealthRecord;
use App\Models\MedicalDocument;
use App\Models\Pet;
use App\Models\Product;
use App\Models\User;
use App\Models\Vaccination;
use App\Support\OwnerPetHealth;
use App\Support\VetNotifications;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $f = $request->validate(['search' => ['nullable', 'string', 'max:200'], 'panel' => ['nullable', 'in:notifications'], 'notice_status' => ['nullable', 'in:all,read,unread']]);
        $owner = Auth::id();
        $ownedPets = Pet::where('owner_id', $owner)->select('id');
        $upcoming = Appointment::where('owner_id', $owner)->whereIn('status', ['pending', 'approved', 'rescheduled'])
            ->where(fn ($q) => $q->whereDate('appointment_date', '>', today())->orWhere(fn ($q) => $q->whereDate('appointment_date', today())->where('appointment_time', '>=', now()->format('H:i:s'))));
        $health = HealthRecord::whereIn('pet_id', clone $ownedPets);
        $vaccines = Vaccination::whereIn('pet_id', clone $ownedPets);
        $stats = ['pets' => (clone $ownedPets)->count(), 'upcoming' => (clone $upcoming)->count(),
            'vaccinations' => OwnerPetHealth::due(clone $vaccines)->count(), 'health_records' => (clone $health)->count(),
            'cart_items' => (int) (Cart::where('owner_id', $owner)->first()?->items()->sum('quantity') ?? 0)];
        $pets = Pet::where('owner_id', $owner)->with(['species', 'breed', 'images'])->withExists(['vaccinations as vaccination_due' => fn ($q) => OwnerPetHealth::due($q)])->latest()->orderByDesc('id')->limit(3)->get();
        $appointments = (clone $upcoming)->with(['pet.images', 'vet'])->orderBy('appointment_date')->orderBy('appointment_time')->limit(3)->get();
        $reminders = OwnerPetHealth::due(clone $vaccines)->with('pet')->orderBy('next_due_date')->limit(4)->get();
        $activity = (clone $health)->with('pet')->latest('created_at')->limit(4)->get()->map(fn ($r) => (object) ['title' => ucfirst($r->record_type ?: 'Health').' record added for '.$r->pet->name, 'date' => $r->created_at, 'icon' => 'activity', 'url' => route('owner.health.index', $r->pet)]);
        $activity = $activity->concat((clone $vaccines)->with('pet')->latest()->limit(4)->get()->map(fn ($r) => (object) ['title' => $r->vaccine_name.' vaccination added for '.$r->pet->name, 'date' => $r->created_at, 'icon' => 'vaccination', 'url' => route('owner.health.index', $r->pet)]))
            ->concat(MedicalDocument::whereIn('pet_id', clone $ownedPets)->with('pet')->latest()->limit(4)->get()->map(fn ($r) => (object) ['title' => 'Medical document uploaded for '.$r->pet->name, 'date' => $r->created_at, 'icon' => 'document', 'url' => route('owner.health.index', $r->pet)]))->sortByDesc('date')->take(4);
        $searchResults = collect();
        $search = trim($f['search'] ?? '');
        if ($search !== '') {
            $searchResults = Pet::where('owner_id', $owner)->where(fn ($q) => $q->where('name', 'like', "%{$search}%")->orWhereHas('breed', fn ($q) => $q->where('name', 'like', "%{$search}%"))->orWhereHas('species', fn ($q) => $q->where('name', 'like', "%{$search}%")))->limit(4)->get()->map(fn ($p) => (object) ['name' => $p->name, 'type' => 'My Pet', 'url' => route('owner.pets.show', $p)]);
            $searchResults = $searchResults->concat(User::where('status', 'active')->whereHas('roles', fn ($q) => $q->where('slug', 'vet'))->whereHas('vetProfile', fn ($q) => $q->where('is_verified', true))->where('name', 'like', "%{$search}%")->limit(4)->get()->map(fn ($p) => (object) ['name' => $p->name, 'type' => 'Veterinarian', 'url' => route('owner.browse-vets.show', $p)]));
            $searchResults = $searchResults->concat(Product::where('status', 'active')->where('name', 'like', "%{$search}%")->limit(4)->get()->map(fn ($p) => (object) ['name' => $p->name, 'type' => 'Product', 'url' => route('owner.products.show', $p)]));
            $searchResults = $searchResults->concat(CareContent::where('status', 'active')->where('title', 'like', "%{$search}%")->limit(4)->get()->map(fn ($p) => (object) ['name' => $p->title, 'type' => 'Pet Care', 'url' => route('owner.care.show', $p)]));
        }
        $notifications = null;
        if (($f['panel'] ?? '') === 'notifications') {
            $q = VetNotifications::query($request->user());
            if (in_array($f['notice_status'] ?? 'all', ['read', 'unread'])) {
                $q->where('is_read', $f['notice_status'] === 'read');
            }
            $notifications = $q->orderByDesc('created_at')->orderByDesc('id')->paginate(10, ['*'], 'notices_page')->withQueryString()->through([VetNotifications::class, 'present']);
        }

        return view('owner.dashboard',compact('stats','pets','appointments','reminders','activity','searchResults','search','notifications'));
    }
}
