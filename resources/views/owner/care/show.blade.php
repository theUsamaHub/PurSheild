@extends('layouts.owner.app')
@section('title', 'Pet Care')
@push('styles')
@include('owner.partials.discovery-styles')
@endpush
@section('content')
<div class="od-page">
<div class="op-breadcrumb"><a href="{{ route('owner.dashboard') }}"><i class="bi bi-house-door-fill"></i> Dashboard</a><i class="bi bi-chevron-right"></i><span>Pet Care</span></div>
<article class="od-care-detail"><div class="op-heading"><div><h1>{{ $careContent->title }}</h1><p>{{ ucfirst($careContent->category) }} · {{ $careContent->created_at->format('d M Y') }} · {{ number_format($careContent->views_count) }} views</p></div></div><section class="od-section">
@if($videoUrl)
@if($isEmbed)<iframe class="od-player" src="{{ $videoUrl }}" title="{{ $careContent->title }}" allow="fullscreen; picture-in-picture" allowfullscreen referrerpolicy="strict-origin-when-cross-origin"></iframe>
@else<video class="od-player" controls preload="metadata" src="{{ $videoUrl }}">Your browser cannot play this video.</video>@endif
@elseif($careContent->content_type==='video' && filter_var($careContent->media_url,FILTER_VALIDATE_URL) && in_array(strtolower(parse_url($careContent->media_url,PHP_URL_SCHEME)),['http','https']))<a class="op-button op-primary" href="{{ $careContent->media_url }}" target="_blank" rel="noopener noreferrer">Watch Video <i class="bi bi-box-arrow-up-right"></i></a>
@elseif($careContent->thumbnail)@include('owner.partials.discovery-photo',['path'=>$careContent->thumbnail,'label'=>$careContent->title])@endif
<div class="od-care-copy">{{ html_entity_decode(strip_tags(preg_replace('~</(?:p|div|h[1-6]|li)>|<br\s*/?>~i',"\n",$careContent->content??'')),ENT_QUOTES,'UTF-8') }}</div><a class="op-button" href="{{ route('owner.care.index') }}">← Back to Pet Care</a></section></article>
</div>
@include('owner.partials.discovery-scripts')
@endsection
