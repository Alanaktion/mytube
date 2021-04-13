@extends('layouts.app')

@section('content')
<div class="container">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl">{{ __('Channels') }}</h2>
        <x-filter-source path="/channels" :value="$source" />
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4 pb-5 lg:mb-6">
        @forelse ($channels as $channel)
            <x-channel-link :channel="$channel" />
        @empty
            <div class="text-gray-400 dark:text-trueGray-600 py-6 col-span-full">
                {{ __('No available channels') }}
            </div>
        @endforelse
    </div>
    {{ $channels->links() }}
</div>
@endsection
