@extends('layouts.app')

@section('content')
<div class="container">
    @if ($video->files->count())
        <video class="w-full aspect-video mb-4 lg:mb-6" controls poster="{{ $video->poster_url ?? '/placeholder-video.svg' }}">
            @foreach ($video->files as $file)
                <source src="{{ $file->url }}" type="{{ $file->mime_type }}">
            @endforeach
        </video>
    @elseif (config('app.embed'))
        {!! $video->embed_html !!}
    @endif
    <header class="sm:flex items-start mb-3 md:mb-4 lg:mb-6">
        <div class="mb-4 sm:mb-0">
            <div class="mb-4">
                <h1 class="text-2xl font-medium text-slate-600 dark:text-neutral-400">
                    {{ $video->title }}
                </h1>
                <div class="text-sm text-slate-400 dark:text-neutral-500" title="{{ $video->published_at }}">
                    {{ $video->published_at->isoFormat('LLLL') }}
                </div>
            </div>
            <p class="text-lg">
                <a class="inline-flex items-center gap-2 text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300" href="{{ route('channels.videos.index', $video->channel) }}">
                    <img class="h-6 w-6 rounded-full" src="{{ $video->channel->image_url ?? '/placeholder-channel.svg' }}" alt="{{ $video->channel->name }}">
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
                @if (Auth::user()->isAdmin())
                    <div id="app-channel-refresh">
                        <video-menu uuid="{{ $video->uuid }}"></video-menu>
                    </div>
                @endif
            @endauth
            @if ($video->files->count())
                <div id="app-download">
                    <download-menu :files='@json($files)'></download-menu>
                </div>
            @endif
        </div>
    </header>
    <div class="sm:flex justify-between gap-4">
        <div class="mb-4">
            <pre class="text-slate-600 dark:text-neutral-400 whitespace-pre-wrap break-words font-sans">@description($video->description)</pre>
        </div>
        <div class="shrink-0 sm:w-72 md:w-80">
            @if ($playlist)
                <div class="text-lg mb-3 truncate text-slate-600 dark:text-neutral-400">
                    <a href="{{ route('playlists.show', $playlist) }}">{{ $playlist->title }}</a>
                </div>
                <div class="shadow-sm overflow-hidden sm:rounded-md">
                    <ul class="bg-white dark:bg-neutral-800 h-96 overflow-y-scroll">
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
