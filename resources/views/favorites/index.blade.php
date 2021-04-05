@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="text-2xl mb-3">{{ __('My Favorites') }}</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4 pb-5 lg:mb-6">
        @forelse ($videos as $video)
            <x-video-link :video="$video" :showChannel="true" />
        @empty
            <div class="text-gray-400 dark:text-trueGray-600 py-6 col-span-full">
                {{ __('No available videos') }}
            </div>
        @endforelse
    </div>
    {{ $videos->links() }}
</div>
@endsection
