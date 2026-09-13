<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\CareContent;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CareController extends Controller
{
    public function index(Request $request): View
    {
        $query = CareContent::where('status', 'active');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        if ($contentType = $request->input('content_type')) {
            $query->where('content_type', $contentType);
        }

        if ($category = $request->input('category')) {
            $query->where('category', $category);
        }

        $careContents = $query->latest()->paginate(15);

        return view('owner.care.index', compact('careContents'));
    }

    public function show(CareContent $careContent): View
    {
        if ($careContent->status !== 'active') {
            abort(404);
        }

        return view('owner.care.show', ['careContent' => $careContent]);
    }
}
