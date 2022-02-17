@extends('layouts.app')

@section('content')
<div class="container">
    @if ($video->file_path)
        <div class="relative mb-4 lg:mb-6 pb-9/16">
            <video class="absolute w-full h-full" controls poster="{{ $video->poster_url ?? "/images/posters/{$video->uuid}" }}">
                <source src="{{ $video->file_link }}">
                <code>{{ $video->file_path }}</code>
            </video>
        </div>
    @elseif (config('app.embed'))
        {!! $video->embed_html !!}
    @endif
    <header class="sm:flex items-start mb-3 md:mb-4 lg:mb-6">
        <div class="mb-4 sm:mb-0">
            <div class="mb-4">
                <h1 class="text-2xl font-medium text-gray-600 dark:text-trueGray-400">
                    {{ $video->title }}
                </h1>
                <div class="text-sm text-gray-400" title="{{ $video->published_at }}">
                    {{ $video->published_at->translatedFormat('F j, Y @ g:ia') }}
                </div>
            </div>
            <p class="text-lg">
                <a class="text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300" href="/channels/{{ $video->channel->uuid }}">
                    {{ $video->channel->title }}
                </a>
            </p>
        </div>
        <div class="sm:ml-auto flex items-center gap-2">
            @if ($video->source_link)
                <a href="{{ $video->source_link }}"
                    class="btn btn-secondary p-2 rounded-full tooltip-right sm:tooltip-left"
                    aria-label="{{ __('Watch on :source', ['source' => $video->source()->getDisplayName()]) }}"
                    data-tooltip>
                    <x-source-icon :type="$video->source_type" class="h-5 w-5" />
                </a>
            @endif
            @auth
                <x-favorite-toggle :model="$video" />
            @endauth
            @if ($video->file_path)
                <x-button href="{{ $video->file_link }}" rounded download>
                    {{ __('Download') }}
                </x-button>
            @endif
        </div>
    </header>
    <div class="sm:flex justify-between gap-4">
        <div class="mb-4">
            <pre class="text-gray-600 dark:text-trueGray-400 whitespace-pre-wrap font-sans">@description($video->description)</pre>
        </div>
        <div class="flex-shrink-0 sm:w-72 md:w-80">
            @if ($playlist)
                <div class="text-lg mb-3 truncate text-gray-600 dark:text-trueGray-400">
                    <a href="{{ route('playlist', $playlist) }}">{{ $playlist->title }}</a>
                </div>
                <div class="shadow overflow-hidden sm:rounded-md">
                    <ul class="bg-white dark:bg-trueGray-800 h-96 overflow-y-scroll">
                        @foreach ($playlist->items as $item)
                            <li>
                                <x-video-playlist-item :video="$item->video" :playlist="$playlist" :current="$item->video_id == $video->id" />
                            </li>
                        @endforeach
                    </ul>
                </div>
            @elseif ($video->playlists->count())
                <div class="text-xl mb-3">
                    {{ __('Related playlists') }}
                </div>
                @foreach ($video->playlists as $playlist)
                    <x-playlist-link :playlist="$playlist" />
                @endforeach
            @endif
        </div>
    </div>
</div>
@endsection

@section('head')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const item = document.querySelector('.current-playlist-item');
    if (!item) return;
    const list = item.closest('ul');
    list.scrollTop = item.offsetTop - list.offsetTop;
});
</script>
@endsection
