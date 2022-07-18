@extends('layouts.app')

@section('content')
<div class="container">
    <div class="flex flex-col sm:flex-row justify-between gap-2 sm:items-center mb-6">
        <h1 class="text-2xl font-medium text-slate-600 dark:text-neutral-400">{{ __('Videos missing files') }}</h1>
        <div id="app-sort-filter" class="flex gap-2">
            <x-sort-by :value="$sort" />
            <x-filter-source :value="$source" />
        </div>
    </div>

    <table class="min-w-full divide-y divide-slate-200 bg-slate-100 dark:divide-neutral-800 dark:bg-neutral-800 shadow dark:shadow-inner-white-top overflow-hidden sm:rounded-lg mb-4">
        <thead>
            <tr>
                <th class="px-6 py-3 text-left text-xs leading-4 font-medium text-slate-500 dark:text-neutral-300 uppercase tracking-wider">
                    {{ __('Title') }}
                </th>
                <th class="px-6 py-3 text-left text-xs leading-4 font-medium text-slate-500 dark:text-neutral-300 uppercase tracking-wider">
                    {{ __('Channel') }}
                </th>
                <th class="px-6 py-3 text-left text-xs leading-4 font-medium text-slate-500 dark:text-neutral-300 uppercase tracking-wider">
                    {{ __('Playlists') }}
                </th>
                <th class="px-6 py-3 text-left text-xs leading-4 font-medium text-slate-500 dark:text-neutral-300 uppercase tracking-wider">
                    {{ __('Published') }}
                </th>
                <th class="px-6 py-3"></th>
            </tr>
        </thead>
        <tbody class="bg-white dark:bg-neutral-850 divide-y divide-slate-200 dark:divide-neutral-800">
            @forelse($videos as $video)
                <tr>
                    <td class="px-6 py-2">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 w-20">
                                <img class="w-full aspect-video object-cover" src="{{ $video->thumbnail_url ?? "/images/thumbs/{$video->uuid}" }}" aria-hidden="true">
                            </div>
                            <div class="ml-4">
                                <a href="/videos/{{ $video->uuid }}" class="leading-5 font-medium text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300">
                                    {{ $video->title }}
                                </a>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <a href="/channels/{{ $video->channel->uuid }}" class="leading-5 text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300">
                            {{ $video->channel->title }}<br>
                            <span class="text-slate-600 dark:text-neutral-400">{{ $video->source()->getDisplayName() }}</span>
                        </a>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex flex-col gap-1">
                            @forelse($video->playlists as $playlist)
                                <a href="/playlists/{{ $playlist->uuid }}" class="block text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300">
                                    {{ $playlist->title }}
                                </a>
                            @empty
                                <div class="text-slate-500 dark:text-neutral-500">None</div>
                            @endforelse
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-slate-700 dark:text-neutral-300">
                        {{ $video->published_at->isoFormat('LL') }}
                    </td>
                    <td class="px-6 py-4 text-right leading-5 font-medium">
                        @if ($video->source_link)
                            <a href="{{ $video->source_link }}"
                                class="text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300"
                                aria-label="{{ __('View on :source', ['source' => $video->source()->getDisplayName()]) }}"
                                data-tooltip>
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                </svg>
                            </a>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-6 py-16 text-slate-500 dark:text-neutral-500 text-center">
                        {{ __('No videos are missing local files.') }}
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{ $videos->links() }}
</div>
@endsection
