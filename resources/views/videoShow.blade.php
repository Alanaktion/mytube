@extends('layouts.app')

@section('content')
<div class="container">
    <div class="relative mb-4 lg:mb-6" style="padding-bottom: 56.25%;">
        <video class="absolute w-full h-full" controls>
            <source src="{{ $video->link() }}">
            <code>{{ $video->file_path }}</code>
        </video>
    </div>
    <header class="mb-3 md:mb-4 lg:mb-6">
        <div class="mb-2">
            <h2 class="text-2xl lg:text-3xl">
                {{ $video->title }}
            </h2>
            <div class="text-sm text-gray-400" title="{{ $video->published_at }}">
                {{ $video->published_at->format('F j, Y @ g:ia') }}
            </div>
        </div>
        <p class="text-lg">
            <a class="text-blue-400 hover:text-blue-300" href="/channels/{{ $video->channel->uuid }}">
                {{ $video->channel->title }}
            </a>
        </p>
    </header>
    <div class="text-gray-400">
        <pre class="font-sans">{{ $video->description }}</pre>
    </div>
</div>
@endsection
