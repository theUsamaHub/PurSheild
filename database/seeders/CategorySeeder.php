<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Food & Nutrition', 'slug' => 'food-nutrition', 'description' => 'Pet food, supplements, and nutritional products', 'sort_order' => 1],
            ['name' => 'Toys & Accessories', 'slug' => 'toys-accessories', 'description' => 'Toys, leashes, collars, and accessories', 'sort_order' => 2],
            ['name' => 'Health & Wellness', 'slug' => 'health-wellness', 'description' => 'Medications, vitamins, and health supplements', 'sort_order' => 3],
            ['name' => 'Grooming', 'slug' => 'grooming', 'description' => 'Shampoos, brushes, and grooming tools', 'sort_order' => 4],
            ['name' => 'Bedding & Housing', 'slug' => 'bedding-housing', 'description' => 'Beds, cages, crates, and housing', 'sort_order' => 5],
            ['name' => 'Clothing', 'slug' => 'clothing', 'description' => 'Pet clothing and costumes', 'sort_order' => 6],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }
    }
}
