@extends('layouts.app')

@section('content')
<div class="container">
    @if ($video->file_path)
        <div class="relative mb-4 lg:mb-6" style="padding-bottom: 56.25%;">
            <video class="absolute w-full h-full" controls poster="{{ $video->poster }}">
                <source src="{{ $video->link() }}">
                <code>{{ $video->file_path }}</code>
            </video>
        </div>
    @endif
    <header class="sm:flex items-center mb-3 md:mb-4 lg:mb-6">
        <div class="mb-3 sm:mb-0">
            <div class="mb-2">
                <h2 class="text-2xl lg:text-3xl -mb-1">
                    {{ $video->title }}
                </h2>
                <div class="text-sm text-gray-400" title="{{ $video->published_at }}">
                    {{ $video->published_at->format('F j, Y @ g:ia') }}
                </div>
            </div>
            <p class="text-lg">
                <a class="text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300" href="/channels/{{ $video->channel->uuid }}">
                    {{ $video->channel->title }}
                </a>
            </p>
        </div>
        <div class="sm:ml-auto flex gap-2">
            @if ($video->source_link)
                <a href="{{ $video->source_link }}" class="bg-red-700 hover:bg-red-600 text-white font-bold py-2 px-4 rounded-full flex items-center" aria-label="Watch on YouTube" data-tooltip>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="inline-block w-5 h-5" aria-hidden="true">
                        <path d="M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 0 0-1.94 2A29 29 0 0 0 1 11.75a29 29 0 0 0 .46 5.33A2.78 2.78 0 0 0 3.4 19c1.72.46 8.6.46 8.6.46s6.88 0 8.6-.46a2.78 2.78 0 0 0 1.94-2 29 29 0 0 0 .46-5.25 29 29 0 0 0-.46-5.33z" fill="none" stroke="currentColor"></path>
                        <polygon points="9.75 15.02 15.5 11.75 9.75 8.48 9.75 15.02" fill="currentColor"></polygon>
                    </svg>
                </a>
            @endif
            @if ($video->file_path)
                <a href="{{ $video->link() }}" class="bg-blue-600 hover:bg-blue-500 dark:bg-blue-700 dark:hover:bg-blue-600 text-white font-bold py-2 px-5 rounded-full" download>
                    Download
                </a>
            @endif
        </div>
    </header>
    <div class="sm:grid grid-cols-3 md:grid-cols-4">
        <div class="sm:col-span-2 md:col-span-3 mb-4">
            <div class="text-gray-600 dark:text-trueGray-400 whitespace-pre-wrap">{{ $video->description }}</div>
        </div>
        <div>
            @if ($video->playlists->count())
                <div class="text-xl mb-3">
                    Related Playlists
                </div>
                @foreach ($video->playlists as $playlist)
                    <x-playlist-link :playlist="$playlist" />
                @endforeach
            @endif
        </div>
    </div>
</div>
@endsection
