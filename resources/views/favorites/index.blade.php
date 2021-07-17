@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="text-2xl mb-3">{{ __('My Favorites') }}</h2>

    <h3 class="text-xl mb-2">{{ __('Videos') }}</h3>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4 pb-5 lg:mb-6">
        @forelse ($videos as $video)
            <x-video-link :video="$video" :showChannel="true" />
        @empty
            <div class="text-gray-400 dark:text-trueGray-600 py-6 col-span-full">
                {{ __('No available videos') }}
            </div>
        @endforelse
    </div>

    <h3 class="text-xl mb-2">{{ __('Playlists') }}</h3>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4 pb-5 lg:mb-6">
        @forelse ($playlists as $playlist)
            <x-playlist-link :playlist="$playlist" :showChannel="true" />
        @empty
            <div class="text-gray-400 dark:text-trueGray-600 py-6 col-span-full">
                {{ __('No available playlists') }}
            </div>
        @endforelse
    </div>

    <h3 class="text-xl mb-2">{{ __('Channels') }}</h3>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6 gap-4 pb-5 lg:mb-6">
        @forelse ($channels as $channel)
            <x-channel-link :channel="$channel" :showChannel="true" />
        @empty
            <div class="text-gray-400 dark:text-trueGray-600 py-6 col-span-full">
                {{ __('No available channels') }}
            </div>
        @endforelse
    </div>

    @php
    // Hacky way of paginating multiple collections
    $maxPageCollection = $videos;
    if ($playlists->lastPage() > $maxPageCollection->lastPage()) {
        $maxPageCollection = $playlists;
    }
    if ($channels->lastPage() > $maxPageCollection->lastPage()) {
        $maxPageCollection = $channels;
    }
    @endphp
    {{ $maxPageCollection->links() }}
</div>
@endsection
