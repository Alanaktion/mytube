<div>
    <a href="/videos/{{ $video->uuid }}" class="block line-clamp leading-snug mb-1 text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300">
        <div class="relative pb-9/16 mb-1">
            <img class="absolute w-full h-full object-cover" src="{{ $video->thumbnail_url ?? "/images/thumbs/{$video->uuid}" }}" alt>
        </div>
        {{ $video->title }}
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
