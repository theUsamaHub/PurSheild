@push('scripts')
<script>
document.addEventListener('DOMContentLoaded',()=>{
 document.querySelectorAll('[data-favorite]').forEach(button=>button.addEventListener('click',async()=>{
  button.disabled=true;const saved=button.getAttribute('aria-pressed')!=='true';
  try{const response=await fetch(button.dataset.favorite,{method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content},body:JSON.stringify({saved})});if(!response.ok)throw new Error('Your favorite could not be saved. Please refresh and try again.');const result=await response.json();button.setAttribute('aria-pressed',String(result.saved));button.querySelector('i').className=result.saved?'bi bi-heart-fill':'bi bi-heart';document.getElementById('discoveryStatus').textContent=result.saved?'Added to favorites.':'Removed from favorites.';}catch(error){document.getElementById('discoveryStatus').textContent=error.message;}finally{button.disabled=false;}
 }));
 const species=document.querySelector('[data-adoption-species]'),breed=document.querySelector('[data-adoption-breed]');
 if(species&&breed){const options=Array.from(breed.options).slice(1).map(o=>({value:o.value,label:o.textContent,species:o.dataset.species}));function update(preserve){const old=preserve?breed.value:'';breed.replaceChildren(new Option('All Breeds',''));options.filter(o=>!species.value||o.species===species.value).forEach(o=>breed.add(new Option(o.label,o.value,false,o.value===old)));}species.addEventListener('change',()=>update(false));update(true);}
 const locate=document.getElementById('useVetLocation');if(locate)locate.addEventListener('click',()=>{
  const status=document.getElementById('locationStatus');if(!navigator.geolocation){status.textContent='Location is unavailable. Search by city instead.';return;}status.textContent='Finding your location…';navigator.geolocation.getCurrentPosition(position=>{document.querySelectorAll('[name=latitude]').forEach(i=>i.value=position.coords.latitude);document.querySelectorAll('[name=longitude]').forEach(i=>i.value=position.coords.longitude);document.getElementById('vetDistance').disabled=false;status.textContent='Location ready. Choose a distance and apply filters.';},()=>status.textContent='Location was not shared. You can still search by city.',{timeout:10000,maximumAge:300000});
 });
 const comparison=document.getElementById('vetCompareForm');if(comparison)comparison.addEventListener('submit',event=>{const count=document.querySelectorAll('[name="compare[]"]:checked').length;if(count<2||count>3){event.preventDefault();document.getElementById('discoveryStatus').textContent='Select two or three veterinarians to compare.';}});
 const type=document.getElementById('healthRecordType');if(type){const general=document.getElementById('generalRecordForm'),vaccine=document.getElementById('vaccinationRecordForm');function change(){const isVaccine=type.value==='vaccination';general.hidden=isVaccine;vaccine.hidden=!isVaccine;document.getElementById('generalRecordType').value=isVaccine?'other':type.value;}type.addEventListener('change',change);change();}
 const selected=document.getElementById('selectedHealthRecord');if(selected&&window.bootstrap)new bootstrap.Modal(selected).show();
 @if($errors->any() && request()->routeIs('owner.health.*'))
 const add=document.getElementById('addHealthRecord');if(add&&window.bootstrap)new bootstrap.Modal(add).show();
 @endif
});
</script>
@endpush
<div id="discoveryStatus" class="od-live-status" role="status" aria-live="polite"></div>
