@php
$assetPath=$path??null;
$assetUrl=$assetPath?(preg_match('~^https?://~i',$assetPath)?$assetPath:(str_starts_with(ltrim($assetPath,'/'),'storage/')?asset(ltrim($assetPath,'/')):\Illuminate\Support\Facades\Storage::disk('public')->url($assetPath))):null;
@endphp
@if($assetUrl)<img class="{{ $photoClass??'od-photo' }}" src="{{ $assetUrl }}" alt="{{ $label??'' }}" loading="lazy" onerror="this.hidden=true;this.nextElementSibling.hidden=false">@endif
<span class="{{ $photoClass??'od-photo' }} od-photo-placeholder" @if($assetUrl) hidden @endif aria-label="{{ $label??'Photo not available' }}">@include('owner.partials.icon',['name'=>$fallbackIcon??'paw'])</span>
