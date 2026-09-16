@if($item->content_type==='faq')
<details class="od-faq"><summary>{{ $item->title }}</summary><p>{{ strip_tags($item->content??'') }}</p><a href="{{ route('owner.care.show',$item) }}">Read More <i class="bi bi-arrow-right"></i></a></details>
@else
<article class="od-article">@include('owner.partials.discovery-photo',['path'=>$item->thumbnail,'label'=>$item->title,'fallbackIcon'=>$item->content_type==='video'?'play-circle-fill':'document'])<h3>{{ $item->title }}</h3><p>{{ Str::limit(strip_tags($item->content??''),140) }}</p><div class="od-meta"><span><i class="bi bi-calendar3"></i> {{ $item->created_at->format('d M Y') }}</span><span><i class="bi bi-eye-fill"></i> {{ number_format($item->views_count) }} views</span></div><a class="op-button" href="{{ route('owner.care.show',$item) }}">{{ $item->content_type==='video'?'Watch Video':'Read More' }} <i class="bi bi-arrow-right"></i></a></article>
@endif
