<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'Admin', 'slug' => 'admin', 'description' => 'Full system access. Can manage all resources, users, and settings.'],
            ['name' => 'Pet Owner', 'slug' => 'owner', 'description' => 'Pet owner. Can manage pets, health records, appointments, browse products.'],
            ['name' => 'Veterinarian', 'slug' => 'vet', 'description' => 'Veterinarian. Can manage appointments, log treatments, view pet medical history.'],
            ['name' => 'Animal Shelter', 'slug' => 'shelter', 'description' => 'Animal shelter. Can list adoptable pets, manage adoption applications.'],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['slug' => $role['slug']],
                $role
            );
        }
    }
}
