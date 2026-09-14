@if(in_array($name, ['paw','animals']))
<svg class="sh-icon {{ $class ?? '' }}" viewBox="0 0 48 48" fill="currentColor" aria-hidden="true"><ellipse cx="11" cy="17" rx="5.5" ry="7.5" transform="rotate(-25 11 17)"/><ellipse cx="21" cy="9" rx="5.5" ry="7.5" transform="rotate(-8 21 9)"/><ellipse cx="33" cy="10" rx="5.5" ry="7.5" transform="rotate(15 33 10)"/><ellipse cx="41" cy="22" rx="5.5" ry="7.5" transform="rotate(30 41 22)"/><path d="M10 36c0-5 6-8 9-13 3-5 8-5 11 0 3 5 9 9 9 14 0 6-5 8-10 6-5-2-7-2-11 0-5 2-8-1-8-7z"/></svg>
@elseif($name==='cat')
<svg class="sh-icon" viewBox="0 0 32 32" fill="currentColor" aria-hidden="true"><path d="M5 3 12 8q4-1 8 0l7-5 1 16c0 8-5 11-12 11S4 27 4 19Z"/><circle cx="11" cy="18" r="1.5" fill="#fff"/><circle cx="21" cy="18" r="1.5" fill="#fff"/><path d="m14 23 2 2 2-2" fill="#fff"/></svg>
@elseif($name==='dog')
<svg class="sh-icon" viewBox="0 0 36 32" fill="currentColor" aria-hidden="true"><path d="M8 13h15l2-8 5-3 3 4-1 4 4 2-2 4h-6l-1 7 2 6h-5l-3-7h-9l-1 7H7l1-10-3-3Q0 14 1 8l3 1q-1 4 4 4Z"/><circle cx="29" cy="7" r="1" fill="#fff"/></svg>
@else
@php($icons=['dashboard'=>'house-door-fill','care'=>'clipboard2-heart-fill','requests'=>'people-fill','history'=>'clock-history','notifications'=>'bell-fill','profile'=>'gear-fill','logout'=>'box-arrow-right','menu'=>'list','plus'=>'plus-lg','search'=>'search','medical'=>'plus-square-fill','feeding'=>'cup-fill','grooming'=>'brush-fill','vaccination'=>'eyedropper','other'=>'clipboard2','check'=>'check-circle-fill','reject'=>'x-circle-fill','pending'=>'hourglass-split','reviewing'=>'search','document'=>'file-earmark-text-fill','heart'=>'heart-fill','calendar'=>'calendar3','clock'=>'clock','tag'=>'tag-fill','health'=>'heart-pulse-fill','chevron'=>'chevron-right','more'=>'three-dots','leaf'=>'leaf-fill','warning'=>'exclamation-circle-fill','bolt'=>'lightning-fill'])
<i class="bi bi-{{ $icons[$name] ?? $name }} sh-icon {{ $class ?? '' }}" aria-hidden="true"></i>
@endif
