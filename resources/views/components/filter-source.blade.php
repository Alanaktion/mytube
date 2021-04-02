<div x-data="{ open: false }" class="relative inline-block text-left">
    <div>
        <button type="button" class="inline-flex items-center justify-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-offset-white focus:ring-blue-500 dark:border-trueGray-700 dark:bg-trueGray-800 dark:text-trueGray-300 dark:focus:ring-offset-trueGray-900" id="source-menu" :aria-expanded="open ? 'true' : 'false'" aria-haspopup="true" @click="open = true">
            <svg class="w-4 h-4 -ml-1 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
            </svg>
            Source
            <svg class="-mr-1 ml-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
        </button>
    </div>
    <div class="origin-top-right absolute left-0 sm:left-auto sm:right-0 mt-2 w-56 rounded-md shadow-lg bg-white dark:bg-trueGray-800 border dark:border-trueGray-850 focus:outline-none z-10"
        role="menu"
        aria-orientation="vertical"
        aria-labelledby="source-menu"
        x-show="open"
        @click.away="open = false"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95">
        <div class="py-1" role="none">
            <a href="{{ url("$path") }}"
                class="block px-4 py-2 text-sm {{ !$value ? 'text-white bg-blue-400 dark:bg-blue-600 hover:bg-blue-500 dark:hover:bg-blue-500' : 'text-gray-700 dark:text-trueGray-300 hover:bg-gray-100 dark:hover:bg-trueGray-700' }}"
                role="menuitem">
                All Sources
            </a>
            <hr class="border-gray-200 dark:border-trueGray-850 my-1">
            @php
                $sources = [
                    'youtube' => 'YouTube',
                    'twitch' => 'Twitch',
                    'twitter' => 'Twitter',
                    'floatplane' => 'Floatplane',
                ];
            @endphp
            @foreach ($sources as $sourceKey => $sourceName)
                <a href="{{ url("$path?source=$sourceKey") }}"
                    class="block px-4 py-2 text-sm {{ $value == $sourceKey ? 'text-white bg-blue-400 dark:bg-blue-600 hover:bg-blue-500 dark:hover:bg-blue-500' : 'text-gray-700 dark:text-trueGray-300 hover:bg-gray-100 dark:hover:bg-trueGray-700' }}"
                    role="menuitem">
                    {{ $sourceName }}
                </a>
            @endforeach
        </div>
    </div>
</div>
