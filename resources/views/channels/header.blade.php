<header class="mb-3 xl:mb-5 sm:flex">
    <div class="flex items-center gap-2">
        <img class="h-10 w-10 rounded-full" src="{{ $channel->image_url ?? '/images/channels/' . $channel->uuid }}" alt="{{ $channel->name }}">
        <h1 class="text-2xl lg:text-3xl ml-3">{{ $channel->title }}</h1>
        @if ($channel->source_link)
            <a href="{{ $channel->source_link }}"
                class="bg-transparent text-red-600 hover:bg-red-600 hover:text-white font-bold py-2 px-4 rounded-full flex"
                aria-label="View on {{ $channel->type == 'youtube' ? 'YouTube' : ucfirst($channel->type) }}"
                data-tooltip>
                <x-source-icon :type="$channel->type" />
            </a>
        @endif
    </div>
    <form class="sm:ml-auto mt-3 sm:mt-0" action="/channels/{{ $channel->uuid }}/search">
        <input type="search" class="dark:bg-trueGray-850 focus:outline-none focus:ring-blue-500 focus:ring-2 rounded-full py-2 pl-5 pr-3 block w-full dark:placeholder-trueGray-400 dark:text-trueGray-100 border-gray-400 dark:border-trueGray-700 appearance-none leading-normal" name="q" value="{{ $channelQ ?? null }}" placeholder="Search channel">
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
        <span class="ml-1 px-2 align-middle text-sm bg-gray-200 dark:bg-trueGray-700 text-black dark:text-white rounded-full">{{ $channel->videos()->count() }}</span>
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
        <span class="ml-1 px-2 align-middle text-sm bg-gray-200 dark:bg-trueGray-700 text-black dark:text-white rounded-full">{{ $channel->playlists()->count() }}</span>
    </a>
    <a
        class="px-3 py-2
            @if($tab == 'about')
                -mb-px border-b-2 text-blue-600 border-blue-600 hover:text-blue-500 dark:text-blue-400 dark:border-blue-400 dark:hover:text-blue-300
            @else
                text-gray-700 hover:text-gray-900 dark:text-trueGray-300 dark:hover:text-white
            @endif"
        href="/channels/{{ $channel->uuid }}/about">
        About
    </a>
    @if($tab == 'search')
        <a
            class="px-3 py-2 -mb-px -mb-px border-b-2 text-blue-600 border-blue-600 hover:text-blue-500 dark:text-blue-400 dark:border-blue-400 dark:hover:text-blue-300"
            href="/channels/{{ $channel->uuid }}/playlists">
            Search Results
        </a>
    @endif
</nav>
