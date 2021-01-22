<div>
    <a href="/videos/{{ $video->uuid }}" class="block line-clamp leading-snug mb-1 text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300">
        <div class="relative pb-9/16 mb-2">
            <img class="absolute w-full h-full object-cover" src="{{ $video->thumbnail_url ?? "/images/thumbs/{$video->uuid}" }}" alt>
        </div>
        {{ $video->title }}
        @if ($video->source_visibility != 'public')
            <div class="absolute w-full top-2 right-2 text-white text-shadow">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    @if ($video->source_visibility == 'unlisted')
                        <path fill-rule="evenodd" d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z" clip-rule="evenodd"></path>
                        <path d="M12.454 16.697L9.75 13.992a4 4 0 01-3.742-3.741L2.335 6.578A9.98 9.98 0 00.458 10c1.274 4.057 5.065 7 9.542 7 .847 0 1.669-.105 2.454-.303z"></path>
                    @else
                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                    @endif
                </svg>
            </div>
        @endif
    </a>
    <div class="text-sm text-gray-800 dark:text-trueGray-400">
        {{ $video->published_at->format('F j, Y') }}
    </div>
    @if ($showChannel)
        <a href="/channels/{{ $video->channel->uuid }}" class="text-gray-600 hover:text-gray-700 dark:text-trueGray-500 dark:hover:text-trueGray-400">
            {{ $video->channel->title }}
        </a>
    @else
        <div class="text-xs line-clamp line-clamp-3 text-gray-500 dark:text-trueGray-600">
            {{ Str::limit($video->description, 120) }}
        </div>
    @endif
</div>
