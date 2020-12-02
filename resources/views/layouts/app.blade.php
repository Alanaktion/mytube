<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }}</title>
    <script src="{{ mix('js/app.js') }}" defer></script>
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    <link rel="search" type="application/opensearchdescription+xml" title="{{ config('app.name') }}" href="{{ url('/opensearch.xml') }}">
    <script>
        // Set color scheme dynamically
        const media = window.matchMedia('(prefers-color-scheme: dark)')
        const docCL = document.documentElement.classList
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && media.matches)) {
            docCL.add('dark')
        } else {
            docCL.remove('dark')
        }
        media.addListener(m => {
            if ('theme' in localStorage) {
                return
            }
            if (m.matches) {
                docCL.add('dark')
            } else {
                docCL.remove('dark')
            }
        })

        function toggleDark() {
            docCL.toggle('dark');
            const dark = docCL.contains('dark');
            localStorage.theme = dark ? 'dark' : 'light';
        }
    </script>
</head>
<body class="dark:bg-trueGray-900 dark:text-trueGray-100 antialiased">
    <nav class="bg-gray-800 dark:bg-trueGray-800 px-safe">
        <div class="container">
            <div class="flex items-center h-16">
                <a href="/" class="flex items-center mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="inline h-8 w-8 text-blue-500" viewBox="0 0 24 24" fill="currentColor">
                        <polygon points="5 3 19 12 5 21 5 3"/>
                    </svg>
                    <span class="text-gray-100 dark:text-trueGray-100 font-semibold text-xl tracking-tight">
                        {{ config('app.name') }}
                    </span>
                </a>
                <div class="hidden md:flex items-center ml-6 gap-2 lg:gap-3 flex-1">
                    <x-nav-link href="/videos" text="Videos" />
                    <x-nav-link href="/playlists" text="Playlists" />
                    <x-nav-link href="/channels" text="Channels" />
                    <button type="button" class="ml-auto px-3 py-2 rounded-md text-sm font-medium focus:outline-none focus:text-white focus:bg-gray-700 dark:focus:bg-trueGray-700 text-gray-300 dark:text-trueGray-300 hover:text-white hover:bg-gray-700 dark:hover:bg-trueGray-700" onclick="toggleDark()" title="Toggle Dark Theme">
                        {{-- TODO: add ability to switch to automatic theme --}}
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                        </svg>
                    </button>
                </div>
                <form class="ml-auto md:ml-6" action="/search">
                    <input type="search" class="dark:bg-trueGray-900 dark:bg-opacity-75 dark:focus:bg-opacity-100 focus:outline-none focus:ring-blue-500 focus:ring-2 rounded-full py-2 pl-5 pr-3 block w-full dark:placeholder-trueGray-400 dark:text-trueGray-100 appearance-none leading-normal" name="q" value="{{ $q ?? null }}" placeholder="Search">
                </form>
            </div>
        </div>
    </nav>

    <main class="py-4 lg:py-6 xl:py-8 px-safe">
        @yield('content')
    </main>
</body>
</html>
