<?php

namespace Database\Seeders;

use App\Models\Species;
use App\Models\Breed;
use App\Models\Specialization;
use Illuminate\Database\Seeder;

class SpeciesAndBreedsSeeder extends Seeder
{
    public function run(): void
    {
        $speciesData = [
            'Dog' => ['Labrador Retriever', 'German Shepherd', 'Bulldog', 'Poodle', 'Beagle', 'Rottweiler', 'Mixed'],
            'Cat' => ['Persian', 'Siamese', 'Maine Coon', 'British Shorthair', 'Ragdoll', 'Mixed'],
            'Bird' => ['Parrot', 'Canary', 'Cockatiel', 'Budgerigar'],
            'Rabbit' => ['Holland Lop', 'Mini Rex', 'Flemish Giant', 'Mixed'],
            'Fish' => ['Goldfish', 'Betta', 'Guppy', 'Angelfish'],
        ];

        foreach ($speciesData as $speciesName => $breeds) {
            $species = Species::updateOrCreate(
                ['name' => $speciesName],
                ['description' => ucfirst($speciesName) . ' species']
            );

            foreach ($breeds as $breedName) {
                Breed::updateOrCreate(
                    ['species_id' => $species->id, 'name' => $breedName],
                    ['description' => $breedName . ' breed of ' . $speciesName]
                );
            }
        }

        $specializations = [
            ['name' => 'General Practice', 'description' => 'General veterinary care and checkups'],
            ['name' => 'Surgery', 'description' => 'Surgical procedures and operations'],
            ['name' => 'Dermatology', 'description' => 'Skin, coat, and allergy treatments'],
            ['name' => 'Cardiology', 'description' => 'Heart and cardiovascular conditions'],
            ['name' => 'Dentistry', 'description' => 'Dental care and oral health'],
            ['name' => 'Orthopedics', 'description' => 'Bone, joint, and muscle conditions'],
            ['name' => 'Ophthalmology', 'description' => 'Eye care and vision conditions'],
            ['name' => 'Internal Medicine', 'description' => 'Internal organ conditions and diagnostics'],
        ];

        foreach ($specializations as $spec) {
            Specialization::updateOrCreate(
                ['name' => $spec['name']],
                $spec
            );
        }
    }
}
