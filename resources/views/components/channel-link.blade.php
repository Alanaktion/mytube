<div class="flex items-center relative rounded-sm hover:bg-gray-100 hover:ring-6 ring-gray-100 dark:hover:bg-trueGray-850 dark:ring-trueGray-850">
    <a href="/channels/{{ $channel->uuid }}" class="relative flex-shrink-0 h-20 w-20" aria-hidden="true" tabindex="-1">
        <img class="h-20 w-20 rounded-full" src="{{ $channel->image_url ?? '/images/channels/' . $channel->uuid }}" alt="{{ $channel->name }}">
        <div class="absolute bottom-0 right-0 rounded-full p-2 leading-none bg-gray-600 dark:bg-trueGray-700 text-white shadow-sm">
            <x-source-icon class="w-4 h-4" :type="$channel->type" />
        </div>
    </a>
    <div class="ml-4">
        <a href="/channels/{{ $channel->uuid }}" class="block mb-1 text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300">
            {{ $channel->title }}
            <div class="absolute inset-0"></div>
        </a>
        <div class="text-sm mb-1">
            {{ $channel->published_at->translatedFormat('F j, Y') }}
        </div>
        <div class="text-sm text-gray-700 dark:text-trueGray-400 line-clamp-2">
            {{ trans_choice('1 video|:count videos', $channel->videos_count) }}
        </div>
    </div>
</div>
