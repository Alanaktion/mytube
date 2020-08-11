@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="text-2xl">Channels</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4 mb-5">
        @forelse ($channels as $channel)
            <div>
                <a href="/user/{{ $channel->uuid }}" class="text-blue-600 hover:text-blue-500">
                    {{ $channel->title }}
                </a>
                <div class="text-xs text-ngray-700 h-16 overflow-hidden">
                    {{ Str::limit($channel->description, 60) }}
                </div>
            </div>
        @empty
            <div class="text-ngray-600 py-6">No available channels</div>
        @endforelse
    </div>
</div>
@endsection
