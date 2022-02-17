@extends('layouts.app')

@section('content')
<div class="container">
    <div class="flex flex-col sm:flex-row justify-between gap-2 sm:items-center mb-6">
        <h1 class="text-2xl font-medium text-gray-600 dark:text-trueGray-400">{{ __('Videos') }}</h1>
        <div class="flex gap-2">
            <x-sort-by :value="$sort" />
            <x-filter-source :value="$source" />
        </div>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 2xl:grid-cols-6 gap-4 lg:gap-6 pb-5 lg:mb-6">
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
