<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CareContent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CareContentController extends Controller
{
    public function index(Request $request): View
    {
        $query = CareContent::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        if ($category = $request->input('category')) {
            $query->where('category', $category);
        }

        if ($contentType = $request->input('content_type')) {
            $query->where('content_type', $contentType);
        }

        $careContents = $query->latest()->paginate(15);

        $counts = CareContent::selectRaw("count(*) as total")
            ->selectRaw("count(case when content_type = 'article' then 1 end) as article_count")
            ->selectRaw("count(case when content_type = 'video' then 1 end) as video_count")
            ->selectRaw("count(case when content_type = 'faq' then 1 end) as faq_count")
            ->first();

        $stats = [
            'total' => $counts->total,
            'articles' => $counts->article_count,
            'videos' => $counts->video_count,
            'faqs' => $counts->faq_count,
        ];

        return view('admin.care-content.index', compact('careContents', 'stats'));
    }

    public function create(): View
    {
        return view('admin.care-content.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'in:feeding,grooming,hygiene,exercise,vaccination,health,training'],
            'content_type' => ['required', 'string', 'in:article,video,faq'],
            'content' => ['required', 'string'],
            'media_url' => ['nullable', 'url', 'max:2048'],
            'thumbnail' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'string', 'in:active,inactive'],
        ], [
            'title.required' => 'The title field is required.',
            'title.max' => 'The title must not exceed 255 characters.',
            'category.required' => 'Please select a category.',
            'category.in' => 'The selected category is invalid.',
            'content_type.required' => 'Please select a content type.',
            'content_type.in' => 'The selected content type is invalid.',
            'content.required' => 'The content field is required.',
            'media_url.url' => 'The media URL must be a valid URL.',
            'status.required' => 'Please select a status.',
            'status.in' => 'The selected status is invalid.',
        ]);

        CareContent::create($validated);

        return redirect()->route('admin.care-content.index')
            ->with('success', 'Care content created successfully.');
    }

    public function edit(CareContent $careContent): View
    {
        return view('admin.care-content.edit', ['careContent' => $careContent]);
    }

    public function update(Request $request, CareContent $careContent): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'in:feeding,grooming,hygiene,exercise,vaccination,health,training'],
            'content_type' => ['required', 'string', 'in:article,video,faq'],
            'content' => ['required', 'string'],
            'media_url' => ['nullable', 'url', 'max:2048'],
            'thumbnail' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'string', 'in:active,inactive'],
        ], [
            'title.required' => 'The title field is required.',
            'title.max' => 'The title must not exceed 255 characters.',
            'category.required' => 'Please select a category.',
            'category.in' => 'The selected category is invalid.',
            'content_type.required' => 'Please select a content type.',
            'content_type.in' => 'The selected content type is invalid.',
            'content.required' => 'The content field is required.',
            'media_url.url' => 'The media URL must be a valid URL.',
            'status.required' => 'Please select a status.',
            'status.in' => 'The selected status is invalid.',
        ]);

        $careContent->update($validated);

        return redirect()->route('admin.care-content.index')
            ->with('success', 'Care content updated successfully.');
    }

    public function destroy(CareContent $careContent): RedirectResponse
    {
        $careContent->delete();

        return redirect()->route('admin.care-content.index')
            ->with('success', 'Care content deleted successfully.');
    }
}
