<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ !empty($title) ? $title . ' - ' : null }}{{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta name="theme-color" content="rgb(30, 41, 59)">
    <link rel="shortcut icon" href="{{ url('/favicon.svg') }}" type="image/svg+xml">
    <link rel="search" type="application/opensearchdescription+xml" title="{{ config('app.name') }}" href="{{ url('/opensearch.xml') }}">
    <x-theme-script />
    @yield('head')
</head>
<body class="bg-slate-50 dark:bg-neutral-900 dark:text-neutral-100 antialiased accent-primary-500 @yield('class')">
<div class="flex flex-col h-full" id="app">
    <a href="#content" class="sr-only focus:not-sr-only text-primary-700 dark:text-primary-400 font-bold px-3 py-1">
        {{ __('Skip to content') }}
    </a>
    <nav class="bg-slate-800 dark:bg-neutral-800 px-safe">
        <div class="container md:flex items-center">
            <div class="flex py-2">
                <a href="/" class="flex items-center mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="inline h-8 w-8 from-primary-400 to-primary-600" viewBox="0 0 24 24" aria-hidden="true">
                        <defs>
                            <linearGradient id="logoGradient" gradientTransform="rotate(90)">
                                <stop offset="0%" stop-color="var(--tw-gradient-from)" />
                                <stop offset="100%" stop-color="var(--tw-gradient-to)" />
                            </linearGradient>
                        </defs>
                        <polygon points="5 3 19 12 5 21 5 3" fill="url(#logoGradient)"/>
                    </svg>
                    <span class="text-slate-100 dark:text-neutral-100 font-semibold text-xl tracking-tight">
                        {{ config('app.name') }}
                    </span>
                </a>
            </div>

            <div id="app-nav" class="flex-1">
                <nav-menu label="{{ __('Toggle Navigation') }}" v-cloak>
                    <div class="flex flex-col items-stretch my-3 md:my-0 md:flex-row md:items-center md:ml-3 lg:ml-6 gap-1 md:gap-2 lg:gap-3 flex-1">
                        <x-nav-link href="/videos">{{ __('Videos') }}</x-nav-link>
                        <x-nav-link href="/playlists">{{ __('Playlists') }}</x-nav-link>
                        <x-nav-link href="/channels">{{ __('Channels') }}</x-nav-link>
                        @auth
                            <x-nav-link href="/favorites">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden sm:block lg:hidden" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" />
                                </svg>
                                <span class="sm:sr-only lg:not-sr-only">
                                    {{ __('Favorites') }}
                                </span>
                            </x-nav-link>
                        @endauth
                    </div>
                    @auth
                        <user-menu name="{{ Auth::user()->name }}" token="{{ csrf_token() }}" :admin="@json(Auth::user()->isAdmin())" v-cloak></user-menu>
                    @endauth
                    <form class="mb-3 md:mb-0 md:block md:ml-2 lg:ml-6 md:py-2" action="/search">
                        <input type="search" class="dark:bg-neutral-900 dark:bg-opacity-75 dark:focus:bg-opacity-100 focus:outline-none focus:ring-primary-500 focus:ring-2 rounded-full py-2 pl-5 pr-3 block w-full dark:placeholder-neutral-400 dark:text-neutral-100 border-slate-900 dark:border-neutral-700 appearance-none leading-normal" name="q" value="{{ $q ?? null }}" placeholder="{{ __('Search') }}">
                    </form>
                </nav-menu>
            </div>
        </div>
    </nav>

    <main class="py-4 lg:py-6 xl:py-8 px-safe" id="content">
        @if (session('message'))
        <div class="container mb-4">
            <x-alert :type="session('messageType', 'success')">
                {{ session('message') }}
            </x-alert>
        </div>
        @endif

        @yield('content')
    </main>

    <footer class="mt-auto">
        <div id="app-footer" class="container flex justify-end my-4">
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
