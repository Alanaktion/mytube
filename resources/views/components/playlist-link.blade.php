<div class="flex flex-col relative group">
    <a href="/playlists/{{ $playlist->uuid }}" class="block line-clamp-2 break-words leading-snug mb-1 text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
        {{ $playlist->title }}
        <div class="absolute inset-0 z-10"></div>
    </a>
    <div class="relative pb-9/16 mb-2 lg:mb-4 order-first group-hover:scale-105 transition motion-reduce:transition-none motion-reduce:transform-none">
        @if ($firstVideo)
            <img class="absolute w-full h-full object-cover rounded lg:shadow-lg" src="{{ $firstVideo->thumbnail_url ?? "/images/thumbs/{$firstVideo->uuid}" }}" alt aria-hidden="true">
        @else
            {{-- No videos in the playlist! --}}
            <div class="absolute inset-0 object-cover rounded bg-gray-700 dark:bg-trueGray-700 lg:shadow-lg"></div>
        @endif
        <div class="absolute w-full bottom-0 px-3 py-2 bg-black bg-opacity-60 text-white backdrop-blur-xl shadow-inner-white-top rounded-b">
            {{ trans_choice('1 video|:count videos', $playlist->items_count) }}
        </div>
    </div>
    <div class="text-sm text-gray-800 dark:text-trueGray-400">
        {{ $playlist->published_at->translatedFormat('F j, Y') }}
    </div>
    @if ($playlist->description)
        <div class="text-xs line-clamp-3 break-words mt-1 text-gray-500 dark:text-trueGray-500">
            {{ Str::limit($playlist->description, 120) }}
        </div>
    @endif
</div>
