@extends('layouts.app')

@section('content')
<div class="container">
    <div class="sm:flex justify-between items-center mb-3">
        <h2 class="text-2xl mb-3 sm:mb-0">{{ __('Playlists') }}</h2>
        <x-filter-source path="/playlists" :value="$source" />
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4 pb-5 lg:mb-6">
        @forelse ($playlists as $playlist)
            <x-playlist-link :playlist="$playlist" />
        @empty
            <div class="text-gray-400 dark:text-trueGray-600 py-6 col-span-full">
                {{ 'No available playlists' }}
            </div>
        @endforelse
    </div>
    {{ $playlists->links() }}
</div>
@endsection
