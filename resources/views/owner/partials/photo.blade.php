@php
$photoPath=($pet->images->firstWhere('is_primary',true)??$pet->images->sortBy('sort_order')->first())?->image_path ?: $pet->profile_image;
$photoUrl=$photoPath?(preg_match('~^https?://~i',$photoPath)?$photoPath:(str_starts_with(ltrim($photoPath,'/'),'storage/')?asset(ltrim($photoPath,'/')):\Illuminate\Support\Facades\Storage::disk('public')->url($photoPath))):null;
@endphp
@if($photoUrl)<img class="op-pet-photo {{ $photoClass??'' }}" src="{{ $photoUrl }}" alt="{{ $pet->name }}" loading="lazy" onerror="this.hidden=true;this.nextElementSibling.hidden=false">@endif
<span class="op-pet-photo op-photo-fallback {{ $photoClass??'' }}" @if($photoUrl) hidden @endif aria-label="{{ $pet->name }}">@include('owner.partials.icon',['name'=>'paw'])</span>