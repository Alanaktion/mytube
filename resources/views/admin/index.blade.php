@extends('layouts.app')

@section('content')
<div class="container">
    <div class="text-sm uppercase font-semibold text-gray-500 dark:text-trueGray-400 mb-2">
        Overview
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pb-5 lg:mb-6">
        <div class="dark:bg-trueGray-800 p-3 lg:p-4 shadow rounded">
            <div class="flex items-center">
                <div class="bg-blue-100 text-blue-600 dark:bg-blue-700 dark:text-white p-2 lg:p-4 rounded-full mr-3 md:mr-4 lg:mr-6">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div>
                    <div class="text-gray-600 dark:text-trueGray-400">{{ __('Videos') }}</div>
                    <div class="text-2xl">{{ $videoCount }}</div>
                </div>
            </div>
        </div>
        <div class="dark:bg-trueGray-800 p-3 lg:p-4 shadow rounded">
            <div class="flex items-center">
                <div class="bg-blue-100 text-blue-600 dark:bg-blue-700 dark:text-white p-2 lg:p-4 rounded-full mr-3 md:mr-4 lg:mr-6">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div>
                    <div class="text-gray-600 dark:text-trueGray-400">{{ __('Channels') }}</div>
                    <div class="text-2xl">{{ $channelCount }}</div>
                </div>
            </div>
        </div>
        <div class="dark:bg-trueGray-800 p-3 lg:p-4 shadow rounded">
            <div class="flex items-center">
                <div class="bg-blue-100 text-blue-600 dark:bg-blue-700 dark:text-white p-2 lg:p-4 rounded-full mr-3 md:mr-4 lg:mr-6">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                    </svg>
                </div>
                <div>
                    <div class="text-gray-600 dark:text-trueGray-400">{{ __('Playlists') }}</div>
                    <div class="text-2xl">{{ $playlistCount }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-6 pb-5 lg:mb-6">
        <form action="/admin/playlists" method="post">
            @csrf
            <div class="text-sm uppercase font-semibold text-gray-500 dark:text-trueGray-400 mb-2">
                Playlist Import
            </div>

            <label for="playlistIds" class="block font-semibold text-gray-700 dark:text-trueGray-300 mb-1">Playlist URLs/IDs</label>
            <textarea name="playlistIds" id="playlistIds" class="focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 dark:bg-trueGray-800 dark:bg-opacity-50 dark:border-trueGray-700 dark:focus:border-blue-500 rounded-md mb-2" rows="5" required></textarea>

            <x-button type="submit" small>
                Import Playlists
            </x-button>
        </form>

        <form action="/admin/videos" method="post">
            @csrf
            <div class="text-sm uppercase font-semibold text-gray-500 dark:text-trueGray-400 mb-2">
                Video Import
            </div>

            <label for="videoIds" class="block font-semibold text-gray-700 dark:text-trueGray-300 mb-1">Video IDs/URLs</label>
            <textarea name="videoIds" id="videoIds" class="focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 dark:bg-trueGray-800 dark:bg-opacity-50 dark:border-trueGray-700 dark:focus:border-blue-500 rounded-md mb-2" rows="5" required></textarea>

            <x-button type="submit" small>
                Import Videos
            </x-button>
        </form>

        <form action="/admin/channels" method="post">
            @csrf
            <div class="text-sm uppercase font-semibold text-gray-500 dark:text-trueGray-400 mb-2">
                Channel Import
            </div>

            <label for="channelId" class="block font-semibold text-gray-700 dark:text-trueGray-300 mb-1">Channel ID</label>
            <input type="text" name="channelId" id="channelId" class="focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 dark:bg-trueGray-800 dark:bg-opacity-50 dark:border-trueGray-700 dark:focus:border-blue-500 rounded-md mb-2" required>

            <div class="flex items-start mb-3">
                <div class="flex items-center h-5">
                    <input id="playlists" name="playlists" type="checkbox" class="focus:ring-blue-500 dark:ring-offset-trueGray-900 h-4 w-4 text-blue-600 border-gray-300 rounded">
                </div>
                <div class="ml-3 text-sm">
                    <label for="playlists" class="font-semibold text-gray-700 dark:text-trueGray-300">Playlists</label>
                    <p class="text-gray-500 dark:text-trueGray-400">Import all playlists on the channel with their corresponding videos.</p>
                </div>
            </div>
            <div class="flex items-start mb-3">
                <div class="flex items-center h-5">
                    <input id="videos" name="videos" type="checkbox" class="focus:ring-blue-500 dark:ring-offset-trueGray-900 h-4 w-4 text-blue-600 border-gray-300 rounded">
                </div>
                <div class="ml-3 text-sm leading-5">
                    <label for="videos" class="font-semibold text-gray-700 dark:text-trueGray-300">Videos</label>
                    <p class="text-gray-500 dark:text-trueGray-400">Import all videos on the channel.</p>
                </div>
            </div>

            <x-button type="submit" small>
                Import Channel
            </x-button>
        </form>

        <div>
            <div class="text-sm uppercase font-semibold text-gray-500 dark:text-trueGray-400 mb-2">
                Missing Files
            </div>
            <p class="mb-3">{{ $missingCount }} videos are missing local files</p>

            <x-button href="/admin/missing" small>
                View all →
            </x-button>
        </div>

        <div>
            <div class="text-sm uppercase font-semibold text-gray-500 dark:text-trueGray-400 mb-2">
                Queued Actions
            </div>
            <p class="mb-3">View any current background activity</p>

            <x-button href="/admin/queue" small>
                View all →
            </x-button>
        </div>
    </div>
</div>
@endsection
