<div>
    <a href="/playlists/{{ $playlist->uuid }}" class="block line-clamp-2 leading-snug mb-1 text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300">
        <div class="relative pb-9/16 mb-2">
            @if ($firstVideo)
                <img class="absolute w-full h-full object-cover rounded-sm" src="{{ $firstVideo->thumbnail_url ?? "/images/thumbs/{$firstVideo->uuid}" }}" alt>
            @else
                {{-- No videos in the playlist! --}}
                <div class="absolute inset-0 object-cover rounded-sm bg-gray-700 dark:bg-trueGray-700"></div>
            @endif
            <div class="absolute w-full bottom-0 px-3 py-2 bg-black bg-opacity-60 text-white backdrop-filter backdrop-blur-xl shadow-inner-white-top rounded-b-sm">
                {{ trans_choice('1 video|:count videos', $playlist->items_count) }}
            </div>
        </div>
        {{ $playlist->title }}
    </a>
    <div class="text-sm text-gray-800 dark:text-trueGray-400">
        {{ $playlist->published_at->translatedFormat('F j, Y') }}
    </div>
    @if ($playlist->description)
        <div class="text-xs text-gray-500 dark:text-trueGray-500 overflow-hidden">
            {{ Str::limit($playlist->description, 60) }}
        </div>
    @endif
</div>
