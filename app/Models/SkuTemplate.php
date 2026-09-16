<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SkuTemplate extends Model
{
    protected $fillable = [
        'name', 'pattern', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function generateSku(Product $product): string
    {
        $sku = $this->pattern;

        // Replace placeholders
        $sku = str_replace('{NAME}', strtoupper(substr($product->name, 0, 3)), $sku);
        $sku = str_replace('{SLUG}', strtoupper(Str::limit($product->slug ?? Str::slug($product->name), 6, '')), $sku);
        $sku = str_replace('{CATEGORY}', strtoupper(substr($product->category?->name ?? '', 0, 3)), $sku);
        $sku = str_replace('{CAT_SLUG}', strtoupper(Str::limit($product->category?->slug ?? '', 3, '')), $sku);
        $sku = str_replace('{ID}', str_pad($product->id ?? 0, 4, '0', STR_PAD_LEFT), $sku);
        $sku = str_replace('{YEAR}', now()->format('Y'), $sku);
        $sku = str_replace('{MONTH}', now()->format('m'), $sku);

        // Handle {####} counter — auto-increment numeric
        if (str_contains($sku, '{####}')) {
            $counter = Product::withTrashed()->count() + 1;
            $sku = str_replace('{####}', str_pad($counter, 4, '0', STR_PAD_LEFT), $sku);
        }

        // Handle {RANDOM} — random 4 chars
        $sku = str_replace('{RANDOM}', strtoupper(substr(uniqid(), -4)), $sku);

        // Ensure uniqueness
        $baseSku = $sku;
        $attempt = 1;
        while (Product::withTrashed()->where('sku', $sku)->exists()) {
            $sku = $baseSku . '-' . str_pad($attempt, 2, '0', STR_PAD_LEFT);
            $attempt++;
        }

        return $sku;
    }
}
