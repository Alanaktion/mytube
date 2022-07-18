@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="text-2xl font-medium text-slate-600 dark:text-neutral-400 mb-6">{{ __('My Favorites') }}</h1>

    <h2 class="text-sm font-semibold text-slate-500 dark:text-neutral-400 uppercase mb-2">{{ __('Videos') }}</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 2xl:grid-cols-6 gap-4 lg:gap-6 pb-5 lg:mb-6">
        @forelse ($videos as $video)
            <x-video-link :video="$video" :showChannel="true" />
        @empty
            <div class="text-slate-400 dark:text-neutral-600 py-6 col-span-full">
                {{ __('No available videos') }}
            </div>
        @endforelse
    </div>

    <h2 class="text-sm font-semibold text-slate-500 dark:text-neutral-400 uppercase mb-2">{{ __('Playlists') }}</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 2xl:grid-cols-6 gap-4 lg:gap-6 pb-5 lg:mb-6">
        @forelse ($playlists as $playlist)
            <x-playlist-link :playlist="$playlist" :showChannel="true" />
        @empty
            <div class="text-slate-400 dark:text-neutral-600 py-6 col-span-full">
                {{ __('No available playlists') }}
            </div>
        @endforelse
    </div>

    <h2 class="text-sm font-semibold text-slate-500 dark:text-neutral-400 uppercase mb-2">{{ __('Channels') }}</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6 gap-4 pb-5 lg:mb-6">
        @forelse ($channels as $channel)
            <x-channel-link :channel="$channel" :showChannel="true" />
        @empty
            <div class="text-slate-400 dark:text-neutral-600 py-6 col-span-full">
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
