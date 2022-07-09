@extends('layouts.app')

@section('content')
<div class="container">
    <div class="text-sm uppercase font-semibold text-gray-500 dark:text-trueGray-400 mb-2">
        Overview
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 lg:gap-6 pb-5 lg:mb-6">
        <div class="bg-white dark:bg-trueGray-800 p-3 lg:p-4 shadow dark:shadow-inner-white-top rounded">
            <div class="flex items-center">
                <div class="bg-blue-100 text-blue-600 dark:bg-blue-700 dark:text-white p-2 lg:p-4 rounded-full mr-3 md:mr-4 lg:mr-6">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div>
                    <div class="text-gray-600 dark:text-trueGray-400">{{ __('Videos') }}</div>
                    <div class="text-2xl">{{ $videoCount }}</div>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-trueGray-800 p-3 lg:p-4 shadow dark:shadow-inner-white-top rounded">
            <div class="flex items-center">
                <div class="bg-blue-100 text-blue-600 dark:bg-blue-700 dark:text-white p-2 lg:p-4 rounded-full mr-3 md:mr-4 lg:mr-6">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div>
                    <div class="text-gray-600 dark:text-trueGray-400">{{ __('Channels') }}</div>
                    <div class="text-2xl">{{ $channelCount }}</div>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-trueGray-800 p-3 lg:p-4 shadow dark:shadow-inner-white-top rounded">
            <div class="flex items-center">
                <div class="bg-blue-100 text-blue-600 dark:bg-blue-700 dark:text-white p-2 lg:p-4 rounded-full mr-3 md:mr-4 lg:mr-6">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
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

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 lg:gap-x-6 gap-y-6 pb-5 lg:mb-6">
        <div id="app-admin-import">
            <import-form csrf-token="{{ csrf_token() }}"></import-form>
        </div>

        <div>
            <x-card>
                <div class="text-sm uppercase font-semibold text-gray-500 dark:text-trueGray-400 mb-2">
                    Missing Files
                </div>
                @if ($missingCount)
                    <p>{{ $missingCount }} videos are missing local files</p>
                @else
                    <p>{{ __('No videos are missing local files.') }}</p>
                @endif
                <x-slot name="footer">
                    <x-button href="/admin/missing" primary>
                        {{ __('View all') }}
                    </x-button>
                </x-slot>
            </x-card>
        </div>

        <div>
            <x-card>
                <div class="text-sm uppercase font-semibold text-gray-500 dark:text-trueGray-400 mb-2">
                    Queued Actions
                </div>
                <p>View any current background activity</p>
                <x-slot name="footer">
                    <x-button href="/admin/queue" primary>
                        {{ __('View all') }}
                    </x-button>
                </x-slot>
            </x-card>
        </div>
    </div>
</div>
@endsection
