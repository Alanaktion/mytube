@extends('layouts.app')

@section('content')
<div class="container">
    <div class="flex flex-col sm:flex-row justify-between gap-2 sm:items-center mb-6">
        <h1 class="text-2xl font-medium text-slate-600 dark:text-neutral-400">{{ __('Playlists') }}</h1>
        <div id="app-sort-filter" class="flex gap-2">
            <x-sort-by :value="$sort" />
            <x-filter-source :value="$source" />
        </div>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 2xl:grid-cols-6 gap-4 lg:gap-6 pb-5 lg:mb-6">
        @forelse ($playlists as $playlist)
            <x-playlist-link :playlist="$playlist" />
        @empty
            <div class="text-slate-400 dark:text-neutral-600 py-6 col-span-full">
                {{ 'No available playlists' }}
            </div>
        @endforelse
    </div>
    {{ $playlists->links() }}
</div>
@endsection
