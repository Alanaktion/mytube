<div>
    <a href="/videos/{{ $video->uuid }}" class="block mb-1 text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300">
        <div class="relative pb-9/16 mb-1">
            <img class="absolute w-full h-full object-cover" src="/images/thumbs/{{ $video->uuid }}" alt>
        </div>
        {{ $video->title }}
    </a>
    <div class="text-sm text-gray-800 dark:text-trueGray-400">
        {{ $video->published_at->format('F j, Y') }}
    </div>
    <div class="text-xs text-gray-500 dark:text-trueGray-600 overflow-hidden">
        {{ Str::limit($video->description, 60) }}
    </div>
</div>
