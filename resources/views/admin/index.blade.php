@extends('layouts.app')

@section('content')
<div class="container">
    @if (session('message'))
    <div class="flex items-center bg-green-100 dark:bg-green-900 bg-opacity-50 border border-green-100 dark:border-green-600 p-3 rounded mb-4">
        <svg class="w-6 h-6 mr-2 text-green-500 dark:text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <span class="text-green-900 dark:text-green-100">
            {{ session('message') }}
        </span>
    </div>
    @endif

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
                    <div class="text-gray-600 dark:text-trueGray-400">Videos</div>
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
                    <div class="text-gray-600 dark:text-trueGray-400">Channels</div>
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
                    <div class="text-gray-600 dark:text-trueGray-400">Playlists</div>
                    <div class="text-2xl">{{ $playlistCount }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pb-5 lg:mb-6">
        <form action="/admin/playlists" method="post">
            @csrf
            <div class="text-sm uppercase font-semibold text-gray-500 dark:text-trueGray-400 mb-2">
                Playlist Import
            </div>

            <label for="playlistIds" class="block font-semibold text-gray-700 dark:text-trueGray-300 mb-1">Playlist URLs/IDs</label>
            <textarea name="playlistIds" id="playlistIds" class="focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 dark:bg-trueGray-800 dark:bg-opacity-50 dark:border-trueGray-700 dark:focus:border-blue-500 rounded-md mb-2" rows="5" required></textarea>

            <button type="submit" class="bg-blue-600 hover:bg-blue-500 dark:bg-blue-700 dark:hover:bg-blue-600 text-white font-semibold py-1 px-3 rounded">
                Import Playlists
            </button>
        </form>

        <form action="/admin/videos" method="post">
            @csrf
            <div class="text-sm uppercase font-semibold text-gray-500 dark:text-trueGray-400 mb-2">
                Video Import
            </div>

            <label for="videoIds" class="block font-semibold text-gray-700 dark:text-trueGray-300 mb-1">Video IDs/URLs</label>
            <textarea name="videoIds" id="videoIds" class="focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 dark:bg-trueGray-800 dark:bg-opacity-50 dark:border-trueGray-700 dark:focus:border-blue-500 rounded-md mb-2" rows="5" required></textarea>

            <button type="submit" class="bg-blue-600 hover:bg-blue-500 dark:bg-blue-700 dark:hover:bg-blue-600 text-white font-semibold py-1 px-3 rounded">
                Import Videos
            </button>
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
                    <input id="playlists" name="playlists" type="checkbox" class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                </div>
                <div class="ml-3 text-sm">
                    <label for="playlists" class="font-semibold text-gray-700 dark:text-trueGray-300">Playlists</label>
                    <p class="text-gray-500 dark:text-trueGray-400">Import all playlists on the channel with their corresponding videos.</p>
                </div>
            </div>
            <div class="flex items-start mb-3">
                <div class="flex items-center h-5">
                    <input id="videos" name="videos" type="checkbox" class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                </div>
                <div class="ml-3 text-sm leading-5">
                    <label for="videos" class="font-semibold text-gray-700 dark:text-trueGray-300">Videos</label>
                    <p class="text-gray-500 dark:text-trueGray-400">Import all videos on the channel.</p>
                </div>
            </div>

            <button type="submit" class="bg-blue-600 hover:bg-blue-500 dark:bg-blue-700 dark:hover:bg-blue-600 text-white font-semibold py-1 px-3 rounded">
                Import Channel
            </button>
        </form>

        <div>
            <div class="text-sm uppercase font-semibold text-gray-500 dark:text-trueGray-400 mb-2">
                Missing Files
            </div>
            <p class="mb-3">{{ $missingCount }} videos are missing local files</p>

            <a href="/admin/missing" class="bg-blue-600 hover:bg-blue-500 dark:bg-blue-700 dark:hover:bg-blue-600 text-white font-semibold py-1 px-3 rounded">
                View all →
            </a>
        </div>
    </div>
</div>
@endsection
