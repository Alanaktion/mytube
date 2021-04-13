<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ !empty($title) ? $title . ' - ' : null }}{{ config('app.name') }}</title>
    <script src="{{ mix('js/app.js') }}" defer></script>
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    <link rel="shortcut icon" href="{{ url('/favicon.svg') }}" type="image/svg+xml">
    <link rel="search" type="application/opensearchdescription+xml" title="{{ config('app.name') }}" href="{{ url('/opensearch.xml') }}">
    <x-theme-script />
</head>
<body class="dark:bg-trueGray-900 dark:text-trueGray-100 antialiased flex flex-col">
    <a href="#content" class="sr-only focus:not-sr-only text-blue-700 dark:text-blue-400 font-bold px-3 py-1">
        {{ __('Skip to content') }}
    </a>
    <nav class="bg-gray-800 dark:bg-trueGray-800 px-safe">
        <div class="container" x-data="{ open: false }">
            <div class="md:flex items-center">
                <div class="flex py-2">
                    <a href="/" class="flex items-center mr-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="inline h-8 w-8 text-blue-500" viewBox="0 0 24 24" fill="currentColor">
                            <polygon points="5 3 19 12 5 21 5 3"/>
                        </svg>
                        <span class="text-gray-100 dark:text-trueGray-100 font-semibold text-xl tracking-tight">
                            {{ config('app.name') }}
                        </span>
                    </a>
                    <x-nav-toggle />
                </div>
                {{-- TODO: add conditional aria-hidden attributes, this is tricky because they only need to be "false" on mobile --}}
                <div class="hidden md:flex flex-col items-stretch my-3 md:my-0 md:flex-row md:items-center md:ml-6 gap-1 md:gap-2 lg:gap-3 flex-1" :class="{ 'hidden': !open, 'flex': open }">
                    <x-nav-link href="/videos" text="Videos" />
                    <x-nav-link href="/playlists" text="Playlists" />
                    <x-nav-link href="/channels" text="Channels" />
                    @auth
                        <x-nav-link href="/favorites" text="Favorites" />
                        <x-nav-link href="/admin" text="Admin" />
                    @endauth
                </div>
                <form class="hidden mb-3 md:mb-0 md:block md:ml-6 md:py-2" :class="{ 'hidden': !open }" action="/search">
                    <input type="search" class="dark:bg-trueGray-900 dark:bg-opacity-75 dark:focus:bg-opacity-100 focus:outline-none focus:ring-blue-500 focus:ring-2 rounded-full py-2 pl-5 pr-3 block w-full dark:placeholder-trueGray-400 dark:text-trueGray-100 border-transparent appearance-none leading-normal" name="q" value="{{ $q ?? null }}" placeholder="{{ __('Search') }}">
                </form>
            </div>
        </div>
    </nav>

    <main class="py-4 lg:py-6 xl:py-8 px-safe" id="content">
        @if (session('message'))
        <div class="container mb-4">
            <div class="flex items-center bg-green-100 dark:bg-green-900 bg-opacity-50 border border-green-100 dark:border-green-600 p-3 rounded">
                <svg class="w-6 h-6 mr-2 text-green-500 dark:text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-green-900 dark:text-green-100">
                    {{ session('message') }}
                </span>
            </div>
        </div>
        @endif

        @yield('content')
    </main>

    <footer class="mt-auto">
        <div class="container flex justify-end my-4">
            <x-lang-menu />
            <x-theme-menu class="ml-2" />
        </div>
    </footer>
</body>
</html>
