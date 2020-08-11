@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="text-2xl">Videos</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4 mb-5">
        @forelse ($videos as $video)
            <div>
                <a href="/videos/{{ $video->uuid }}">{{ $video->title }}</a>
                <div>{{ $video->description }}</div>
            </div>
        @empty
            <div class="text-ngray-600 py-6">No available videos</div>
        @endforelse
    </div>
</div>
@endsection
