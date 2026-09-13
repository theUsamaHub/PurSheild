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
        // Admin
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

        // Pet Owners
        $ownerData = [
            ['name' => 'Sarah Johnson', 'email' => 'sarah@example.com', 'phone' => '+1234567891', 'address' => '456 Oak Avenue, Petville'],
            ['name' => 'Mike Williams', 'email' => 'mike@example.com', 'phone' => '+1234567892', 'address' => '789 Pine Road, Animaltown'],
            ['name' => 'Emily Davis', 'email' => 'emily@example.com', 'phone' => '+1234567893', 'address' => '321 Elm Street, Dogtown'],
        ];

        $owners = [];
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
            $owners[] = $owner;
        }

        // Veterinarians
        $vetData = [
            [
                'name' => 'Dr. James Carter', 'email' => 'dr.carter@furshield.com',
                'phone' => '+1234567894', 'address' => '101 Vet Lane, Medical District',
                'qualification' => 'DVM, BVSc', 'experience_years' => 12,
                'clinic_name' => 'PawCare Veterinary Clinic',
                'clinic_address' => '101 Vet Lane, Medical District',
                'bio' => 'Experienced veterinarian specializing in small animal medicine.',
                'consultation_fee' => 75.00, 'is_verified' => true,
                'specializations' => ['General Practice', 'Surgery'],
            ],
            [
                'name' => 'Dr. Lisa Chen', 'email' => 'dr.chen@furshield.com',
                'phone' => '+1234567895', 'address' => '202 Health Blvd, Medical Center',
                'qualification' => 'DVM, MVSc (Cardiology)', 'experience_years' => 8,
                'clinic_name' => 'HeartBeat Animal Hospital',
                'clinic_address' => '202 Health Blvd, Medical Center',
                'bio' => 'Board-certified veterinary cardiologist.',
                'consultation_fee' => 100.00, 'is_verified' => true,
                'specializations' => ['Cardiology', 'Internal Medicine'],
            ],
            [
                'name' => 'Dr. Ahmed Hassan', 'email' => 'dr.hassan@furshield.com',
                'phone' => '+1234567896', 'address' => '303 Care Street, Wellness Area',
                'qualification' => 'DVM, Diploma in Veterinary Dentistry', 'experience_years' => 5,
                'clinic_name' => 'SmilePet Dental Clinic',
                'clinic_address' => '303 Care Street, Wellness Area',
                'bio' => 'Dedicated to pet dental health and oral surgery.',
                'consultation_fee' => 60.00, 'is_verified' => false,
                'specializations' => ['Dentistry'],
            ],
        ];

        $vets = [];
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
            $vets[] = $vet;
        }

        // Shelters
        $shelterData = [
            [
                'name' => 'Happy Paws Shelter', 'email' => 'info@happypaws.com',
                'phone' => '+1234567897', 'address' => '500 Shelter Road, Greendale',
                'shelter_name' => 'Happy Paws Animal Shelter',
                'description' => 'A non-profit shelter dedicated to finding forever homes.',
                'city' => 'Greendale', 'contact_number' => '+1234567897',
                'website' => 'https://happypaws.com', 'is_verified' => true,
                'capacity' => 50, 'latitude' => 40.7128, 'longitude' => -74.0060,
            ],
            [
                'name' => 'New Beginnings Rescue', 'email' => 'contact@newbeginnings.com',
                'phone' => '+1234567898', 'address' => '600 Rescue Ave, Westside',
                'shelter_name' => 'New Beginnings Animal Rescue',
                'description' => 'Rescue and rehabilitation center for animals in need.',
                'city' => 'Westside', 'contact_number' => '+1234567898',
                'website' => 'https://newbeginnings.com', 'is_verified' => false,
                'capacity' => 30, 'latitude' => 40.7589, 'longitude' => -73.9851,
            ],
        ];

        $shelters = [];
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
            $shelters[] = $shelter;
        }
    }
}
