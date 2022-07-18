@extends('layouts.app')

@section('content')
@include('channels.header', ['tab' => 'videos'])
<div class="container">
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 2xl:grid-cols-6 gap-4 lg:gap-6 pb-5 lg:mb-6">
        @forelse ($videos as $video)
            <x-video-link :video="$video" />
        @empty
            <div class="text-slate-400 dark:text-neutral-600 py-6 col-span-full">
                {{ __('No available videos') }}
            </div>
        @endforelse
    </div>
    {{ $videos->links() }}
</div>
@endsection
