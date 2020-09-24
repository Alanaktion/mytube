@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="text-2xl mb-3">All Videos</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4 pb-5 lg:mb-6">
        @forelse ($videos as $video)
            <div>
                <div class="relative pb-9/16 mb-3">
                    <img class="absolute w-full h-full object-cover" src="/images/thumbs/{{ $video->uuid }}" alt>
                </div>
                <a href="/videos/{{ $video->uuid }}" class="block mb-1 text-blue-400 hover:text-blue-300">
                    {{ $video->title }}
                </a>
                <a href="/channels/{{ $video->channel->uuid }}" class="font-bold text-sm text-ngray-300 hover:text-ngray-100">
                    {{ $video->channel->title }}
                </a>
                <div class="text-sm text-ngray-400">
                    {{ $video->published_at->format('F j, Y') }}
                </div>
            </div>
        @empty
            <div class="text-ngray-600 py-6">No available videos</div>
        @endforelse
    </div>
    {{ $videos->links() }}
</div>
@endsection
