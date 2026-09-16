@extends('layouts.owner.app')
@section('title', 'Edit '.$pet->name)
@push('styles')
@include('owner.partials.discovery-styles')
<style>
.pet-photo-thumb{width:100%;aspect-ratio:1;border-radius:6px;object-fit:cover;}
.pet-initials-thumb{width:100%;aspect-ratio:1;border-radius:6px;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,#e2f5ed,#f3fdfa);font-size:28px;font-weight:700;color:#00865e;}
</style>
@endpush
@section('content')
@php
$primaryImage=$pet->images->firstWhere('is_primary',true)??$pet->images->sortBy('sort_order')->first();
$storageUrl=fn($path)=>$path?\Illuminate\Support\Facades\Storage::disk('public')->url($path):null;
@endphp
<div class="od-page">
<div class="op-breadcrumb"><a href="{{ route('owner.dashboard') }}"><i class="bi bi-house-door-fill"></i> Dashboard</a><i class="bi bi-chevron-right"></i><a href="{{ route('owner.pets.index') }}">My Pets</a><i class="bi bi-chevron-right"></i><a href="{{ route('owner.pets.show',$pet) }}">{{ $pet->name }}</a><i class="bi bi-chevron-right"></i><span>Edit</span></div>

<div class="op-heading">
    <div><h1>@include('owner.partials.icon',['name'=>'paw']) Edit {{ $pet->name }}</h1><p>{{ $pet->species->name??'' }}{{ $pet->breed?' · '.$pet->breed->name:'' }}</p></div>
    <a class="op-button" href="{{ route('owner.pets.show',$pet) }}"><i class="bi bi-arrow-left"></i> Back to Profile</a>
</div>

<form action="{{ route('owner.pets.update',$pet) }}" method="POST" enctype="multipart/form-data" id="editPetForm">@csrf @method('PUT')

<div class="od-layout">
<div>

{{-- Basic Info --}}
<section class="od-section">
<div class="od-section-header"><h2>@include('owner.partials.icon',['name'=>'info-circle']) Basic Information</h2></div>
<div class="od-fields" style="grid-template-columns:1fr 1fr;">
<div><label for="name">Pet Name *</label><input type="text" id="name" name="name" value="{{ old('name',$pet->name) }}" required></div>
<div><label for="gender">Gender *</label>
<div style="display:flex;gap:16px;margin-top:6px;">
<label style="display:flex;align-items:center;gap:6px;font-size:13px;cursor:pointer;font-weight:500;"><input type="radio" name="gender" value="male" {{ old('gender',$pet->gender)==='male'?'checked':'' }} style="accent-color:#00875e;"> <i class="bi bi-gender-male" style="color:#007dff;font-size:16px;"></i> Male</label>
<label style="display:flex;align-items:center;gap:6px;font-size:13px;cursor:pointer;font-weight:500;"><input type="radio" name="gender" value="female" {{ old('gender',$pet->gender)==='female'?'checked':'' }} style="accent-color:#00875e;"> <i class="bi bi-gender-female" style="color:#ec4899;font-size:16px;"></i> Female</label>
</div></div>
<div><label for="species_id">Species *</label><select id="species_id" name="species_id" required><option value="">Select Species</option>@foreach($species as $s)<option value="{{ $s->id }}" {{ old('species_id',$pet->species_id)==$s->id?'selected':'' }}>{{ $s->name }}</option>@endforeach</select></div>
<div><label for="breed_id">Breed</label><select id="breed_id" name="breed_id"><option value="">Select breed (optional)</option>@if($pet->breed)<option value="{{ $pet->breed->id }}" selected>{{ $pet->breed->name }}</option>@endif</select></div>
</div>
</section>

{{-- Details --}}
<section class="od-section">
<div class="od-section-header"><h2>@include('owner.partials.icon',['name'=>'clipboard2']) Details</h2></div>
<div class="od-fields" style="grid-template-columns:1fr 1fr;">
<div><label for="date_of_birth">Date of Birth *</label><input type="date" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth',$pet->date_of_birth?($pet->date_of_birth instanceof \Carbon\Carbon?$pet->date_of_birth->format('Y-m-d'):$pet->date_of_birth):'') }}" required></div>
<div><label for="weight">Weight (kg)</label><input type="number" id="weight" name="weight" step="0.1" min="0" value="{{ old('weight',$pet->weight) }}"></div>
<div><label for="color">Color</label><input type="text" id="color" name="color" value="{{ old('color',$pet->color) }}" placeholder="e.g. Golden, Black, White"></div>
<div><label for="microchip_number">Microchip Number</label><input type="text" id="microchip_number" name="microchip_number" value="{{ old('microchip_number',$pet->microchip_number) }}" placeholder="Optional"></div>
<div class="full"><label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-weight:500;"><input type="checkbox" name="is_neutered" value="1" {{ old('is_neutered',$pet->is_neutered)?'checked':'' }} style="accent-color:#00875e;width:16px;height:16px;"> Neutered / Spayed</label></div>
<div class="full"><label for="description">Description</label><textarea id="description" name="description" rows="3" placeholder="Tell us about your pet's personality, habits, or anything special...">{{ old('description',$pet->description) }}</textarea></div>
</div>
</section>

{{-- Save --}}
<section class="od-section">
<div style="display:flex;justify-content:flex-end;gap:8px;">
<a class="op-button" href="{{ route('owner.pets.show',$pet) }}">Cancel</a>
<button type="submit" class="op-button op-primary"><i class="bi bi-check-lg"></i> Save Changes</button>
</div>
</section>

</div>

<aside class="od-aside">
{{-- Pet Photo --}}
<section class="od-side-card">
<div class="od-section-header"><h2>@include('owner.partials.icon',['name'=>'images']) Photo</h2></div>
@if($primaryImage)
<div style="border-radius:8px;overflow:hidden;margin-bottom:10px;">
<img src="{{ $storageUrl($primaryImage->image_path) }}" alt="{{ $pet->name }}" style="width:100%;aspect-ratio:1;object-fit:cover;display:block;" onerror="this.parentElement.innerHTML='<div class=\'pet-initials-thumb\'>{{ mb_strtoupper(mb_substr($pet->name,0,1)) }}</div>'">
</div>
@if($pet->images->count()>1)
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:4px;">
@foreach($pet->images->sortBy('sort_order')->take(4) as $img)
<img src="{{ $storageUrl($img->image_path) }}" alt="" style="width:100%;aspect-ratio:1;border-radius:4px;object-fit:cover;{{ $img->is_primary?'border:2px solid #00865e;':'' }}" onerror="this.style.display='none'">
@endforeach
</div>
@endif
@else
<div style="text-align:center;padding:24px;background:linear-gradient(135deg,#e9fbf4,#f3fdfa);border-radius:8px;">
<div style="width:56px;height:56px;border-radius:50%;background:linear-gradient(135deg,#00865e,#27bd96);display:inline-flex;align-items:center;justify-content:center;color:#fff;font-size:1.4rem;font-weight:700;">{{ mb_strtoupper(mb_substr($pet->name,0,1)) }}</div>
<p style="margin:8px 0 0;font-size:12px;color:#99a8c2;">No photo yet</p>
</div>
@endif
</section>

{{-- Manage Images --}}
<section class="od-side-card">
<div class="od-section-header"><h2>@include('owner.partials.icon',['name'=>'upload']) Manage Images</h2></div>
@if($pet->images->count()>0)
<div style="margin-bottom:10px;">
<div style="font-size:11px;color:#52699b;margin-bottom:6px;">Current Images ({{ $pet->images->count() }}/6) — check to remove:</div>
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:4px;">
@foreach($pet->images->sortBy('sort_order') as $image)
<div style="position:relative;border-radius:6px;overflow:hidden;" id="img-wrap-{{ $image->id }}">
<img src="{{ $storageUrl($image->image_path) }}" alt="" style="width:100%;aspect-ratio:1;object-fit:cover;display:block;" onerror="this.style.display='none'">
@if($image->is_primary)<span style="position:absolute;top:3px;left:3px;background:#f5ae00;color:#fff;font-size:8px;font-weight:700;padding:2px 5px;border-radius:3px;"><i class="bi bi-star-fill"></i></span>@endif
<label style="position:absolute;bottom:0;left:0;right:0;background:rgba(0,0,0,.6);display:flex;justify-content:center;gap:6px;padding:3px;cursor:pointer;">
<input type="checkbox" name="remove_images[]" value="{{ $image->id }}" class="d-none" onchange="document.getElementById('img-wrap-{{ $image->id }}').style.opacity=this.checked?.3:1">
<i class="bi bi-trash" style="color:#fff;font-size:10px;"></i>
</label>
@if(!$image->is_primary)
<label style="position:absolute;top:3px;right:3px;cursor:pointer;background:rgba(0,0,0,.5);border-radius:50%;width:20px;height:20px;display:flex;align-items:center;justify-content:center;">
<input type="radio" name="primary_image_id" value="{{ $image->id }}" class="d-none">
<i class="bi bi-star" style="color:#fff;font-size:9px;" onclick="setPrimary(this,{{ $image->id }})"></i>
</label>
@endif
</div>
@endforeach
</div>
@else
<p style="font-size:12px;color:#99a8c2;margin:0 0 8px;">No images uploaded.</p>
@endif
<div><input type="file" name="images[]" id="images" multiple accept=".jpg,.jpeg,.png,.gif,.webp" style="width:100%;font-size:12px;"><small style="color:#99a8c2;font-size:11px;">Upload new images (max 6 total, 5MB each).</small></div>
<div id="image-previews" style="display:flex;flex-wrap:wrap;gap:6px;margin-top:8px;"></div>
</section>

{{-- Quick Info --}}
<section class="od-side-card">
<div class="od-section-header"><h2>@include('owner.partials.icon',['name'=>'info-circle']) Quick Info</h2></div>
<div style="font-size:12px;display:grid;gap:6px;">
<div style="display:flex;justify-content:space-between;"><span style="color:#52699b;">Created</span><span>{{ $pet->created_at->format('M d, Y') }}</span></div>
<hr style="margin:0;border-color:#edf2f6;">
<div style="display:flex;justify-content:space-between;"><span style="color:#52699b;">Last Updated</span><span>{{ $pet->updated_at->format('M d, Y') }}</span></div>
<hr style="margin:0;border-color:#edf2f6;">
<div style="display:flex;justify-content:space-between;"><span style="color:#52699b;">Health Records</span><span>{{ $pet->healthRecords->count() }}</span></div>
<hr style="margin:0;border-color:#edf2f6;">
<div style="display:flex;justify-content:space-between;"><span style="color:#52699b;">Vaccinations</span><span>{{ $pet->vaccinations->count() }}</span></div>
</div>
</section>
</aside>
</div>
</form>
@include('owner.partials.discovery-banner')
</div>

@push('scripts')
<script>
function setPrimary(el){document.querySelectorAll('input[name="primary_image_id"]').forEach(r=>r.checked=false);el.previousElementSibling.checked=true;document.querySelectorAll('#img-wrap- span[style*="star-fill"]').forEach(b=>b.remove());el.closest('div[style*="position"]').insertAdjacentHTML('afterbegin','<span style="position:absolute;top:3px;left:3px;background:#f5ae00;color:#fff;font-size:8px;font-weight:700;padding:2px 5px;border-radius:3px;"><i class="bi bi-star-fill"></i></span>');}
document.getElementById('images').addEventListener('change',function(e){const c=document.getElementById('image-previews');c.innerHTML='';Array.from(e.target.files).slice(0,6).forEach(f=>{if(f.type.startsWith('image/')){const r=new FileReader();r.onload=ev=>{const d=document.createElement('div');d.style.cssText='width:64px;height:64px;border-radius:6px;overflow:hidden;';d.innerHTML='<img src="'+ev.target.result+'" style="width:100%;height:100%;object-fit:cover;">';c.appendChild(d);};r.readAsDataURL(f);}});});
document.getElementById('species_id').addEventListener('change',function(){const sid=this.value,breedSelect=document.getElementById('breed_id'),current='{{ old("breed_id",$pet->breed_id) }}';breedSelect.innerHTML='<option value="">Loading...</option>';if(!sid){breedSelect.innerHTML='<option value="">Select breed (optional)</option>';return;}fetch('/owner/species/'+sid+'/breeds').then(r=>r.json()).then(data=>{let o='<option value="">Select breed (optional)</option>';(data.data||(Array.isArray(data)?data:[])).forEach(b=>{o+='<option value="'+b.id+'"'+(b.id==current?' selected':'')+'>'+b.name+'</option>';});breedSelect.innerHTML=o;}).catch(()=>{breedSelect.innerHTML='<option value="">Select breed (optional)</option>';});});
const sp=document.getElementById('species_id');if(sp.value)sp.dispatchEvent(new Event('change'));
</script>
@endpush
@endsection
