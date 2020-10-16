@extends('layouts.app')

@section('content')
<div class="container">
    @if (session('status'))
        <div class="alert alert-success" role="alert">
            {{ session('status') }}
        </div>
    @endif

    <div class="text-lg font-medium mb-2">
        Overview
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pb-5 lg:mb-6">
        <div class="bg-ngray-800 p-3 lg:p-4 rounded">
            <div class="flex items-center">
                <div class="bg-blue-500 p-2 lg:p-4 rounded-full mr-3 md:mr-4 lg:mr-6">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div>
                    <div class="text-ngray-400">Videos</div>
                    <div class="text-2xl">{{ $videoCount }}</div>
                </div>
            </div>
        </div>
        <div class="bg-ngray-800 p-3 lg:p-4 rounded">
            <div class="flex items-center">
                <div class="bg-blue-500 p-2 lg:p-4 rounded-full mr-3 md:mr-4 lg:mr-6">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div>
                    <div class="text-ngray-400">Channels</div>
                    <div class="text-2xl">{{ $channelCount }}</div>
                </div>
            </div>
        </div>
        <div class="bg-ngray-800 p-3 lg:p-4 rounded">
            <div class="flex items-center">
                <div class="bg-blue-500 p-2 lg:p-4 rounded-full mr-3 md:mr-4 lg:mr-6">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                    </svg>
                </div>
                <div>
                    <div class="text-ngray-400">Playlists</div>
                    <div class="text-2xl">{{ $playlistCount }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- TODO: show admin actions (import channel/playlist/files, etc.) --}}
</div>
@endsection
