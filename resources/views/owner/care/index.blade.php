@extends('layouts.owner.app')
@section('title', 'Pet Care')
@push('styles')
@include('owner.partials.discovery-styles')
@endpush
@section('content')
<div class="od-page">
<div class="op-breadcrumb"><a href="{{ route('owner.dashboard') }}"><i class="bi bi-house-door-fill"></i> Dashboard</a><i class="bi bi-chevron-right"></i><span>Pet Care</span></div>
<div class="op-heading"><div><h1>@include('owner.partials.icon',['name'=>'lightbulb-fill']) Pet Care</h1><p>Helpful tips, expert advice, and resources to keep your pets healthy and happy.</p></div><div class="od-heading-art">@include('owner.partials.icon',['name'=>'paw'])<p>“Knowledge today,<br>for healthier tomorrows.”</p><img src="{{ asset('images/owner/companions-banner.png') }}" alt=""></div></div>
<nav class="od-care-tabs" aria-label="Pet care categories">
@foreach([['feeding','Feeding','feeding','green'],['grooming','Grooming','scissors','pink'],['hygiene','Hygiene','shield-shaded','blue'],['exercise','Exercise','exercise','amber'],['vaccination','Vaccination','vaccination','purple'],['health','General Health','heart','green'],['faq','FAQs','question-circle-fill','pink']] as [$value,$label,$icon,$tone])
<a class="od-tone-{{ $tone }} {{ $category===$value?'active':'' }}" href="{{ route('owner.care.index',array_merge(request()->except(['page','category','content_type']),$category===$value?[]:['category'=>$value])) }}" @if($category===$value) aria-current="page" @endif>@include('owner.partials.icon',['name'=>$icon]){{ $label }}</a>
@endforeach
</nav>
@if(request()->filled('search')||$category||request()->filled('content_type'))<div class="od-filter-notice">Showing {{ $category?ucfirst($category):'all categories' }}{{ request('search')?' matching “'.request('search').'”':'' }}. <a href="{{ route('owner.care.index') }}">Clear Filters</a></div>@endif
<div class="od-layout"><div>
@if($careContents)
<section class="od-section"><div class="od-section-header"><h2>{{ request('content_type')==='faq'?'Frequently Asked Questions':(request('content_type')==='video'?'Educational Videos':'All Articles') }}</h2><a href="{{ route('owner.care.index',request()->except(['page','content_type'])) }}">Back to overview</a></div><div class="{{ request('content_type')==='faq'?'':'od-articles' }}">
@forelse($careContents as $item)@include('owner.care.card',['item'=>$item])@empty<div class="op-empty">No resources match your filters.</div>@endforelse
</div>@include('owner.partials.pagination',['items'=>$careContents])</section>
@else
<section class="od-section"><div class="od-section-header"><h2>@include('owner.partials.icon',['name'=>'document']) Featured Articles</h2><a href="{{ route('owner.care.index',array_merge(request()->except('page'),['content_type'=>'article'])) }}">View All <i class="bi bi-arrow-right"></i></a></div><div class="od-articles">@forelse($articles as $item)@include('owner.care.card',['item'=>$item])@empty<div class="op-empty">No articles match this category yet.</div>@endforelse</div></section>
<section class="od-section"><div class="od-section-header"><h2>@include('owner.partials.icon',['name'=>'play-circle-fill']) Educational Videos</h2><a href="{{ route('owner.care.index',array_merge(request()->except('page'),['content_type'=>'video'])) }}">View All <i class="bi bi-arrow-right"></i></a></div><div class="od-videos">@forelse($videos as $item)<a class="od-video" href="{{ route('owner.care.show',$item) }}"><div class="od-video-cover">@include('owner.partials.discovery-photo',['path'=>$item->thumbnail,'label'=>$item->title,'fallbackIcon'=>'play-circle-fill'])<b><i class="bi bi-play-circle-fill"></i></b></div><strong>{{ $item->title }}</strong><p>{{ Str::limit(strip_tags($item->content??''),65) }}</p></a>@empty<div class="op-empty">No videos match this category yet.</div>@endforelse</div></section>
@if($faqs->isNotEmpty())<section class="od-section"><div class="od-section-header"><h2>Frequently Asked Questions</h2><a href="{{ route('owner.care.index',array_merge(request()->except('page'),['content_type'=>'faq'])) }}">View All</a></div>@foreach($faqs as $item)@include('owner.care.card',['item'=>$item])@endforeach</section>@endif
@endif
@include('owner.partials.discovery-banner',['bannerTitle'=>'Happy Pets','bannerSubtitle'=>'Happier Lives ♥','bannerMessage'=>'Learn. Care. Grow Together.'])
</div><aside class="od-aside">
<section class="od-side-card od-quick-tips"><header><h2>@include('owner.partials.icon',['name'=>'lightbulb-fill']) Quick Tips</h2><a href="{{ route('owner.care.index',['content_type'=>'faq']) }}">View All</a></header><ul>@foreach(['Keep fresh water available.','Follow your pet’s recommended feeding plan.','Keep vaccination records up to date.','Choose grooming suited to your pet’s coat.','Make time for suitable exercise and play.'] as $tip)<li><i class="bi bi-check-circle-fill"></i>{{ $tip }}</li>@endforeach</ul></section>
<a class="od-info-card" href="{{ route('owner.care.index',['content_type'=>'article']) }}">@include('owner.partials.icon',['name'=>'book-fill'])<div><h2>Pet Care Resources</h2><p>Explore our collection of guides, articles, and expert tips to help you give the best care to your pets.</p><i class="bi bi-arrow-right-circle-fill float-end fs-4"></i></div></a>
<section class="od-side-card"><h2>@include('owner.partials.icon',['name'=>'fire']) Popular Topics</h2><div class="od-topics">@forelse($popular as $item)<a href="{{ route('owner.care.show',$item) }}"><i class="bi bi-heart-fill"></i>{{ $item->title }}<i class="bi bi-chevron-right"></i></a>@empty<p class="op-empty">Published resources will appear here.</p>@endforelse</div></section>
</aside></div>
</div>
@include('owner.partials.discovery-scripts')
@endsection
