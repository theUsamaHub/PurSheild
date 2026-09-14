<span class="sh-person-avatar">
@if($person?->profile_image)<img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($person->profile_image) }}" alt="{{ $person->name }}" onerror="this.hidden=true;this.nextElementSibling.hidden=false"><span hidden>{{ mb_substr($person->name,0,1) }}</span>
@else {{ mb_substr($person?->name ?? '?',0,1) }} @endif
</span>
