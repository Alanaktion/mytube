@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="text-2xl mb-3">Channels</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4 mb-5">
        @forelse ($channels as $channel)
            <div>
                <a href="/user/{{ $channel->uuid }}" class="block mb-1 text-blue-400 hover:text-blue-300">
                    {{ $channel->title }}
                </a>
                <div class="text-sm text-ngray-400">
                    {{ $channel->published_at->format('F j, Y ga') }}
                </div>
                <div class="text-xs text-ngray-600 overflow-hidden">
                    {{ Str::limit($channel->description, 60) }}
                </div>
            </div>
        @empty
            <div class="text-ngray-600 py-6">No available channels</div>
        @endforelse
    </div>
</div>
@endsection
