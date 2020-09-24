@extends('layouts.app')

@section('content')
<div class="container">
    <header class="mb-3 md:mb-4 lg:mb-6 sm:flex">
        <div class="flex items-center gap-2">
            <h1 class="text-2xl lg:text-3xl mb-2">{{ $channel->title }}</h1>
            @if ($channel->source_link)
                <a href="{{ $channel->source_link }}" class="bg-transparent text-red-600 hover:bg-red-600 hover:text-white font-bold py-2 px-4 rounded-full flex" aria-label="View on YouTube" data-tooltip>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="inline-block w-5 h-5" aria-hidden="true">
                        <path d="M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 0 0-1.94 2A29 29 0 0 0 1 11.75a29 29 0 0 0 .46 5.33A2.78 2.78 0 0 0 3.4 19c1.72.46 8.6.46 8.6.46s6.88 0 8.6-.46a2.78 2.78 0 0 0 1.94-2 29 29 0 0 0 .46-5.25 29 29 0 0 0-.46-5.33z" fill="none" stroke="currentColor"></path>
                        <polygon points="9.75 15.02 15.5 11.75 9.75 8.48 9.75 15.02" fill="currentColor"></polygon>
                    </svg>
                </a>
            @endif
        </div>
        <form class="sm:ml-auto mt-3 sm:mt-0">
            <input type="search" class="bg-ngray-800 focus:outline-none focus:shadow-outline rounded-lg py-2 px-4 block w-full placeholder-ngray-400 text-ngray-100 appearance-none leading-normal" name="q" value="{{ $channelQ ?? null }}" placeholder="Search channel">
        </form>
    </header>
    <h2 class="text-2xl mb-3 mt-6">{{ trans_choice('1 video|:count videos', $channel->videos->count()) }}</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4 pb-5 lg:mb-6">
        @forelse ($videos as $video)
            <div>
                <a href="/videos/{{ $video->uuid }}" class="block mb-1 text-blue-400 hover:text-blue-300">
                    <div class="relative pb-9/16 mb-1">
                        <img class="absolute w-full h-full object-cover" src="/images/thumbs/{{ $video->uuid }}" alt>
                    </div>
                    {{ $video->title }}
                </a>
                <div class="text-sm text-ngray-400">
                    {{ $video->published_at->format('F j, Y') }}
                </div>
                <div class="text-xs text-ngray-600 overflow-hidden">
                    {{ Str::limit($video->description, 60) }}
                </div>
            </div>
        @empty
            <div class="text-ngray-600 py-6">No available videos</div>
        @endforelse
    </div>
    {{ $videos->links() }}

    @if ($playlists->count())
        <h2 class="text-2xl mb-3 mt-6">{{ trans_choice('1 playlist|:count playlists', $playlists->count()) }}</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4 mb-5 lg:mb-8 xl:mb-12">
            @foreach ($playlists as $playlist)
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
            @endforeach
        </div>
    @endif
</div>
@endsection
