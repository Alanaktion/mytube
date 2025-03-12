<div class="flex sm:flex-col gap-x-6 gap-y-4 relative group">
    <a href="{{ route('channels.videos.index', $channel) }}" class="block relative sm:w-40 shrink-0" aria-hidden="true" tabindex="-1">
        <img class="w-20 sm:w-40 aspect-square rounded-full lg:shadow-lg group-hover:scale-105 transition motion-reduce:transition-none motion-reduce:transform-none" src="{{ $channel->image_url ?? '/placeholder-channel.svg' }}" alt="{{ $channel->name }}">
        <div class="absolute bottom-0 right-0 lg:bottom-1 lg:right-1 rounded-full p-2 leading-none bg-slate-600 dark:bg-neutral-700 text-white shadow-xs">
            <x-source-icon class="w-4 h-4" :type="$channel->type" />
        </div>
    </a>
    <div>
        <a href="{{ route('channels.videos.index', $channel) }}" class="block line-clamp-2 actually-break-words mb-1 text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300">
            {{ $channel->title }}
            <div class="absolute inset-0"></div>
        </a>
        <div class="text-sm mb-1">
            {{ $channel->published_at->isoFormat('LL') }}
        </div>
        <div class="text-sm text-slate-700 dark:text-neutral-400">
            {{ trans_choice('1 video|:count videos', $channel->videos_count) }}
        </div>
    </div>
</div>
