<header class="mb-3 sm:flex">
    <div class="flex items-center gap-2">
        <h1 class="text-2xl lg:text-3xl mb-2">{{ $channel->title }}</h1>
        @if ($channel->source_link)
            <a href="{{ $channel->source_link }}" class="bg-transparent text-red-600 hover:bg-red-600 hover:text-white font-bold py-2 px-4 rounded-full flex" aria-label="View on YouTube" data-tooltip>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="inline-block w-5 h-5" aria-hidden="true">
                    <path d="M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 0 0-1.94 2A29 29 0 0 0 1 11.75a29 29 0 0 0 .46 5.33A2.78 2.78 0 0 0 3.4 19c1.72.46 8.6.46 8.6.46s6.88 0 8.6-.46a2.78 2.78 0 0 0 1.94-2 29 29 0 0 0 .46-5.25 29 29 0 0 0-.46-5.33z" fill="none" stroke="currentColor"></path>
                    <polygon points="9.75 15.02 15.5 11.75 9.75 8.48 9.75 15.02" fill="currentColor"></polygon>
                </svg>
            </a>
        @endif
    </div>
    <form class="sm:ml-auto mt-3 sm:mt-0" action="/channels/{{ $channel->uuid }}/search">
        <input type="search" class="dark:bg-trueGray-850 focus:outline-none focus:ring-blue-500 focus:ring-2 rounded-full py-2 pl-5 pr-3 block w-full dark:placeholder-trueGray-400 dark:text-trueGray-100 appearance-none leading-normal" name="q" value="{{ $channelQ ?? null }}" placeholder="Search channel">
    </form>
</header>
<nav class="flex border-b border-gray-300 dark:border-trueGray-700 mb-3 md:mb-4 lg:mb-6">
    <a
        class="px-3 py-2
            @if($tab == 'videos')
                -mb-px border-b-2 text-blue-600 border-blue-600 hover:text-blue-500 dark:text-blue-400 dark:border-blue-400 dark:hover:text-blue-300
            @else
                text-gray-700 hover:text-gray-900 dark:text-trueGray-300 dark:hover:text-white
            @endif mr-2"
        href="/channels/{{ $channel->uuid }}">
        Videos
    </a>
    <a
        class="px-3 py-2
            @if($tab == 'playlists')
                -mb-px border-b-2 text-blue-600 border-blue-600 hover:text-blue-500 dark:text-blue-400 dark:border-blue-400 dark:hover:text-blue-300
            @else
                text-gray-700 hover:text-gray-900 dark:text-trueGray-300 dark:hover:text-white
            @endif"
        href="/channels/{{ $channel->uuid }}/playlists">
        Playlists
    </a>
    @if($tab == 'search')
        <a
            class="px-3 py-2 -mb-px -mb-px border-b-2 text-blue-600 border-blue-600 hover:text-blue-500 dark:text-blue-400 dark:border-blue-400 dark:hover:text-blue-300"
            href="/channels/{{ $channel->uuid }}/playlists">
            Search Results
        </a>
    @endif
</nav>
