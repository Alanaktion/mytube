<div class="relative group">
    <div class="relative">
    <div class="mb-2 lg:mb-4 group-hover:scale-105 transition motion-reduce:transition-none motion-reduce:transform-none">
        <img class="w-full aspect-video object-cover rounded-sm lg:shadow-lg" src="{{ $video->thumbnail_url ?? "/placeholder-video.svg" }}" alt>
    </div>
    @if (!$video->files_count)
        <div class="absolute top-1 left-1 lg:top-2 lg:left-2 text-white bg-black/60 backdrop-blur-md rounded-full p-1 z-20" aria-label="No video file available" title="No video file available">
            {{-- solid:x --}}
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
        </div>
    @endif
    @if ($video->source_visibility != 'public')
        <div class="absolute top-1 right-1 lg:top-2 lg:right-2 text-white bg-black/60 backdrop-blur-md rounded-full p-1 z-20" aria-label="Visibility: {{ ucfirst($video->source_visibility) }}" title="{{ ucfirst($video->source_visibility) }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                @if ($video->source_visibility == 'unlisted')
                    {{-- solid:eye-off --}}
                    <path fill-rule="evenodd" d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z" clip-rule="evenodd"></path>
                    <path d="M12.454 16.697L9.75 13.992a4 4 0 01-3.742-3.741L2.335 6.578A9.98 9.98 0 00.458 10c1.274 4.057 5.065 7 9.542 7 .847 0 1.669-.105 2.454-.303z"></path>
                @else
                    {{-- solid:lock-closed --}}
                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                @endif
            </svg>
        </div>
    @endif
    @if ($video->files_max_height)
        <div class="absolute bottom-1 left-1 lg:bottom-2 lg:left-2 text-white bg-red-600/80 backdrop-blur-md font-semibold rounded-xs text-xs px-1 py-px">
            @if ($video->files_max_height < 720)
                SD
            @elseif ($video->files_max_height == 2160)
                4K
            @elseif ($video->files_max_height == 4320)
                8K
            @else
                {{ $video->files_max_height }}p
            @endif
            @if ($video->is_livestream)
                &middot;
                {{ __('Stream') }}
            @endif
        </div>
    @elseif ($video->is_livestream)
        <div class="absolute bottom-1 left-1 lg:bottom-2 lg:left-2 text-white bg-red-600/80 backdrop-blur-md font-semibold rounded-xs text-xs px-1 py-px">
            {{ __('Stream') }}
        </div>
    @endif
    @if ($video->duration)
        <div class="absolute bottom-1 right-1 lg:bottom-2 lg:right-2 text-white bg-black/60 backdrop-blur-md font-semibold rounded-xs text-xs px-1 py-px">
            <span class="sr-only">{{ __('Duration') }}</span>
            {{ format_time($video->duration) }}
        </div>
    @endif
    </div>

    <div class="flex items-start">
        @if ($showChannel)
            <a href="{{ route('channels.videos.index', $video->channel) }}" class="relative z-10 shrink-0 mr-2" aria-hidden="true" tabindex="-1">
                <img class="w-8 xl:w-10 aspect-square rounded-full" src="{{ $video->channel->image_url ?? '/placeholder-channel.svg' }}" alt="{{ $video->channel->name }}">
            </a>
        @endif
        <div>
            <a href="{{ $playlist ? route('videos.show', ['video' => $video, 'playlist' => $playlist]) : route('videos.show', $video) }}" class="block line-clamp-2 actually-break-words leading-snug mb-1 text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300" title="{{ $video->title }}">
                {{ $video->title }}
                <div class="absolute inset-0"></div>
            </a>
            <div class="text-sm text-slate-800 dark:text-neutral-400">
                {{ $video->published_at->isoFormat('LL') }}
            </div>
            @if ($showChannel)
                <a href="{{ route('channels.videos.index', $video->channel) }}" class="text-slate-600 hover:text-slate-700 dark:text-neutral-500 dark:hover:text-neutral-400 relative z-10">
                    {{ $video->channel->title }}
                </a>
            @else
                <div class="text-xs line-clamp-3 actually-break-words mt-1 text-slate-500 dark:text-neutral-500">
                    {{ Str::limit($video->description, 120) }}
                </div>
            @endif
        </div>
    </div>
</div>
