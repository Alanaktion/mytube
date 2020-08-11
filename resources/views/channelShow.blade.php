@extends('layouts.app')

@section('content')
<div class="container">
    <header class="mb-3 md:mb-4 lg:mb-6">
        <h1 class="text-2xl lg:text-3xl mb-2">{{ $channel->title }}</h1>
        <p class="text-lg">{{ $channel->videos->count() }} videos</p>
    </header>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4 pb-5">
        @forelse ($videos as $video)
            <div>
                <a href="/videos/{{ $video->uuid }}" class="block mb-1 text-blue-400 hover:text-blue-300">
                    {{ $video->title }}
                </a>
                <div class="text-sm text-ngray-400">
                    {{ $video->published_at->format('F j, Y') }}
                </div>
                <div class="text-xs text-ngray-600 overflow-hidden">
                    {{ Str::limit($video->description, 60) }}
                </div>
            </div>
        @empty
            <div class="text-ngray-600 py-6">No available videos</div>
        @endforelse
    </div>
    {{ $videos->links() }}
</div>
@endsection
