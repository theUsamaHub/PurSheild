<!DOCTYPE html>
<html lang="{{ str_replace('_','-',app()->getLocale()) }}"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="csrf-token" content="{{ csrf_token() }}"><title>@yield('title','Shelter Profile') · FurShield</title><link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;600;700;800&family=Caveat:wght@400;500&display=swap" rel="stylesheet">
@vite(['resources/css/app.scss','resources/js/app.js'])
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.css" integrity="sha384-Bk5cbLkZQ5raZ0+H2/+VbfYx3WpvxvQK4zqXZr7sYODuaX7bKXoSOnipQxkaS8sv" crossorigin="anonymous">
@include('shelter.partials.styles') @stack('styles')</head>
<body class="sh-body">
@php($shelterName=Auth::user()->shelterProfile?->shelter_name ?: Auth::user()->name)
@php($unreadCount=\App\Support\VetNotifications::unreadCount(Auth::user()))
@include('layouts.shelter.sidebar')
<div class="sh-main"><header class="sh-topbar"><button type="button" class="sh-menu" onclick="toggleShelterSidebar()" aria-label="Toggle navigation" aria-controls="shelterSidebar" aria-expanded="true">@include('shelter.partials.icon',['name'=>'menu'])</button><strong>@yield('title','Shelter Profile')</strong><div class="sh-topbar-right"><a href="{{ route('shelter.notifications.index') }}" class="sh-bell" aria-label="Notifications, {{ $unreadCount }} unread">@include('shelter.partials.icon',['name'=>'notifications'])@if($unreadCount)<span>{{ $unreadCount }}</span>@endif</a><div class="dropdown"><button class="sh-account" type="button" data-bs-toggle="dropdown" aria-expanded="false"><span class="sh-shelter-avatar">@if(Auth::user()->profile_image)<img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url(Auth::user()->profile_image) }}" alt="">@else 🏡 @endif</span><span>{{ $shelterName }}</span><i class="bi bi-chevron-down"></i></button><ul class="dropdown-menu dropdown-menu-end"><li><a class="dropdown-item" href="{{ route('profile.edit') }}">Shelter Profile</a></li><li><a class="dropdown-item" href="{{ route('shelter.reviews.index') }}">My Reviews</a></li><li><form method="POST" action="{{ route('logout') }}">@csrf<button class="dropdown-item">Logout</button></form></li></ul></div></div></header>
<main class="sh-content">
@if(session('success'))<div class="alert alert-success alert-dismissible fade show" role="status">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>@endif
@if(session('error'))<div class="alert alert-danger" role="alert">{{ session('error') }}</div>@endif
@if(session('status'))<div class="alert alert-success" role="status">{{ session('status')==='profile-updated'?'Profile updated successfully.':session('status') }}</div>@endif
@if($errors->any())<div class="alert alert-danger" role="alert"><strong>Please check the following:</strong><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
@yield('content')</main></div>
<script>
function toggleShelterSidebar(){const mobile=window.matchMedia('(max-width: 991px)').matches;document.body.classList.toggle(mobile?'sh-nav-open':'sh-nav-collapsed');document.querySelector('.sh-menu').setAttribute('aria-expanded',String(mobile?document.body.classList.contains('sh-nav-open'):!document.body.classList.contains('sh-nav-collapsed')));}
document.addEventListener('keydown',event=>{if(event.key==='Escape'){document.body.classList.remove('sh-nav-open');if(window.matchMedia('(max-width: 991px)').matches)document.querySelector('.sh-menu').setAttribute('aria-expanded','false');}});
document.addEventListener('DOMContentLoaded',()=>{
document.querySelector('.sh-menu').setAttribute('aria-expanded',String(!window.matchMedia('(max-width: 991px)').matches));
document.querySelectorAll('form[data-filters]').forEach(form=>{
form.querySelectorAll('select,input[type=date]').forEach(input=>input.addEventListener('change',()=>{if(input.name==='species_id'){const breed=form.querySelector('[name=breed_id]');if(breed)breed.value='';}if(input.name==='period'){const dates=form.querySelector('[data-custom-dates]');if(dates){dates.hidden=input.value!=='custom';if(input.value==='custom'){dates.querySelector('input').focus();return;}}form.querySelectorAll('input[type=date]').forEach(date=>date.value='');}form.requestSubmit();}));
form.querySelectorAll('input[type=search]').forEach(input=>input.addEventListener('search',()=>form.requestSubmit()));
});
});
</script>@stack('scripts')</body></html>
