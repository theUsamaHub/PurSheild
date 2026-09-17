@if ($paginator->hasPages())
<nav class="fs-pagination" aria-label="Page navigation">

    @if ($paginator->total() > 0)
    <div class="fs-page-info">
        Showing {{ $paginator->firstItem() }} to {{ $paginator->lastItem() }} of {{ $paginator->total() }} results
    </div>
    @endif

    <ul class="fs-page-list">

        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <li class="fs-page-item disabled" aria-disabled="true" aria-label="Previous">
                <span class="fs-page-btn" aria-hidden="true">&lsaquo;</span>
            </li>
        @else
            <li class="fs-page-item">
                <a class="fs-page-btn" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Previous">&lsaquo;</a>
            </li>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <li class="fs-page-item disabled" aria-disabled="true">
                    <span class="fs-page-btn fs-page-dots">{{ $element }}</span>
                </li>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <li class="fs-page-item active" aria-current="page">
                            <span class="fs-page-btn">{{ $page }}</span>
                        </li>
                    @else
                        <li class="fs-page-item">
                            <a class="fs-page-btn" href="{{ $url }}">{{ $page }}</a>
                        </li>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <li class="fs-page-item">
                <a class="fs-page-btn" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Next">&rsaquo;</a>
            </li>
        @else
            <li class="fs-page-item disabled" aria-disabled="true" aria-label="Next">
                <span class="fs-page-btn" aria-hidden="true">&rsaquo;</span>
            </li>
        @endif

    </ul>
</nav>
@endif
