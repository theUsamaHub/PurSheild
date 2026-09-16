@extends('layouts.owner.app')
@section('title', $pet->name)
@push('styles')
@include('owner.partials.discovery-styles')
<style>
.pet-photo-hero{position:relative;width:100%;max-height:420px;border-radius:10px;overflow:hidden;background:#f0f7f4;cursor:zoom-in;}
.pet-photo-hero img{width:100%;max-height:420px;object-fit:contain;display:block;background:#f6f9fc;}
.pet-photo-hero:hover img{opacity:.92;}
.pet-photo-hero .pet-initials{width:100%;height:280px;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,#e2f5ed,#f3fdfa);font-size:64px;font-weight:700;color:#00865e;}
.pet-thumbs{display:flex;gap:6px;flex-wrap:wrap;margin-top:8px;}
.pet-thumb{width:60px;height:60px;border-radius:6px;object-fit:cover;border:2px solid transparent;cursor:pointer;opacity:.6;transition:border-color .15s,opacity .15s;}
.pet-thumb:hover,.pet-thumb.active{border-color:#00865e;opacity:1;}
.pet-info-grid{display:grid;grid-template-columns:1fr 1fr;gap:0;}
.pet-info-item{padding:10px 0;border-bottom:1px solid #edf2f7;}
.pet-info-item:nth-child(odd){padding-right:16px;}
.pet-info-item:nth-child(even){padding-left:16px;}
.pet-info-label{font-size:11px;color:#52699b;margin-bottom:3px;}
.pet-info-value{font-size:13px;font-weight:600;color:#0a1648;}
.od-lightbox{position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,.88);display:flex;align-items:center;justify-content:center;opacity:0;pointer-events:none;transition:opacity .25s ease;}
.od-lightbox.is-open{opacity:1;pointer-events:auto;}
.od-lightbox img{max-width:92vw;max-height:88vh;border-radius:8px;box-shadow:0 8px 40px rgba(0,0,0,.45);object-fit:contain;}
.od-lightbox-close{position:absolute;top:16px;right:20px;width:40px;height:40px;border-radius:50%;border:0;background:rgba(255,255,255,.15);color:#fff;font-size:22px;cursor:pointer;display:flex;align-items:center;justify-content:center;z-index:1;}
.od-lightbox-close:hover{background:rgba(255,255,255,.3);}
.od-lightbox-nav{position:absolute;top:50%;transform:translateY(-50%);width:44px;height:44px;border-radius:50%;border:0;background:rgba(255,255,255,.12);color:#fff;font-size:20px;cursor:pointer;display:flex;align-items:center;justify-content:center;z-index:1;}
.od-lightbox-nav:hover{background:rgba(255,255,255,.28);}
.od-lightbox-prev{left:16px;}
.od-lightbox-next{right:16px;}
.od-lightbox-counter{position:absolute;bottom:16px;left:50%;transform:translateX(-50%);color:rgba(255,255,255,.7);font-size:13px;font-weight:500;}
</style>
@endpush
@section('content')
@php
$primaryImage=$pet->images->firstWhere('is_primary',true)??$pet->images->sortBy('sort_order')->first();
$sortedImages=$pet->images->sortBy('sort_order')->values();
$storageUrl=fn($path)=>$path?\Illuminate\Support\Facades\Storage::disk('public')->url($path):null;
@endphp
<div class="od-page">
<div class="op-breadcrumb"><a href="{{ route('owner.dashboard') }}"><i class="bi bi-house-door-fill"></i> Dashboard</a><i class="bi bi-chevron-right"></i><a href="{{ route('owner.pets.index') }}">My Pets</a><i class="bi bi-chevron-right"></i><span>{{ $pet->name }}</span></div>

<div class="op-heading">
    <div><h1>@include('owner.partials.icon',['name'=>'paw']) {{ $pet->name }}</h1>
    <p>{{ $pet->species->name??'' }}{{ $pet->breed?' · '.$pet->breed->name:'' }} @if($pet->gender) · {{ ucfirst($pet->gender) }} @endif @if($pet->date_of_birth) · @include('owner.partials.age',['pet'=>$pet]) @endif</p></div>
    <div style="display:flex;gap:8px;"><a class="op-button" href="{{ route('owner.pets.index') }}"><i class="bi bi-arrow-left"></i> Back</a><a class="op-button op-primary" href="{{ route('owner.pets.edit',$pet) }}"><i class="bi bi-pencil"></i> Edit</a></div>
</div>

<div class="od-layout">
<div>

 {{-- Pet Photo --}}
<section class="od-section" style="margin-bottom:12px;">
@if($sortedImages->count()>0)
<div>
    <div class="pet-photo-hero" onclick="document.querySelector('.od-lightbox').classList.add('is-open');document.body.style.overflow='hidden'">
        @if($primaryImage)
        <img src="{{ $storageUrl($primaryImage->image_path) }}" alt="{{ $pet->name }}" loading="lazy" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
        <div class="pet-initials" style="display:none;">{{ mb_strtoupper(mb_substr($pet->name,0,1)) }}</div>
        @else
        <div class="pet-initials">{{ mb_strtoupper(mb_substr($pet->name,0,1)) }}</div>
        @endif
    </div>
    @if($sortedImages->count()>1)
    <div class="pet-thumbs">
    @foreach($sortedImages as $idx=>$image)
    <img class="pet-thumb" data-idx="{{ $idx }}" src="{{ $storageUrl($image->image_path) }}" alt="{{ $pet->name }} photo {{ $idx+1 }}" onclick="switchPetImage({{ $idx }})" loading="lazy">
    @endforeach
    </div>
    @endif
</div>
@else
<div style="text-align:center;padding:50px;background:linear-gradient(135deg,#e9fbf4,#f3fdfa);border-radius:8px;">
    <div style="width:80px;height:80px;border-radius:50%;background:linear-gradient(135deg,#00865e,#27bd96);display:inline-flex;align-items:center;justify-content:center;color:#fff;font-size:2rem;font-weight:700;">{{ mb_strtoupper(mb_substr($pet->name,0,1)) }}</div>
    <p style="margin:10px 0 0;font-size:12px;color:#99a8c2;">No photos uploaded yet</p>
    <a class="op-button op-primary op-small" style="margin-top:10px;" href="{{ route('owner.pets.edit',$pet) }}"><i class="bi bi-upload"></i> Upload Photo</a>
</div>
@endif

{{-- Lightbox --}}
<div class="od-lightbox" id="petLightbox" onclick="if(event.target===this)closePetLightbox()">
<button class="od-lightbox-close" onclick="closePetLightbox()"><i class="bi bi-x-lg"></i></button>
<button class="od-lightbox-nav od-lightbox-prev" id="lbPrev" onclick="navPetLightbox(-1)"><i class="bi bi-chevron-left"></i></button>
<img id="lbImg" src="" alt="{{ $pet->name }}">
<button class="od-lightbox-nav od-lightbox-next" id="lbNext" onclick="navPetLightbox(1)"><i class="bi bi-chevron-right"></i></button>
<div class="od-lightbox-counter" id="lbCounter"></div>
</div>
</section>

 {{-- Pet Details --}}
<section class="od-section" style="margin-bottom:12px;">
<div class="od-section-header"><h2>@include('owner.partials.icon',['name'=>'info-circle']) Pet Details</h2></div>
<div class="pet-info-grid">
    <div class="pet-info-item"><div class="pet-info-label">Species</div><div class="pet-info-value">{{ $pet->species->name??'-' }}</div></div>
    <div class="pet-info-item"><div class="pet-info-label">Breed</div><div class="pet-info-value">{{ $pet->breed->name??'-' }}</div></div>
    <div class="pet-info-item"><div class="pet-info-label">Gender</div><div class="pet-info-value">@if($pet->gender)<i class="bi bi-gender-{{ $pet->gender==='male'?'male':'female' }}" style="color:{{ $pet->gender==='male'?'#007dff':'#ec4899' }};"></i> {{ ucfirst($pet->gender) }}@else-@endif</div></div>
    <div class="pet-info-item"><div class="pet-info-label">Date of Birth</div><div class="pet-info-value">{{ $pet->date_of_birth?\Carbon\Carbon::parse($pet->date_of_birth)->format('M d, Y'):'-' }}</div></div>
    <div class="pet-info-item"><div class="pet-info-label">Weight</div><div class="pet-info-value">{{ $pet->weight?number_format($pet->weight,1).' kg':'-' }}</div></div>
    <div class="pet-info-item"><div class="pet-info-label">Color</div><div class="pet-info-value">{{ $pet->color?:'-' }}</div></div>
    <div class="pet-info-item"><div class="pet-info-label">Neutered / Spayed</div><div class="pet-info-value">@if($pet->is_neutered)<span style="color:#00825a;"><i class="bi bi-check-circle-fill"></i> Yes</span>@else<span style="color:#99a8c2;">No</span>@endif</div></div>
    <div class="pet-info-item"><div class="pet-info-label">Microchip</div><div class="pet-info-value"><code style="font-size:11px;background:#f2f6fb;padding:2px 6px;border-radius:3px;">{{ $pet->microchip_number?:'Not registered' }}</code></div></div>
</div>
@if($pet->description)
<div style="padding:10px 0 0;font-size:12px;color:#3d5885;line-height:1.6;">{{ $pet->description }}</div>
@endif
</section>

 {{-- Health Tabs --}}
<section class="od-section">
<div class="od-section-header"><h2>@include('owner.partials.icon',['name'=>'heart-pulse-fill']) Health</h2><a class="op-button op-small" href="{{ route('owner.health.overview',['pet_id'=>$pet->id]) }}" style="font-size:11px;"><i class="bi bi-arrow-right"></i> Full Overview</a></div>
<nav class="od-health-tabs" role="tablist">
<a class="active" href="#tab-health" role="tab" data-bs-toggle="tab" onclick="return false;"><i class="bi bi-clipboard2-pulse"></i> Records</a>
<a href="#tab-vaccines" role="tab" data-bs-toggle="tab" onclick="return false;"><i class="bi bi-shield-check"></i> Vaccinations</a>
<a href="#tab-docs" role="tab" data-bs-toggle="tab" onclick="return false;"><i class="bi bi-file-earmark-medical"></i> Documents</a>
</nav>
<div class="tab-content">

{{-- Health Records --}}
<div class="tab-pane fade show active" id="tab-health">
<div style="overflow-x:auto;">
<table class="od-health-table">
<thead><tr><th>Date</th><th>Type</th><th>Description</th><th>Vet</th></tr></thead>
<tbody>
@forelse($pet->healthRecords as $record)
<tr>
<td style="color:#52699b;font-size:12px;">{{ $record->record_date?\Carbon\Carbon::parse($record->record_date)->format('M d, Y'):'-' }}</td>
<td><span style="background:#e6f2ff;color:#007dff;padding:3px 8px;border-radius:4px;font-size:10px;font-weight:600;">{{ ucfirst($record->record_type??'-') }}</span></td>
<td style="font-size:12px;">{{ $record->description?:'-' }}</td>
<td style="color:#52699b;font-size:12px;">{{ $record->vet->name??'-' }}</td>
</tr>
@empty
<tr><td colspan="4" style="text-align:center;padding:30px;color:#99a8c2;">No health records yet. <a href="{{ route('owner.health.overview',['pet_id'=>$pet->id]) }}">Add one</a></td></tr>
@endforelse
</tbody>
</table>
</div>
</div>

{{-- Vaccinations --}}
<div class="tab-pane fade" id="tab-vaccines">
<div style="overflow-x:auto;">
<table class="od-health-table">
<thead><tr><th>Vaccine</th><th>Date</th><th>Next Due</th><th>Batch No.</th></tr></thead>
<tbody>
@forelse($pet->vaccinations as $v)
<tr>
<td style="font-weight:600;font-size:12px;">{{ $v->vaccine_name?:'-' }}</td>
<td style="color:#52699b;font-size:12px;">{{ $v->vaccination_date?\Carbon\Carbon::parse($v->vaccination_date)->format('M d, Y'):'-' }}</td>
<td style="font-size:12px;">@if($v->next_due_date)@php $overdue=\Carbon\Carbon::parse($v->next_due_date)->isPast()@endphp<span style="color:{{ $overdue?'#e92e56':'#52699b' }};{{ $overdue?'font-weight:600;':'' }}">{{ \Carbon\Carbon::parse($v->next_due_date)->format('M d, Y') }} @if($overdue)<i class="bi bi-exclamation-triangle"></i>@endif</span>@else-@endif</td>
<td><code style="font-size:11px;">{{ $v->batch_number?:'-' }}</code></td>
</tr>
@empty
<tr><td colspan="4" style="text-align:center;padding:30px;color:#99a8c2;">No vaccination records yet.</td></tr>
@endforelse
</tbody>
</table>
</div>
</div>

{{-- Documents --}}
<div class="tab-pane fade" id="tab-docs">
@forelse($pet->medicalDocuments as $doc)
<div style="display:flex;align-items:center;justify-content:space-between;padding:10px;border-bottom:1px solid #edf2f7;">
<div style="display:flex;align-items:center;gap:10px;">
@if(str_contains($doc->mime_type??'','image'))<i class="bi bi-image" style="color:#00865e;font-size:18px;"></i>
@elseif(str_contains($doc->mime_type??'','pdf'))<i class="bi bi-file-earmark-pdf" style="color:#e92e56;font-size:18px;"></i>
@else<i class="bi bi-file-earmark" style="color:#007dff;font-size:18px;"></i>
@endif
<div><div style="font-size:12px;font-weight:600;">{{ $doc->file_name }}</div><small style="color:#99a8c2;">{{ $doc->document_type?:'Medical Document' }} · {{ $doc->created_at->format('M d, Y') }}</small></div>
</div>
<a class="op-button op-small" href="{{ route('owner.health.document',[$pet,$doc]) }}" target="_blank" style="min-height:30px;font-size:11px;background:#e4f1ff;color:#0072ff;border:0;"><i class="bi bi-eye"></i></a>
</div>
@empty
<div style="text-align:center;padding:30px;color:#99a8c2;">No medical documents uploaded yet.</div>
@endforelse
</div>
</div>
</section>
</div>

<aside class="od-aside">
{{-- Quick Stats --}}
<section class="od-side-card">
<div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
<div style="text-align:center;padding:12px 8px;background:#e2f5ed;border-radius:8px;">
<div style="font-size:18px;font-weight:700;color:#00865e;">{{ $pet->healthRecords->count() }}</div>
<div style="font-size:10px;color:#3d5885;">Health Records</div>
</div>
<div style="text-align:center;padding:12px 8px;background:#e6f2ff;border-radius:8px;">
<div style="font-size:18px;font-weight:700;color:#007dff;">{{ $pet->vaccinations->count() }}</div>
<div style="font-size:10px;color:#3d5885;">Vaccinations</div>
</div>
<div style="text-align:center;padding:12px 8px;background:#fff3df;border-radius:8px;">
<div style="font-size:18px;font-weight:700;color:#f29300;">{{ $pet->medicalDocuments->count() }}</div>
<div style="font-size:10px;color:#3d5885;">Documents</div>
</div>
<div style="text-align:center;padding:12px 8px;background:#f0e7ff;border-radius:8px;">
<div style="font-size:18px;font-weight:700;color:#823aff;">{{ $pet->images->count() }}</div>
<div style="font-size:10px;color:#3d5885;">Photos</div>
</div>
</div>
</section>

{{-- Actions --}}
<section class="od-side-card">
<div class="od-section-header"><h2>Actions</h2></div>
<div style="display:grid;gap:8px;">
<a class="op-button op-primary" style="width:100%;justify-content:center;" href="{{ route('owner.pets.edit',$pet) }}"><i class="bi bi-pencil"></i> Edit Pet</a>
<a class="op-button" style="width:100%;justify-content:center;" href="{{ route('owner.health.overview',['pet_id'=>$pet->id]) }}"><i class="bi bi-clipboard2-pulse"></i> Health Overview</a>
<form action="{{ route('owner.pets.destroy',$pet) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete {{ $pet->name }}? This cannot be undone.')">
@csrf @method('DELETE')
<button type="submit" class="op-button op-danger" style="width:100%;justify-content:center;"><i class="bi bi-trash"></i> Delete Pet</button>
</form>
</div>
</section>

{{-- Member Info --}}
<section class="od-info-card">@include('owner.partials.icon',['name'=>'paw'])<div><h2>{{ $pet->name }}</h2><p>Member since {{ $pet->created_at->format('M Y') }}.</p></div></section>
</aside>
</div>
@include('owner.partials.discovery-banner')
</div>

@push('scripts')
<script>
(function(){
var urls=@js($sortedImages->map(fn($img)=>\Illuminate\Support\Facades\Storage::disk('public')->url($img->image_path))->filter()->values()->toArray());
var current=0;
var lb=document.getElementById('petLightbox');
var lbImg=document.getElementById('lbImg');
var lbCounter=document.getElementById('lbCounter');
var lbPrev=document.getElementById('lbPrev');
var lbNext=document.getElementById('lbNext');
function updateLightbox(){lbImg.src=urls[current];lbCounter.textContent=(current+1)+' / '+urls.length;lbPrev.style.display=urls.length>1?'flex':'none';lbNext.style.display=urls.length>1?'flex':'none';}
window.openPetLightbox=function(i){current=i;updateLightbox();lb.classList.add('is-open');document.body.style.overflow='hidden';};
window.closePetLightbox=function(){lb.classList.remove('is-open');document.body.style.overflow='';};
window.navPetLightbox=function(d){current=(current+d+urls.length)%urls.length;updateLightbox();};
window.switchPetImage=function(i){current=i;var hero=document.querySelector('.pet-photo-hero img');if(hero)hero.src=urls[i];document.querySelectorAll('.pet-thumb').forEach(function(t,idx){t.classList.toggle('active',idx===i);});};
document.addEventListener('keydown',function(e){if(!lb.classList.contains('is-open'))return;if(e.key==='Escape')closePetLightbox();if(e.key==='ArrowLeft')navPetLightbox(-1);if(e.key==='ArrowRight')navPetLightbox(1);});
var heroImg=document.querySelector('.pet-photo-hero img');
if(heroImg&&urls.length>0)heroImg.src=urls[0];
var thumbs=document.querySelectorAll('.pet-thumb');
if(thumbs.length>0)thumbs[0].classList.add('active');
})();
</script>
@endpush
@endsection
