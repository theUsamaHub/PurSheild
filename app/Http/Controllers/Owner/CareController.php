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
        $f = $request->validate(['search' => ['nullable', 'string', 'max:200'], 'category' => ['nullable', 'in:feeding,grooming,hygiene,exercise,vaccination,health,training,faq'], 'content_type' => ['nullable', 'in:article,video,faq']]);
        $query = CareContent::where('status', 'active');
        $search = trim($f['search'] ?? '');
        if ($search !== '') {
            $query->where(fn ($q) => $q->where('title', 'like', "%{$search}%")->orWhere('content', 'like', "%{$search}%"));
        }
        $category = $f['category'] ?? '';
        if ($category === 'faq') {
            $query->where('content_type', 'faq');
        } elseif ($category === 'grooming') {
            $query->where(function ($q) {
                $q->where('category', 'grooming')->orWhere(function ($legacy) {
                    $legacy->where('category', 'hygiene')->where(function ($text) {
                        foreach (['groom', 'brush', 'bath', 'coat', 'nail'] as $term) {
                            $text->orWhere('title', 'like', '%'.$term.'%')->orWhere('content', 'like', '%'.$term.'%');
                        }
                    });
                });
            });
        } elseif ($category === 'vaccination') {
            $query->where(fn ($q) => $q->where('category', 'vaccination')->orWhere(fn ($legacy) => $legacy->where('category', 'health')->where(fn ($text) => $text->where('title', 'like', '%vaccin%')->orWhere('content', 'like', '%vaccin%'))));
        } elseif ($category !== '') {
            $query->where('category', $category);
        }
        if ($f['content_type'] ?? null) {
            $query->where('content_type', $f['content_type']);
        }
        $articles = (clone $query)->where('content_type', 'article')->latest()->limit(3)->get();
        $videos = (clone $query)->where('content_type', 'video')->latest()->limit(4)->get();
        $faqs = (clone $query)->where('content_type', 'faq')->latest()->limit(6)->get();
        $careContents = ($f['content_type'] ?? null) ? (clone $query)->latest()->orderByDesc('id')->paginate(12)->withQueryString() : null;
        $popular = CareContent::where('status', 'active')->orderByDesc('views_count')->latest()->limit(5)->get();

        return view('owner.care.index', compact('articles', 'videos', 'faqs', 'careContents', 'popular', 'category'));
    }

    public function show(Request $request, CareContent $careContent): View
    {
        abort_unless($careContent->status === 'active', 404);
        $key = 'care_viewed.'.$careContent->id;
        if (! $request->session()->has($key)) {
            $careContent->increment('views_count');
            $request->session()->put($key, true);
        }
        $videoUrl = null;
        $isEmbed = false;
        if ($careContent->media_url && in_array(strtolower(parse_url($careContent->media_url, PHP_URL_SCHEME) ?? ''), ['https', 'http'])) {
            $host = strtolower(parse_url($careContent->media_url, PHP_URL_HOST) ?? '');
            parse_str(parse_url($careContent->media_url, PHP_URL_QUERY) ?? '', $params);
            $path = parse_url($careContent->media_url, PHP_URL_PATH) ?? '';
            $id = in_array($host, ['youtube.com', 'www.youtube.com', 'm.youtube.com']) ? ($params['v'] ?? (str_starts_with($path, '/embed/') ? substr($path, 7) : null)) : ($host === 'youtu.be' ? trim($path, '/') : null);
            if (is_string($id) && preg_match('/^[a-zA-Z0-9_-]{11}$/', $id)) {
                $videoUrl = 'https://www.youtube-nocookie.com/embed/'.$id;
                $isEmbed = true;
            } elseif (preg_match('/\.(mp4|webm|ogg)$/i',$path)) {
                $videoUrl = $careContent->media_url;
            }
        }

        return view('owner.care.show',compact('careContent','videoUrl','isEmbed'));
    }
}
