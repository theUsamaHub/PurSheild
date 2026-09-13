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

        $categories = $query->orderBy('name')->paginate(15);

        return view('admin.product-categories.index', compact('categories'));
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
            'is_active' => ['boolean'],
        ], [
            'name.required' => 'Category name is required.',
            'name.unique' => 'A category with this name already exists.',
            'slug.unique' => 'A category with this slug already exists.',
        ]);

        $data = $validated;
        $data['slug'] = $validated['slug'] ?: Str::slug($validated['name']);
        $data['created_by'] = auth()->id();
        $data['updated_by'] = auth()->id();

        if (!isset($data['is_active'])) {
            $data['is_active'] = false;
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
            'is_active' => ['boolean'],
        ], [
            'name.required' => 'Category name is required.',
            'name.unique' => 'A category with this name already exists.',
        ]);

        $data = $validated;
        $data['updated_by'] = auth()->id();

        if (!isset($data['is_active'])) {
            $data['is_active'] = false;
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
            ->with('success', 'Product category deleted successfully.');
    }
}
