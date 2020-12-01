<div>
    <a href="/channels/{{ $channel->uuid }}" class="block mb-1 text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300">
        {{ $channel->title }}
    </a>
    <div class="text-sm text-gray-800 dark:text-trueGray-400">
        {{ $channel->published_at->format('F j, Y') }}
    </div>
    <div class="text-xs text-gray-500 dark:text-trueGray-600 overflow-hidden">
        {{ Str::limit($channel->description, 60) }}
    </div>
</div>
