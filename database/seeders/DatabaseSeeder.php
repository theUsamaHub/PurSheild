<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // 1. Roles (admin, owner, vet, shelter)
        $this->call(RoleSeeder::class);

        // 1b. SKU Templates
        $this->call(SkuTemplateSeeder::class);

        // 2. Species, Breeds, Specializations
        $this->call(SpeciesAndBreedsSeeder::class);

        // 3. Product Categories
        $this->call(CategorySeeder::class);

        // 4. Starter kit settings (keep existing)
        $this->call(SettingsSeeder::class);

        // 5. Users: admin, owners, vets (with profiles), shelters (with profiles)
        $this->call(FurShieldUsersSeeder::class);

        // 6. Sample data: pets, health records, appointments, products, adoption listings
        $this->call(FurShieldDataSeeder::class);
    }
}
