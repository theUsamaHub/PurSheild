<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $productId = $this->route('product')?->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:products,slug,' . $productId],
            'description' => ['nullable', 'string'],
            'sku' => ['nullable', 'string', 'max:100'],
            'price' => ['required', 'numeric', 'min:0'],
            'special_price' => ['nullable', 'numeric', 'min:0', 'lt:price'],
            'discount_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'weight' => ['nullable', 'numeric', 'min:0'],
            'category_id' => ['required', 'exists:categories,id'],
            'sku_template_id' => ['nullable', 'integer', 'exists:sku_templates,id'],
            'is_featured' => ['boolean'],
            'status' => ['required', 'in:active,inactive'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string'],
            'images' => ['nullable', 'array', 'max:6'],
            'images.*' => ['file', 'max:5120', 'mimes:jpg,jpeg,png,gif,webp'],
            'remove_images' => ['nullable', 'array'],
            'remove_images.*' => ['integer', 'exists:product_images,id'],
            'primary_image_id' => ['nullable', 'integer', 'exists:product_images,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Product name is required.',
            'name.max' => 'Product name must not exceed 255 characters.',
            'slug.unique' => 'This slug is already taken.',
            'price.required' => 'Price is required.',
            'price.numeric' => 'Price must be a number.',
            'price.min' => 'Price must be at least 0.',
            'special_price.numeric' => 'Special price must be a number.',
            'special_price.min' => 'Special price must be at least 0.',
            'special_price.lt' => 'Special price must be less than the original price.',
            'discount_percent.numeric' => 'Discount must be a number.',
            'discount_percent.min' => 'Discount must be at least 0%.',
            'discount_percent.max' => 'Discount cannot exceed 100%.',
            'stock_quantity.required' => 'Stock quantity is required.',
            'stock_quantity.integer' => 'Stock quantity must be a whole number.',
            'stock_quantity.min' => 'Stock quantity must be at least 0.',
            'category_id.required' => 'Please select a category.',
            'category_id.exists' => 'Selected category does not exist.',
            'sku_template_id.exists' => 'Selected SKU template does not exist.',
            'status.required' => 'Status is required.',
            'status.in' => 'Status must be active or inactive.',
            'images.max' => 'You can upload a maximum of 6 images.',
            'images.*.max' => 'Each image must not exceed 5MB.',
            'images.*.mimes' => 'Each image must be a JPG, PNG, GIF, or WebP file.',
        ];
    }
}
