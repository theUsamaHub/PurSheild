<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $query = Product::with(['category', 'images']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($categoryId = $request->input('category_id')) {
            $query->where('category_id', $categoryId);
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $products = $query->latest()->paginate(15);
        $categories = Category::orderBy('name')->get();

        $counts = Product::selectRaw("count(*) as total")
            ->selectRaw("count(case when status = 'active' then 1 end) as active_count")
            ->selectRaw("count(case when stock_quantity <= 0 then 1 end) as out_of_stock_count")
            ->first();

        $stats = [
            'total' => $counts->total,
            'active' => $counts->active_count,
            'out_of_stock' => $counts->out_of_stock_count,
        ];

        return view('admin.products.index', compact('products', 'categories', 'stats'));
    }

    public function create(): View
    {
        $categories = Category::orderBy('name')->get();

        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:products,slug'],
            'description' => ['nullable', 'string'],
            'sku' => ['nullable', 'string', 'max:100'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'weight' => ['nullable', 'numeric', 'min:0'],
            'category_id' => ['required', 'exists:categories,id'],
            'is_featured' => ['boolean'],
            'status' => ['required', 'in:active,inactive'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string'],
            'images' => ['nullable', 'array', 'max:6'],
            'images.*' => ['file', 'max:5120', 'mimes:jpg,jpeg,png,gif,webp'],
        ], [
            'name.required' => 'Product name is required.',
            'name.max' => 'Product name must not exceed 255 characters.',
            'slug.unique' => 'This slug is already taken.',
            'price.required' => 'Price is required.',
            'price.numeric' => 'Price must be a number.',
            'price.min' => 'Price must be at least 0.',
            'stock_quantity.required' => 'Stock quantity is required.',
            'stock_quantity.integer' => 'Stock quantity must be a whole number.',
            'stock_quantity.min' => 'Stock quantity must be at least 0.',
            'category_id.required' => 'Please select a category.',
            'category_id.exists' => 'Selected category does not exist.',
            'status.required' => 'Status is required.',
            'status.in' => 'Status must be active or inactive.',
            'images.max' => 'You can upload a maximum of 6 images.',
            'images.*.max' => 'Each image must not exceed 5MB.',
            'images.*.mimes' => 'Each image must be a JPG, PNG, GIF, or WebP file.',
        ]);

        $data = collect($validated)->except(['images'])->toArray();
        $data['slug'] = $validated['slug'] ?: Str::slug($validated['name']);
        $data['created_by'] = auth()->id();
        $data['updated_by'] = auth()->id();

        if (!isset($data['is_featured'])) {
            $data['is_featured'] = false;
        }

        $product = Product::create($data);

        if ($request->hasFile('images')) {
            $files = $request->file('images');
            foreach ($files as $index => $file) {
                $path = $file->store('uploads/products', 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $path,
                    'is_primary' => $index === 0,
                    'sort_order' => $index,
                ]);
            }
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Product created successfully.');
    }

    public function show(Product $product): View
    {
        $product->load(['category', 'images', 'createdBy', 'updatedBy']);

        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product): View
    {
        $product->load('images');
        $categories = Category::orderBy('name')->get();

        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:products,slug,' . $product->id],
            'description' => ['nullable', 'string'],
            'sku' => ['nullable', 'string', 'max:100'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'weight' => ['nullable', 'numeric', 'min:0'],
            'category_id' => ['required', 'exists:categories,id'],
            'is_featured' => ['boolean'],
            'status' => ['required', 'in:active,inactive'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string'],
            'images' => ['nullable', 'array', 'max:6'],
            'images.*' => ['file', 'max:5120', 'mimes:jpg,jpeg,png,gif,webp'],
            'remove_images' => ['nullable', 'array'],
            'remove_images.*' => ['integer', 'exists:product_images,id'],
            'primary_image_id' => ['nullable', 'integer', 'exists:product_images,id'],
        ], [
            'name.required' => 'Product name is required.',
            'name.max' => 'Product name must not exceed 255 characters.',
            'slug.unique' => 'This slug is already taken.',
            'price.required' => 'Price is required.',
            'price.numeric' => 'Price must be a number.',
            'price.min' => 'Price must be at least 0.',
            'stock_quantity.required' => 'Stock quantity is required.',
            'stock_quantity.integer' => 'Stock quantity must be a whole number.',
            'stock_quantity.min' => 'Stock quantity must be at least 0.',
            'category_id.required' => 'Please select a category.',
            'category_id.exists' => 'Selected category does not exist.',
            'status.required' => 'Status is required.',
            'status.in' => 'Status must be active or inactive.',
            'primary_image_id.exists' => 'Selected primary image does not exist.',
            'images.max' => 'You can upload a maximum of 6 images total.',
            'images.*.max' => 'Each image must not exceed 5MB.',
            'images.*.mimes' => 'Each image must be a JPG, PNG, GIF, or WebP file.',
        ]);

        $data = collect($validated)->except(['images', 'remove_images', 'primary_image_id'])->toArray();
        $data['slug'] = $validated['slug'] ?: Str::slug($validated['name']);
        $data['updated_by'] = auth()->id();

        if (!isset($data['is_featured'])) {
            $data['is_featured'] = false;
        }

        $product->update($data);

        // Remove selected images
        if (!empty($validated['remove_images'])) {
            foreach ($validated['remove_images'] as $imageId) {
                $image = ProductImage::where('id', $imageId)
                    ->where('product_id', $product->id)
                    ->first();

                if ($image) {
                    Storage::disk('public')->delete($image->image_path);
                    $image->delete();
                }
            }
        }

        // Upload new images
        if ($request->hasFile('images')) {
            $existingCount = $product->images()->count();
            $files = $request->file('images');

            foreach ($files as $index => $file) {
                $path = $file->store('uploads/products', 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $path,
                    'is_primary' => false,
                    'sort_order' => $existingCount + $index,
                ]);
            }
        }

        // Update primary image
        if (!empty($validated['primary_image_id'])) {
            ProductImage::where('product_id', $product->id)->update(['is_primary' => false]);
            ProductImage::where('id', $validated['primary_image_id'])
                ->where('product_id', $product->id)
                ->update(['is_primary' => true]);
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        foreach ($product->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }

        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Product deleted successfully.');
    }
}
