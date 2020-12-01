@extends('layouts.app')

@section('content')
<div class="container">
    @include('channels.header', ['tab' => 'videos'])

    <h2 class="text-2xl mb-3 mt-6">{{ trans_choice('1 video|:count videos', $channel->videos->count()) }}</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4 pb-5 lg:mb-6">
        @forelse ($videos as $video)
            <x-video-link :video="$video" />
        @empty
            <div class="text-gray-400 dark:text-trueGray-600 py-6">No available videos</div>
        @endforelse
    </div>
    {{ $videos->links() }}
</div>
@endsection
