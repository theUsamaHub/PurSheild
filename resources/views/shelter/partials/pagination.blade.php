<div class="sh-pagination">
    <span>Showing {{ $items->firstItem() ?? 0 }} to {{ $items->lastItem() ?? 0 }} of {{ $items->total() }} {{ $noun ?? 'records' }}</span>
    @if($items->hasPages())<nav aria-label="Pagination"><a class="sh-page {{ $items->onFirstPage()?'disabled':'' }}" @if(!$items->onFirstPage()) href="{{ $items->previousPageUrl() }}" @else aria-disabled="true" @endif aria-label="Previous page">‹</a>
    @php($pages=array_unique(array_filter([1,$items->currentPage()-1,$items->currentPage(),$items->currentPage()+1,$items->lastPage()],fn($p)=>$p>=1&&$p<=$items->lastPage())))
    @php(sort($pages))
    @foreach($pages as $page)
        @if(!$loop->first && $page-$pages[$loop->index-1]>1)<span class="sh-page">…</span>@endif
        <a class="sh-page {{ $page===$items->currentPage()?'active':'' }}" href="{{ $items->url($page) }}" @if($page===$items->currentPage()) aria-current="page" @endif>{{ $page }}</a>
    @endforeach
    <a class="sh-page {{ !$items->hasMorePages()?'disabled':'' }}" @if($items->hasMorePages()) href="{{ $items->nextPageUrl() }}" @else aria-disabled="true" @endif aria-label="Next page">›</a></nav>@endif
</div>
