@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="text-3xl mb-4 lg:mb-5"></h1>

    <h2 class="text-2xl mb-3">Video results</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4 mb-5 lg:mb-8 xl:mb-12">
        @forelse ($videos as $video)
            <div>
                <div class="relative pb-9/16 mb-3">
                    <img class="absolute w-full h-full object-cover" src="/images/thumbs/{{ $video->uuid }}" alt>
                </div>
                <a href="/videos/{{ $video->uuid }}" class="block mb-1 text-blue-400 hover:text-blue-300">
                    {{ $video->title }}
                </a>
                <a href="/channels/{{ $video->channel->uuid }}" class="font-bold text-sm text-ngray-300 hover:text-ngray-100">
                    {{ $video->channel->title }}
                </a>
                <div class="text-sm text-ngray-400">
                    {{ $video->published_at->format('F j, Y') }}
                </div>
            </div>
        @empty
            <div class="text-ngray-600 py-6">No videos found</div>
        @endforelse
    </div>

    <div class="mb-10 xl:mb-16"></div>

    <h2 class="text-2xl mb-3">Playlist results</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4 mb-5 lg:mb-8 xl:mb-12">
        @forelse ($playlists as $playlist)
            <div>
                <a href="/playlists/{{ $playlist->uuid }}" class="block mb-1 text-blue-400 hover:text-blue-300">
                    {{ $playlist->title }}
                </a>
                <div class="text-sm text-ngray-400">
                    {{ $playlist->published_at->format('F j, Y') }}
                </div>
                <div class="text-xs text-ngray-600 overflow-hidden">
                    {{ Str::limit($playlist->description, 60) }}
                </div>
            </div>
        @empty
            <div class="text-ngray-600 py-6">No playlists found</div>
        @endforelse
    </div>

    <div class="mb-10 xl:mb-16"></div>

    <h2 class="text-2xl mb-3">Channel results</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4 mb-5 lg:mb-8 xl:mb-12">
        @forelse ($channels as $channel)
            <div>
                <a href="/channels/{{ $channel->uuid }}" class="block mb-1 text-blue-400 hover:text-blue-300">
                    {{ $channel->title }}
                </a>
                <div class="text-sm text-ngray-400">
                    {{ $channel->published_at->format('F j, Y') }}
                </div>
                <div class="text-xs text-ngray-600 overflow-hidden">
                    {{ Str::limit($channel->description, 60) }}
                </div>
            </div>
        @empty
            <div class="text-ngray-600 py-6">No channels found</div>
        @endforelse
    </div>
</div>
@endsection
