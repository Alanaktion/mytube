@extends('layouts.app')

@section('content')
<div class="container">
    <div class="flex flex-col sm:flex-row justify-between gap-2 sm:items-center mb-6">
        <h1 class="text-2xl font-medium text-slate-600 dark:text-neutral-400">{{ __('Channels') }}</h1>
        <div id="app-sort-filter" class="flex gap-2">
            <x-sort-by :value="$sort" />
            <x-filter-source :value="$source" />
        </div>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 2xl:grid-cols-6 gap-4 pb-5 lg:mb-6">
        @forelse ($channels as $channel)
            <x-channel-link :channel="$channel" />
        @empty
            <div class="text-slate-400 dark:text-neutral-600 py-6 col-span-full">
                {{ __('No available channels') }}
            </div>
        @endforelse
    </div>
    {{ $channels->links() }}
</div>
@endsection
