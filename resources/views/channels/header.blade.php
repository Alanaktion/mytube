<header class="mb-3 xl:mb-5 sm:flex">
    <div class="flex items-center gap-2">
        <img class="h-10 w-10 rounded-full" src="{{ $channel->image_url ?? '/images/channels/' . $channel->uuid }}" alt="{{ $channel->name }}">
        <h1 class="text-2xl lg:text-3xl ml-3">{{ $channel->title }}</h1>
        @if ($channel->source_link)
            <a href="{{ $channel->source_link }}"
                class="p-2 rounded-full text-sm text-red-600 focus:bg-gray-200 dark:focus:bg-trueGray-800 dark:text-red-500 hover:bg-gray-300 dark:hover:bg-trueGray-700 tooltip-right sm:tooltip-center focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 dark:focus:ring-offset-trueGray-900 dark:focus:ring-red-600"
                aria-label="{{ __('View on :source', ['source' => $channel->type == 'youtube' ? 'YouTube' : ucfirst($channel->type)]) }}"
                data-tooltip>
                <x-source-icon :type="$channel->type" class="h-6 w-6" />
            </a>
        @endif
        @auth
            <x-favorite-toggle :model="$channel" />
        @endauth
    </div>
    <form class="sm:ml-auto mt-3 sm:mt-0" action="/channels/{{ $channel->uuid }}/search">
        <input type="search" class="dark:bg-trueGray-850 focus:outline-none focus:ring-blue-500 focus:ring-2 rounded-full py-2 pl-5 pr-3 block w-full dark:placeholder-trueGray-400 dark:text-trueGray-100 border-gray-400 dark:border-trueGray-700 appearance-none leading-normal" name="q" value="{{ $channelQ ?? null }}" placeholder="{{ __('Search channel') }}">
    </form>
</header>
<nav class="flex border-b border-gray-300 dark:border-trueGray-700 mb-3 md:mb-4 lg:mb-6">
    <a
        class="px-3 py-2
            {{ ($tab == 'videos')
                ? '-mb-px border-b-2 text-blue-600 border-blue-600 hover:text-blue-500 dark:text-blue-400 dark:border-blue-400 dark:hover:text-blue-300'
                : 'text-gray-700 hover:text-gray-900 dark:text-trueGray-300 dark:hover:text-white'
            }}"
        href="/channels/{{ $channel->uuid }}">
        {{ __('Videos') }}
        <span class="ml-1 px-2 align-middle text-sm bg-gray-200 dark:bg-trueGray-700 text-black dark:text-white rounded-full">{{ $channel->videos()->count() }}</span>
    </a>
    <a
        class="px-3 py-2
            {{ ($tab == 'playlists')
                ? '-mb-px border-b-2 text-blue-600 border-blue-600 hover:text-blue-500 dark:text-blue-400 dark:border-blue-400 dark:hover:text-blue-300'
                : 'text-gray-700 hover:text-gray-900 dark:text-trueGray-300 dark:hover:text-white'
            }}"
        href="/channels/{{ $channel->uuid }}/playlists">
        {{ __('Playlists') }}
        <span class="ml-1 px-2 align-middle text-sm bg-gray-200 dark:bg-trueGray-700 text-black dark:text-white rounded-full">{{ $channel->playlists()->count() }}</span>
    </a>
    <a
        class="px-3 py-2
            {{ ($tab == 'about')
                ? '-mb-px border-b-2 text-blue-600 border-blue-600 hover:text-blue-500 dark:text-blue-400 dark:border-blue-400 dark:hover:text-blue-300'
                : 'text-gray-700 hover:text-gray-900 dark:text-trueGray-300 dark:hover:text-white'
            }}"
        href="/channels/{{ $channel->uuid }}/about">
        {{ __('About') }}
    </a>
    @if($tab == 'search')
        <a
            class="px-3 py-2 -mb-px border-b-2 text-blue-600 border-blue-600 hover:text-blue-500 dark:text-blue-400 dark:border-blue-400 dark:hover:text-blue-300"
            href="/channels/{{ $channel->uuid }}/playlists">
            {{ __('Search Results') }}
        </a>
    @endif
</nav>
