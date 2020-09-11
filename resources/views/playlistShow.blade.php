@extends('layouts.app')

@section('content')
<div class="container">
    <header class="mb-3 md:mb-4 lg:mb-6">
        <div class="flex items-center gap-2">
            <h1 class="text-2xl lg:text-3xl mb-2">{{ $playlist->title }}</h1>
            @if ($playlist->source_link)
                <a href="{{ $playlist->source_link }}" class="bg-transparent text-red-600 hover:bg-red-600 hover:text-white font-bold py-2 px-3 ml-2 rounded-full flex" aria-label="View on YouTube" data-tooltip>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="inline-block w-5 h-5" aria-hidden="true">
                        <path d="M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 0 0-1.94 2A29 29 0 0 0 1 11.75a29 29 0 0 0 .46 5.33A2.78 2.78 0 0 0 3.4 19c1.72.46 8.6.46 8.6.46s6.88 0 8.6-.46a2.78 2.78 0 0 0 1.94-2 29 29 0 0 0 .46-5.25 29 29 0 0 0-.46-5.33z" fill="none" stroke="currentColor"></path>
                        <polygon points="9.75 15.02 15.5 11.75 9.75 8.48 9.75 15.02" fill="currentColor"></polygon>
                    </svg>
                </a>
            @endif
        </div>
        <p class="text-lg">
            {{ $items->count() }} videos
            <span class="mx-2">&middot;</span>
            <a class="text-blue-400 hover:text-blue-300" href="/channels/{{ $playlist->channel->uuid }}">
                {{ $playlist->channel->title }}
            </a>
        </p>
    </header>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4 pb-5 lg:mb-6">
        @forelse ($items as $item)
            <div>
                <a href="/videos/{{ $item->video->uuid }}" class="block mb-1 text-blue-400 hover:text-blue-300">
                    {{ $item->video->title }}
                </a>
                <div class="text-sm text-ngray-400">
                    {{ $item->video->published_at->format('F j, Y') }}
                </div>
                <div class="text-xs text-ngray-600 overflow-hidden">
                    {{ Str::limit($item->video->description, 60) }}
                </div>
            </div>
        @empty
            <div class="text-ngray-600 py-6">No available videos</div>
        @endforelse
    </div>
    {{ $items->links() }}
</div>
@endsection
