<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ !empty($title) ? $title . ' - ' : null }}{{ config('app.name') }}</title>
    <script src="{{ mix('js/app.js') }}" defer></script>
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    <meta name="theme-color" content="rgb(30, 41, 59)">
    <meta name="theme-color" media="(prefers-color-scheme: dark)" content="rgb(38, 38, 38)">
    <link rel="shortcut icon" href="{{ url('/favicon.svg') }}" type="image/svg+xml">
    <link rel="search" type="application/opensearchdescription+xml" title="{{ config('app.name') }}" href="{{ url('/opensearch.xml') }}">
    <x-theme-script />
    @yield('head')
</head>
<body class="bg-gray-50 dark:bg-trueGray-900 dark:text-trueGray-100 antialiased accent-blue-500 @yield('class')">
<div class="flex flex-col h-full" id="app">
    <a href="#content" class="sr-only focus:not-sr-only text-blue-700 dark:text-blue-400 font-bold px-3 py-1">
        {{ __('Skip to content') }}
    </a>
    <nav class="bg-gray-800 dark:bg-trueGray-800 px-safe">
        <div class="container md:flex items-center">
            <div class="flex py-2">
                <a href="/" class="flex items-center mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="inline h-8 w-8 from-blue-400 to-blue-600" viewBox="0 0 24 24" aria-hidden="true">
                        <defs>
                            <linearGradient id="logoGradient" gradientTransform="rotate(90)">
                                <stop offset="0%" stop-color="var(--tw-gradient-from)" />
                                <stop offset="100%" stop-color="var(--tw-gradient-to)" />
                            </linearGradient>
                        </defs>
                        <polygon points="5 3 19 12 5 21 5 3" fill="url(#logoGradient)"/>
                    </svg>
                    <span class="text-gray-100 dark:text-trueGray-100 font-semibold text-xl tracking-tight">
                        {{ config('app.name') }}
                    </span>
                </a>
            </div>

            <nav-menu label="{{ __('Toggle Navigation') }}" v-cloak>
                <div class="flex flex-col items-stretch my-3 md:my-0 md:flex-row md:items-center md:ml-3 lg:ml-6 gap-1 md:gap-2 lg:gap-3 flex-1">
                    <x-nav-link href="/videos">{{ __('Videos') }}</x-nav-link>
                    <x-nav-link href="/playlists">{{ __('Playlists') }}</x-nav-link>
                    <x-nav-link href="/channels">{{ __('Channels') }}</x-nav-link>
                    @auth
                        <x-nav-link href="/favorites">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 xl:hidden" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" />
                            </svg>
                            <span class="sr-only xl:not-sr-only">
                                {{ __('Favorites') }}
                            </span>
                        </x-nav-link>
                    @endauth
                </div>
                @auth
                    <user-menu name="{{ Auth::user()->name }}" token="{{ csrf_token() }}" :admin="@json(Auth::user()->isAdmin())" v-cloak></user-menu>
                @endauth
                <form class="mb-3 md:mb-0 md:block md:ml-2 lg:ml-6 md:py-2" action="/search">
                    <input type="search" class="dark:bg-trueGray-900 dark:bg-opacity-75 dark:focus:bg-opacity-100 focus:outline-none focus:ring-blue-500 focus:ring-2 rounded-full py-2 pl-5 pr-3 block w-full dark:placeholder-trueGray-400 dark:text-trueGray-100 border-gray-900 dark:border-trueGray-700 appearance-none leading-normal" name="q" value="{{ $q ?? null }}" placeholder="{{ __('Search') }}">
                </form>
            </nav-menu>
        </div>
    </nav>

    <main class="py-4 lg:py-6 xl:py-8 px-safe" id="content">
        @if (session('message'))
        <div class="container mb-4">
            <div class="flex items-center bg-green-100 dark:bg-green-900 bg-opacity-50 border border-green-100 dark:border-green-600 p-3 rounded">
                <svg class="w-6 h-6 mr-2 text-green-500 dark:text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
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
            <lang-menu
                :locales='@json(config('app.locale_list'))'
            ></lang-menu>
            <theme-menu
                class="ml-2"
            ></theme-menu>
        </div>
    </footer>
</div>
</body>
</html>
