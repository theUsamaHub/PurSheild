<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Product;
use App\Models\CareContent;
use App\Models\ShelterProfile;
use App\Models\VetProfile;

class PublicController extends Controller
{
    public function home()
    {
        $vetsCount = User::whereHas('roles', function($q){ $q->where('slug', 'vet'); })->count();
        $sheltersCount = User::whereHas('roles', function($q){ $q->where('slug', 'shelter'); })->count();
        $productsCount = Product::count();
        $petsCount = \App\Models\Pet::count();

        $vets = User::where('status', 'active')->whereHas('roles', function($q){ $q->where('slug', 'vet'); })->with('vetProfile')->take(4)->get();
        $shelters = User::where('status', 'active')->whereHas('roles', function($q){ $q->where('slug', 'shelter'); })->with('shelterProfile')->take(4)->get();
        $products = Product::where('status', 'active')->with('images')->take(6)->get();
        
        return view('home', compact('vets', 'shelters', 'products', 'vetsCount', 'sheltersCount', 'productsCount', 'petsCount'));
    }

    public function about()
    {
        return view('public.about');
    }

    public function products(Request $request)
    {
        $query = Product::where('status', 'active')->with(['category', 'images']);

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $categories = \App\Models\Category::where('is_active', true)->get();
        $products = $query->paginate(12)->withQueryString();

        return view('public.products', compact('products', 'categories'));
    }

    public function care()
    {
        $careContents = CareContent::where('status', 'active')->paginate(10);
        return view('public.care', compact('careContents'));
    }

    public function vets()
    {
        $vets = User::where('status', 'active')->whereHas('roles', function($q){ $q->where('slug', 'vet'); })->with('vetProfile')->paginate(12);
        return view('public.vets', compact('vets'));
    }

    public function shelters()
    {
        $shelters = User::where('status', 'active')->whereHas('roles', function($q){ $q->where('slug', 'shelter'); })->with('shelterProfile')->paginate(12);
        return view('public.shelters', compact('shelters'));
    }

    public function contact()
    {
        return view('public.contact');
    }
}
