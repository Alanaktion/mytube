<div class="flex flex-col relative group">
    <a href="{{ route('playlists.show', $playlist) }}" class="block line-clamp-2 actually-break-words leading-snug mb-1 text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300">
        {{ $playlist->title }}
        <div class="absolute inset-0 z-10"></div>
    </a>
    <div class="relative mb-2 lg:mb-4 order-first group-hover:scale-105 transition motion-reduce:transition-none motion-reduce:transform-none">
        @if ($firstVideo)
            <img class="w-full aspect-video object-cover rounded-sm lg:shadow-lg" src="{{ $firstVideo->thumbnail_url ?? "/placeholder-video.svg" }}" alt aria-hidden="true">
        @else
            {{-- No videos in the playlist! --}}
            <div class="absolute inset-0 object-cover rounded-sm bg-slate-700 dark:bg-neutral-700 lg:shadow-lg"></div>
        @endif
        <div class="absolute w-full flex justify-between bottom-0 px-3 py-2 bg-black/60 text-white backdrop-blur-xl shadow-inner-white-top rounded-b">
            <span>{{ trans_choice('1 video|:count videos', $playlist->items_count) }}</span>
            @if ($playlist->duration)
                <span>{{ format_time($playlist->duration) }}</span>
            @endif
        </div>
    </div>
    <div class="text-sm text-slate-800 dark:text-neutral-400">
        {{ $playlist->published_at->isoFormat('LL') }}
    </div>
    @if ($playlist->description)
        <div class="text-xs line-clamp-3 actually-break-words mt-1 text-slate-500 dark:text-neutral-500">
            {{ Str::limit($playlist->description, 120) }}
        </div>
    @endif
</div>
