@extends('layouts.app')

@section('content')
<div class="container">
    @include('channels.header', ['tab' => 'search'])

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
