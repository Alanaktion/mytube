@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="text-sm font-semibold text-slate-500 dark:text-neutral-400 uppercase mb-3">{{ __('Recent videos') }}</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 2xl:grid-cols-6 gap-4 lg:gap-6 mb-5 lg:mb-8 xl:mb-12">
        @forelse ($videos as $video)
            <x-video-link :video="$video" :showChannel="true" />
        @empty
            <div class="text-slate-400 dark:text-neutral-600 py-6">
                {{ __('No available videos') }}
            </div>
        @endforelse
    </div>
    <x-button rounded primary large :href="route('videos.index')">
        {{ __('All videos') }} →
    </x-button>

    <div class="mb-10 xl:mb-16"></div>

    <h2 class="text-sm font-semibold text-slate-500 dark:text-neutral-400 uppercase mb-3">{{ __('Recent playlists') }}</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 2xl:grid-cols-6 gap-4 lg:gap-6 mb-5 lg:mb-8 xl:mb-12">
        @forelse ($playlists as $playlist)
            <x-playlist-link :playlist="$playlist" />
        @empty
            <div class="text-slate-400 dark:text-neutral-600 py-6">
                {{ __('No available playlists') }}
            </div>
        @endforelse
    </div>
    <x-button rounded primary large :href="route('playlists.index')">
        {{ __('All playlists') }} →
    </x-button>

    <div class="mb-10 xl:mb-16"></div>

    <h2 class="text-sm font-semibold text-slate-500 dark:text-neutral-400 uppercase mb-3">{{ __('Recent channels') }}</h2>
    <div class="grid grid-cols-1 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 2xl:grid-cols-6 gap-4 lg:gap-6 mb-5 lg:mb-8 xl:mb-12">
        @forelse ($channels as $channel)
            <x-channel-link :channel="$channel" />
        @empty
            <div class="text-slate-400 dark:text-neutral-600 py-6">
                {{ __('No available channels') }}
            </div>
        @endforelse
    </div>
    <x-button rounded primary large :href="route('channels.index')">
        {{ __('All channels') }} →
    </x-button>
</div>
@endsection
