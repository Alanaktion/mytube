<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>@yield('title')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="shortcut icon" href="{{ url('/favicon.svg') }}" type="image/svg+xml">
    <x-theme-script />
</head>
<body>
    <div class="relative flex items-top justify-center min-h-screen bg-slate-50 dark:bg-neutral-900 sm:items-center sm:pt-0 px-safe">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="flex items-center pt-8 sm:justify-start sm:pt-0 mb-4 md:mb-8">
                <div class="px-4 text-xl text-slate-700 dark:text-neutral-300 border-r border-neutral-400 tracking-wider">
                    @yield('code')
                </div>
                <div class="ml-4 text-xl text-slate-700 dark:text-neutral-300 uppercase tracking-wider">
                    @yield('message')
                </div>
            </div>
            <div class="text-center">
                <a href="/" class="border-2 border-primary-500 hover:border-primary-400 focus:border-primary-400 text-primary-500 hover:text-primary-400 focus:text-primary-400 dark:border-primary-400 dark:hover:border-white dark:focus:border-white dark:text-primary-400 dark:hover:text-white dark:focus:text-white font-bold py-2 px-5 rounded-full">
                    {{ __('Go Home') }} →
                </a>
            </div>
        </div>
    </div>
</body>
</html>
