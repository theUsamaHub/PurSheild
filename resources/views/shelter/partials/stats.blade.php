<div class="sh-stats {{ ($compact ?? false) ? 'sh-stats-compact' : '' }}" style="--stat-count:{{ count($cards) }}">
@foreach($cards as $card)
<a class="sh-stat sh-stat-{{ $card['tone'] }}" href="{{ $card['url'] }}">
    <span class="sh-stat-icon">@include('shelter.partials.icon',['name'=>$card['icon']])</span>
    <div><span class="sh-stat-label">{{ $card['label'] }}</span><strong>{{ $card['value'] }}</strong></div>
    @if(isset($card['description']))<small>{{ $card['description'] }}</small><span class="sh-stat-ghost">@include('shelter.partials.icon',['name'=>$card['ghost']??$card['icon']])</span>@endif
</a>
@endforeach
</div>
