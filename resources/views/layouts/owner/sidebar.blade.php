<aside class="op-sidebar" id="ownerSidebar">
<a class="op-brand" href="{{ route('owner.dashboard') }}">@include('owner.partials.icon',['name'=>'paw'])<span><strong>FurShield</strong><small>Every Paw Deserves a Shield</small></span></a><div class="op-panel-label">Pet Owner Panel</div>
<nav class="op-nav" aria-label="Owner navigation">
@foreach([
 ['dashboard','Dashboard',route('owner.dashboard'),request()->routeIs('owner.dashboard')&&!request('panel')],
 ['paw','My Pets',route('owner.pets.index'),request()->routeIs('owner.pets.*')&&request('view')!=='health'],
 ['heart','Health Records',route('owner.health.overview'),request()->routeIs('owner.health.*')||request('view')==='health'],
 ['calendar','Appointments',route('owner.appointments.index'),request()->routeIs('owner.appointments.*')],
 ['vet','Find Veterinarians',route('owner.browse-vets'),request()->routeIs('owner.browse-vets*')],
 ['dashboard','Adopt a Pet',route('owner.browse-adoption'),request()->routeIs('owner.browse-adoption','owner.adoption.*')],
 ['lightbulb-fill','Pet Care',route('owner.care.index'),request()->routeIs('owner.care.*')],
 ['cart-fill','Products / Shop',route('owner.products.index'),request()->routeIs('owner.products.*','owner.cart.*','owner.orders.*')],
 ['notifications','Notifications',route('owner.dashboard',['panel'=>'notifications']),request('panel')==='notifications'],
 ['person-fill','My Profile',route('profile.edit'),request()->routeIs('profile.*')]
] as [$icon,$label,$url,$active])<a href="{{ $url }}" class="{{ $active?'active':'' }}" @if($active) aria-current="page" @endif><span class="op-nav-icon">@include('owner.partials.icon',['name'=>$icon])@if($label==='Notifications'&&$ownerUnread)<b>{{ $ownerUnread }}</b>@endif</span><span>{{ $label }}</span></a>@endforeach
</nav><form class="op-logout" method="POST" action="{{ route('logout') }}">@csrf<button type="submit">@include('owner.partials.icon',['name'=>'logout']) Logout</button></form><div class="op-sidebar-art">@include('owner.partials.companions')<p>Happier Pets<br>Happier People ♥</p></div>
</aside><button class="op-overlay" id="ownerOverlay" type="button" aria-label="Close navigation" onclick="toggleOwnerSidebar()"></button>
