@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="text-2xl mb-3">{{ __('Recent videos') }}</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4 mb-5 lg:mb-8 xl:mb-12">
        @forelse ($videos as $video)
            <x-video-link :video="$video" :showChannel="true" />
        @empty
            <div class="text-gray-400 dark:text-trueGray-600 py-6">
                {{ __('No available videos') }}
            </div>
        @endforelse
    </div>
    <x-button rounded primary large href="/videos">
        {{ __('All videos') }} →
    </x-button>

    <div class="mb-10 xl:mb-16"></div>

    <h2 class="text-2xl mb-3">{{ __('Recent playlists') }}</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4 mb-5 lg:mb-8 xl:mb-12">
        @forelse ($playlists as $playlist)
            <x-playlist-link :playlist="$playlist" />
        @empty
            <div class="text-gray-400 dark:text-trueGray-600 py-6">
                {{ __('No available playlists') }}
            </div>
        @endforelse
    </div>
    <x-button rounded primary large href="/playlists">
        {{ __('All playlists') }} →
    </x-button>

    <div class="mb-10 xl:mb-16"></div>

    <h2 class="text-2xl mb-3">{{ __('Recent channels') }}</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6 gap-4 mb-5 lg:mb-8 xl:mb-12">
        @forelse ($channels as $channel)
            <x-channel-link :channel="$channel" />
        @empty
            <div class="text-gray-400 dark:text-trueGray-600 py-6">
                {{ __('No available channels') }}
            </div>
        @endforelse
    </div>
    <x-button rounded primary large href="/channels">
        {{ __('All channels') }} →
    </x-button>
</div>
@endsection
