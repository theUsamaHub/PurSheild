<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $query = Product::with(['category', 'images'])
            ->where('status', 'active');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($categoryId = $request->input('category_id')) {
            $query->where('category_id', $categoryId);
        }

        $products = $query->latest()->paginate(15);
        $categories = Category::orderBy('name')->get();

        $activeQuery = Product::where('status', 'active');
        $stats = [
            'total'      => (clone $activeQuery)->count(),
            'in_stock'   => (clone $activeQuery)->where('stock_quantity', '>', 0)->count(),
            'out_stock'  => (clone $activeQuery)->where('stock_quantity', '<=', 0)->count(),
            'on_sale'    => (clone $activeQuery)->whereNotNull('special_price')->where('special_price', '>', 0)->count(),
        ];

        return view('owner.products.index', compact('products', 'categories', 'stats'));
    }

    public function show(Product $product): View
    {
        if ($product->status !== 'active') {
            abort(404);
        }

        $product->load(['category', 'images', 'reviews' => function ($q) {
            $q->with('user')->latest();
        }]);

        return view('owner.products.show', compact('product'));
    }
}
