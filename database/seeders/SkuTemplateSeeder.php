<?php

namespace Database\Seeders;

use App\Models\SkuTemplate;
use Illuminate\Database\Seeder;

class SkuTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            ['name' => 'Standard', 'pattern' => 'PRD-{SLUG}-{####}', 'is_active' => true, 'sort_order' => 1],
            ['name' => 'Category-Based', 'pattern' => '{CAT_SLUG}-{SLUG}-{####}', 'is_active' => true, 'sort_order' => 2],
            ['name' => 'Short ID', 'pattern' => 'FS-{####}', 'is_active' => true, 'sort_order' => 3],
            ['name' => 'Yearly', 'pattern' => '{YEAR}-{CAT_SLUG}-{####}', 'is_active' => false, 'sort_order' => 4],
        ];

        foreach ($templates as $template) {
            SkuTemplate::create($template);
        }
    }
}
