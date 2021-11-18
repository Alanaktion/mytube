@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex justify-between">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="btn btn-secondary disabled">
                {!! __('pagination.previous') !!}
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="btn btn-secondary">
                {!! __('pagination.previous') !!}
            </a>
        @endif

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="btn btn-secondary ml-3">
                {!! __('pagination.next') !!}
            </a>
        @else
            <span class="btn btn-secondary disabled ml-3">
                {!! __('pagination.next') !!}
            </span>
        @endif
    </nav>
@endif
