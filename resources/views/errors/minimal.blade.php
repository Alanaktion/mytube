<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>@yield('title')</title>
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <div class="relative flex items-top justify-center min-h-screen bg-gray-50 dark:bg-trueGray-900 sm:items-center sm:pt-0 px-safe">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="flex items-center pt-8 sm:justify-start sm:pt-0 mb-4 md:mb-8">
                <div class="px-4 text-xl text-gray-700 dark:text-trueGray-300 border-r border-trueGray-400 tracking-wider">
                    @yield('code')
                </div>
                <div class="ml-4 text-xl text-gray-700 dark:text-trueGray-300 uppercase tracking-wider">
                    @yield('message')
                </div>
            </div>
            <div class="text-center">
                <a href="/" class="border-2 border-blue-500 hover:border-blue-400 focus:border-blue-400 text-blue-500 hover:text-blue-400 focus:text-blue-400 dark:border-blue-400 dark:hover:border-white dark:focus:border-white dark:text-blue-400 dark:hover:text-white dark:focus:text-white font-bold py-2 px-5 rounded-full">
                    Go Home →
                </a>
            </div>
        </div>
    </div>
</body>
</html>
