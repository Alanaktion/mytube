@extends('layouts.app')

@section('content')
<div class="container">
    @auth
        <job-details type="playlist" id="{{ $playlist->id }}"></job-details>
    @endauth
    <header class="mb-3 md:mb-4 lg:mb-6">
        <div class="sm:flex items-center mb-2">
            <h1 class="flex items-center gap-2 text-2xl font-medium text-gray-600 dark:text-trueGray-400 mb-2 sm:mb-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z" />
                </svg>
                {{ $playlist->title }}
            </h1>
            <div class="flex items-center gap-2 ml-auto">
                @if ($playlist->source_link)
                    <a href="{{ $playlist->source_link }}"
                        class="btn btn-secondary p-2 rounded-full tooltip-right sm:tooltip-center"
                        aria-label="{{ __('View on :source', ['source' => $playlist->channel->source()->getDisplayName()]) }}"
                        data-tooltip>
                        <x-source-icon :type="$playlist->channel->type" class="h-5 w-5" />
                    </a>
                @endif
                @if (Auth::check() && Auth::user()->isAdmin())
                    <x-favorite-toggle :model="$playlist" />
                    <form action="/playlists/{{ $playlist->uuid }}/refresh" method="post">
                        @csrf
                        <x-button type="submit" title="{{ __('Refresh the playlist from the source.') }}" rounded>
                            {{ __('Refresh') }}
                        </x-button>
                    </form>
                @endif
            </div>
        </div>
        <p class="text-lg">
            {{ trans_choice('1 video|:count videos', $items->total()) }}
            <span class="mx-2">&middot;</span>
            <a class="text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300" href="/channels/{{ $playlist->channel->uuid }}">
                {{ $playlist->channel->title }}
            </a>
        </p>
    </header>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 2xl:grid-cols-6 gap-4 lg:gap-6 pb-5 lg:mb-6">
        @forelse ($items as $item)
            <x-video-link :video="$item->video" :playlist="$playlist" :showChannel="true" />
        @empty
            <div class="text-gray-400 dark:text-trueGray-600 py-6">
                {{ __('No available videos') }}
            </div>
        @endforelse
    </div>
    {{ $items->links() }}
</div>
@endsection
