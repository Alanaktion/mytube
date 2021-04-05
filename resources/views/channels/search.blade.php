@extends('layouts.app')

@section('content')
<div class="container">
    @include('channels.header', ['tab' => 'search'])

    <h2 class="text-2xl mb-3 mt-6">{{ trans_choice('1 video|:count videos', $channel->videos->count()) }}</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4 pb-5 lg:mb-6">
        @forelse ($videos as $video)
            <x-video-link :video="$video" />
        @empty
            <div class="text-gray-400 dark:text-trueGray-600 py-6 col-span-full">
                {{ __('No available videos') }}
            </div>
        @endforelse
    </div>
    {{ $videos->links() }}

    @if ($playlists->count())
        <h2 class="text-2xl mb-3 mt-6">{{ trans_choice('1 playlist|:count playlists', $playlists->count()) }}</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4 mb-5 lg:mb-8 xl:mb-12">
            @foreach ($playlists as $playlist)
                <x-playlist-link :playlist="$playlist" />
            @endforeach
        </div>
    @endif
</div>
@endsection
