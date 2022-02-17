@extends('layouts.app')

@section('content')
<div class="-mt-4 lg:-mt-6 xl:-mt-8 pt-4 lg:pt-6 xl:pt-8 mb-3 md:mb-4 lg:mb-6 bg-white dark:bg-trueGray-850 border-b border-gray-200 dark:border-trueGray-700">
    <div class="container">
        @include('channels.header', ['tab' => 'videos'])
    </div>
</div>
<div class="container">
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 2xl:grid-cols-6 gap-4 lg:gap-6 pb-5 lg:mb-6">
        @forelse ($videos as $video)
            <x-video-link :video="$video" />
        @empty
            <div class="text-gray-400 dark:text-trueGray-600 py-6 col-span-full">
                {{ __('No available videos') }}
            </div>
        @endforelse
    </div>
    {{ $videos->links() }}
</div>
@endsection
