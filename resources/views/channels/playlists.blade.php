@extends('layouts.app')

@section('content')
@include('channels.header', ['tab' => 'playlists'])
<div class="container">
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 2xl:grid-cols-6 gap-4 lg:gap-6 mb-5 lg:mb-8 xl:mb-12">
        @forelse ($playlists as $playlist)
            <x-playlist-link :playlist="$playlist" />
        @empty
            <div class="text-gray-400 dark:text-trueGray-600 py-6 col-span-full">
                {{ __('No available playlists') }}
            </div>
        @endforelse
    </div>
</div>
@endsection
