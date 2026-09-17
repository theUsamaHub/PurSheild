<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\PublicController;

// ─── Public Pages ────────────────────────────────────────
Route::get('/', [PublicController::class, 'home'])->name('home');
Route::get('/about', [PublicController::class, 'about'])->name('about');
Route::get('/products', [PublicController::class, 'products'])->name('products');
Route::get('/care', [PublicController::class, 'care'])->name('care');
Route::get('/vets', [PublicController::class, 'vets'])->name('vets');
Route::get('/shelters', [PublicController::class, 'shelters'])->name('shelters');
Route::get('/contact', [PublicController::class, 'contact'])->name('contact');

Route::post('/contact', [\App\Http\Controllers\ContactController::class, 'store'])->name('contact.store');

// Dashboard - redirect based on role
Route::get('/dashboard', function () {
    $user = auth()->user();

    if ($user->hasRole('admin')) {
        return redirect()->route('admin.dashboard');
    }
    if ($user->hasRole('vet')) {
        return redirect()->route('vet.dashboard');
    }
    if ($user->hasRole('shelter')) {
        return redirect()->route('shelter.dashboard');
    }

    return redirect()->route('owner.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// ─── Pet Owner Routes ───────────────────────────────────
Route::middleware(['auth', 'verified', 'role:owner'])->prefix('owner')->name('owner.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Owner\DashboardController::class, 'index'])->name('dashboard');

    Route::patch('/notifications/read-all', [\App\Http\Controllers\Owner\NotificationController::class, 'readAll'])->name('notifications.readAll');
    Route::patch('/notifications/{source}/{notification}/read', [\App\Http\Controllers\Owner\NotificationController::class, 'read'])->whereIn('source', ['practice', 'system'])->name('notifications.read');

    // Pets
    Route::get('/pets', [\App\Http\Controllers\Owner\PetController::class, 'index'])->name('pets.index');
    Route::get('/pets/create', [\App\Http\Controllers\Owner\PetController::class, 'create'])->name('pets.create');
    Route::post('/pets', [\App\Http\Controllers\Owner\PetController::class, 'store'])->name('pets.store');
    Route::get('/pets/{pet}', [\App\Http\Controllers\Owner\PetController::class, 'show'])->name('pets.show');
    Route::get('/pets/{pet}/edit', [\App\Http\Controllers\Owner\PetController::class, 'edit'])->name('pets.edit');
    Route::put('/pets/{pet}', [\App\Http\Controllers\Owner\PetController::class, 'update'])->name('pets.update');
    Route::delete('/pets/{pet}', [\App\Http\Controllers\Owner\PetController::class, 'destroy'])->name('pets.destroy');

    Route::post('/favorites/{kind}/{target}', [\App\Http\Controllers\Owner\FavoriteController::class, 'store'])->whereIn('kind',['vet','pet'])->whereNumber('target')->name('favorites.store');
    Route::get('/health-records', [\App\Http\Controllers\Owner\HealthController::class, 'index'])->name('health.overview');
    Route::get('/pets/{pet}/health/download', [\App\Http\Controllers\Owner\HealthController::class, 'download'])->name('health.download');
    Route::get('/pets/{pet}/documents/{document}', [\App\Http\Controllers\Owner\HealthController::class, 'document'])->name('health.document');
    // Health Records
    Route::get('/pets/{pet}/health', [\App\Http\Controllers\Owner\HealthController::class, 'index'])->name('health.index');
    Route::post('/pets/{pet}/health', [\App\Http\Controllers\Owner\HealthController::class, 'store'])->name('health.store');
    Route::post('/pets/{pet}/documents', [\App\Http\Controllers\Owner\HealthController::class, 'storeDocument'])->name('health.storeDocument');
    Route::post('/pets/{pet}/vaccinations', [\App\Http\Controllers\Owner\HealthController::class, 'storeVaccination'])->name('health.storeVaccination');

    // Species/Breeds lookup
    Route::get('/species/{species}/breeds', function (\App\Models\Species $species) {
        return response()->json($species->breeds()->where('status', 'active')->orderBy('name')->get(['id', 'name']));
    })->name('species.breeds');

    // Appointments
    Route::get('/appointments', [\App\Http\Controllers\Owner\AppointmentController::class, 'index'])->name('appointments.index');
    Route::get('/appointments/create', [\App\Http\Controllers\Owner\AppointmentController::class, 'create'])->name('appointments.create');
    Route::post('/appointments', [\App\Http\Controllers\Owner\AppointmentController::class, 'store'])->name('appointments.store');
    Route::delete('/appointments/{appointment}', [\App\Http\Controllers\Owner\AppointmentController::class, 'destroy'])->name('appointments.destroy');

    // Browse Vets
    Route::get('/browse-vets', [\App\Http\Controllers\Owner\BrowseVetController::class, 'index'])->name('browse-vets');
    Route::get('/browse-vets/{vet}', [\App\Http\Controllers\Owner\BrowseVetController::class, 'show'])->name('browse-vets.show');
    Route::post('/browse-vets/{vet}/review', [\App\Http\Controllers\Owner\ReviewController::class, 'store'])->name('reviews.store');

    // Browse Adoption
    Route::get('/browse-adoption', [\App\Http\Controllers\Owner\AdoptionController::class, 'index'])->name('browse-adoption');
    Route::get('/browse-adoption/{listing}', [\App\Http\Controllers\Owner\AdoptionController::class, 'show'])->name('adoption.show');
    Route::post('/browse-adoption/{listing}/apply', [\App\Http\Controllers\Owner\AdoptionController::class, 'apply'])->name('adoption.apply');

    // Products
    Route::get('/products', [\App\Http\Controllers\Owner\ProductController::class, 'index'])->name('products.index');
    Route::get('/products/{product}', [\App\Http\Controllers\Owner\ProductController::class, 'show'])->name('products.show');

    // Cart
    Route::get('/cart', [\App\Http\Controllers\Owner\CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [\App\Http\Controllers\Owner\CartController::class, 'add'])->name('cart.add');
    Route::put('/cart/{item}', [\App\Http\Controllers\Owner\CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{item}', [\App\Http\Controllers\Owner\CartController::class, 'remove'])->name('cart.remove');
    Route::post('/cart/checkout', [\App\Http\Controllers\Owner\CartController::class, 'checkout'])->name('cart.checkout');

    // Orders
    Route::get('/orders', [\App\Http\Controllers\Owner\OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [\App\Http\Controllers\Owner\OrderController::class, 'show'])->name('orders.show');

    // Care Content
    Route::get('/care', [\App\Http\Controllers\Owner\CareController::class, 'index'])->name('care.index');
    Route::get('/care/{careContent}', [\App\Http\Controllers\Owner\CareController::class, 'show'])->name('care.show');
});

// ─── Veterinarian Routes ────────────────────────────────
Route::middleware(['auth', 'verified', 'role:vet'])->prefix('vet')->name('vet.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Vet\DashboardController::class, 'index'])->name('dashboard');

    // Appointments
    Route::get('/appointments', [\App\Http\Controllers\Vet\AppointmentController::class, 'index'])->name('appointments.index');
    Route::get('/appointments/{appointment}', [\App\Http\Controllers\Vet\AppointmentController::class, 'show'])->name('appointments.show');
    Route::put('/appointments/{appointment}/approve', [\App\Http\Controllers\Vet\AppointmentController::class, 'approve'])->name('appointments.approve');
    Route::put('/appointments/{appointment}/reject', [\App\Http\Controllers\Vet\AppointmentController::class, 'reject'])->name('appointments.reject');
    Route::put('/appointments/{appointment}/status', [\App\Http\Controllers\Vet\AppointmentController::class, 'updateStatus'])->name('appointments.updateStatus');
    Route::put('/appointments/{appointment}/reschedule', [\App\Http\Controllers\Vet\AppointmentController::class, 'reschedule'])->name('appointments.reschedule');

    // Availability
    Route::get('/availability', [\App\Http\Controllers\Vet\AvailabilityController::class, 'index'])->name('availability.index');
    Route::post('/availability', [\App\Http\Controllers\Vet\AvailabilityController::class, 'store'])->name('availability.store');
    Route::put('/availability/{availability}', [\App\Http\Controllers\Vet\AvailabilityController::class, 'update'])->name('availability.update');
    Route::delete('/availability/{availability}', [\App\Http\Controllers\Vet\AvailabilityController::class, 'destroy'])->name('availability.destroy');
    Route::post('/availability/copy-week', [\App\Http\Controllers\Vet\AvailabilityController::class, 'copyWeek'])->name('availability.copyWeek');

    // Patients
    Route::get('/patients', [\App\Http\Controllers\Vet\PatientController::class, 'index'])->name('patients.index');
    Route::get('/patients/{pet}', [\App\Http\Controllers\Vet\PatientController::class, 'show'])->name('patients.show');
    Route::get('/patients/{pet}/documents/{document}', [\App\Http\Controllers\Vet\PatientController::class, 'document'])->name('patients.document');

    // Treatments
    Route::get('/treatments', [\App\Http\Controllers\Vet\TreatmentController::class, 'index'])->name('treatments.index');
    Route::get('/treatments/{appointment}/create', [\App\Http\Controllers\Vet\TreatmentController::class, 'create'])->name('treatments.create');
    Route::post('/treatments/{appointment}', [\App\Http\Controllers\Vet\TreatmentController::class, 'store'])->name('treatments.store');
    Route::get('/treatments/{treatment}', [\App\Http\Controllers\Vet\TreatmentController::class, 'show'])->name('treatments.show');

    // Reviews
    Route::get('/reviews', [\App\Http\Controllers\Vet\ReviewController::class, 'index'])->name('reviews.index');
    Route::post('/reviews/{review}/reply', [\App\Http\Controllers\Vet\ReviewController::class, 'reply'])->name('reviews.reply');

    // Notifications
     Route::get('/notifications', [\App\Http\Controllers\Vet\NotificationController::class, 'index'])
    ->name('notifications.index');
    Route::patch('/notifications/read-all', [\App\Http\Controllers\Vet\NotificationController::class, 'markAllRead'])->name('notifications.readAll');
    Route::patch('/notifications/{source}/{notification}/read', [\App\Http\Controllers\Vet\NotificationController::class, 'markRead'])->whereIn('source', ['practice', 'system'])->name('notifications.read');
});

// ─── Animal Shelter Routes ──────────────────────────────
Route::middleware(['auth', 'verified', 'role:shelter'])->prefix('shelter')->name('shelter.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Shelter\DashboardController::class, 'index'])->name('dashboard');

    // Adoption Listings
    Route::get('/listings', [\App\Http\Controllers\Shelter\ListingController::class, 'index'])->name('listings.index');
    Route::get('/listings/create', [\App\Http\Controllers\Shelter\ListingController::class, 'create'])->name('listings.create');
    Route::post('/listings', [\App\Http\Controllers\Shelter\ListingController::class, 'store'])->name('listings.store');
    Route::get('/listings/{listing}', [\App\Http\Controllers\Shelter\ListingController::class, 'show'])->name('listings.show');
    Route::get('/listings/{listing}/edit', [\App\Http\Controllers\Shelter\ListingController::class, 'edit'])->name('listings.edit');
    Route::put('/listings/{listing}', [\App\Http\Controllers\Shelter\ListingController::class, 'update'])->name('listings.update');
    Route::delete('/listings/{listing}', [\App\Http\Controllers\Shelter\ListingController::class, 'destroy'])->name('listings.destroy');

    Route::get('/adoption-history', [\App\Http\Controllers\Shelter\ApplicationController::class, 'history'])->name('history.index');

    // Applications
    Route::get('/applications', [\App\Http\Controllers\Shelter\ApplicationController::class, 'index'])->name('applications.index');
    Route::get('/applications/{application}', [\App\Http\Controllers\Shelter\ApplicationController::class, 'show'])->name('applications.show');
    Route::put('/applications/{application}/status', [\App\Http\Controllers\Shelter\ApplicationController::class, 'updateStatus'])->name('applications.updateStatus');
    Route::post('/applications/{application}/finalize', [\App\Http\Controllers\Shelter\ApplicationController::class, 'finalizeAdoption'])->name('applications.finalize');

    // Reviews
    Route::get('/reviews', [\App\Http\Controllers\Shelter\ReviewController::class, 'index'])->name('reviews.index');
    Route::post('/reviews/{review}/reply', [\App\Http\Controllers\Shelter\ReviewController::class, 'reply'])->name('reviews.reply');

    // Care Status
    Route::get('/care-status', [\App\Http\Controllers\Shelter\CareController::class, 'index'])->name('care-status.index');
    Route::get('/care-status/create', [\App\Http\Controllers\Shelter\CareController::class, 'create'])->name('care-status.create');
    Route::get('/care-status/{log}', [\App\Http\Controllers\Shelter\CareController::class, 'show'])->name('care-status.show');
    Route::get('/care-status/{log}/edit', [\App\Http\Controllers\Shelter\CareController::class, 'edit'])->name('care-status.edit');
    Route::put('/care-status/{log}', [\App\Http\Controllers\Shelter\CareController::class, 'update'])->name('care-status.update');
    Route::get('/notifications', [\App\Http\Controllers\Shelter\NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('/notifications/read-all', [\App\Http\Controllers\Shelter\NotificationController::class, 'readAll'])->name('notifications.readAll');
    Route::patch('/notifications/{source}/{notification}/read', [\App\Http\Controllers\Shelter\NotificationController::class, 'read'])->whereIn('source',['practice','system'])->name('notifications.read');
    Route::post('/care-status', [\App\Http\Controllers\Shelter\CareController::class, 'store'])->name('care-status.store');
    Route::delete('/care-status/{log}', [\App\Http\Controllers\Shelter\CareController::class, 'destroy'])->name('care-status.destroy');
});

// ─── Profile (shared across all roles) ──────────────────
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [\App\Http\Controllers\ProfileController::class, 'destroy'])->name('profile.destroy');
});
