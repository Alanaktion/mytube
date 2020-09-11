@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="text-2xl mb-3">All Playlists</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4 pb-5 lg:mb-6">
        @forelse ($playlists as $playlist)
            <div>
                <a href="/playlists/{{ $playlist->uuid }}" class="block mb-1 text-blue-400 hover:text-blue-300">
                    {{ $playlist->title }}
                </a>
                <div class="text-sm text-ngray-400">
                    {{ $playlist->published_at->format('F j, Y') }}
                </div>
                <div class="text-xs text-ngray-600 overflow-hidden">
                    {{ Str::limit($playlist->description, 60) }}
                </div>
            </div>
        @empty
            <div class="text-ngray-600 py-6">No available playlists</div>
        @endforelse
    </div>
    {{ $playlists->links() }}
</div>
@endsection
