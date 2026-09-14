@php
    $primaryImage = $pet->images->firstWhere('is_primary', true) ?? $pet->images->first();
    $path = $primaryImage?->image_path ?: $pet->profile_image;
    $photoUrl = null;
    if ($path) {
        if (preg_match('~^https?://~i', $path)) {
            $photoUrl = $path;
        } elseif (str_starts_with($path, '/storage/') || str_starts_with($path, 'storage/')) {
            $photoUrl = asset(ltrim($path, '/'));
        } elseif (!str_contains($path, '/')) {
            $photoUrl = asset('uploads/pets/'.$path);
        } else {
            $photoUrl = \Illuminate\Support\Facades\Storage::disk('public')->url($path);
        }
    }
@endphp
@if($photoUrl)
    <img class="vs-pet-avatar" src="{{ $photoUrl }}" alt="{{ $pet->name }}" loading="lazy" onerror="this.hidden=true;this.nextElementSibling.hidden=false;">
@endif
<span class="vs-pet-avatar" @if($photoUrl) hidden @endif aria-label="{{ $pet->name }}">{{ mb_substr($pet->name,0,1) }}</span>
