<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Food & Nutrition',    'slug' => 'food-nutrition',    'description' => 'Pet food, supplements, and nutritional products', 'sort_order' => 1],
            ['name' => 'Toys & Accessories',  'slug' => 'toys-accessories',  'description' => 'Toys, leashes, collars, and fun accessories',     'sort_order' => 2],
            ['name' => 'Health & Wellness',   'slug' => 'health-wellness',   'description' => 'Medications, vitamins, and health supplements',    'sort_order' => 3],
            ['name' => 'Grooming & Care',     'slug' => 'grooming',          'description' => 'Shampoos, brushes, and grooming tools',            'sort_order' => 4],
            ['name' => 'Bedding & Housing',   'slug' => 'bedding-housing',   'description' => 'Beds, cages, crates, and furniture',              'sort_order' => 5],
            ['name' => 'Clothing & Apparel',  'slug' => 'clothing',          'description' => 'Pet clothing, coats, and costumes',                'sort_order' => 6],
            ['name' => 'Litter & Cleanup',    'slug' => 'litter-cleanup',    'description' => 'Cat litter, waste bags, and stain removers',        'sort_order' => 7],
            ['name' => 'Travel & Carriers',   'slug' => 'travel-carriers',   'description' => 'Carriers, travel crates, and car safety gear',      'sort_order' => 8],
            ['name' => 'Treats & Chews',      'slug' => 'treats-chews',      'description' => 'Tasty treats, dental chews, and reward snacks',    'sort_order' => 9],
            ['name' => 'Aquatics & Terrarium','slug' => 'aquatics-terrarium','description' => 'Fish food, aquarium filters, and reptile lights',  'sort_order' => 10],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }
    }
}
