<div>
    <a href="/playlists/{{ $playlist->uuid }}" class="block line-clamp leading-snug mb-1 text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300">
        {{ $playlist->title }}
    </a>
    <div class="text-sm text-gray-800 dark:text-trueGray-400">
        {{ trans_choice('1 video|:count videos', $playlist->items_count) }} &middot; {{ $playlist->published_at->format('F j, Y') }}
    </div>
    @if ($playlist->description)
        <div class="text-xs text-gray-500 dark:text-trueGray-600 overflow-hidden">
            {{ Str::limit($playlist->description, 60) }}
        </div>
    @endif
</div>
