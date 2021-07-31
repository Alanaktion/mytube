@extends('layouts.app')

@section('content')
<div class="container">
    <header class="mb-3 md:mb-4 lg:mb-6">
        <div class="sm:flex items-center mb-2">
            <h1 class="text-2xl lg:text-3xl mb-2 sm:mb-0">{{ $playlist->title }}</h1>
            <div class="flex items-center gap-2 ml-auto">
                @if ($playlist->source_link)
                    <a href="{{ $playlist->source_link }}"
                        class="p-2 rounded-full text-sm text-red-600 focus:bg-gray-200 dark:focus:bg-trueGray-800 dark:text-red-500 hover:bg-gray-300 dark:hover:bg-trueGray-700 tooltip-right sm:tooltip-center focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 dark:focus:ring-offset-trueGray-900 dark:focus:ring-red-600"
                        aria-label="{{ __('View on :source', ['source' => $playlist->channel->type == 'youtube' ? 'YouTube' : ucfirst($playlist->channel->type)]) }}"
                        data-tooltip>
                        <x-source-icon :type="$playlist->channel->type" class="h-6 w-6" />
                    </a>
                @endif
                @if (Auth::check() && Auth::user()->isAdmin())
                    <x-favorite-toggle :model="$playlist" />
                    <form action="/playlists/{{ $playlist->uuid }}/refresh" method="post">
                        @csrf
                        <x-button type="submit" small title="{{ __('Refresh the playlist from the source.') }}">
                            {{ __('Refresh') }}
                        </x-button>
                    </form>
                @endif
            </div>
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
            <div class="text-gray-400 dark:text-trueGray-600 py-6">
                {{ __('No available videos') }}
            </div>
        @endforelse
    </div>
    {{ $items->links() }}
</div>
@endsection
