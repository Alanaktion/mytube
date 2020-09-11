<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'MyTube') }}</title>
    <script src="{{ mix('js/app.js') }}" defer></script>
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
</head>
<body class="bg-ngray-900 text-ngray-100">
    <nav class="bg-ngray-800">
        <div class="container">
            <div class="flex items-center h-16">
                <a href="/" class="flex items-center mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="inline h-8 w-8 text-blue-500" viewBox="0 0 24 24" fill="currentColor">
                        <polygon points="5 3 19 12 5 21 5 3"/>
                    </svg>
                    <span class="font-semibold text-xl tracking-tight">
                        {{ config('app.name', 'MyTube') }}
                    </span>
                </a>
                <div class="hidden md:flex items-baseline ml-6">
                    <a href="/videos" class="px-3 py-2 rounded-md text-sm font-medium focus:outline-none {{ Route::current()->uri == 'videos' ? 'text-white bg-ngray-700 focus:text-white focus:bg-ngray-700' : 'text-ngray-300 hover:text-white hover:bg-ngray-700 focus:text-white focus:bg-ngray-700' }}">
                        Videos
                    </a>
                    <a href="/playlists" class="ml-3 px-3 py-2 rounded-md text-sm font-medium focus:outline-none {{ Route::current()->uri == 'playlists' ? 'text-white bg-ngray-700 focus:text-white focus:bg-ngray-700' : 'text-ngray-300 hover:text-white hover:bg-ngray-700 focus:text-white focus:bg-ngray-700' }}">
                        Playlists
                    </a>
                    <a href="/channels" class="ml-3 px-3 py-2 rounded-md text-sm font-medium focus:outline-none {{ Route::current()->uri == 'channels' ? 'text-white bg-ngray-700 focus:text-white focus:bg-ngray-700' : 'text-ngray-300 hover:text-white hover:bg-ngray-700 focus:text-white focus:bg-ngray-700' }}">
                        Channels
                    </a>
                </div>
                <form class="ml-auto" action="/search">
                    <input type="search" class="bg-ngray-900 bg-opacity-75 focus:bg-opacity-100 focus:outline-none focus:shadow-outline rounded-lg py-2 px-4 block w-full placeholder-ngray-400 text-ngray-100 appearance-none leading-normal" name="q" value="{{ $q ?? null }}" placeholder="Search">
                </form>
            </div>
        </div>
    </nav>

    <main class="py-4 lg:py-6 xl:py-8">
        @yield('content')
    </main>
</body>
</html>
