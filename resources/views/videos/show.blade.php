@extends('layouts.app')

@section('content')
<div class="container">
    @if ($video->file_path)
        <div class="relative mb-4 lg:mb-6" style="padding-bottom: 56.25%;">
            <video class="absolute w-full h-full" controls poster="{{ $video->poster_url ?? "/images/posters/{$video->uuid}" }}">
                <source src="{{ $video->file_link }}">
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
                <a href="{{ $video->source_link }}"
                    class="bg-red-700 hover:bg-red-600 text-white font-bold py-2 px-4 rounded-full flex items-center"
                    aria-label="Watch on {{ $video->source_type == 'youtube' ? 'YouTube' : ucfirst($video->source_type) }}"
                    data-tooltip>
                    <x-source-icon :type="$video->source_type" />
                </a>
            @endif
            @if ($video->file_path)
                <a href="{{ $video->file_link }}" class="bg-blue-600 hover:bg-blue-500 dark:bg-blue-700 dark:hover:bg-blue-600 text-white font-bold py-2 px-5 rounded-full" download>
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
