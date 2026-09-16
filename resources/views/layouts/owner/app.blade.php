<!DOCTYPE html><html lang="{{ str_replace('_','-',app()->getLocale()) }}"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="csrf-token" content="{{ csrf_token() }}"><title>@yield('title','Pet Owner') · FurShield</title><link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;600;700&display=swap" rel="stylesheet">@vite(['resources/css/app.scss','resources/js/app.js'])
@include('owner.partials.styles')@stack('styles')</head><body class="op-body">
@php
$ownerUnread=\App\Support\VetNotifications::unreadCount(Auth::user());
@endphp
@include('layouts.owner.sidebar')
<div class="op-main"><header class="op-topbar"><button type="button" class="op-menu" aria-label="Toggle navigation" aria-controls="ownerSidebar" aria-expanded="true" onclick="toggleOwnerSidebar()"><i class="bi bi-list"></i></button><strong>@yield('title','Pet Owner')</strong>
@php
$ownerSearchRoute=match(true){
request()->routeIs('owner.pets.*')=>route('owner.pets.index'),
request()->routeIs('owner.health.*')=>route('owner.health.overview'),
request()->routeIs('owner.care.*')=>route('owner.care.index'),
request()->routeIs('owner.browse-vets*')=>route('owner.browse-vets'),
request()->routeIs('owner.browse-adoption','owner.adoption.*')=>route('owner.browse-adoption'),
default=>route('owner.dashboard')};
$ownerSearchLabel=match(true){
request()->routeIs('owner.pets.*')=>'Search pets by name, breed, species...',
request()->routeIs('owner.health.*')=>'Search records, pet name, or keyword...',
request()->routeIs('owner.care.*')=>'Search articles, videos, or topics...',
request()->routeIs('owner.browse-vets*')=>'Search vets, clinics, locations...',
request()->routeIs('owner.browse-adoption','owner.adoption.*')=>'Search pets, breeds, or keywords...',
default=>'Search pets, vets, products, or care tips...'};
@endphp
<form class="op-top-search" method="GET" action="{{ $ownerSearchRoute }}"><i class="bi bi-search"></i><input type="search" name="search" value="{{ is_string(request('search'))?request('search'):'' }}" aria-label="{{ $ownerSearchLabel }}" placeholder="{{ $ownerSearchLabel }}" maxlength="200">
@foreach(request()->except(['search','page','record','compare','pet_id']) as $field=>$value)
@if(is_scalar($value))<input type="hidden" name="{{ $field }}" value="{{ $value }}">@endif
@endforeach
@if(request()->routeIs('owner.health.*') && isset($pet) && $pet?->exists)<input type="hidden" name="pet_id" value="{{ $pet->id }}">@endif
<button type="submit" class="visually-hidden">Search</button></form>
<div class="op-topbar-right"><a class="op-bell" href="{{ route('owner.dashboard',['panel'=>'notifications']) }}" aria-label="Notifications, {{ $ownerUnread }} unread">@include('owner.partials.icon',['name'=>'notifications'])@if($ownerUnread)<b>{{ $ownerUnread }}</b>@endif</a><div class="dropdown"><button class="op-account" type="button" data-bs-toggle="dropdown" aria-expanded="false"><span class="op-avatar">@if(Auth::user()->profile_image)<img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url(Auth::user()->profile_image) }}" alt="" onerror="this.hidden=true">@else{{ mb_substr(Auth::user()->name,0,1) }}@endif</span><span><strong>{{ Auth::user()->name }}</strong><small>Pet Owner</small></span><i class="bi bi-chevron-down"></i></button><ul class="dropdown-menu dropdown-menu-end"><li><a class="dropdown-item" href="{{ route('profile.edit') }}">My Profile</a></li><li><a class="dropdown-item" href="{{ route('owner.cart.index') }}">My Cart</a></li><li><a class="dropdown-item" href="{{ route('owner.orders.index') }}">My Orders</a></li><li><form method="POST" action="{{ route('logout') }}">@csrf<button class="dropdown-item">Logout</button></form></li></ul></div></div></header>
<main class="op-content">@if(session('success'))<div class="alert alert-success alert-dismissible fade show" role="status">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>@endif @if(session('error'))<div class="alert alert-danger" role="alert">{{ session('error') }}</div>@endif @if($errors->any())<div class="alert alert-danger" role="alert"><strong>Please check the following:</strong><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif @if(session('status'))<div class="alert alert-success">{{ session('status')==='profile-updated'?'Profile updated successfully.':session('status') }}</div>@endif
@yield('content')</main></div>
<script>
function syncOwnerNav(){const mobile=matchMedia('(max-width: 991px)').matches;document.querySelector('.op-menu').setAttribute('aria-expanded',String(mobile?document.body.classList.contains('op-nav-open'):!document.body.classList.contains('op-nav-collapsed')));}
function toggleOwnerSidebar(){document.body.classList.toggle(matchMedia('(max-width: 991px)').matches?'op-nav-open':'op-nav-collapsed');syncOwnerNav();}
document.addEventListener('keydown',event=>{if(event.key==='Escape'){document.body.classList.remove('op-nav-open');syncOwnerNav();}});
document.addEventListener('DOMContentLoaded',()=>{syncOwnerNav();document.querySelectorAll('form[data-owner-filters] select').forEach(select=>select.addEventListener('change',()=>select.form.requestSubmit()));document.querySelector('.op-top-search input[type=search]').addEventListener('search',event=>event.target.form.requestSubmit());});
window.addEventListener('resize',syncOwnerNav);
</script>@stack('scripts')@include('partials.command-palette')</body></html>
