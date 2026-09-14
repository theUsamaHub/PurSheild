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
            'Dog'        => ['Labrador Retriever', 'German Shepherd', 'Bulldog', 'Poodle', 'Beagle', 'Rottweiler', 'Mixed'],
            'Cat'        => ['Persian', 'Siamese', 'Maine Coon', 'British Shorthair', 'Ragdoll', 'Mixed'],
            'Bird'       => ['Parrot', 'Canary', 'Cockatiel', 'Budgerigar'],
            'Rabbit'     => ['Holland Lop', 'Mini Rex', 'Flemish Giant', 'Mixed'],
            'Fish'       => ['Goldfish', 'Betta', 'Guppy', 'Angelfish'],
            'Hamster'    => ['Syrian Hamster', 'Dwarf Hamster', 'Roborovski'],
            'Guinea Pig' => ['American Guinea Pig', 'Abyssinian', 'Peruvian'],
            'Ferret'     => ['Sable Ferret', 'Albino Ferret'],
            'Horse'      => ['Arabian', 'Thoroughbred', 'Quarter Horse'],
            'Reptile'    => ['Bearded Dragon', 'Leopard Gecko', 'Ball Python'],
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
            ['name' => 'General Practice',   'description' => 'General veterinary care and routine checkups'],
            ['name' => 'Surgery',            'description' => 'Surgical procedures and soft tissue operations'],
            ['name' => 'Dermatology',        'description' => 'Skin, coat, ear, and allergy treatments'],
            ['name' => 'Cardiology',         'description' => 'Heart and cardiovascular condition diagnosis'],
            ['name' => 'Dentistry',          'description' => 'Dental scaling, cleaning, and oral surgery'],
            ['name' => 'Orthopedics',        'description' => 'Bone, joint, and musculoskeletal conditions'],
            ['name' => 'Ophthalmology',      'description' => 'Eye care and vision condition management'],
            ['name' => 'Internal Medicine',  'description' => 'Internal organ conditions and diagnostics'],
            ['name' => 'Neurology',          'description' => 'Nervous system, spine, and brain disorders'],
            ['name' => 'Oncology',           'description' => 'Cancer diagnosis and veterinary oncology'],
        ];

        foreach ($specializations as $spec) {
            Specialization::updateOrCreate(
                ['name' => $spec['name']],
                $spec
            );
        }
    }
}
