@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="text-2xl">Recent videos</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4 mb-5">
        @forelse ($videos as $video)
            <div>
                <a href="/videos/{{ $video->uuid }}">{{ $video->title }}</a>
                <div>{{ $video->description }}</div>
            </div>
        @empty
            <div class="text-ngray-600 py-6">No recent videos</div>
        @endforelse
    </div>
    <a class="bg-blue-700 hover:bg-blue-600 text-white font-bold py-2 px-5 rounded-full" href="/videos">
        All videos →
    </a>

    <div class="mb-10 xl:mb-16"></div>

    <h2 class="text-2xl">Recent channels</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4 mb-5">
        @forelse ($channels as $channel)
            <div>
                <a href="/user/{{ $channel->uuid }}">{{ $channel->title }}</a>
                <div>{{ $channel->description }}</div>
            </div>
        @empty
            <div class="text-ngray-600 py-6">No recent channels</div>
        @endforelse
    </div>
    <a class="bg-blue-700 hover:bg-blue-600 text-white font-bold py-2 px-5 rounded-full" href="/videos">
        All channels →
    </a>
</div>
@endsection
