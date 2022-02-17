<div class="flex sm:flex-col gap-x-6 gap-y-4 relative">
    <a href="/channels/{{ $channel->uuid }}" class="block relative sm:w-40 flex-shrink-0" aria-hidden="true" tabindex="-1">
        <img class="w-20 sm:w-40 aspect-square rounded-full lg:shadow-lg" src="{{ $channel->image_url ?? '/images/channels/' . $channel->uuid }}" alt="{{ $channel->name }}">
        <div class="absolute bottom-0 right-0 lg:bottom-1 lg:right-1 rounded-full p-2 leading-none bg-gray-600 dark:bg-trueGray-700 text-white shadow-sm">
            <x-source-icon class="w-4 h-4" :type="$channel->type" />
        </div>
    </a>
    <div>
        <a href="/channels/{{ $channel->uuid }}" class="block mb-1 text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
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
