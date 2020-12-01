@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex justify-between">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border-gray-300 dark:text-trueGray-600 dark:bg-trueGray-800 border dark:border-trueGray-700 cursor-default leading-5 rounded-md">
                {!! __('pagination.previous') !!}
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 dark:text-trueGray-400 dark:bg-trueGray-800 dark:border-trueGray-700 leading-5 rounded-md hover:text-blue-500 dark:hover:text-trueGray-300 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-100 dark:active:bg-trueGray-700 active:text-gray-700 dark:active:text-trueGray-400">
                {!! __('pagination.previous') !!}
            </a>
        @endif

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 dark:text-trueGray-400 dark:bg-trueGray-800 dark:border-trueGray-700 leading-5 rounded-md hover:text-blue-500 dark:hover:text-trueGray-300 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-100 dark:active:bg-trueGray-700 active:text-gray-700 dark:active:text-trueGray-400">
                {!! __('pagination.next') !!}
            </a>
        @else
            <span class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-gray-500 bg-white border-gray-300 dark:text-trueGray-600 dark:bg-trueGray-800 border dark:border-trueGray-700 cursor-default leading-5 rounded-md">
                {!! __('pagination.next') !!}
            </span>
        @endif
    </nav>
@endif
