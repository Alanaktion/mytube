@extends('layouts.app')

@section('content')
<div class="container">
    <div class="text-sm uppercase font-semibold text-gray-500 dark:text-trueGray-400 mb-2 lg:mb-3">
        Videos missing files
    </div>

    <table class="min-w-full divide-y divide-gray-200 dark:divide-trueGray-800 shadow overflow-hidden sm:rounded-lg mb-4">
        <thead>
            <tr>
                <th class="px-6 py-3 bg-gray-50 dark:bg-trueGray-800 text-left text-xs leading-4 font-medium text-gray-500 dark:text-trueGray-300 uppercase tracking-wider">
                    Video Title
                </th>
                <th class="px-6 py-3 bg-gray-50 dark:bg-trueGray-800 text-left text-xs leading-4 font-medium text-gray-500 dark:text-trueGray-300 uppercase tracking-wider">
                    Channel
                </th>
                <th class="px-6 py-3 bg-gray-50 dark:bg-trueGray-800 text-left text-xs leading-4 font-medium text-gray-500 dark:text-trueGray-300 uppercase tracking-wider">
                    Playlists
                </th>
                <th class="px-6 py-3 bg-gray-50 dark:bg-trueGray-800 text-left text-xs leading-4 font-medium text-gray-500 dark:text-trueGray-300 uppercase tracking-wider">
                    Published
                </th>
                <th class="px-6 py-3 bg-gray-50 dark:bg-trueGray-800"></th>
            </tr>
        </thead>
        <tbody class="bg-white dark:bg-trueGray-800 dark:bg-opacity-50 divide-y divide-gray-200 dark:divide-trueGray-800">
            @forelse($videos as $video)
                <tr>
                    <td class="px-6 py-2">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 w-20">
                                <div class="relative pb-9/16">
                                    <img class="absolute w-full h-full object-cover" src="{{ $video->thumbnail_url }}" alt>
                                </div>
                            </div>
                            <div class="ml-4">
                                <a href="/videos/{{ $video->uuid }}" class="leading-5 font-medium text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300">
                                    {{ $video->title }}
                                </a>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <a href="/channels/{{ $video->channel->uuid }}" class="leading-5 text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300">
                            {{ $video->channel->title }}
                        </a>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex flex-col gap-1">
                            @forelse($video->playlists as $playlist)
                                <a href="/playlists/{{ $playlist->uuid }}" class="block text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300">
                                    {{ $playlist->title }}
                                </a>
                            @empty
                                <div class="text-gray-500 dark:text-trueGray-500">None</div>
                            @endforelse
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-700 dark:text-trueGray-300">
                        {{ $video->published_at->format('F j, Y') }}
                    </td>
                    <td class="px-6 py-4 text-right leading-5 font-medium">
                        @if ($video->source_link)
                            <a href="{{ $video->source_link }}" class="text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                </svg>
                            </a>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-6 py-16 text-gray-500 dark:text-trueGray-500 text-center">
                        No videos are missing local files.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{ $videos->links() }}
</div>
@endsection
