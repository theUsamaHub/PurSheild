<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Pet;
use App\Models\PetImage;
use App\Models\Species;
use App\Models\Breed;
use App\Models\VetAvailability;
use App\Models\HealthRecord;
use App\Models\Vaccination;
use App\Models\MedicalDocument;
use App\Models\InsurancePolicy;
use App\Models\Appointment;
use App\Models\Treatment;
use App\Models\Prescription;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\AdoptionListing;
use App\Models\AdoptionImage;
use App\Models\AdoptionApplication;
use App\Models\CareContent;
use App\Models\CareStatusLog;
use App\Models\FurshieldNotification;
use App\Models\Review;
use App\Models\Contact;
use App\Models\Subscriber;
use App\Models\Tag;
use App\Models\ActivityLog;
use App\Models\Media;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Notifications\DatabaseNotification;

class FurShieldDataSeeder extends Seeder
{
    public function run(): void
    {
        $owners   = User::whereHas('roles', fn ($q) => $q->where('slug', 'owner'))->get();
        $vets     = User::whereHas('roles', fn ($q) => $q->where('slug', 'vet'))->get();
        $shelters = User::whereHas('roles', fn ($q) => $q->where('slug', 'shelter'))->get();
        $admin    = User::whereHas('roles', fn ($q) => $q->where('slug', 'admin'))->first();

        // ── Species & Breeds ──────────────────────────────────────────────
        $dog        = Species::where('name', 'Dog')->first();
        $cat        = Species::where('name', 'Cat')->first();
        $bird       = Species::where('name', 'Bird')->first();
        $rabbit     = Species::where('name', 'Rabbit')->first();
        $fish       = Species::where('name', 'Fish')->first();
        $hamster    = Species::where('name', 'Hamster')->first();
        $guineaPig  = Species::where('name', 'Guinea Pig')->first();
        $ferret     = Species::where('name', 'Ferret')->first();
        $horse      = Species::where('name', 'Horse')->first();
        $reptile    = Species::where('name', 'Reptile')->first();

        $lab        = Breed::where('name', 'Labrador Retriever')->first();
        $persian    = Breed::where('name', 'Persian')->first();
        $siamese    = Breed::where('name', 'Siamese')->first();
        $gsd        = Breed::where('name', 'German Shepherd')->first();
        $bulldog    = Breed::where('name', 'Bulldog')->first();
        $poodle     = Breed::where('name', 'Poodle')->first();
        $maineCoon  = Breed::where('name', 'Maine Coon')->first();
        $ragdoll    = Breed::where('name', 'Ragdoll')->first();
        $parrot     = Breed::where('name', 'Parrot')->first();
        $hollandLop = Breed::where('name', 'Holland Lop')->first();

        // ── 1. Pets (15+) ──────────────────────────────────────────────────
        $petsData = [
            ['owner' => $owners[0] ?? null, 'name' => 'Buddy',    'species' => $dog,       'breed' => $lab,        'gender' => 'male',   'dob' => '2022-03-15', 'weight' => 30.5, 'color' => 'Golden',        'desc' => 'Friendly and energetic Labrador.',         'neutered' => true,  'chip' => 'MCH-001-2022'],
            ['owner' => $owners[0] ?? null, 'name' => 'Luna',     'species' => $cat,       'breed' => $persian,    'gender' => 'female', 'dob' => '2023-01-20', 'weight' => 4.2,  'color' => 'White',         'desc' => 'Calm and affectionate Persian cat.',      'neutered' => true,  'chip' => null],
            ['owner' => $owners[0] ?? null, 'name' => 'Charlie',  'species' => $dog,       'breed' => $poodle,     'gender' => 'male',   'dob' => '2021-11-05', 'weight' => 8.0,  'color' => 'Brown',         'desc' => 'Intelligent and playful Poodle.',        'neutered' => true,  'chip' => 'MCH-003-2021'],
            ['owner' => $owners[1] ?? null, 'name' => 'Max',      'species' => $dog,       'breed' => $gsd,        'gender' => 'male',   'dob' => '2021-07-10', 'weight' => 35.0, 'color' => 'Black and Tan', 'desc' => 'Loyal German Shepherd.',                  'neutered' => false, 'chip' => 'MCH-002-2021'],
            ['owner' => $owners[1] ?? null, 'name' => 'Bella',    'species' => $cat,       'breed' => $maineCoon,  'gender' => 'female', 'dob' => '2022-08-14', 'weight' => 5.5,  'color' => 'Brown Tabby',   'desc' => 'Fluffy and gentle Maine Coon.',           'neutered' => true,  'chip' => null],
            ['owner' => $owners[1] ?? null, 'name' => 'Rocky',    'species' => $dog,       'breed' => $bulldog,    'gender' => 'male',   'dob' => '2020-05-22', 'weight' => 25.0, 'color' => 'White & Brindle','desc' => 'Calm and lovable English Bulldog.',      'neutered' => false, 'chip' => 'MCH-004-2020'],
            ['owner' => $owners[2] ?? null, 'name' => 'Milo',     'species' => $cat,       'breed' => $siamese,    'gender' => 'male',   'dob' => '2023-06-05', 'weight' => 3.8,  'color' => 'Cream',         'desc' => 'Playful Siamese cat.',                    'neutered' => false, 'chip' => null],
            ['owner' => $owners[2] ?? null, 'name' => 'Coco',     'species' => $cat,       'breed' => $ragdoll,    'gender' => 'female', 'dob' => '2022-02-18', 'weight' => 6.1,  'color' => 'Blue Bicolor',  'desc' => 'Docile and affectionate Ragdoll.',        'neutered' => true,  'chip' => null],
            ['owner' => $owners[3] ?? null, 'name' => 'Tweety',   'species' => $bird,      'breed' => $parrot,     'gender' => 'male',   'dob' => '2021-04-10', 'weight' => 0.4,  'color' => 'Green',         'desc' => 'Talkative and curious parrot.',           'neutered' => false, 'chip' => null],
            ['owner' => $owners[3] ?? null, 'name' => 'Snowball', 'species' => $rabbit,    'breed' => $hollandLop, 'gender' => 'female', 'dob' => '2023-09-01', 'weight' => 2.1,  'color' => 'White',         'desc' => 'Gentle Holland Lop rabbit.',              'neutered' => true,  'chip' => null],
            ['owner' => $owners[4] ?? null, 'name' => 'Bubbles',  'species' => $fish,      'breed' => null,        'gender' => 'male',   'dob' => '2024-01-01', 'weight' => 0.1,  'color' => 'Orange/Gold',   'desc' => 'Bright goldfish in freshwater tank.',    'neutered' => false, 'chip' => null],
            ['owner' => $owners[5] ?? null, 'name' => 'Nibbles',  'species' => $hamster,   'breed' => null,        'gender' => 'female', 'dob' => '2023-11-12', 'weight' => 0.15, 'color' => 'Golden Brown',  'desc' => 'Active Syrian hamster.',                  'neutered' => false, 'chip' => null],
            ['owner' => $owners[6] ?? null, 'name' => 'Ziggy',    'species' => $guineaPig, 'breed' => null,        'gender' => 'male',   'dob' => '2022-12-01', 'weight' => 0.9,  'color' => 'Tricolor',      'desc' => 'Vocal and friendly guinea pig.',         'neutered' => true,  'chip' => null],
            ['owner' => $owners[7] ?? null, 'name' => 'Bandit',   'species' => $ferret,    'breed' => null,        'gender' => 'male',   'dob' => '2022-05-19', 'weight' => 1.4,  'color' => 'Sable',         'desc' => 'Curious ferret who loves tunnels.',       'neutered' => true,  'chip' => 'MCH-005-2022'],
            ['owner' => $owners[8] ?? null, 'name' => 'Draco',    'species' => $reptile,   'breed' => null,        'gender' => 'male',   'dob' => '2021-08-30', 'weight' => 0.45, 'color' => 'Sandy Yellow',  'desc' => 'Docile bearded dragon.',                  'neutered' => false, 'chip' => null],
        ];

        $pets = [];
        foreach ($petsData as $pd) {
            if (!$pd['owner']) continue;
            $pet = Pet::updateOrCreate(
                ['owner_id' => $pd['owner']->id, 'name' => $pd['name']],
                [
                    'species_id'       => $pd['species']?->id,
                    'breed_id'         => $pd['breed']?->id,
                    'gender'           => $pd['gender'],
                    'date_of_birth'    => $pd['dob'],
                    'weight'           => $pd['weight'],
                    'color'            => $pd['color'],
                    'description'      => $pd['desc'],
                    'is_neutered'      => $pd['neutered'],
                    'microchip_number' => $pd['chip'],
                ]
            );
            $pets[] = $pet;
        }

        // ── 2. Pet Images (15+) ───────────────────────────────────────────
        foreach ($pets as $i => $pet) {
            PetImage::updateOrCreate(
                ['pet_id' => $pet->id, 'image_path' => "pets/pet_{$pet->id}_main.jpg"],
                ['is_primary' => true, 'sort_order' => 1]
            );
        }

        // ── 3. Health Records (15+) ───────────────────────────────────────
        if (count($pets) >= 10 && $vets->count() >= 1) {
            $hrData = [
                ['pet' => $pets[0], 'vet' => $vets[0], 'date' => '2026-01-15', 'type' => 'checkup',     'desc' => 'Annual checkup – all vitals normal.',         'notes' => 'Weight slightly above ideal.'],
                ['pet' => $pets[0], 'vet' => $vets[0], 'date' => '2026-06-20', 'type' => 'vaccination', 'desc' => 'Rabies booster administered.',                 'notes' => 'No adverse reactions.'],
                ['pet' => $pets[0], 'vet' => $vets[0], 'date' => '2026-07-10', 'type' => 'checkup',     'desc' => 'Annual health checkup completed.',              'notes' => 'Diet plan given.'],
                ['pet' => $pets[1], 'vet' => $vets[0], 'date' => '2026-04-10', 'type' => 'checkup',     'desc' => 'Routine wellness exam.',                       'notes' => 'Healthy. Weight ideal.'],
                ['pet' => $pets[1], 'vet' => $vets[0], 'date' => '2026-07-15', 'type' => 'treatment',   'desc' => 'Treated for allergic dermatitis.',              'notes' => 'Antihistamines prescribed.'],
                ['pet' => $pets[2], 'vet' => $vets[0], 'date' => '2026-08-01', 'type' => 'vaccination', 'desc' => 'DHPP + Rabies boosters administered.',          'notes' => 'No side effects observed.'],
                ['pet' => $pets[3], 'vet' => $vets[1] ?? $vets[0], 'date' => '2026-05-20', 'type' => 'checkup', 'desc' => 'Pre-surgery health evaluation.',        'notes' => 'All bloodwork normal.'],
                ['pet' => $pets[3], 'vet' => $vets[1] ?? $vets[0], 'date' => '2026-08-05', 'type' => 'treatment', 'desc' => 'Soft tissue injury – left hind limb.', 'notes' => 'Rest and anti-inflammatories.'],
                ['pet' => $pets[4], 'vet' => $vets[2] ?? $vets[0], 'date' => '2026-08-20', 'type' => 'surgery',   'desc' => 'Dental cleaning under general anaesthesia.','notes' => 'Grade II periodontal disease.'],
                ['pet' => $pets[5], 'vet' => $vets[0], 'date' => '2026-06-15', 'type' => 'checkup',     'desc' => 'Annual wellness exam.',                         'notes' => 'Healthy adult dog.'],
                ['pet' => $pets[6], 'vet' => $vets[0], 'date' => '2026-09-02', 'type' => 'treatment',   'desc' => 'Acute gastroenteritis – IV fluids given.',      'notes' => 'Good recovery.'],
                ['pet' => $pets[7], 'vet' => $vets[3] ?? $vets[0], 'date' => '2026-03-10', 'type' => 'checkup', 'desc' => 'Routine health check for new patient.',    'notes' => 'Young healthy cat.'],
                ['pet' => $pets[8], 'vet' => $vets[0], 'date' => '2026-04-12', 'type' => 'checkup',     'desc' => 'Avian beak and feather check.',                'notes' => 'Excellent plumage health.'],
                ['pet' => $pets[9], 'vet' => $vets[0], 'date' => '2026-05-01', 'type' => 'checkup',     'desc' => 'Rabbit dental inspection.',                    'notes' => 'Incisors trimmed.'],
                ['pet' => $pets[13] ?? $pets[0], 'vet' => $vets[0], 'date' => '2026-06-01', 'type' => 'vaccination', 'desc' => 'Ferret distemper vaccination.', 'notes' => 'Single dose administered.'],
            ];

            foreach ($hrData as $hr) {
                HealthRecord::create([
                    'pet_id'      => $hr['pet']->id,
                    'vet_id'      => $hr['vet']->id,
                    'record_date' => $hr['date'],
                    'record_type' => $hr['type'],
                    'description' => $hr['desc'],
                    'notes'       => $hr['notes'] ?? null,
                ]);
            }
        }

        // ── 4. Vaccinations (15+) ─────────────────────────────────────────
        if (count($pets) >= 8) {
            $vaxData = [
                ['pet' => $pets[0], 'name' => 'Rabies',              'date' => '2026-06-20', 'next' => '2027-06-20', 'batch' => 'RAB-2026-001',  'notes' => 'No adverse reactions.'],
                ['pet' => $pets[0], 'name' => 'DHPP',                'date' => '2026-08-01', 'next' => '2027-08-01', 'batch' => 'DHPP-2026-005', 'notes' => null],
                ['pet' => $pets[0], 'name' => 'Bordetella',          'date' => '2026-03-10', 'next' => '2027-03-10', 'batch' => 'BOR-2026-002',  'notes' => null],
                ['pet' => $pets[1], 'name' => 'FVRCP',               'date' => '2026-04-15', 'next' => '2027-04-15', 'batch' => 'FVRCP-2026-003','notes' => null],
                ['pet' => $pets[1], 'name' => 'Rabies',              'date' => '2026-04-15', 'next' => '2027-04-15', 'batch' => 'RAB-2026-003',  'notes' => 'Given same day as FVRCP.'],
                ['pet' => $pets[2], 'name' => 'DHPP',                'date' => '2026-08-01', 'next' => '2027-08-01', 'batch' => 'DHPP-2026-010', 'notes' => null],
                ['pet' => $pets[2], 'name' => 'Rabies',              'date' => '2026-08-01', 'next' => '2027-08-01', 'batch' => 'RAB-2026-010',  'notes' => null],
                ['pet' => $pets[3], 'name' => 'DHPP',                'date' => '2026-05-15', 'next' => '2027-05-15', 'batch' => 'DHPP-2026-007', 'notes' => null],
                ['pet' => $pets[3], 'name' => 'Rabies',              'date' => '2026-05-15', 'next' => '2027-05-15', 'batch' => 'RAB-2026-007',  'notes' => null],
                ['pet' => $pets[4], 'name' => 'FVRCP',               'date' => '2026-06-10', 'next' => '2027-06-10', 'batch' => 'FVRCP-2026-008','notes' => null],
                ['pet' => $pets[4], 'name' => 'FeLV',                'date' => '2026-06-10', 'next' => '2027-06-10', 'batch' => 'FELV-2026-002', 'notes' => 'First FeLV vaccination.'],
                ['pet' => $pets[5], 'name' => 'Rabies',              'date' => '2026-07-12', 'next' => '2027-07-12', 'batch' => 'RAB-2026-015',  'notes' => null],
                ['pet' => $pets[6], 'name' => 'FVRCP',               'date' => '2026-07-01', 'next' => '2027-07-01', 'batch' => 'FVRCP-2026-012','notes' => null],
                ['pet' => $pets[7], 'name' => 'FVRCP',               'date' => '2026-02-18', 'next' => '2027-02-18', 'batch' => 'FVRCP-2026-001','notes' => null],
                ['pet' => $pets[13] ?? $pets[0], 'name' => 'Canine Distemper (Ferret)', 'date' => '2026-06-01', 'next' => '2027-06-01', 'batch' => 'FER-2026-001', 'notes' => null],
            ];

            foreach ($vaxData as $vax) {
                Vaccination::create([
                    'pet_id'           => $vax['pet']->id,
                    'vaccine_name'     => $vax['name'],
                    'vaccination_date' => $vax['date'],
                    'next_due_date'    => $vax['next'],
                    'batch_number'     => $vax['batch'],
                    'notes'            => $vax['notes'],
                ]);
            }
        }

        // ── 5. Medical Documents (12+) ───────────────────────────────────
        if (count($pets) >= 6) {
            for ($i = 0; $i < 12; $i++) {
                $p = $pets[$i % count($pets)];
                MedicalDocument::create([
                    'pet_id'        => $p->id,
                    'document_type' => ['X-Ray', 'Blood Report', 'Vaccination Certificate', 'Prescription Sheet'][$i % 4],
                    'file_name'     => "medical_doc_{$p->id}_" . ($i + 1) . ".pdf",
                    'file_path'     => "documents/medical_doc_{$p->id}_" . ($i + 1) . ".pdf",
                    'mime_type'     => 'application/pdf',
                    'description'   => "Official medical report for {$p->name}.",
                ]);
            }
        }

        // ── 6. Insurance Policies (12+) ──────────────────────────────────
        if (count($pets) >= 6) {
            $providers = ['PetProtect Insurance', 'PawSecure Global', 'HealthyPaws Co', 'FurShield Assurance'];
            for ($i = 0; $i < 12; $i++) {
                $p = $pets[$i % count($pets)];
                InsurancePolicy::create([
                    'pet_id'          => $p->id,
                    'provider_name'   => $providers[$i % 4],
                    'policy_number'   => 'POL-' . rand(100000, 999999),
                    'start_date'      => '2026-01-01',
                    'expiry_date'     => '2027-01-01',
                    'policy_document' => "policies/pol_{$p->id}.pdf",
                    'claim_document'  => null,
                    'notes'           => "Full comprehensive coverage for {$p->name}.",
                ]);
            }
        }

        // ── 7. Vet Availabilities (30+) ──────────────────────────────────
        foreach ($vets as $vIndex => $vet) {
            $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
            foreach ($days as $day) {
                VetAvailability::updateOrCreate(
                    ['vet_id' => $vet->id, 'day_of_week' => $day],
                    [
                        'start_time'   => '09:00:00',
                        'end_time'     => ($day === 'saturday' ? '13:00:00' : '17:00:00'),
                        'is_available' => true,
                    ]
                );
            }
        }

        // ── 8. Appointments (18+) ─────────────────────────────────────────
        $appointments = [];
        if (count($pets) >= 6 && $vets->count() >= 2 && $owners->count() >= 3) {
            $apptData = [
                // Completed (for treatments)
                ['pet' => $pets[0], 'owner' => $pets[0]->owner, 'vet' => $vets[0], 'date' => '2026-07-10', 'time' => '09:00', 'reason' => 'Annual health checkup',         'status' => 'completed'],
                ['pet' => $pets[1], 'owner' => $pets[1]->owner, 'vet' => $vets[0], 'date' => '2026-07-15', 'time' => '10:00', 'reason' => 'Skin rash examination',          'status' => 'completed'],
                ['pet' => $pets[2], 'owner' => $pets[2]->owner, 'vet' => $vets[0], 'date' => '2026-08-01', 'time' => '11:00', 'reason' => 'Routine vaccination booster',    'status' => 'completed'],
                ['pet' => $pets[3], 'owner' => $pets[3]->owner, 'vet' => $vets[0], 'date' => '2026-08-05', 'time' => '09:30', 'reason' => 'Limping on left hind leg',       'status' => 'completed'],
                ['pet' => $pets[4], 'owner' => $pets[4]->owner, 'vet' => $vets[0], 'date' => '2026-08-20', 'time' => '14:00', 'reason' => 'Dental cleaning',                'status' => 'completed'],
                ['pet' => $pets[6], 'owner' => $pets[6]->owner, 'vet' => $vets[0], 'date' => '2026-09-02', 'time' => '10:30', 'reason' => 'Stomach upset and vomiting',     'status' => 'completed'],
                ['pet' => $pets[5], 'owner' => $pets[5]->owner, 'vet' => $vets[1], 'date' => '2026-09-03', 'time' => '11:30', 'reason' => 'Ear infection evaluation',       'status' => 'completed'],
                ['pet' => $pets[7], 'owner' => $pets[7]->owner, 'vet' => $vets[1], 'date' => '2026-09-04', 'time' => '15:00', 'reason' => 'Heart murmur consultation',       'status' => 'completed'],
                ['pet' => $pets[8], 'owner' => $pets[8]->owner, 'vet' => $vets[2] ?? $vets[0], 'date' => '2026-09-05', 'time' => '14:00', 'reason' => 'Feather clipping & checkup', 'status' => 'completed'],
                ['pet' => $pets[9], 'owner' => $pets[9]->owner, 'vet' => $vets[0], 'date' => '2026-09-06', 'time' => '09:30', 'reason' => 'Rabbit dental trim',             'status' => 'completed'],

                // Approved (upcoming)
                ['pet' => $pets[0], 'owner' => $pets[0]->owner, 'vet' => $vets[0], 'date' => '2026-09-20', 'time' => '09:00', 'reason' => 'Annual vaccination booster',     'status' => 'approved'],
                ['pet' => $pets[5], 'owner' => $pets[5]->owner, 'vet' => $vets[0], 'date' => '2026-09-22', 'time' => '11:00', 'reason' => 'Post-surgery follow-up',         'status' => 'approved'],
                ['pet' => $pets[1], 'owner' => $pets[1]->owner, 'vet' => $vets[1], 'date' => '2026-09-23', 'time' => '10:00', 'reason' => 'Cardiology screening',           'status' => 'approved'],

                // Pending
                ['pet' => $pets[7], 'owner' => $pets[7]->owner, 'vet' => $vets[0], 'date' => '2026-09-25', 'time' => '10:00', 'reason' => 'Eye discharge and redness',      'status' => 'pending'],
                ['pet' => $pets[8], 'owner' => $pets[8]->owner, 'vet' => $vets[0], 'date' => '2026-09-26', 'time' => '14:00', 'reason' => 'General health check',           'status' => 'pending'],
                ['pet' => $pets[2], 'owner' => $pets[2]->owner, 'vet' => $vets[2] ?? $vets[0], 'date' => '2026-09-27', 'time' => '11:00', 'reason' => 'Dental checkup request',    'status' => 'pending'],

                // Cancelled / Rescheduled
                ['pet' => $pets[1], 'owner' => $pets[1]->owner, 'vet' => $vets[0], 'date' => '2026-09-05', 'time' => '09:00', 'reason' => 'Weight management consultation','status' => 'cancelled'],
                ['pet' => $pets[3], 'owner' => $pets[3]->owner, 'vet' => $vets[0], 'date' => '2026-09-28', 'time' => '15:00', 'reason' => 'Follow-up for leg injury',       'status' => 'rescheduled'],
            ];

            foreach ($apptData as $ad) {
                $appt = Appointment::create([
                    'pet_id'           => $ad['pet']->id,
                    'owner_id'         => $ad['owner']->id,
                    'vet_id'           => $ad['vet']->id,
                    'appointment_date' => $ad['date'],
                    'appointment_time' => $ad['time'],
                    'reason'           => $ad['reason'],
                    'status'           => $ad['status'],
                ]);
                $appointments[$ad['status']][] = $appt;
                $appointments['all'][] = $appt;
            }
        }

        // ── 9. Treatments & Prescriptions (12+) ───────────────────────────
        $treatmentData = [
            [
                'symptoms'  => 'Slightly overweight, otherwise all vitals normal.',
                'diagnosis' => 'Mild obesity – weight management required.',
                'treatment' => 'Dietary plan adjusted. Prescribed low-calorie diet and daily walks.',
                'follow_up' => '2026-10-15', 'notes' => 'Owner counselled on portion control.',
                'rx' => [
                    ['medicine_name' => 'Hill\'s Metabolic Dog Food', 'dosage' => '1 cup', 'frequency' => 'Twice daily', 'duration' => 'Ongoing', 'instructions' => 'Replace current food gradually over 7 days.'],
                ],
            ],
            [
                'symptoms'  => 'Red, flaky skin on belly and inner thighs. Excessive scratching.',
                'diagnosis' => 'Allergic dermatitis – likely food allergy.',
                'treatment' => 'Antihistamine injection administered. Hypoallergenic diet recommended.',
                'follow_up' => '2026-08-15', 'notes' => 'Avoid chicken-based food for 8 weeks.',
                'rx' => [
                    ['medicine_name' => 'Diphenhydramine', 'dosage' => '25mg', 'frequency' => 'Once daily', 'duration' => '10 days', 'instructions' => 'Give with food.'],
                    ['medicine_name' => 'Apoquel', 'dosage' => '5.4mg', 'frequency' => 'Twice daily', 'duration' => '14 days', 'instructions' => 'Can be given with or without food.'],
                ],
            ],
            [
                'symptoms'  => 'Due for annual DHPP and Rabies boosters. No symptoms.',
                'diagnosis' => 'Healthy – routine vaccination visit.',
                'treatment' => 'DHPP and Rabies vaccinations administered.',
                'follow_up' => '2027-08-01', 'notes' => 'Next vaccination due Aug 2027.',
                'rx' => [
                    ['medicine_name' => 'DHPP Vaccine', 'dosage' => '1ml IM', 'frequency' => 'Single dose', 'duration' => 'N/A', 'instructions' => 'Observe post-injection.'],
                ],
            ],
            [
                'symptoms'  => 'Limping on left hind leg since 2 days.',
                'diagnosis' => 'Mild soft tissue injury – left hind limb strain.',
                'treatment' => 'Rest prescribed. Anti-inflammatory medication.',
                'follow_up' => '2026-08-20', 'notes' => 'X-ray showed no fractures.',
                'rx' => [
                    ['medicine_name' => 'Meloxicam', 'dosage' => '0.1mg/kg', 'frequency' => 'Once daily', 'duration' => '7 days', 'instructions' => 'Give with food.'],
                    ['medicine_name' => 'Tramadol', 'dosage' => '2mg/kg', 'frequency' => 'Twice daily', 'duration' => '5 days', 'instructions' => 'For pain management.'],
                ],
            ],
            [
                'symptoms'  => 'Tartar buildup, bad breath, mild gum inflammation.',
                'diagnosis' => 'Grade II periodontal disease.',
                'treatment' => 'Professional dental scaling and polishing.',
                'follow_up' => '2026-09-20', 'notes' => 'Two teeth extracted.',
                'rx' => [
                    ['medicine_name' => 'Amoxicillin-Clavulanate', 'dosage' => '12.5mg/kg', 'frequency' => 'Twice daily', 'duration' => '7 days', 'instructions' => 'Complete full course.'],
                ],
            ],
            [
                'symptoms'  => 'Vomiting 3 times, lethargy, reduced appetite.',
                'diagnosis' => 'Acute gastroenteritis.',
                'treatment' => 'IV fluid therapy administered. Anti-emetic injection given.',
                'follow_up' => '2026-09-09', 'notes' => 'Bland diet for 3 days.',
                'rx' => [
                    ['medicine_name' => 'Maropitant (Cerenia)', 'dosage' => '1mg/kg', 'frequency' => 'Once daily', 'duration' => '5 days', 'instructions' => 'Give before meals.'],
                    ['medicine_name' => 'Metronidazole', 'dosage' => '15mg/kg', 'frequency' => 'Twice daily', 'duration' => '5 days', 'instructions' => 'Give with food.'],
                ],
            ],
            [
                'symptoms'  => 'Head shaking, brown discharge in left ear.',
                'diagnosis' => 'Otitis externa (Ear infection).',
                'treatment' => 'Ear flushed and topical antibiotic drop applied.',
                'follow_up' => '2026-09-17', 'notes' => 'Keep ears clean and dry.',
                'rx' => [
                    ['medicine_name' => 'Posatex Otic Drops', 'dosage' => '4 drops', 'frequency' => 'Once daily', 'duration' => '7 days', 'instructions' => 'Instill into left ear.'],
                ],
            ],
            [
                'symptoms'  => 'Mild lethargy and low grade heart murmur detected.',
                'diagnosis' => 'Grade I mitral valve insufficiency.',
                'treatment' => 'Echocardiogram conducted. Benazepril prescribed.',
                'follow_up' => '2026-12-04', 'notes' => 'Monitor sleeping respiratory rate.',
                'rx' => [
                    ['medicine_name' => 'Benazepril', 'dosage' => '0.5mg/kg', 'frequency' => 'Once daily', 'duration' => '30 days', 'instructions' => 'Give every morning.'],
                ],
            ],
            [
                'symptoms'  => 'Overgrown beak hindering feeding.',
                'diagnosis' => 'Avian beak elongation.',
                'treatment' => 'Beak shaped and trimmed with rotary tool.',
                'follow_up' => '2026-11-05', 'notes' => 'Dietary cuttlebone provided.',
                'rx' => [
                    ['medicine_name' => 'Avian Vitamin Drops', 'dosage' => '2 drops in water', 'frequency' => 'Daily', 'duration' => '14 days', 'instructions' => 'Add to fresh drinking water.'],
                ],
            ],
            [
                'symptoms'  => 'Incisor teeth misaligned.',
                'diagnosis' => 'Dental malocclusion.',
                'treatment' => 'Rabbit incisors safely trimmed.',
                'follow_up' => '2026-10-06', 'notes' => 'Increase Timothy hay intake.',
                'rx' => [
                    ['medicine_name' => 'Meloxicam Oral (Rabbit)', 'dosage' => '0.5mg/kg', 'frequency' => 'Once daily', 'duration' => '3 days', 'instructions' => 'Syringe feed.'],
                ],
            ],
        ];

        $completedAppts = $appointments['completed'] ?? [];
        foreach ($completedAppts as $i => $appt) {
            if (!isset($treatmentData[$i])) break;
            $td = $treatmentData[$i];
            $treatment = Treatment::create([
                'appointment_id' => $appt->id,
                'pet_id'         => $appt->pet_id,
                'vet_id'         => $appt->vet_id,
                'symptoms'       => $td['symptoms'],
                'diagnosis'      => $td['diagnosis'],
                'treatment'      => $td['treatment'],
                'follow_up_date' => $td['follow_up'],
                'notes'          => $td['notes'],
            ]);
            foreach ($td['rx'] as $rx) {
                $treatment->prescriptions()->create($rx);
            }
        }

        // ── 10. Products & Product Images (15+) ───────────────────────────
        $foodCat   = Category::where('slug', 'food-nutrition')->first() ?? Category::first();
        $toysCat   = Category::where('slug', 'toys-accessories')->first() ?? $foodCat;
        $healthCat = Category::where('slug', 'health-wellness')->first() ?? $foodCat;

        $productsData = [
            ['slug' => 'premium-dog-food',       'cat' => $foodCat,   'name' => 'Premium Adult Dog Food',    'sku' => 'DOG-FOOD-001',  'price' => 49.99,  'stock' => 100, 'featured' => true],
            ['slug' => 'kitten-formula',         'cat' => $foodCat,   'name' => 'Kitten Growth Formula',     'sku' => 'CAT-FOOD-001',  'price' => 29.99,  'stock' => 75,  'featured' => false],
            ['slug' => 'senior-dog-food',        'cat' => $foodCat,   'name' => 'Senior Dog Dry Food',       'sku' => 'DOG-FOOD-002',  'price' => 54.99,  'stock' => 60,  'featured' => false],
            ['slug' => 'grain-free-cat-food',    'cat' => $foodCat,   'name' => 'Grain-Free Salmon Cat Kibble','sku' => 'CAT-FOOD-002','price' => 39.99,  'stock' => 50,  'featured' => true],
            ['slug' => 'pet-vitamin-supplement', 'cat' => $healthCat, 'name' => 'Multivitamin Soft Chews',   'sku' => 'SUPP-001',      'price' => 24.99,  'stock' => 200, 'featured' => true],
            ['slug' => 'flea-tick-treatment',    'cat' => $healthCat, 'name' => 'Flea & Tick Defense Solution','sku' => 'HEALTH-001','price' => 34.99,  'stock' => 80,  'featured' => false],
            ['slug' => 'interactive-dog-toy',    'cat' => $toysCat,   'name' => 'Interactive Treat Puzzle',  'sku' => 'TOY-001',       'price' => 19.99,  'stock' => 45,  'featured' => false],
            ['slug' => 'cat-scratching-post',    'cat' => $toysCat,   'name' => 'Deluxe Sisal Scratch Post', 'sku' => 'TOY-002',       'price' => 44.99,  'stock' => 30,  'featured' => false],
            ['slug' => 'orthopedic-dog-bed',     'cat' => $toysCat,   'name' => 'Orthopedic Memory Foam Bed','sku' => 'BED-001',       'price' => 79.99,  'stock' => 25,  'featured' => true],
            ['slug' => 'automatic-pet-feeder',   'cat' => $foodCat,   'name' => 'Smart Automatic Pet Feeder','sku' => 'FEED-001',      'price' => 89.99,  'stock' => 20,  'featured' => true],
            ['slug' => 'cat-water-fountain',     'cat' => $toysCat,   'name' => 'Stainless Steel Cat Fountain','sku' => 'ACC-001',     'price' => 32.99,  'stock' => 40,  'featured' => false],
            ['slug' => 'dental-chews-dogs',      'cat' => $healthCat, 'name' => 'Daily Dental Chews for Dogs','sku' => 'DENT-001',     'price' => 17.99,  'stock' => 150, 'featured' => false],
            ['slug' => 'rabbit-hay-feeder',      'cat' => $foodCat,   'name' => 'Timothy Hay & Pellet Mix',  'sku' => 'RAB-001',       'price' => 14.99,  'stock' => 90,  'featured' => false],
            ['slug' => 'bird-cage-accessories',  'cat' => $toysCat,   'name' => 'Parrot Play Perch Set',     'sku' => 'BIRD-001',      'price' => 22.99,  'stock' => 35,  'featured' => false],
            ['slug' => 'aquarium-water-conditioner','cat'=>$healthCat, 'name' => 'Aquarium Water Conditioner','sku' => 'AQUA-001',     'price' => 12.99,  'stock' => 110, 'featured' => false],
        ];

        $products = [];
        foreach ($productsData as $pd) {
            $product = Product::updateOrCreate(
                ['slug' => $pd['slug']],
                [
                    'category_id'    => $pd['cat']->id,
                    'name'           => $pd['name'],
                    'slug'           => $pd['slug'],
                    'description'    => "High quality {$pd['name']} designed for optimal pet health and enjoyment.",
                    'sku'            => $pd['sku'],
                    'price'          => $pd['price'],
                    'stock_quantity' => $pd['stock'],
                    'is_featured'    => $pd['featured'],
                    'status'         => 'active',
                ]
            );
            $products[] = $product;

            ProductImage::updateOrCreate(
                ['product_id' => $product->id, 'image_path' => "products/prod_{$product->id}.jpg"],
                ['is_primary' => true]
            );
        }

        // ── 11. Carts & Cart Items (10+) ─────────────────────────────────
        foreach ($owners as $idx => $owner) {
            $cart = Cart::updateOrCreate(['owner_id' => $owner->id]);
            if (isset($products[$idx])) {
                CartItem::updateOrCreate(
                    ['cart_id' => $cart->id, 'product_id' => $products[$idx]->id],
                    ['quantity' => 2, 'price' => $products[$idx]->price]
                );
            }
        }

        // ── 12. Orders & Order Items (12+) ────────────────────────────────
        if ($owners->count() >= 3 && count($products) >= 4) {
            for ($i = 1; $i <= 12; $i++) {
                $owner = $owners[$i % count($owners)];
                $order = Order::create([
                    'owner_id'         => $owner->id,
                    'order_number'     => 'ORD-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                    'total_amount'     => 49.99 + ($i * 10),
                    'status'           => ['placed', 'processing', 'completed', 'cancelled'][$i % 4],
                    'shipping_address' => $owner->address ?? '123 Main St',
                    'order_date'       => now()->subDays($i),
                ]);

                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $products[$i % count($products)]->id,
                    'quantity'   => 1,
                    'price_each' => $products[$i % count($products)]->price,
                ]);
                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $products[($i + 1) % count($products)]->id,
                    'quantity'   => 2,
                    'price_each' => $products[($i + 1) % count($products)]->price,
                ]);
            }
        }

        // ── 13. Adoption Listings & Images (12+) ─────────────────────────
        $listings = [];
        if ($shelters->count() >= 2) {
            $listingsData = [
                ['shelter' => $shelters[0], 'name' => 'Bella',    'species' => $dog,    'breed' => $lab,      'age' => '2 years',  'gender' => 'female', 'health' => 'Vaccinated, spayed, healthy',       'desc' => 'Sweet and gentle Lab looking for a loving family.'],
                ['shelter' => $shelters[0], 'name' => 'Oliver',   'species' => $cat,    'breed' => $siamese,  'age' => '1 year',   'gender' => 'male',   'health' => 'Vaccinated, neutered, healthy',      'desc' => 'Playful Siamese kitten, great with kids.'],
                ['shelter' => $shelters[0], 'name' => 'Duke',     'species' => $dog,    'breed' => $bulldog,  'age' => '3 years',  'gender' => 'male',   'health' => 'Vaccinated, neutered, heart-healthy','desc' => 'Calm English Bulldog who loves short walks.'],
                ['shelter' => $shelters[0], 'name' => 'Misty',    'species' => $cat,    'breed' => $persian,  'age' => '4 years',  'gender' => 'female', 'health' => 'Spayed, vaccinated, dental cleaned', 'desc' => 'Quiet Persian who loves sunbathing.'],
                ['shelter' => $shelters[1], 'name' => 'Peanut',   'species' => $rabbit, 'breed' => $hollandLop,'age' => '6 months','gender' => 'male',   'health' => 'Neutered, healthy',                  'desc' => 'Adorable Holland Lop rabbit, litter-trained.'],
                ['shelter' => $shelters[1], 'name' => 'Shadow',   'species' => $dog,    'breed' => $gsd,      'age' => '5 years',  'gender' => 'male',   'health' => 'Vaccinated, neutered',               'desc' => 'Loyal GSD, good with experienced owners.'],
                ['shelter' => $shelters[1], 'name' => 'Whiskers', 'species' => $cat,    'breed' => $maineCoon,'age' => '2 years',  'gender' => 'female', 'health' => 'Spayed, vaccinated',                 'desc' => 'Fluffy Maine Coon, great indoor companion.'],
                ['shelter' => $shelters[2] ?? $shelters[0], 'name' => 'Cooper',   'species' => $dog, 'breed' => $poodle, 'age' => '1.5 years', 'gender' => 'male', 'health' => 'Vaccinated, neutered', 'desc' => 'Energetic mini poodle.'],
                ['shelter' => $shelters[3] ?? $shelters[0], 'name' => 'Cleo',     'species' => $cat, 'breed' => $ragdoll, 'age' => '3 years', 'gender' => 'female', 'health' => 'Spayed, healthy',    'desc' => 'Loving Ragdoll cat.'],
                ['shelter' => $shelters[4] ?? $shelters[0], 'name' => 'Barnaby',  'species' => $bird, 'breed' => $parrot, 'age' => '2 years', 'gender' => 'male', 'health' => 'Health checked',        'desc' => 'Talking Amazon parrot.'],
                ['shelter' => $shelters[5] ?? $shelters[0], 'name' => 'Goldie',   'species' => $fish, 'breed' => null, 'age' => '1 year', 'gender' => 'female', 'health' => 'Healthy',               'desc' => 'Pond-raised fancy goldfish.'],
                ['shelter' => $shelters[6] ?? $shelters[0], 'name' => 'Squeaky',  'species' => $guineaPig, 'breed' => null, 'age' => '8 months', 'gender' => 'female', 'health' => 'Healthy',         'desc' => 'Cute tricolor guinea pig.'],
            ];

            foreach ($listingsData as $ld) {
                $listing = AdoptionListing::updateOrCreate(
                    ['shelter_id' => $ld['shelter']->id, 'pet_name' => $ld['name']],
                    [
                        'species_id'    => $ld['species']?->id,
                        'breed_id'      => $ld['breed']?->id,
                        'age'           => $ld['age'],
                        'gender'        => $ld['gender'],
                        'health_status' => $ld['health'],
                        'description'   => $ld['desc'],
                        'status'        => 'available',
                    ]
                );
                $listings[] = $listing;

                AdoptionImage::updateOrCreate(
                    ['listing_id' => $listing->id, 'image_path' => "adoptions/listing_{$listing->id}.jpg"],
                    ['caption' => "Photo of {$listing->pet_name}"]
                );
            }
        }

        // ── 14. Adoption Applications (12+) ──────────────────────────────
        if (count($listings) >= 4 && count($owners) >= 4) {
            for ($i = 0; $i < 12; $i++) {
                $listing = $listings[$i % count($listings)];
                $applicant = $owners[$i % count($owners)];
                AdoptionApplication::create([
                    'listing_id'       => $listing->id,
                    'applicant_id'     => $applicant->id,
                    'message'          => "I would love to adopt {$listing->pet_name}! I have a spacious home and pet experience.",
                    'status'           => ['pending', 'approved', 'rejected', 'completed'][$i % 4],
                    'shelter_response' => "Thank you for applying to adopt {$listing->pet_name}.",
                ]);
            }
        }

        // ── 15. Care Contents (12+) ───────────────────────────────────────
        $categoriesList = ['feeding','hygiene','exercise','health','training'];
        $typesList      = ['article','video','faq'];
        for ($i = 1; $i <= 12; $i++) {
            CareContent::create([
                'title'        => "Essential Pet Care Guide #{$i}",
                'category'     => $categoriesList[$i % 5],
                'content_type' => $typesList[$i % 3],
                'content'      => "Detailed care instructions and expert advice for keeping your pets healthy and happy.",
                'media_url'    => "https://example.com/care/media_{$i}",
                'thumbnail'    => "care/thumb_{$i}.jpg",
                'status'       => 'active',
            ]);
        }

        // ── 16. Care Status Logs (12+) ─────────────────────────────────────
        if (count($shelters) >= 1) {
            $types = ['feeding', 'grooming', 'medical', 'other'];
            for ($i = 1; $i <= 12; $i++) {
                $shelter = $shelters[$i % count($shelters)];
                $listing = $listings[$i % count($listings)] ?? null;
                CareStatusLog::create([
                    'shelter_id'  => $shelter->id,
                    'listing_id'  => $listing?->id,
                    'animal_name' => $listing?->pet_name ?? "Shelter Animal #{$i}",
                    'type'        => $types[$i % 4],
                    'notes'       => "Daily care log #{$i} completed successfully.",
                    'log_date'    => now()->subDays($i)->toDateString(),
                ]);
            }
        }

        // ── 17. Furshield Notifications (15+) ──────────────────────────────
        if (count($owners) >= 3) {
            for ($i = 1; $i <= 15; $i++) {
                $user = $owners[$i % count($owners)];
                FurshieldNotification::create([
                    'user_id' => $user->id,
                    'title'   => "Notification Update #{$i}",
                    'message' => "This is a notification update regarding your account activities or appointments.",
                    'type'    => ['appointment', 'vaccination', 'treatment', 'order', 'health'][$i % 5],
                    'is_read' => ($i % 2 === 0),
                ]);
            }
        }

        // ── 18. Standard Database Notifications (15+) ─────────────────────
        foreach ($vets as $vIndex => $vet) {
            for ($k = 1; $k <= 3; $k++) {
                DatabaseNotification::create([
                    'id'              => (string) Str::uuid(),
                    'type'            => 'App\\Notifications\\SystemNotification',
                    'notifiable_type' => 'App\\Models\\User',
                    'notifiable_id'   => $vet->id,
                    'data'            => [
                        'title'   => "Appointment Update #{$k}",
                        'message' => "New appointment notification for Dr. {$vet->name}.",
                        'type'    => 'request',
                    ],
                    'read_at'         => null,
                ]);
            }
        }

        // ── 19. Reviews (20+) ─────────────────────────────────────────────
        if (count($owners) >= 3 && count($vets) >= 2) {
            // Vet reviews
            foreach ($vets as $vIndex => $vet) {
                for ($r = 0; $r < 2; $r++) {
                    Review::create([
                        'user_id'         => $owners[($vIndex + $r) % count($owners)]->id,
                        'reviewable_type' => 'App\\Models\\User',
                        'reviewable_id'   => $vet->id,
                        'rating'          => 4 + ($r % 2),
                        'comment'         => "Dr. {$vet->name} provided outstanding treatment and wonderful care for my pet!",
                    ]);
                }
            }

            // Product reviews
            foreach ($products as $pIndex => $prod) {
                if ($pIndex >= 6) break;
                Review::create([
                    'user_id'         => $owners[$pIndex % count($owners)]->id,
                    'reviewable_type' => 'App\\Models\\Product',
                    'reviewable_id'   => $prod->id,
                    'rating'          => 5,
                    'comment'         => "Great product! My pet loves {$prod->name}.",
                ]);
            }

            // Adoption reviews
            foreach ($listings as $lIndex => $list) {
                if ($lIndex >= 6) break;
                Review::create([
                    'user_id'         => $owners[$lIndex % count($owners)]->id,
                    'reviewable_type' => 'App\\Models\\AdoptionListing',
                    'reviewable_id'   => $list->id,
                    'rating'          => 5,
                    'comment'         => "Wonderful adoption listing. {$list->pet_name} is so sweet!",
                ]);
            }
        }

        // ── 20. Contacts (12+) ─────────────────────────────────────────────
        for ($c = 1; $c <= 12; $c++) {
            Contact::create([
                'name'       => "User Inquiry {$c}",
                'email'      => "inquiry{$c}@example.com",
                'subject'    => "Inquiry regarding services #{$c}",
                'message'    => "Hello, I have a question regarding veterinary appointments and clinic hours.",
                'ip_address' => "192.168.1.{$c}",
                'status'     => ['new', 'read', 'replied'][$c % 3],
            ]);
        }

        // ── 21. Subscribers (12+) ──────────────────────────────────────────
        for ($s = 1; $s <= 12; $s++) {
            Subscriber::create([
                'email'         => "subscriber{$s}@example.com",
                'name'          => "Subscriber {$s}",
                'subscribed_at' => now()->subDays($s),
                'ip_address'    => "192.168.1.1{$s}",
            ]);
        }

        // ── 22. Tags & Taggables (12+) ────────────────────────────────────
        $tagNames = ['Pet Care', 'Nutrition', 'Vaccination', 'Surgery', 'Puppy', 'Kitten', 'Grooming', 'Dental', 'Emergency', 'Sanctuary', 'Adoption', 'Wellness'];
        foreach ($tagNames as $tIndex => $tName) {
            $tag = Tag::updateOrCreate(
                ['slug' => Str::slug($tName)],
                ['name' => $tName, 'color' => '#1a6b3c']
            );

            if (isset($products[$tIndex])) {
                \Illuminate\Support\Facades\DB::table('taggables')->insertOrIgnore([
                    'tag_id'        => $tag->id,
                    'taggable_type' => 'App\\Models\\Product',
                    'taggable_id'   => $products[$tIndex]->id,
                ]);
            }
        }

        // ── 23. Activity Logs (15+) ───────────────────────────────────────
        if ($admin) {
            for ($a = 1; $a <= 15; $a++) {
                ActivityLog::create([
                    'user_id'        => $admin->id,
                    'event'          => ['created', 'updated', 'approved', 'login'][$a % 4],
                    'auditable_type' => 'App\\Models\\User',
                    'auditable_id'   => $a,
                    'old_values'     => ['status' => 'pending'],
                    'new_values'     => ['status' => 'active'],
                    'ip_address'     => '127.0.0.1',
                    'user_agent'     => 'Mozilla/5.0 (Windows NT 10.0)',
                ]);
            }
        }

        // ── 24. Media Records (12+) ────────────────────────────────────────
        if (count($pets) >= 6) {
            for ($m = 0; $m < 12; $m++) {
                $pet = $pets[$m % count($pets)];
                Media::create([
                    'mediable_type' => 'App\\Models\\Pet',
                    'mediable_id'   => $pet->id,
                    'name'          => "media_pet_{$pet->id}",
                    'original_name' => "pet_{$pet->id}_photo.jpg",
                    'mime_type'     => 'image/jpeg',
                    'size'          => 204800,
                    'path'          => "uploads/pets/pet_{$pet->id}.jpg",
                    'disk'          => 'public',
                    'created_by'    => $pet->owner_id,
                ]);
            }
        }
    }
}
