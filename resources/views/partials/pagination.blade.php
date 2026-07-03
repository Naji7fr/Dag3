<div class="pagination">
    @if($paginator->onFirstPage())
        <span>&lsaquo;</span>
    @else
        <a href="{{ $paginator->previousPageUrl() }}">&lsaquo;</a>
    @endif

    @for($pagina = 1; $pagina <= $paginator->lastPage(); $pagina++)
        @if($pagina === $paginator->currentPage())
            <span class="active">{{ $pagina }}</span>
        @else
            <a href="{{ $paginator->url($pagina) }}">{{ $pagina }}</a>
        @endif
    @endfor

    @if($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}">&rsaquo;</a>
    @else
        <span>&rsaquo;</span>
    @endif
</div>
