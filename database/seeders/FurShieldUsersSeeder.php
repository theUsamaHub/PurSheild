<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\VetProfile;
use App\Models\ShelterProfile;
use App\Models\Specialization;
use App\Models\VetSpecialization;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class FurShieldUsersSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Admin
        $admin = User::updateOrCreate(
            ['email' => 'admin@furshield.com'],
            [
                'name' => 'FurShield Admin',
                'phone' => '+1234567890',
                'address' => '123 Admin Street, Tech City',
                'status' => 'active',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole('admin');

        // 2. Pet Owners (10+)
        $ownerData = [
            ['name' => 'Sarah Johnson',   'email' => 'sarah@example.com',   'phone' => '+1234567891', 'address' => '456 Oak Avenue, Petville'],
            ['name' => 'Mike Williams',   'email' => 'mike@example.com',    'phone' => '+1234567892', 'address' => '789 Pine Road, Animaltown'],
            ['name' => 'Emily Davis',     'email' => 'emily@example.com',   'phone' => '+1234567893', 'address' => '321 Elm Street, Dogtown'],
            ['name' => 'David Miller',    'email' => 'david@example.com',   'phone' => '+1234567894', 'address' => '15 Maple Lane, Cattown'],
            ['name' => 'Jessica Taylor',  'email' => 'jessica@example.com', 'phone' => '+1234567895', 'address' => '88 Cedar Crest, Barking'],
            ['name' => 'Robert Martinez', 'email' => 'robert@example.com',  'phone' => '+1234567896', 'address' => '204 Birch Road, Meow Valley'],
            ['name' => 'Amanda White',    'email' => 'amanda@example.com',  'phone' => '+1234567897', 'address' => '501 Willow Way, Pawsburg'],
            ['name' => 'Daniel Harris',   'email' => 'daniel@example.com',  'phone' => '+1234567898', 'address' => '77 Chestnut St, Furry Creek'],
            ['name' => 'Sophia Clark',    'email' => 'sophia@example.com',  'phone' => '+1234567899', 'address' => '99 Spruce Ave, Petville'],
            ['name' => 'James Wilson',    'email' => 'james@example.com',   'phone' => '+1234567800', 'address' => '630 Ash Boulevard, Woofland'],
        ];

        foreach ($ownerData as $data) {
            $owner = User::updateOrCreate(
                ['email' => $data['email']],
                array_merge($data, [
                    'status' => 'active',
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ])
            );
            $owner->assignRole('owner');
        }

        // 3. Veterinarians (10+)
        $vetData = [
            [
                'name' => 'Dr. James Carter', 'email' => 'dr.carter@furshield.com',
                'phone' => '+1234567811', 'address' => '101 Vet Lane, Medical District',
                'qualification' => 'DVM, BVSc', 'experience_years' => 12,
                'clinic_name' => 'PawCare Veterinary Clinic', 'clinic_address' => '101 Vet Lane, Medical District',
                'bio' => 'Experienced veterinarian specializing in small animal medicine.',
                'consultation_fee' => 75.00, 'is_verified' => true, 'specializations' => ['General Practice', 'Surgery'],
            ],
            [
                'name' => 'Dr. Lisa Chen', 'email' => 'dr.chen@furshield.com',
                'phone' => '+1234567812', 'address' => '202 Health Blvd, Medical Center',
                'qualification' => 'DVM, MVSc (Cardiology)', 'experience_years' => 8,
                'clinic_name' => 'HeartBeat Animal Hospital', 'clinic_address' => '202 Health Blvd, Medical Center',
                'bio' => 'Board-certified veterinary cardiologist.',
                'consultation_fee' => 100.00, 'is_verified' => true, 'specializations' => ['Cardiology', 'Internal Medicine'],
            ],
            [
                'name' => 'Dr. Ahmed Hassan', 'email' => 'dr.hassan@furshield.com',
                'phone' => '+1234567813', 'address' => '303 Care Street, Wellness Area',
                'qualification' => 'DVM, Diploma in Veterinary Dentistry', 'experience_years' => 5,
                'clinic_name' => 'SmilePet Dental Clinic', 'clinic_address' => '303 Care Street, Wellness Area',
                'bio' => 'Dedicated to pet dental health and oral surgery.',
                'consultation_fee' => 60.00, 'is_verified' => true, 'specializations' => ['Dentistry'],
            ],
            [
                'name' => 'Dr. Elena Rostova', 'email' => 'dr.rostova@furshield.com',
                'phone' => '+1234567814', 'address' => '404 Derma Way, Skin Clinic Zone',
                'qualification' => 'DVM, Specialist in Dermatology', 'experience_years' => 10,
                'clinic_name' => 'DermaPets Skin & Allergy Center', 'clinic_address' => '404 Derma Way, Skin Clinic Zone',
                'bio' => 'Expert in animal skin conditions and allergy management.',
                'consultation_fee' => 85.00, 'is_verified' => true, 'specializations' => ['Dermatology'],
            ],
            [
                'name' => 'Dr. Marcus Vance', 'email' => 'dr.vance@furshield.com',
                'phone' => '+1234567815', 'address' => '505 Bone St, Ortho Center',
                'qualification' => 'DVM, MS Orthopedics', 'experience_years' => 14,
                'clinic_name' => 'OrthoPaw Surgical Specialists', 'clinic_address' => '505 Bone St, Ortho Center',
                'bio' => 'Specializing in complex fracture repair and joint replacement.',
                'consultation_fee' => 120.00, 'is_verified' => true, 'specializations' => ['Orthopedics', 'Surgery'],
            ],
            [
                'name' => 'Dr. Priya Sharma', 'email' => 'dr.sharma@furshield.com',
                'phone' => '+1234567816', 'address' => '606 Eye Care Ave, Vision Plaza',
                'qualification' => 'DVM, Eye Specialist Certificate', 'experience_years' => 7,
                'clinic_name' => 'ClearVision Animal Eye Care', 'clinic_address' => '606 Eye Care Ave, Vision Plaza',
                'bio' => 'Focusing on cataract treatment and glaucoma management in pets.',
                'consultation_fee' => 90.00, 'is_verified' => true, 'specializations' => ['Ophthalmology'],
            ],
            [
                'name' => 'Dr. Thomas Wright', 'email' => 'dr.wright@furshield.com',
                'phone' => '+1234567817', 'address' => '707 Neuro Clinic, Brain Health Zone',
                'qualification' => 'DVM, Ph.D. Veterinary Neurology', 'experience_years' => 15,
                'clinic_name' => 'NeuroPaw Brain & Spine Center', 'clinic_address' => '707 Neuro Clinic, Brain Health Zone',
                'bio' => 'Consultant neurologist for spinal and cerebral conditions.',
                'consultation_fee' => 150.00, 'is_verified' => true, 'specializations' => ['Neurology'],
            ],
            [
                'name' => 'Dr. Chloe Bennett', 'email' => 'dr.bennett@furshield.com',
                'phone' => '+1234567818', 'address' => '808 Hope Lane, Oncology Wing',
                'qualification' => 'DVM, DACVIM (Oncology)', 'experience_years' => 9,
                'clinic_name' => 'Hope Veterinary Cancer Center', 'clinic_address' => '808 Hope Lane, Oncology Wing',
                'bio' => 'Compassionate cancer therapy and oncology care for pets.',
                'consultation_fee' => 110.00, 'is_verified' => true, 'specializations' => ['Oncology'],
            ],
            [
                'name' => 'Dr. Kevin O\'Connor', 'email' => 'dr.oconnor@furshield.com',
                'phone' => '+1234567819', 'address' => '909 General Rd, Family Pet District',
                'qualification' => 'DVM, BVetMed', 'experience_years' => 6,
                'clinic_name' => 'Family Pet Wellness Clinic', 'clinic_address' => '909 General Rd, Family Pet District',
                'bio' => 'Preventative health care, vaccinations, and family pet wellness.',
                'consultation_fee' => 65.00, 'is_verified' => true, 'specializations' => ['General Practice'],
            ],
            [
                'name' => 'Dr. Rachel Kim', 'email' => 'dr.kim@furshield.com',
                'phone' => '+1234567820', 'address' => '1010 Surgery Suite, Medical Park',
                'qualification' => 'DVM, Board Certified Surgeon', 'experience_years' => 11,
                'clinic_name' => 'Apex Veterinary Surgical Suite', 'clinic_address' => '1010 Surgery Suite, Medical Park',
                'bio' => 'Minimally invasive laparoscopic and soft tissue surgeries.',
                'consultation_fee' => 115.00, 'is_verified' => false, 'specializations' => ['Surgery'],
            ],
        ];

        foreach ($vetData as $data) {
            $specNames = $data['specializations'];
            unset($data['specializations']);

            $vet = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'], 'phone' => $data['phone'], 'address' => $data['address'],
                    'status' => $data['is_verified'] ? 'active' : 'pending_verification',
                    'password' => Hash::make('password'), 'email_verified_at' => now(),
                ]
            );
            $vet->assignRole('vet');

            VetProfile::updateOrCreate(
                ['user_id' => $vet->id],
                [
                    'qualification' => $data['qualification'],
                    'experience_years' => $data['experience_years'],
                    'clinic_name' => $data['clinic_name'],
                    'clinic_address' => $data['clinic_address'],
                    'bio' => $data['bio'],
                    'consultation_fee' => $data['consultation_fee'],
                    'is_verified' => $data['is_verified'],
                    'verified_at' => $data['is_verified'] ? now() : null,
                    'verified_by' => $data['is_verified'] ? $admin->id : null,
                ]
            );

            foreach ($specNames as $specName) {
                $spec = Specialization::where('name', $specName)->first();
                if ($spec) {
                    VetSpecialization::updateOrCreate(
                        ['vet_id' => $vet->id, 'specialization_id' => $spec->id]
                    );
                }
            }
        }

        // 4. Shelters (10+)
        $shelterData = [
            [
                'name' => 'Happy Paws Shelter', 'email' => 'info@happypaws.com',
                'phone' => '+1234567821', 'address' => '500 Shelter Road, Greendale',
                'shelter_name' => 'Happy Paws Animal Shelter',
                'description' => 'A non-profit shelter dedicated to finding forever homes.',
                'city' => 'Greendale', 'contact_number' => '+1234567821',
                'website' => 'https://happypaws.com', 'is_verified' => true,
                'capacity' => 50, 'latitude' => 40.7128, 'longitude' => -74.0060,
            ],
            [
                'name' => 'New Beginnings Rescue', 'email' => 'contact@newbeginnings.com',
                'phone' => '+1234567822', 'address' => '600 Rescue Ave, Westside',
                'shelter_name' => 'New Beginnings Animal Rescue',
                'description' => 'Rescue and rehabilitation center for animals in need.',
                'city' => 'Westside', 'contact_number' => '+1234567822',
                'website' => 'https://newbeginnings.com', 'is_verified' => true,
                'capacity' => 35, 'latitude' => 40.7589, 'longitude' => -73.9851,
            ],
            [
                'name' => 'Safe Haven Animal Haven', 'email' => 'info@safehaven.org',
                'phone' => '+1234567823', 'address' => '700 Safety Court, Havenville',
                'shelter_name' => 'Safe Haven Pet Rescue',
                'description' => 'Providing sanctuary and emergency shelter to stray pets.',
                'city' => 'Havenville', 'contact_number' => '+1234567823',
                'website' => 'https://safehaven.org', 'is_verified' => true,
                'capacity' => 45, 'latitude' => 40.7300, 'longitude' => -73.9900,
            ],
            [
                'name' => 'Furry Friends Sanctuary', 'email' => 'hello@furryfriends.org',
                'phone' => '+1234567824', 'address' => '800 Sanctuary Lane, Northwood',
                'shelter_name' => 'Furry Friends Companion Sanctuary',
                'description' => 'Community sanctuary focusing on cat and rabbit rescues.',
                'city' => 'Northwood', 'contact_number' => '+1234567824',
                'website' => 'https://furryfriends.org', 'is_verified' => true,
                'capacity' => 40, 'latitude' => 40.7400, 'longitude' => -73.9700,
            ],
            [
                'name' => 'Second Chance Haven', 'email' => 'adopt@secondchance.org',
                'phone' => '+1234567825', 'address' => '900 Chance St, Eastside',
                'shelter_name' => 'Second Chance Pet Adoption Center',
                'description' => 'Giving second chances to abandoned dogs and puppies.',
                'city' => 'Eastside', 'contact_number' => '+1234567825',
                'website' => 'https://secondchance.org', 'is_verified' => true,
                'capacity' => 60, 'latitude' => 40.7200, 'longitude' => -73.9600,
            ],
            [
                'name' => 'Warm Hearts Rescue', 'email' => 'support@warmhearts.org',
                'phone' => '+1234567826', 'address' => '100 Heart Way, Southridge',
                'shelter_name' => 'Warm Hearts Animal League',
                'description' => 'No-kill rescue shelter supporting senior animals.',
                'city' => 'Southridge', 'contact_number' => '+1234567826',
                'website' => 'https://warmhearts.org', 'is_verified' => true,
                'capacity' => 30, 'latitude' => 40.7100, 'longitude' => -73.9500,
            ],
            [
                'name' => 'Pawsitive Living Rescue', 'email' => 'info@pawsitiveliving.org',
                'phone' => '+1234567827', 'address' => '110 Hope Boulevard, Sun Valley',
                'shelter_name' => 'Pawsitive Living Rescue & Adoption',
                'description' => 'Fostering and rehoming pets with specialized care.',
                'city' => 'Sun Valley', 'contact_number' => '+1234567827',
                'website' => 'https://pawsitiveliving.org', 'is_verified' => true,
                'capacity' => 25, 'latitude' => 40.7600, 'longitude' => -73.9400,
            ],
            [
                'name' => 'Little Tails Rescue', 'email' => 'contact@littletails.org',
                'phone' => '+1234567828', 'address' => '120 Little Way, Meadowbrook',
                'shelter_name' => 'Little Tails Small Animal Rescue',
                'description' => 'Dedicated rescue center for birds, rabbits, and rodents.',
                'city' => 'Meadowbrook', 'contact_number' => '+1234567828',
                'website' => 'https://littletails.org', 'is_verified' => true,
                'capacity' => 20, 'latitude' => 40.7700, 'longitude' => -73.9300,
            ],
            [
                'name' => 'Guardian Angels Pets', 'email' => 'angels@guardianpets.org',
                'phone' => '+1234567829', 'address' => '130 Angel Drive, Riverside',
                'shelter_name' => 'Guardian Angels Pet Sanctuary',
                'description' => 'Providing medical care and rehab prior to adoption.',
                'city' => 'Riverside', 'contact_number' => '+1234567829',
                'website' => 'https://guardianpets.org', 'is_verified' => true,
                'capacity' => 55, 'latitude' => 40.7800, 'longitude' => -73.9200,
            ],
            [
                'name' => 'Forever Homes League', 'email' => 'info@foreverhomes.org',
                'phone' => '+1234567830', 'address' => '140 Heritage Road, Oldtown',
                'shelter_name' => 'Forever Homes Animal League',
                'description' => 'Community animal shelter connecting families with loving pets.',
                'city' => 'Oldtown', 'contact_number' => '+1234567830',
                'website' => 'https://foreverhomes.org', 'is_verified' => false,
                'capacity' => 40, 'latitude' => 40.7900, 'longitude' => -73.9100,
            ],
        ];

        foreach ($shelterData as $data) {
            $shelter = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'], 'phone' => $data['phone'], 'address' => $data['address'],
                    'status' => $data['is_verified'] ? 'active' : 'pending_verification',
                    'password' => Hash::make('password'), 'email_verified_at' => now(),
                ]
            );
            $shelter->assignRole('shelter');

            ShelterProfile::updateOrCreate(
                ['user_id' => $shelter->id],
                [
                    'shelter_name' => $data['shelter_name'],
                    'description' => $data['description'],
                    'address' => $data['address'],
                    'city' => $data['city'],
                    'contact_number' => $data['contact_number'],
                    'website' => $data['website'],
                    'is_verified' => $data['is_verified'],
                    'verified_at' => $data['is_verified'] ? now() : null,
                    'verified_by' => $data['is_verified'] ? $admin->id : null,
                    'capacity' => $data['capacity'],
                    'latitude' => $data['latitude'],
                    'longitude' => $data['longitude'],
                ]
            );
        }
    }
}
