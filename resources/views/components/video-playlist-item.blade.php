<a href="/videos/{{ $video->uuid }}?playlist={{ $playlist->uuid }}" class="flex items-center px-3 py-2 group {{ $current ? 'bg-blue-500 current-playlist-item' : '' }}">
    <div class="relative w-16 h-9 mr-2 flex-shrink-0 shadow-sm">
        <img class="absolute w-full h-full object-cover" src="{{ $video->thumbnail_url ?? "/images/thumbs/{$video->uuid}" }}" alt loading="lazy">
    </div>
    <div class="truncate">
        <div title="{{ $video->title }}" class="{{ $current ? 'text-white' : 'text-blue-600 group-hover:text-blue-500 dark:text-blue-400 dark:group-hover:text-blue-300' }} truncate">
            {{ $video->title }}
        </div>
        <div class="{{ $current ? 'text-white text-opacity-75' : 'text-gray-600 dark:text-trueGray-500' }} text-xs truncate">
            {{ $video->channel->title }}
        </div>
    </div>
</a>
