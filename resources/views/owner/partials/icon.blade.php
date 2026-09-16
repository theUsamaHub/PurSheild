@if(in_array($name, ['paw','animals']))
<svg class="op-icon {{ $class ?? '' }}" viewBox="0 0 48 48" fill="currentColor" aria-hidden="true"><ellipse cx="11" cy="17" rx="5.5" ry="7.5" transform="rotate(-25 11 17)"/><ellipse cx="21" cy="9" rx="5.5" ry="7.5" transform="rotate(-8 21 9)"/><ellipse cx="33" cy="10" rx="5.5" ry="7.5" transform="rotate(15 33 10)"/><ellipse cx="41" cy="22" rx="5.5" ry="7.5" transform="rotate(30 41 22)"/><path d="M10 36c0-5 6-8 9-13 3-5 8-5 11 0 3 5 9 9 9 14 0 6-5 8-10 6-5-2-7-2-11 0-5 2-8-1-8-7z"/></svg>
@elseif($name==='feeding')
<svg class="op-icon" viewBox="0 0 32 32" fill="currentColor" aria-hidden="true"><ellipse cx="16" cy="11" rx="10" ry="4"/><path d="M6 12q10 7 20 0l4 13q-14 7-28 0z"/><path d="M12 6h8" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"/></svg>
@elseif($name==='exercise')
<svg class="op-icon" viewBox="0 0 32 32" fill="currentColor" aria-hidden="true"><rect x="3" y="8" width="6" height="16" rx="2"/><rect x="23" y="8" width="6" height="16" rx="2"/><rect x="9" y="14" width="14" height="4"/><rect x="0" y="12" width="3" height="8" rx="1"/><rect x="29" y="12" width="3" height="8" rx="1"/></svg>
@elseif($name==='vaccination')
<svg class="op-icon" viewBox="0 0 32 32" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m8 18 10-10 7 7-10 10zM15 5l12 12M23 4l5 5M21 10l4-4M9 24l-5 5M7 21l4 4M12 14l4 4M16 10l4 4"/></svg>
@elseif($name==='flask-fill')
<svg class="op-icon" viewBox="0 0 32 32" fill="currentColor" aria-hidden="true"><path d="M11 2h10v3h-2v7l9 14q2 4-3 4H7q-5 0-3-4l9-14V5h-2zm4 11-5 9h12l-5-9V5h-2z"/><circle cx="14" cy="25" r="1.4" fill="#fff"/></svg>
@elseif($name==='weight')
<svg class="op-icon" viewBox="0 0 28 28" fill="currentColor" aria-hidden="true"><path d="M8 9h12l5 17H3z"/><circle cx="14" cy="6" r="4" fill="none" stroke="currentColor" stroke-width="3"/><path d="M14 13v5" fill="none" stroke="#fff" stroke-width="2"/></svg>
@elseif($name==='vet')
<svg class="op-icon" viewBox="0 0 32 32" fill="none" stroke="currentColor" stroke-width="3" aria-hidden="true"><path d="M5 3v10a7 7 0 0 0 14 0V3M3 3h5m8 0h5M12 20v3a6 6 0 0 0 12 0v-4"/><circle cx="24" cy="16" r="3"/></svg>
@elseif($name==='rabbit')
<svg class="op-icon" viewBox="0 0 32 32" fill="currentColor" aria-hidden="true"><ellipse cx="11" cy="8" rx="3" ry="8" transform="rotate(-15 11 8)"/><ellipse cx="21" cy="8" rx="3" ry="8" transform="rotate(15 21 8)"/><ellipse cx="16" cy="22" rx="11" ry="9"/></svg>
@elseif($name==='cat')
<svg class="op-icon" viewBox="0 0 32 32" fill="currentColor" aria-hidden="true"><path d="M5 3 12 8q4-1 8 0l7-5 1 16c0 8-5 11-12 11S4 27 4 19Z"/><circle cx="11" cy="18" r="1.5" fill="#fff"/><circle cx="21" cy="18" r="1.5" fill="#fff"/><path d="m14 23 2 2 2-2" fill="#fff"/></svg>
@elseif($name==='dog')
<svg class="op-icon" viewBox="0 0 36 32" fill="currentColor" aria-hidden="true"><path d="M8 13h15l2-8 5-3 3 4-1 4 4 2-2 4h-6l-1 7 2 6h-5l-3-7h-9l-1 7H7l1-10-3-3Q0 14 1 8l3 1q-1 4 4 4Z"/><circle cx="29" cy="7" r="1" fill="#fff"/></svg>
@else
@php($icons=['dashboard'=>'house-door-fill','care'=>'clipboard2-heart-fill','requests'=>'people-fill','history'=>'clock-history','notifications'=>'bell-fill','profile'=>'gear-fill','logout'=>'box-arrow-right','menu'=>'list','plus'=>'plus-lg','search'=>'search','medical'=>'plus-square-fill','feeding'=>'cup-fill','grooming'=>'brush-fill','vaccination'=>'eyedropper','other'=>'clipboard2','check'=>'check-circle-fill','reject'=>'x-circle-fill','pending'=>'hourglass-split','reviewing'=>'search','document'=>'file-earmark-text-fill','heart'=>'heart-fill','calendar'=>'calendar3','clock'=>'clock','tag'=>'tag-fill','health'=>'heart-pulse-fill','chevron'=>'chevron-right','more'=>'three-dots','leaf'=>'leaf-fill','warning'=>'exclamation-circle-fill','bolt'=>'lightning-fill'])
<i class="bi bi-{{ $icons[$name] ?? $name }} op-icon {{ $class ?? '' }}" aria-hidden="true"></i>
@endif
