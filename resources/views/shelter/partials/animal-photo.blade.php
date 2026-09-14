@php
    $imagePath=$animal?->images?->first()?->image_path;
    $photo=null;
    if($imagePath) {
        $photo=preg_match('~^https?://~i',$imagePath)?$imagePath:(str_starts_with($imagePath,'storage/')||str_starts_with($imagePath,'/storage/')?asset(ltrim($imagePath,'/')):\Illuminate\Support\Facades\Storage::disk('public')->url($imagePath));
    }
@endphp
@if($photo)<img src="{{ $photo }}" class="sh-animal-photo {{ $photoClass ?? '' }}" alt="{{ $animal->pet_name }}" loading="lazy" onerror="this.hidden=true;this.nextElementSibling.hidden=false">@endif
<span class="sh-animal-photo sh-photo-fallback {{ $photoClass ?? '' }}" @if($photo) hidden @endif aria-label="{{ $animal?->pet_name ?? 'Animal' }}">@include('shelter.partials.icon',['name'=>'paw'])</span>
