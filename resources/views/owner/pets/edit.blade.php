@extends('layouts.owner.app')
@section('title', 'Edit '.$pet->name)
@push('styles')
@include('owner.partials.discovery-styles')
@endpush
@section('content')
<div class="od-page">
<div class="op-breadcrumb"><a href="{{ route('owner.dashboard') }}"><i class="bi bi-house-door-fill"></i> Dashboard</a><i class="bi bi-chevron-right"></i><a href="{{ route('owner.pets.index') }}">My Pets</a><i class="bi bi-chevron-right"></i><a href="{{ route('owner.pets.show',$pet) }}">{{ $pet->name }}</a><i class="bi bi-chevron-right"></i><span>Edit</span></div>
<div class="od-layout"><div>
<div class="op-heading"><div><h1>@include('owner.partials.icon',['name'=>'paw']) Edit {{ $pet->name }}</h1><p>Update your pet's information.</p></div><a class="op-button" href="{{ route('owner.pets.show',$pet) }}"><i class="bi bi-arrow-left"></i> Back</a></div>

<form action="{{ route('owner.pets.update',$pet) }}" method="POST" enctype="multipart/form-data">@csrf @method('PUT')

{{-- Basic Info --}}
<section class="od-section" style="margin-bottom:12px;">
<div class="od-section-header"><h2>@include('owner.partials.icon',['name'=>'info-circle']) Basic Information</h2></div>
<div class="od-fields" style="grid-template-columns:1fr 1fr;">
<div><label for="name">Pet Name *</label><input type="text" id="name" name="name" value="{{ old('name',$pet->name) }}" required></div>
<div><label for="species_id">Species *</label><select id="species_id" name="species_id" required><option value="">Select Species</option>@foreach($species as $s)<option value="{{ $s->id }}" {{ old('species_id',$pet->species_id)==$s->id?'selected':'' }}>{{ $s->name }}</option>@endforeach</select></div>
<div><label for="breed_id">Breed</label><select id="breed_id" name="breed_id"><option value="">Select breed (optional)</option>@if($pet->breed)<option value="{{ $pet->breed->id }}" selected>{{ $pet->breed->name }}</option>@endif</select></div>
<div><label>Gender *</label><div style="display:flex;gap:16px;margin-top:4px;"><label style="display:flex;align-items:center;gap:5px;font-size:12px;cursor:pointer;"><input type="radio" name="gender" value="male" {{ old('gender',$pet->gender)==='male'?'checked':'' }} style="accent-color:#00875e;"> <i class="bi bi-gender-male" style="color:#007dff;"></i> Male</label><label style="display:flex;align-items:center;gap:5px;font-size:12px;cursor:pointer;"><input type="radio" name="gender" value="female" {{ old('gender',$pet->gender)==='female'?'checked':'' }} style="accent-color:#00875e;"> <i class="bi bi-gender-female" style="color:#ec4899;"></i> Female</label></div></div>
<div class="full"><label for="description">Description</label><textarea id="description" name="description" rows="3" placeholder="Tell us about your pet...">{{ old('description',$pet->description) }}</textarea></div>
</div>
</section>

{{-- Details --}}
<section class="od-section" style="margin-bottom:12px;">
<div class="od-section-header"><h2>@include('owner.partials.icon',['name'=>'clipboard2']) Details</h2></div>
<div class="od-fields" style="grid-template-columns:1fr 1fr;">
<div><label for="date_of_birth">Date of Birth *</label><input type="date" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth',$pet->date_of_birth?($pet->date_of_birth instanceof \Carbon\Carbon?$pet->date_of_birth->format('Y-m-d'):$pet->date_of_birth):'') }}" required></div>
<div><label for="weight">Weight (kg)</label><input type="number" id="weight" name="weight" step="0.1" min="0" value="{{ old('weight',$pet->weight) }}"></div>
<div><label for="color">Color</label><input type="text" id="color" name="color" value="{{ old('color',$pet->color) }}"></div>
<div><label for="microchip_number">Microchip Number</label><input type="text" id="microchip_number" name="microchip_number" value="{{ old('microchip_number',$pet->microchip_number) }}" placeholder="Optional"><small style="color:#99a8c2;font-size:11px;">Optional</small></div>
<div class="full"><label style="display:flex;align-items:center;gap:8px;cursor:pointer;"><input type="checkbox" name="is_neutered" value="1" {{ old('is_neutered',$pet->is_neutered)?'checked':'' }} style="accent-color:#00875e;"> Neutered / Spayed</label></div>
</div>
</section>

{{-- Actions --}}
<section class="od-section" style="margin-bottom:12px;">
<div style="display:flex;justify-content:flex-end;gap:8px;">
<a class="op-button" href="{{ route('owner.pets.show',$pet) }}">Cancel</a>
<button type="submit" class="op-button op-primary"><i class="bi bi-check-lg"></i> Update Pet</button>
</div>
</section>

{{-- Health Tabs --}}
<section class="od-section">
<div class="od-section-header"><h2>@include('owner.partials.icon',['name'=>'heart-pulse-fill']) Health Management</h2></div>
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
<button type="button" class="op-button op-primary op-small" data-bs-toggle="modal" data-bs-target="#addHealthRecordModal"><i class="bi bi-plus-lg"></i> Add Record</button>
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
<button type="button" class="op-button op-primary op-small" data-bs-toggle="modal" data-bs-target="#addVaccinationModal"><i class="bi bi-plus-lg"></i> Add Vaccination</button>
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
<button type="button" class="op-button op-primary op-small" data-bs-toggle="modal" data-bs-target="#addDocumentModal"><i class="bi bi-upload"></i> Upload</button>
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
</div>
</section>
</form>
</div>

