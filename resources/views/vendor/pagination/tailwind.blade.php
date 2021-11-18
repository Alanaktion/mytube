@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex items-center justify-between">
        <div class="flex justify-between flex-1 sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="btn btn-secondary disabled">
                    {!! __('pagination.previous') !!}
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="btn btn-secondary">
                    {!! __('pagination.previous') !!}
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="btn btn-secondary">
                    {!! __('pagination.next') !!}
                </a>
            @else
                <span class="btn btn-secondary disabled ml-3">
                    {!! __('pagination.next') !!}
                </span>
            @endif
        </div>

        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-gray-700 dark:text-trueGray-400 leading-5">
                    {!! __('Showing :first to :last of :total results', [
                        'first' => '<span class="font-medium">' . $paginator->firstItem() . '</span>',
                        'last' => '<span class="font-medium">' . $paginator->lastItem() . '</span>',
                        'total' => '<span class="font-medium">' . $paginator->total() . '</span>',
                    ]) !!}
                </p>
            </div>

            <div>
                <span class="relative z-0 inline-flex shadow-sm rounded-md">
                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <span aria-disabled="true" aria-label="{{ __('pagination.previous') }}">
                            <span class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 dark:text-trueGray-500 dark:bg-trueGray-800 dark:border-trueGray-700 cursor-default rounded-l-md leading-5" aria-hidden="true">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 dark:text-trueGray-500 dark:bg-trueGray-800 dark:border-trueGray-700 rounded-l-md leading-5 hover:text-blue-500 dark:hover:text-trueGray-400 focus:z-10 focus:outline-none focus:ring ring-gray-300 dark:ring-trueGray-600 focus:border-blue-300 active:bg-gray-100 active:text-gray-500 dark:active:bg-trueGray-700 dark:active:text-trueGray-500" aria-label="{{ __('pagination.previous') }}">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <span aria-disabled="true">
                                <span class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-gray-700 bg-white border border-gray-300 dark:text-trueGray-400 dark:bg-trueGray-800 dark:border-trueGray-700 cursor-default leading-5">{{ $element }}</span>
                            </span>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span aria-current="page">
                                        <span class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-white bg-gradient-to-b from-blue-500 to-blue-600 border border-blue-500 cursor-default leading-5">{{ $page }}</span>
                                    </span>
                                @else
                                    <a href="{{ $url }}" class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-gray-700 bg-white border border-gray-300 dark:text-trueGray-400 dark:bg-trueGray-800 dark:border-trueGray-700 leading-5 hover:text-blue-500 dark:hover:text-trueGray-300 focus:z-10 focus:outline-none focus:ring ring-gray-300 dark:ring-trueGray-600 focus:border-blue-300 active:bg-gray-100 active:text-gray-700 dark:active:bg-trueGray-700 dark:active:text-trueGray-400" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="relative inline-flex items-center px-2 py-2 -ml-px text-sm font-medium text-gray-500 bg-white border border-gray-300 dark:text-trueGray-500 dark:bg-trueGray-800 dark:border-trueGray-700 rounded-r-md leading-5 hover:text-blue-500 dark:hover:text-trueGray-400 focus:z-10 focus:outline-none focus:ring ring-gray-300 dark:ring-trueGray-600 focus:border-blue-300 active:bg-gray-100 active:text-gray-500 dark:active:bg-trueGray-700 dark:active:text-trueGray-500" aria-label="{{ __('pagination.next') }}">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    @else
                        <span aria-disabled="true" aria-label="{{ __('pagination.next') }}">
                            <span class="relative inline-flex items-center px-2 py-2 -ml-px text-sm font-medium text-gray-500 bg-white border border-gray-300 dark:text-trueGray-500 dark:bg-trueGray-800 dark:border-trueGray-700 cursor-default rounded-r-md leading-5" aria-hidden="true">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        </span>
                    @endif
                </span>
            </div>
        </div>
    </nav>
@endif
