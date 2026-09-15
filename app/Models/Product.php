<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Product extends Model
{
    use SoftDeletes, LogsActivity;

    protected $fillable = [
        'category_id', 'sku_template_id', 'name', 'slug', 'description', 'sku',
        'price', 'special_price', 'discount_percent',
        'stock_quantity', 'weight', 'is_featured',
        'meta_title', 'meta_description', 'status',
        'created_by', 'updated_by',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'special_price' => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'stock_quantity' => 'integer',
        'weight' => 'decimal:2',
        'is_featured' => 'boolean',
    ];

    public function getEffectivePriceAttribute(): float
    {
        if ($this->special_price !== null && $this->special_price > 0) {
            return (float) $this->special_price;
        }
        return (float) $this->price;
    }

    public function getSavingsAttribute(): float
    {
        return (float) $this->price - $this->effective_price;
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Product $product) {
            if (empty($product->sku)) {
                if ($product->sku_template_id) {
                    $template = SkuTemplate::find($product->sku_template_id);
                    $product->sku = $template ? $template->generateSku($product) : self::generateUniqueSku();
                } else {
                    $product->sku = self::generateUniqueSku();
                }
            }
        });
    }

    public static function generateUniqueSku(): string
    {
        do {
            $sku = 'PRD-' . strtoupper(Str::random(8));
        } while (static::withTrashed()->where('sku', $sku)->exists());

        return $sku;
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function skuTemplate(): BelongsTo
    {
        return $this->belongsTo(SkuTemplate::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function reviews(): MorphMany
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function orders(): HasManyThrough
    {
        return $this->hasManyThrough(Order::class, OrderItem::class, 'product_id', 'id', 'id', 'order_id');
    }
}
