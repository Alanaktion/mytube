@extends('layouts.app')

@section('content')
@include('channels.header', ['tab' => 'search'])
<div class="container">
    <h2 class="text-sm font-semibold text-slate-500 dark:text-neutral-400 uppercase mb-3 mt-6">{{ trans_choice('1 video|:count videos', $videos->count()) }}</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 2xl:grid-cols-6 gap-4 lg:gap-6 pb-5 lg:mb-6">
        @forelse ($videos as $video)
            <x-video-link :video="$video" />
        @empty
            <div class="text-slate-400 dark:text-neutral-600 py-6 col-span-full">
                {{ __('No available videos') }}
            </div>
        @endforelse
    </div>
    {{ $videos->links() }}

    @if ($playlists->count())
        <h2 class="text-sm font-semibold text-slate-500 dark:text-neutral-400 uppercase mb-3 mt-6">{{ trans_choice('1 playlist|:count playlists', $playlists->count()) }}</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 2xl:grid-cols-6 gap-4 lg:gap-6 mb-5 lg:mb-8 xl:mb-12">
            @foreach ($playlists as $playlist)
                <x-playlist-link :playlist="$playlist" />
            @endforeach
        </div>
    @endif
</div>
@endsection
