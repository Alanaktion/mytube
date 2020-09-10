@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex justify-between">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-ngray-600 bg-ngray-800 border border-ngray-700 cursor-default leading-5 rounded-md">
                {!! __('pagination.previous') !!}
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-ngray-400 bg-ngray-800 border border-ngray-700 leading-5 rounded-md hover:text-ngray-300 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 active:bg-ngray-700 active:text-ngray-400">
                {!! __('pagination.previous') !!}
            </a>
        @endif

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-ngray-400 bg-ngray-800 border border-ngray-700 leading-5 rounded-md hover:text-ngray-300 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 active:bg-ngray-700 active:text-ngray-400">
                {!! __('pagination.next') !!}
            </a>
        @else
            <span class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-ngray-600 bg-ngray-800 border border-ngray-700 cursor-default leading-5 rounded-md">
                {!! __('pagination.next') !!}
            </span>
        @endif
    </nav>
@endif
