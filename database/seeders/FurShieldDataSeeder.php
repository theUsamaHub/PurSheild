<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Pet;
use App\Models\Species;
use App\Models\Breed;
use App\Models\VetAvailability;
use App\Models\HealthRecord;
use App\Models\Vaccination;
use App\Models\Appointment;
use App\Models\Treatment;
use App\Models\Prescription;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\AdoptionListing;
use App\Models\AdoptionImage;
use App\Models\FurshieldNotification;
use App\Models\Review;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Seeder;

class FurShieldDataSeeder extends Seeder
{
    public function run(): void
    {
        $owners = User::whereHas('roles', fn ($q) => $q->where('slug', 'owner'))->get();
        $vets = User::whereHas('roles', fn ($q) => $q->where('slug', 'vet'))->get();
        $shelters = User::whereHas('roles', fn ($q) => $q->where('slug', 'shelter'))->get();

        $dogSpecies = Species::where('name', 'Dog')->first();
        $catSpecies = Species::where('name', 'Cat')->first();

        $labBreed = Breed::where('name', 'Labrador Retriever')->first();
        $persianBreed = Breed::where('name', 'Persian')->first();
        $siameseBreed = Breed::where('name', 'Siamese')->first();
        $gsdBreed = Breed::where('name', 'German Shepherd')->first();

        // Pets
        $pets = [];
        if ($owners->count() >= 3) {
            $buddy = Pet::updateOrCreate(
                ['owner_id' => $owners[0]->id, 'name' => 'Buddy'],
                ['species_id' => $dogSpecies->id, 'breed_id' => $labBreed->id,
                 'gender' => 'male', 'date_of_birth' => '2022-03-15', 'weight' => 30.5,
                 'color' => 'Golden', 'description' => 'Friendly and energetic Labrador.',
                 'is_neutered' => true, 'microchip_number' => 'MCH-001-2022']
            );
            $pets[] = $buddy;

            $luna = Pet::updateOrCreate(
                ['owner_id' => $owners[0]->id, 'name' => 'Luna'],
                ['species_id' => $catSpecies->id, 'breed_id' => $persianBreed->id,
                 'gender' => 'female', 'date_of_birth' => '2023-01-20', 'weight' => 4.2,
                 'color' => 'White', 'description' => 'Calm and affectionate Persian cat.',
                 'is_neutered' => true]
            );
            $pets[] = $luna;

            $max = Pet::updateOrCreate(
                ['owner_id' => $owners[1]->id, 'name' => 'Max'],
                ['species_id' => $dogSpecies->id, 'breed_id' => $gsdBreed->id,
                 'gender' => 'male', 'date_of_birth' => '2021-07-10', 'weight' => 35.0,
                 'color' => 'Black and Tan', 'description' => 'Loyal German Shepherd.',
                 'is_neutered' => false, 'microchip_number' => 'MCH-002-2021']
            );
            $pets[] = $max;

            $milo = Pet::updateOrCreate(
                ['owner_id' => $owners[2]->id, 'name' => 'Milo'],
                ['species_id' => $catSpecies->id, 'breed_id' => $siameseBreed->id,
                 'gender' => 'male', 'date_of_birth' => '2023-06-05', 'weight' => 3.8,
                 'color' => 'Cream', 'description' => 'Playful Siamese cat.',
                 'is_neutered' => false]
            );
            $pets[] = $milo;

            // Health Records
            if ($vets->count() >= 2) {
                HealthRecord::create([
                    'pet_id' => $buddy->id, 'vet_id' => $vets[0]->id,
                    'record_date' => '2026-01-15', 'record_type' => 'checkup',
                    'description' => 'Annual checkup - all vitals normal.',
                    'notes' => 'Weight slightly above ideal. Increase exercise.',
                ]);
                HealthRecord::create([
                    'pet_id' => $buddy->id, 'vet_id' => $vets[0]->id,
                    'record_date' => '2026-06-20', 'record_type' => 'vaccination',
                    'description' => 'Rabies booster administered.',
                ]);

                Vaccination::create([
                    'pet_id' => $buddy->id, 'vaccine_name' => 'Rabies',
                    'vaccination_date' => '2026-06-20', 'next_due_date' => '2027-06-20',
                    'batch_number' => 'RAB-2026-001', 'notes' => 'No adverse reactions.',
                ]);
                Vaccination::create([
                    'pet_id' => $buddy->id, 'vaccine_name' => 'DHPP',
                    'vaccination_date' => '2026-03-10', 'next_due_date' => '2027-03-10',
                    'batch_number' => 'DHPP-2026-005',
                ]);
                Vaccination::create([
                    'pet_id' => $luna->id, 'vaccine_name' => 'FVRCP',
                    'vaccination_date' => '2026-04-15', 'next_due_date' => '2027-04-15',
                    'batch_number' => 'FVRCP-2026-003',
                ]);
            }
        }

        // Vet Availabilities
        if ($vets->count() >= 1) {
            $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
            foreach ($days as $day) {
                VetAvailability::updateOrCreate(
                    ['vet_id' => $vets[0]->id, 'day_of_week' => $day],
                    ['start_time' => '09:00', 'end_time' => '17:00', 'is_available' => true]
                );
            }
            VetAvailability::updateOrCreate(
                ['vet_id' => $vets[0]->id, 'day_of_week' => 'saturday'],
                ['start_time' => '09:00', 'end_time' => '13:00', 'is_available' => true]
            );
        }

        // Appointments
        if (count($pets) >= 2 && $vets->count() >= 1 && $owners->count() >= 1) {
            $appt = Appointment::create([
                'pet_id' => $pets[0]->id, 'owner_id' => $owners[0]->id, 'vet_id' => $vets[0]->id,
                'appointment_date' => '2026-09-20', 'appointment_time' => '10:00',
                'reason' => 'Annual vaccination booster', 'status' => 'approved',
            ]);

            Treatment::create([
                'appointment_id' => $appt->id, 'pet_id' => $pets[0]->id, 'vet_id' => $vets[0]->id,
                'symptoms' => 'No symptoms - routine visit',
                'diagnosis' => 'Healthy - no issues found',
                'treatment' => 'Rabies booster administered',
                'follow_up_date' => '2027-09-20',
                'notes' => 'Pet is in excellent health.',
            ]);

            Prescription::create([
                'treatment_id' => Treatment::latest()->first()->id,
                'medicine_name' => 'Rabies Vaccine (IMRAB 3)',
                'dosage' => '1ml', 'frequency' => 'Single dose',
                'duration' => 'N/A', 'instructions' => 'Monitor for 24 hours. No strenuous activity.',
            ]);
        }

        // Products
        $categories = \App\Models\Category::where('slug', 'food-nutrition')->first();
        if ($categories) {
            $prod1 = Product::updateOrCreate(
                ['slug' => 'premium-dog-food'],
                ['category_id' => $categories->id, 'name' => 'Premium Dog Food',
                 'slug' => 'premium-dog-food', 'description' => 'High-quality nutrition for adult dogs.',
                 'sku' => 'DOG-FOOD-001', 'price' => 49.99, 'stock_quantity' => 100,
                 'is_featured' => true, 'status' => 'active']
            );
            $prod2 = Product::updateOrCreate(
                ['slug' => 'kitten-formula'],
                ['category_id' => $categories->id, 'name' => 'Kitten Formula',
                 'slug' => 'kitten-formula', 'description' => 'Specially formulated for kittens.',
                 'sku' => 'CAT-FOOD-001', 'price' => 29.99, 'stock_quantity' => 75,
                 'is_featured' => false, 'status' => 'active']
            );
        }

        // Adoption Listings
        if ($shelters->count() >= 1) {
            $listing1 = AdoptionListing::updateOrCreate(
                ['shelter_id' => $shelters[0]->id, 'pet_name' => 'Bella'],
                ['species_id' => $dogSpecies->id, 'breed_id' => $labBreed->id,
                 'age' => '2 years', 'gender' => 'female',
                 'health_status' => 'Vaccinated, spayed, healthy',
                 'description' => 'Sweet and gentle Lab looking for a loving family.',
                 'status' => 'available']
            );
            AdoptionListing::updateOrCreate(
                ['shelter_id' => $shelters[0]->id, 'pet_name' => 'Oliver'],
                ['species_id' => $catSpecies->id, 'breed_id' => $siameseBreed->id,
                 'age' => '1 year', 'gender' => 'male',
                 'health_status' => 'Vaccinated, neutered, healthy',
                 'description' => 'Playful Siamese kitten, great with kids.',
                 'status' => 'available']
            );
        }

        // Notifications
        if ($owners->count() >= 1) {
            FurshieldNotification::create([
                'user_id' => $owners[0]->id,
                'title' => 'Appointment Confirmed',
                'message' => 'Your appointment with Dr. Carter has been approved for Sep 20, 2026.',
                'type' => 'appointment',
                'is_read' => false,
            ]);
            FurshieldNotification::create([
                'user_id' => $owners[0]->id,
                'title' => 'Vaccination Reminder',
                'message' => 'Luna\'s FVRCP vaccination is due on Apr 15, 2027.',
                'type' => 'vaccination',
                'is_read' => false,
            ]);
        }

        // Reviews
        if ($owners->count() >= 1 && $vets->count() >= 1) {
            Review::create([
                'user_id' => $owners[0]->id,
                'reviewable_type' => 'App\\Models\\User',
                'reviewable_id' => $vets[0]->id,
                'rating' => 5,
                'comment' => 'Dr. Carter is amazing with Buddy. Very professional and caring.',
            ]);
        }

        // Product Reviews
        if ($owners->count() >= 2 && isset($prod1)) {
            Review::create([
                'user_id' => $owners[0]->id,
                'reviewable_type' => 'App\\Models\\Product',
                'reviewable_id' => $prod1->id,
                'rating' => 5,
                'comment' => 'My dog loves this food! Great quality and fast shipping.',
            ]);
            Review::create([
                'user_id' => $owners[1]->id,
                'reviewable_type' => 'App\\Models\\Product',
                'reviewable_id' => $prod1->id,
                'rating' => 4,
                'comment' => 'Good product, noticed improvement in coat health after 2 weeks.',
            ]);
        }
        if ($owners->count() >= 1 && isset($prod2)) {
            Review::create([
                'user_id' => $owners[0]->id,
                'reviewable_type' => 'App\\Models\\Product',
                'reviewable_id' => $prod2->id,
                'rating' => 4,
                'comment' => 'Luna switched to this formula and she seems to enjoy it.',
            ]);
        }

        // Shelter Reviews
        if ($owners->count() >= 2 && isset($listing1)) {
            Review::create([
                'user_id' => $owners[0]->id,
                'reviewable_type' => 'App\\Models\\AdoptionListing',
                'reviewable_id' => $listing1->id,
                'rating' => 5,
                'comment' => 'Bella is such a sweetheart! The shelter was very helpful and professional.',
            ]);
            Review::create([
                'user_id' => $owners[1]->id,
                'reviewable_type' => 'App\\Models\\AdoptionListing',
                'reviewable_id' => $listing1->id,
                'rating' => 4,
                'comment' => 'Great shelter, clean facilities and friendly staff. Bella is well cared for.',
            ]);
        }
        if ($owners->count() >= 1 && isset($listing2)) {
            Review::create([
                'user_id' => $owners[0]->id,
                'reviewable_type' => 'App\\Models\\AdoptionListing',
                'reviewable_id' => $listing2->id,
                'rating' => 5,
                'comment' => 'Oliver is adorable! The adoption process was smooth and well-organized.',
            ]);
        }

        // Orders for owner[0]
        if ($owners->count() >= 1 && isset($prod1) && isset($prod2)) {
            $order = Order::create([
                'owner_id' => $owners[0]->id,
                'order_number' => 'ORD-' . now()->timestamp,
                'total_amount' => 129.97,
                'status' => 'completed',
                'shipping_address' => '123 Pet Lane, Animal City, AC 12345',
                'notes' => 'Leave at front door',
                'order_date' => now()->subDays(5),
            ]);
            OrderItem::create([
                'order_id' => $order->id, 'product_id' => $prod1->id,
                'quantity' => 2, 'price_each' => $prod1->price,
            ]);
            OrderItem::create([
                'order_id' => $order->id, 'product_id' => $prod2->id,
                'quantity' => 1, 'price_each' => $prod2->price,
            ]);

            $order2 = Order::create([
                'owner_id' => $owners[0]->id,
                'order_number' => 'ORD-' . (now()->timestamp - 1),
                'total_amount' => 49.99,
                'status' => 'processing',
                'shipping_address' => '123 Pet Lane, Animal City, AC 12345',
                'order_date' => now()->subDays(1),
            ]);
            OrderItem::create([
                'order_id' => $order2->id, 'product_id' => $prod1->id,
                'quantity' => 1, 'price_each' => $prod1->price,
            ]);
        }
    }
}