<aside class="od-aside">
{{-- Current Images --}}
<section class="od-side-card">
<div class="od-section-header"><h2>@include('owner.partials.icon',['name'=>'images']) Current Images ({{ $pet->images->count() }})</h2></div>
@if($pet->images->count()>0)
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:6px;">
@foreach($pet->images->sortBy('sort_order') as $image)
<div style="position:relative;border-radius:6px;overflow:hidden;aspect-ratio:1;">
<img src="{{ asset('storage/'.$image->image_path) }}" alt="" style="width:100%;height:100%;object-fit:cover;" loading="lazy">
@if($image->is_primary)<span style="position:absolute;top:4px;left:4px;background:#f5ae00;color:#fff;font-size:8px;font-weight:700;padding:2px 5px;border-radius:3px;"><i class="bi bi-star-fill"></i></span>@endif
<div style="position:absolute;bottom:0;left:0;right:0;background:rgba(0,0,0,.65);display:flex;justify-content:center;gap:8px;padding:4px;">
<label style="color:#fff;cursor:pointer;font-size:10px;" title="Remove"><input type="checkbox" name="remove_images[]" value="{{ $image->id }}" class="d-none" onchange="this.closest('div[style]').style.opacity=this.checked?0.3:1;"><i class="bi bi-trash"></i></label>
@if(!$image->is_primary)<label style="color:#fff;cursor:pointer;font-size:10px;" title="Set as primary"><input type="radio" name="primary_image_id" value="{{ $image->id }}" class="d-none"><i class="bi bi-star" onclick="setPrimary(this,{{ $image->id }})"></i></label>@endif
</div>
</div>
@endforeach
</div>
@else
<p style="font-size:12px;color:#99a8c2;margin:0;">No images uploaded yet.</p>
@endif
</section>

{{-- Upload New --}}
<section class="od-side-card">
<div class="od-section-header"><h2>@include('owner.partials.icon',['name'=>'upload']) Upload New Images</h2></div>
<div class="od-fields">
<div><input type="file" name="images[]" id="images" multiple accept=".jpg,.jpeg,.png,.gif,.webp" style="width:100%;"><small style="color:#99a8c2;font-size:11px;">Max 6 images total, 5MB each.</small></div>
</div>
<div id="image-previews" style="display:flex;flex-wrap:wrap;gap:6px;margin-top:8px;"></div>
</section>

{{-- Pet Info --}}
<section class="od-side-card">
<div class="od-section-header"><h2>@include('owner.partials.icon',['name'=>'info-circle']) Pet Info</h2></div>
<div style="font-size:12px;display:grid;gap:8px;">
<div style="display:flex;justify-content:space-between;"><span style="color:#52699b;">Species</span><strong style="color:#0a1648;">{{ $pet->species->name??'-' }}</strong></div>
<hr style="margin:0;border-color:#edf2f6;">
<div style="display:flex;justify-content:space-between;"><span style="color:#52699b;">Breed</span><strong style="color:#0a1648;">{{ $pet->breed->name??'-' }}</strong></div>
<hr style="margin:0;border-color:#edf2f6;">
<div style="display:flex;justify-content:space-between;"><span style="color:#52699b;">Created</span><span>{{ $pet->created_at->format('M d, Y') }}</span></div>
<hr style="margin:0;border-color:#edf2f6;">
<div style="display:flex;justify-content:space-between;"><span style="color:#52699b;">Updated</span><span>{{ $pet->updated_at->format('M d, Y') }}</span></div>
</div>
</section>
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
function setPrimary(el,imageId){document.querySelectorAll('input[name="primary_image_id"]').forEach(r=>r.checked=false);el.previousElementSibling.checked=true;document.querySelectorAll('.od-side-card span[style*="star-fill"]').forEach(b=>b.remove());el.closest('div[style*="position"]').insertAdjacentHTML('afterbegin','<span style="position:absolute;top:4px;left:4px;background:#f5ae00;color:#fff;font-size:8px;font-weight:700;padding:2px 5px;border-radius:3px;"><i class="bi bi-star-fill"></i></span>');}
document.getElementById('images').addEventListener('change',function(e){const c=document.getElementById('image-previews');c.innerHTML='';Array.from(e.target.files).slice(0,6).forEach(f=>{if(f.type.startsWith('image/')){const r=new FileReader();r.onload=ev=>{const d=document.createElement('div');d.style.cssText='width:72px;height:72px;border-radius:6px;overflow:hidden;';d.innerHTML='<img src="'+ev.target.result+'" style="width:100%;height:100%;object-fit:cover;">';c.appendChild(d);};r.readAsDataURL(f);}});});
document.getElementById('species_id').addEventListener('change',function(){const sid=this.value,breedSelect=document.getElementById('breed_id'),current='{{ old("breed_id",$pet->breed_id) }}';breedSelect.innerHTML='<option value="">Loading...</option>';if(!sid){breedSelect.innerHTML='<option value="">Select breed (optional)</option>';return;}fetch('/owner/species/'+sid+'/breeds').then(r=>r.json()).then(data=>{let o='<option value="">Select breed (optional)</option>';(data.data||(Array.isArray(data)?data:[])).forEach(b=>{o+='<option value="'+b.id+'"'+(b.id==current?' selected':'')+'>'+b.name+'</option>';});breedSelect.innerHTML=o;}).catch(()=>{breedSelect.innerHTML='<option value="">Select breed (optional)</option>';});});
const sp=document.getElementById('species_id');if(sp.value)sp.dispatchEvent(new Event('change'));
</script>
@endpush
@endsection
