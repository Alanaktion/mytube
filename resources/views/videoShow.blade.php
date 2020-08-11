@extends('layouts.app')

@section('content')
<div class="container">
    <header class="mb-3 md:mb-4 lg:mb-6">
        <h2 class="text-2xl lg:text-3xl mb-2">{{ $video->title }}</h2>
        <p class="text-lg">
            <a class="text-blue-400 hover:text-blue-300" href="/channels/{{ $video->channel->uuid }}">
                {{ $video->channel->title }}
            </a>
        </p>
    </header>
    <code>{{ $video->file_path }}</code>
</div>
@endsection
