<div class="flex items-center">
    <a href="/channels/{{ $channel->uuid }}" class="flex-shrink-0 h-20 w-20">
        <img class="h-20 w-20 rounded-full" src="{{ $channel->image_url ?? '/images/channels/' . $channel->uuid }}" alt="{{ $channel->name }}">
    </a>
    <div class="ml-4">
        <a href="/channels/{{ $channel->uuid }}" class="block mb-1 text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300">
            {{ $channel->title }}
        </a>
        <div class="text-sm mb-1">
            {{ $channel->published_at->translatedFormat('F j, Y') }}
        </div>
        <div class="text-sm text-gray-700 dark:text-trueGray-400 line-clamp-2">
            {{ trans_choice('1 video|:count videos', $channel->videos_count) }}
        </div>
    </div>
</div>
