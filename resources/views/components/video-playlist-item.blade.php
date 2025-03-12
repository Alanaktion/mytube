<a href="{{ route('videos.show', ['video' => $video, 'playlist' => $playlist->uuid]) }}" class="flex items-center px-3 py-2 group {{ $current ? 'bg-primary-500 current-playlist-item' : '' }}">
    <div class="relative w-16 h-9 mr-2 shrink-0 shadow-xs">
        <img class="absolute w-full h-full object-cover rounded-xs" src="{{ $video->thumbnail_url ?? "/placeholder-video.svg" }}" alt loading="lazy">
    </div>
    <div class="truncate">
        <div title="{{ $video->title }}" class="{{ $current ? 'text-white' : 'text-primary-600 group-hover:text-primary-700 dark:text-primary-400 dark:group-hover:text-primary-300' }} truncate">
            {{ $video->title }}
        </div>
        <div class="{{ $current ? 'text-white text-opacity-75' : 'text-slate-600 dark:text-neutral-500' }} text-xs truncate">
            {{ $video->channel->title }}
        </div>
    </div>
</a>
