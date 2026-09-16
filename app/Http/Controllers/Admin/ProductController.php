<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\SkuTemplate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

        $products = $query->latest()->paginate(15)->withQueryString();
        $categories = \App\Models\Category::orderBy('name')->get();

        $counts = Product::selectRaw("count(*) as total")
            ->selectRaw("count(case when status = 'active' then 1 end) as active_count")
            ->selectRaw("count(case when status = 'inactive' then 1 end) as inactive_count")
            ->selectRaw("count(case when stock_quantity <= 0 then 1 end) as out_of_stock_count")
            ->first();

        $stats = [
            'total' => $counts->total,
            'active' => $counts->active_count,
            'inactive' => $counts->inactive_count,
            'out_of_stock' => $counts->out_of_stock_count,
        ];

        return view('admin.products.index', compact('products', 'categories', 'stats'));
    }

    public function bulkAction(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', 'exists:products,id'],
            'action' => ['required', 'in:activate,deactivate,delete'],
        ]);

        $products = Product::whereIn('id', $validated['ids']);

        switch ($validated['action']) {
            case 'activate':
                $products->update(['status' => 'active']);
                $message = count($validated['ids']) . ' product(s) activated.';
                break;
            case 'deactivate':
                $products->update(['status' => 'inactive']);
                $message = count($validated['ids']) . ' product(s) deactivated.';
                break;
            case 'delete':
                $products->each(function ($product) {
                    $product->delete();
                });
                $message = count($validated['ids']) . ' product(s) deleted.';
                break;
        }

        return redirect()->route('admin.products.index')
            ->with('success', $message);
    }

    public function create(): View
    {
        $categories = \App\Models\Category::where('is_active', true)->orderBy('name')->get();
        $skuTemplates = SkuTemplate::where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();

        return view('admin.products.create', compact('categories', 'skuTemplates'));
    }

    public function store(\App\Http\Requests\Admin\ProductRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $data = collect($validated)->except(['images', 'sku'])->toArray();
        $data['slug'] = $validated['slug'] ?: Str::slug($validated['name']);
        $data['created_by'] = auth()->id();
        $data['updated_by'] = auth()->id();
        $data['sku_template_id'] = $validated['sku_template_id'] ?? null;

        if (!isset($data['is_featured'])) {
            $data['is_featured'] = false;
        }

        if (!empty($data['discount_percent']) && empty($data['special_price'])) {
            $data['special_price'] = round($data['price'] - ($data['price'] * $data['discount_percent'] / 100), 2);
        } elseif (!empty($data['special_price']) && empty($data['discount_percent'])) {
            $data['discount_percent'] = round((1 - $data['special_price'] / $data['price']) * 100, 2);
        }

        $product = Product::create($data);

        if ($request->hasFile('images')) {
            $files = $request->file('images');
            foreach ($files as $index => $file) {
                $path = $file->store('uploads/products', 'public');
                \App\Models\ProductImage::create([
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
        $product->load(['category', 'images', 'createdBy', 'updatedBy', 'skuTemplate']);

        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product): View
    {
        $product->load('images');
        $categories = \App\Models\Category::where('is_active', true)->orderBy('name')->get();
        $skuTemplates = SkuTemplate::where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();

        return view('admin.products.edit', compact('product', 'categories', 'skuTemplates'));
    }

    public function update(\App\Http\Requests\Admin\ProductRequest $request, Product $product): RedirectResponse
    {
        $validated = $request->validated();

        $data = collect($validated)->except(['images', 'remove_images', 'primary_image_id'])->toArray();
        $data['slug'] = $validated['slug'] ?: Str::slug($validated['name']);
        $data['updated_by'] = auth()->id();

        if (!isset($data['is_featured'])) {
            $data['is_featured'] = false;
        }

        if (!empty($data['discount_percent']) && empty($data['special_price'])) {
            $data['special_price'] = round($data['price'] - ($data['price'] * $data['discount_percent'] / 100), 2);
        } elseif (!empty($data['special_price']) && empty($data['discount_percent'])) {
            $data['discount_percent'] = round((1 - $data['special_price'] / $data['price']) * 100, 2);
        }

        $product->update($data);

        if (!empty($validated['remove_images'])) {
            foreach ($validated['remove_images'] as $imageId) {
                $image = \App\Models\ProductImage::where('id', $imageId)
                    ->where('product_id', $product->id)
                    ->first();

                if ($image) {
                    \Storage::disk('public')->delete($image->image_path);
                    $image->delete();
                }
            }
        }

        if ($request->hasFile('images')) {
            $existingCount = $product->images()->count();
            $files = $request->file('images');

            foreach ($files as $index => $file) {
                $path = $file->store('uploads/products', 'public');
                \App\Models\ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $path,
                    'is_primary' => false,
                    'sort_order' => $existingCount + $index,
                ]);
            }
        }

        if (!empty($validated['primary_image_id'])) {
            \App\Models\ProductImage::where('product_id', $product->id)->update(['is_primary' => false]);
            \App\Models\ProductImage::where('id', $validated['primary_image_id'])
                ->where('product_id', $product->id)
                ->update(['is_primary' => true]);
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Product moved to recycle bin.');
    }
}
