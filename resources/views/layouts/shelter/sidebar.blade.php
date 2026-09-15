<aside class="sh-sidebar" id="shelterSidebar">
<a class="sh-brand" href="{{ route('shelter.dashboard') }}">@include('shelter.partials.icon',['name'=>'paw'])<span><strong>FurShield</strong><small>Shelter Panel</small></span></a>
<nav class="sh-nav" aria-label="Shelter navigation">
@foreach([
 ['dashboard','Dashboard','shelter.dashboard','shelter.dashboard'], ['paw','My Animals','shelter.listings.index','shelter.listings.*'],
 ['care','Care Records','shelter.care-status.index','shelter.care-status.*'], ['requests','Adoption Requests','shelter.applications.index','shelter.applications.*'],
 ['history','Adoption History','shelter.history.index','shelter.history.*'], ['notifications','Notifications','shelter.notifications.index','shelter.notifications.*'],
 ['star-fill','My Reviews','shelter.reviews.index','shelter.reviews.*'],
 ['profile','Shelter Profile','profile.edit','profile.*']
] as [$icon,$label,$route,$active])
<a href="{{ route($route) }}" class="{{ request()->routeIs($active)?'active':'' }}" @if(request()->routeIs($active)) aria-current="page" @endif>@include('shelter.partials.icon',['name'=>$icon])<span>{{ $label }}</span></a>
@endforeach
</nav>
<div class="sh-sidebar-bottom"><div class="sh-silhouette" aria-hidden="true"><svg viewBox="0 0 200 118" fill="currentColor"><path d="M20 99C-5 99 3 73 14 69c-8 13-9 22 6 21 1-31 17-42 33-56L68 13c7-7 15-5 18 4l16 4-4 14-15 5-5 26 10 35 9 4v6H64l-3-27-11 27H21z"/><path d="M113 110c-6-4-8-11-5-22l5-22 1-23 11 9 12-2 11-9 2 25c13 7 19 25 14 35 22 2 28-13 19-22 19 8 11 31-12 33h-37l-5-21-5 21z"/></svg></div><p>More Happy Tails<br>Brighter Tomorrows ♥</p><form action="{{ route('logout') }}" method="POST">@csrf<button type="submit">@include('shelter.partials.icon',['name'=>'logout']) Logout</button></form></div>
</aside><button id="shelterOverlay" class="sh-overlay" aria-label="Close navigation" type="button" onclick="toggleShelterSidebar()"></button>
