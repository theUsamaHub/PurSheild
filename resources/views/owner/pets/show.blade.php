@extends('layouts.owner.app')
@section('title', $pet->name)
@push('styles')
@include('owner.partials.discovery-styles')
<style>
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
.od-pet-main-img{width:100%;max-height:380px;border-radius:8px;object-fit:cover;background:#f6f9fc;cursor:zoom-in;}
.od-pet-main-img:hover{opacity:.92;}
.od-pet-thumbs{display:flex;gap:6px;flex-wrap:wrap;margin-top:8px;}
.od-pet-thumb{width:64px;height:64px;border-radius:6px;object-fit:cover;border:2px solid transparent;cursor:pointer;opacity:.6;transition:border-color .15s,opacity .15s;}
.od-pet-thumb:hover,.od-pet-thumb.is-active{border-color:#00865e;opacity:1;}
</style>
@endpush
@section('content')
@php
$primaryImage=$pet->images->firstWhere('is_primary')??$pet->images->first();
$sortedImages=$pet->images->sortBy('sort_order')->values();
$imageUrls=$sortedImages->map(fn($img)=>asset('storage/'.$img->image_path))->toArray();
@endphp
<div class="od-page">
<div class="op-breadcrumb"><a href="{{ route('owner.dashboard') }}"><i class="bi bi-house-door-fill"></i> Dashboard</a><i class="bi bi-chevron-right"></i><a href="{{ route('owner.pets.index') }}">My Pets</a><i class="bi bi-chevron-right"></i><span>{{ $pet->name }}</span></div>
<div class="od-layout"><div x-data="petLightbox()">
<div class="op-heading"><div><h1>@include('owner.partials.icon',['name'=>'paw']) {{ $pet->name }}</h1><p>{{ $pet->species->name??'' }}{{ $pet->breed?' · '.$pet->breed->name:'' }}</p></div><div style="display:flex;gap:8px;"><a class="op-button" href="{{ route('owner.pets.index') }}"><i class="bi bi-arrow-left"></i> Back</a><a class="op-button op-primary" href="{{ route('owner.pets.edit',$pet) }}"><i class="bi bi-pencil"></i> Edit</a></div></div>

{{-- Pet Photo --}}
<section class="od-section" style="margin-bottom:12px;">
@if($sortedImages->count()>0)
<div>
<img class="od-pet-main-img" :src="images[current]" alt="{{ $pet->name }}" @click="open(current)" loading="lazy">
@if($sortedImages->count()>1)
<div class="od-pet-thumbs">
@foreach($sortedImages as $idx=>$image)
<img class="od-pet-thumb" :class="{'is-active':current==={{ $idx }}}" src="{{ asset('storage/'.$image->image_path) }}" alt="{{ $pet->name }}" @click="current={{ $idx }}" loading="lazy">
@endforeach
</div>
@endif
</div>
@else
<div style="text-align:center;padding:60px;background:linear-gradient(135deg,#e9fbf4,#f3fdfa);border-radius:8px;">
<div style="width:80px;height:80px;border-radius:50%;background:linear-gradient(135deg,#00865e,#27bd96);display:inline-flex;align-items:center;justify-content:center;color:#fff;font-size:2rem;font-weight:700;">{{ mb_substr($pet->name,0,1) }}</div>
<p style="margin:10px 0 0;font-size:12px;color:#99a8c2;">No photos uploaded</p>
</div>
@endif
{{-- Lightbox --}}
<div class="od-lightbox" :class="{'is-open':opened}" @click.self="close()" @keydown.escape.window="close()" @keydown.left.window="prev()" @keydown.right.window="next()" x-show="opened" x-cloak>
<button class="od-lightbox-close" @click="close()"><i class="bi bi-x-lg"></i></button>
<button class="od-lightbox-nav od-lightbox-prev" @click="prev()" x-show="images.length>1"><i class="bi bi-chevron-left"></i></button>
<img :src="images[current]" alt="{{ $pet->name }}">
<button class="od-lightbox-nav od-lightbox-next" @click="next()" x-show="images.length>1"><i class="bi bi-chevron-right"></i></button>
<div class="od-lightbox-counter" x-text="`${current+1} / ${images.length}`" x-show="images.length>1"></div>
</div>
</section>

{{-- Health Tabs --}}
<section class="od-section" style="margin-bottom:12px;">
<nav class="od-health-tabs" role="tablist">
<a class="active" href="#tab-health" role="tab" data-bs-toggle="tab" onclick="return false;"><i class="bi bi-clipboard2-pulse"></i> Health Records</a>
<a href="#tab-vaccines" role="tab" data-bs-toggle="tab" onclick="return false;"><i class="bi bi-shield-check"></i> Vaccinations</a>
<a href="#tab-docs" role="tab" data-bs-toggle="tab" onclick="return false;"><i class="bi bi-file-earmark-medical"></i> Documents</a>
</nav>
<div class="tab-content">

{{-- Health Records --}}
<div class="tab-pane fade show active" id="tab-health">
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
<h2 style="font-size:15px;font-weight:700;margin:0;">Health Records</h2>
<button class="op-button op-primary op-small" data-bs-toggle="modal" data-bs-target="#addHealthRecordModal"><i class="bi bi-plus-lg"></i> Add Record</button>
</div>
<div style="overflow-x:auto;">
<table class="od-health-table">
<thead><tr><th style="width:16%;">Date</th><th style="width:14%;">Type</th><th style="width:40%;">Description</th><th style="width:18%;">Vet</th></tr></thead>
<tbody>
@forelse($pet->healthRecords??[] as $record)
<tr>
<td style="color:#52699b;font-size:12px;">{{ $record->record_date?\Carbon\Carbon::parse($record->record_date)->format('M d, Y'):'-' }}</td>
<td><span style="background:#e6f2ff;color:#007dff;padding:3px 8px;border-radius:4px;font-size:10px;font-weight:600;">{{ ucfirst($record->record_type??'-') }}</span></td>
<td style="font-size:12px;">{{ $record->description?:'-' }}</td>
<td style="color:#52699b;font-size:12px;">{{ $record->vet->name??'-' }}</td>
</tr>
@empty
<tr><td colspan="4" style="text-align:center;padding:30px;color:#99a8c2;">No health records yet.</td></tr>
@endforelse
</tbody>
</table>
</div>
</div>

{{-- Vaccinations --}}
<div class="tab-pane fade" id="tab-vaccines">
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
<h2 style="font-size:15px;font-weight:700;margin:0;">Vaccinations</h2>
<button class="op-button op-primary op-small" data-bs-toggle="modal" data-bs-target="#addVaccinationModal"><i class="bi bi-plus-lg"></i> Add Vaccination</button>
</div>
<div style="overflow-x:auto;">
<table class="od-health-table">
<thead><tr><th style="width:22%;">Vaccine</th><th style="width:18%;">Date</th><th style="width:22%;">Next Due</th><th style="width:18%;">Batch No.</th></tr></thead>
<tbody>
@forelse($pet->vaccinations??[] as $v)
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
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
<h2 style="font-size:15px;font-weight:700;margin:0;">Medical Documents</h2>
<button class="op-button op-primary op-small" data-bs-toggle="modal" data-bs-target="#addDocumentModal"><i class="bi bi-upload"></i> Upload</button>
</div>
@forelse($pet->medicalDocuments??[] as $doc)
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
{{-- Pet Info --}}
<section class="od-side-card">
<div class="od-section-header"><h2>@include('owner.partials.icon',['name'=>'paw']) {{ $pet->name }}</h2></div>
<div style="font-size:12px;display:grid;gap:8px;">
<div style="display:flex;justify-content:space-between;"><span style="color:#52699b;">Species</span><strong style="color:#0a1648;">{{ $pet->species->name??'-' }}</strong></div>
<hr style="margin:0;border-color:#edf2f6;">
<div style="display:flex;justify-content:space-between;"><span style="color:#52699b;">Breed</span><strong style="color:#0a1648;">{{ $pet->breed->name??'-' }}</strong></div>
<hr style="margin:0;border-color:#edf2f6;">
<div style="display:flex;justify-content:space-between;"><span style="color:#52699b;">Gender</span>@if($pet->gender)<span style="font-weight:600;"><i class="bi bi-{{ $pet->gender==='male'?'gender-male':'gender-female' }}" style="color:{{ $pet->gender==='male'?'#007dff':'#ec4899' }};"></i> {{ ucfirst($pet->gender) }}</span>@else<span>-</span>@endif</div>
<hr style="margin:0;border-color:#edf2f6;">
<div style="display:flex;justify-content:space-between;"><span style="color:#52699b;">Date of Birth</span><span>{{ $pet->date_of_birth?\Carbon\Carbon::parse($pet->date_of_birth)->format('M d, Y'):'-' }}</span></div>
<hr style="margin:0;border-color:#edf2f6;">
<div style="display:flex;justify-content:space-between;"><span style="color:#52699b;">Weight</span><span>{{ $pet->weight?$pet->weight.' kg':'-' }}</span></div>
<hr style="margin:0;border-color:#edf2f6;">
<div style="display:flex;justify-content:space-between;"><span style="color:#52699b;">Color</span><span>{{ $pet->color?:'-' }}</span></div>
<hr style="margin:0;border-color:#edf2f6;">
<div style="display:flex;justify-content:space-between;"><span style="color:#52699b;">Neutered</span>@if($pet->is_neutered)<span style="color:#00825a;font-weight:600;">Yes</span>@else<span style="color:#99a8c2;">No</span>@endif</div>
<hr style="margin:0;border-color:#edf2f6;">
<div style="display:flex;justify-content:space-between;"><span style="color:#52699b;">Microchip</span><code style="font-size:11px;">{{ $pet->microchip_number?:'-' }}</code></div>
@if($pet->description)
<hr style="margin:0;border-color:#edf2f6;">
<div><span style="color:#52699b;display:block;margin-bottom:3px;">Description</span><p style="margin:0;color:#3d5885;line-height:1.5;">{{ $pet->description }}</p></div>
@endif
</div>
</section>

{{-- Actions --}}
<section class="od-side-card">
<div class="od-section-header"><h2>@include('owner.partials.icon',['name'=>'gear-fill']) Actions</h2></div>
<div style="display:grid;gap:8px;">
<a class="op-button op-primary" style="width:100%;justify-content:center;" href="{{ route('owner.pets.edit',$pet) }}"><i class="bi bi-pencil"></i> Edit Pet</a>
<a class="op-button" style="width:100%;justify-content:center;" href="{{ route('owner.health.overview',['pet_id'=>$pet->id]) }}"><i class="bi bi-clipboard2-pulse"></i> Health Overview</a>
<form action="{{ route('owner.pets.destroy',$pet) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this pet? This cannot be undone.')">
@csrf @method('DELETE')
<button type="submit" class="op-button op-danger" style="width:100%;justify-content:center;"><i class="bi bi-trash"></i> Delete Pet</button>
</form>
</div>
</section>

<section class="od-info-card">@include('owner.partials.icon',['name'=>'paw'])<div><h2>{{ $pet->name }}</h2><p>Member since {{ $pet->created_at->format('M Y') }}.</p></div></section>
</aside>
</div>
@include('owner.partials.discovery-banner')
</div>

{{-- Modals --}}
<div class="modal fade" id="addHealthRecordModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
<form action="{{ route('owner.health.store',$pet) }}" method="POST">@csrf
<div class="modal-header"><h5 class="modal-title fw-semibold">Add Health Record</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
<div class="modal-body">
<div class="mb-3"><label class="form-label fw-semibold" style="font-size:12px;">Date *</label><input type="date" name="record_date" class="form-control" value="{{ old('record_date',now()->format('Y-m-d')) }}" required></div>
<div class="mb-3"><label class="form-label fw-semibold" style="font-size:12px;">Type *</label><select name="record_type" class="form-select" required><option value="">Select type</option>@foreach(['checkup','surgery','illness','injury','dental','emergency','other'] as $t)<option value="{{ $t }}" {{ old('record_type')==$t?'selected':'' }}>{{ ucfirst(__($t)) }}</option>@endforeach</select></div>
<div class="mb-3"><label class="form-label fw-semibold" style="font-size:12px;">Description</label><textarea name="description" class="form-control" rows="3" placeholder="Describe the health record...">{{ old('description') }}</textarea></div>
<div class="mb-3"><label class="form-label fw-semibold" style="font-size:12px;">Notes</label><textarea name="notes" class="form-control" rows="2" placeholder="Additional notes">{{ old('notes') }}</textarea></div>
</div>
<div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="op-button op-primary">Save Record</button></div>
</form>
</div></div></div>

<div class="modal fade" id="addVaccinationModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
<form action="{{ route('owner.health.storeVaccination',$pet) }}" method="POST">@csrf
<div class="modal-header"><h5 class="modal-title fw-semibold">Add Vaccination</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
<div class="modal-body">
<div class="mb-3"><label class="form-label fw-semibold" style="font-size:12px;">Vaccine Name *</label><input type="text" name="vaccine_name" class="form-control" value="{{ old('vaccine_name') }}" placeholder="e.g. Rabies, DHPP" required></div>
<div class="row mb-3"><div class="col-md-6"><label class="form-label fw-semibold" style="font-size:12px;">Date *</label><input type="date" name="vaccination_date" class="form-control" value="{{ old('vaccination_date',now()->format('Y-m-d')) }}" required></div><div class="col-md-6"><label class="form-label fw-semibold" style="font-size:12px;">Next Due</label><input type="date" name="next_due_date" class="form-control" value="{{ old('next_due_date') }}"></div></div>
<div class="mb-3"><label class="form-label fw-semibold" style="font-size:12px;">Batch Number</label><input type="text" name="batch_number" class="form-control" value="{{ old('batch_number') }}" placeholder="Optional"></div>
<div class="mb-3"><label class="form-label fw-semibold" style="font-size:12px;">Notes</label><textarea name="notes" class="form-control" rows="2" placeholder="Additional notes">{{ old('notes') }}</textarea></div>
</div>
<div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="op-button op-primary">Save Vaccination</button></div>
</form>
</div></div></div>

<div class="modal fade" id="addDocumentModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
<form action="{{ route('owner.health.storeDocument',$pet) }}" method="POST" enctype="multipart/form-data">@csrf
<div class="modal-header"><h5 class="modal-title fw-semibold">Upload Medical Document</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
<div class="modal-body">
<div class="mb-3"><label class="form-label fw-semibold" style="font-size:12px;">Document Type</label><select name="document_type" class="form-select"><option value="">Select type</option>@foreach(['lab_result','prescription','xray','vaccination_cert','insurance','other'] as $t)<option value="{{ $t }}" {{ old('document_type')==$t?'selected':'' }}>{{ ucfirst(str_replace('_',' ',$t)) }}</option>@endforeach</select></div>
<div class="mb-3"><label class="form-label fw-semibold" style="font-size:12px;">File *</label><input type="file" name="file" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" required><small style="color:#99a8c2;">PDF, JPG, PNG, DOC. Max 10MB.</small></div>
<div class="mb-3"><label class="form-label fw-semibold" style="font-size:12px;">Description</label><input type="text" name="description" class="form-control" value="{{ old('description') }}" placeholder="Brief description"></div>
</div>
<div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="op-button op-primary">Upload</button></div>
</form>
</div></div></div>

@push('scripts')
<script>
function petLightbox(){return{current:0,opened:false,images:@js($imageUrls),open(i){this.current=i;this.opened=true;document.body.style.overflow='hidden';},close(){this.opened=false;document.body.style.overflow='';},next(){this.current=(this.current+1)%this.images.length;},prev(){this.current=(this.current-1+this.images.length)%this.images.length;};}
</script>
@endpush
@endsection
