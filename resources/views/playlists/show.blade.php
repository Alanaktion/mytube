@extends('layouts.app')

@section('content')
<div class="container">
    <header class="mb-3 md:mb-4 lg:mb-6">
        <div class="flex items-center gap-2">
            <h1 class="text-2xl lg:text-3xl mb-2">{{ $playlist->title }}</h1>
            @if ($playlist->source_link)
                <a href="{{ $playlist->source_link }}"
                    class="bg-transparent text-red-600 hover:bg-red-600 hover:text-white font-bold py-2 px-3 rounded-full flex"
                    aria-label="View on {{ $playlist->channel->type == 'youtube' ? 'YouTube' : ucfirst($playlist->channel->type) }}"
                    data-tooltip>
                    <x-source-icon :type="$playlist->channel->type" />
                </a>
            @endif
        </div>
        <p class="text-lg">
            {{ trans_choice('1 video|:count videos', $items->total()) }}
            <span class="mx-2">&middot;</span>
            <a class="text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300" href="/channels/{{ $playlist->channel->uuid }}">
                {{ $playlist->channel->title }}
            </a>
        </p>
    </header>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4 pb-5 lg:mb-6">
        @forelse ($items as $item)
            <x-video-link :video="$item->video" />
        @empty
            <div class="text-gray-400 dark:text-trueGray-600 py-6">No available videos</div>
        @endforelse
    </div>
    {{ $items->links() }}
</div>
@endsection
