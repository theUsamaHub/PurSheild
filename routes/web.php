<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

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

    // Pets
    Route::get('/pets', [\App\Http\Controllers\Owner\PetController::class, 'index'])->name('pets.index');
    Route::get('/pets/create', [\App\Http\Controllers\Owner\PetController::class, 'create'])->name('pets.create');
    Route::post('/pets', [\App\Http\Controllers\Owner\PetController::class, 'store'])->name('pets.store');
    Route::get('/pets/{pet}', [\App\Http\Controllers\Owner\PetController::class, 'show'])->name('pets.show');
    Route::get('/pets/{pet}/edit', [\App\Http\Controllers\Owner\PetController::class, 'edit'])->name('pets.edit');
    Route::put('/pets/{pet}', [\App\Http\Controllers\Owner\PetController::class, 'update'])->name('pets.update');
    Route::delete('/pets/{pet}', [\App\Http\Controllers\Owner\PetController::class, 'destroy'])->name('pets.destroy');

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
});

// ─── Animal Shelter Routes ──────────────────────────────
Route::middleware(['auth', 'verified', 'role:shelter'])->prefix('shelter')->name('shelter.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Shelter\DashboardController::class, 'index'])->name('dashboard');
});

// ─── Profile (shared across all roles) ──────────────────
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [\App\Http\Controllers\ProfileController::class, 'destroy'])->name('profile.destroy');
});
