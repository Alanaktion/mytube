@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="text-2xl mb-3">Recent videos</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4 mb-5 lg:mb-8 xl:mb-12">
        @forelse ($videos as $video)
            <x-video-link :video="$video" :showChannel="true" />
        @empty
            <div class="text-gray-400 dark:text-trueGray-600 py-6">No recent videos</div>
        @endforelse
    </div>
    <a class="bg-blue-600 hover:bg-blue-500 dark:bg-blue-700 dark:hover:bg-blue-600 text-white font-bold py-2 px-5 rounded-full" href="/videos">
        All videos →
    </a>

    <div class="mb-10 xl:mb-16"></div>

    <h2 class="text-2xl mb-3">Recent playlists</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4 mb-5 lg:mb-8 xl:mb-12">
        @forelse ($playlists as $playlist)
            <x-playlist-link :playlist="$playlist" />
        @empty
            <div class="text-gray-400 dark:text-trueGray-600 py-6">No recent playlists</div>
        @endforelse
    </div>
    <a class="bg-blue-600 hover:bg-blue-500 dark:bg-blue-700 dark:hover:bg-blue-600 text-white font-bold py-2 px-5 rounded-full" href="/playlists">
        All playlists →
    </a>

    <div class="mb-10 xl:mb-16"></div>

    <h2 class="text-2xl mb-3">Recent channels</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4 mb-5 lg:mb-8 xl:mb-12">
        @forelse ($channels as $channel)
            <x-channel-link :channel="$channel" />
        @empty
            <div class="text-gray-400 dark:text-trueGray-600 py-6">No recent channels</div>
        @endforelse
    </div>
    <a class="bg-blue-600 hover:bg-blue-500 dark:bg-blue-700 dark:hover:bg-blue-600 text-white font-bold py-2 px-5 rounded-full" href="/channels">
        All channels →
    </a>
</div>
@endsection
