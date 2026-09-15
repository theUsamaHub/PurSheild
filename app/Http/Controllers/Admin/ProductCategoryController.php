<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Str;

class ProductCategoryController extends Controller
{
    public function index(Request $request): View
    {
        $query = Category::withCount('products');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        if (($active = $request->input('active')) !== null && $active !== '') {
            $query->where('is_active', (int) $active);
        }

        $categories = $query->orderBy('sort_order')->orderBy('name')->paginate(15)->withQueryString();

        $stats = [
            'total' => Category::count(),
            'active' => Category::where('is_active', true)->count(),
            'inactive' => Category::where('is_active', false)->count(),
            'products' => \App\Models\Product::count(),
        ];

        return view('admin.product-categories.index', compact('categories', 'stats'));
    }

    public function show(Category $category): View
    {
        $category->loadCount('products');
        $products = $category->products()->with('images')->latest()->paginate(12);

        return view('admin.product-categories.show', compact('category', 'products'));
    }

    public function create(): View
    {
        return view('admin.product-categories.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:categories,slug'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'file', 'max:2048', 'mimes:jpg,jpeg,png,gif,webp'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ], [
            'name.required' => 'Category name is required.',
            'name.unique' => 'A category with this name already exists.',
            'slug.unique' => 'A category with this slug already exists.',
            'image.max' => 'Image must not exceed 2MB.',
            'image.mimes' => 'Image must be a JPG, PNG, GIF, or WebP file.',
        ]);

        $data = $validated;
        $data['slug'] = $validated['slug'] ?: Str::slug($validated['name']);
        $data['created_by'] = auth()->id();
        $data['updated_by'] = auth()->id();

        if (!isset($data['is_active'])) {
            $data['is_active'] = false;
        }

        if (!isset($data['sort_order'])) {
            $data['sort_order'] = 0;
        }

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('uploads/categories', 'public');
        }

        Category::create($data);

        return redirect()->route('admin.product-categories.index')
            ->with('success', 'Product category created successfully.');
    }

    public function edit(Category $category): View
    {
        return view('admin.product-categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name,' . $category->id],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'file', 'max:2048', 'mimes:jpg,jpeg,png,gif,webp'],
            'remove_image' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ], [
            'name.required' => 'Category name is required.',
            'name.unique' => 'A category with this name already exists.',
            'image.max' => 'Image must not exceed 2MB.',
            'image.mimes' => 'Image must be a JPG, PNG, GIF, or WebP file.',
        ]);

        $data = $validated;
        $data['updated_by'] = auth()->id();

        if (!isset($data['is_active'])) {
            $data['is_active'] = false;
        }

        if (!isset($data['sort_order'])) {
            $data['sort_order'] = $category->sort_order;
        }

        if (!empty($validated['remove_image']) && $category->image) {
            \Storage::disk('public')->delete($category->image);
            $data['image'] = null;
            unset($data['remove_image']);
        } else {
            unset($data['remove_image']);
        }

        if ($request->hasFile('image')) {
            if ($category->image) {
                \Storage::disk('public')->delete($category->image);
            }
            $data['image'] = $request->file('image')->store('uploads/categories', 'public');
        }

        $category->update($data);

        return redirect()->route('admin.product-categories.index')
            ->with('success', 'Product category updated successfully.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->products()->count() > 0) {
            return redirect()->route('admin.product-categories.index')
                ->with('error', 'Cannot delete category. It has products assigned to it.');
        }

        $category->delete();

        return redirect()->route('admin.product-categories.index')
            ->with('success', 'Category moved to recycle bin.');
    }

    public function bulkAction(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', 'exists:categories,id'],
            'action' => ['required', 'in:activate,deactivate'],
        ]);

        $categories = Category::whereIn('id', $validated['ids']);

        switch ($validated['action']) {
            case 'activate':
                $categories->update(['is_active' => true]);
                $message = count($validated['ids']) . ' category(ies) activated.';
                break;
            case 'deactivate':
                $categories->update(['is_active' => false]);
                $message = count($validated['ids']) . ' category(ies) deactivated.';
                break;
        }

        return redirect()->route('admin.product-categories.index')
            ->with('success', $message);
    }
}
