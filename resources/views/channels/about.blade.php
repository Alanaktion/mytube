@extends('layouts.app')

@section('content')
<div class="container">
    @include('channels.header', ['tab' => 'about'])

    <div class="text-sm uppercase font-semibold text-gray-500 dark:text-trueGray-400 mb-2">
        Source
    </div>
    <div class="mb-4 lg:mb-6">
        @if ($channel->type == 'youtube')
            YouTube
        @else
            Other
        @endif
        @if ($channel->source_link)
            &mdash; <a class="text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300" href="{{ $channel->source_link }}">{{ $channel->source_link }}</a>
        @endif
    </div>

    <div class="text-sm uppercase font-semibold text-gray-500 dark:text-trueGray-400 mb-2">
        Description
    </div>
    <div class="whitespace-pre-wrap">{{ $channel->description }}</div>
</div>
@endsection
