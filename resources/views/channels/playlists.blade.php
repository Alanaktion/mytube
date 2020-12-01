@extends('layouts.app')

@section('content')
<div class="container">
    @include('channels.header', ['tab' => 'playlists'])

    <h2 class="text-2xl mb-3 mt-6">{{ trans_choice('1 playlist|:count playlists', $playlists->count()) }}</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4 mb-5 lg:mb-8 xl:mb-12">
        @forelse ($playlists as $playlist)
            <x-playlist-link :playlist="$playlist" />
        @empty
            <div class="text-gray-400 dark:text-trueGray-600 py-6">No available playlists</div>
        @endforelse
    </div>
</div>
@endsection
