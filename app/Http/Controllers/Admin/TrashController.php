<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CareContent;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TrashController extends Controller
{
    public function index(Request $request): View
    {
        $type = $request->input('type', 'all');
        $search = $request->input('search');

        $products = Product::onlyTrashed()
            ->when($search, fn($q) => $q->where('name', 'like', "%{$search}%"))
            ->with('category')
            ->latest('deleted_at')
            ->paginate(10);

        $categories = Category::onlyTrashed()
            ->when($search, fn($q) => $q->where('name', 'like', "%{$search}%"))
            ->withCount('products')
            ->latest('deleted_at')
            ->paginate(10);

        $careContents = CareContent::onlyTrashed()
            ->when($search, fn($q) => $q->where('title', 'like', "%{$search}%"))
            ->latest('deleted_at')
            ->paginate(10);

        $stats = [
            'products' => Product::onlyTrashed()->count(),
            'categories' => Category::onlyTrashed()->count(),
            'care_contents' => CareContent::onlyTrashed()->count(),
        ];

        return view('admin.trash.index', compact('products', 'categories', 'careContents', 'stats', 'type'));
    }

    public function restore(string $type, int $id): RedirectResponse
    {
        $model = $this->getModel($type);
        $item = $model::onlyTrashed()->findOrFail($id);
        $item->restore();

        return redirect()->route('admin.trash.index')
            ->with('success', ucfirst($type) . ' restored successfully.');
    }

    public function forceDelete(string $type, int $id): RedirectResponse
    {
        $model = $this->getModel($type);
        $item = $model::onlyTrashed()->findOrFail($id);

        if ($type === 'product') {
            foreach ($item->images as $image) {
                \Storage::disk('public')->delete($image->image_path);
            }
        }

        if ($type === 'category' && $item->image) {
            \Storage::disk('public')->delete($item->image);
        }

        $item->forceDelete();

        return redirect()->route('admin.trash.index')
            ->with('success', ucfirst($type) . ' permanently deleted.');
    }

    private function getModel(string $type): string
    {
        return match ($type) {
            'product' => Product::class,
            'category' => Category::class,
            'care-content' => CareContent::class,
            default => abort(404),
        };
    }
}
