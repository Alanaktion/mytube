<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title')</title>
    <script src="{{ mix('js/app.js') }}" defer></script>
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <div class="relative flex items-top justify-center min-h-screen bg-ngray-900 sm:items-center sm:pt-0">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="flex items-center pt-8 sm:justify-start sm:pt-0 mb-4 md:mb-8">
                <div class="px-4 text-xl text-ngray-300 border-r border-ngray-400 tracking-wider">
                    @yield('code')
                </div>
                <div class="ml-4 text-xl text-ngray-300 uppercase tracking-wider">
                    @yield('message')
                </div>
            </div>
            <div class="text-center">
                <a href="/" class="border-2 border-blue-400 hover:border-white focus:border-white text-blue-400 hover:text-white focus:text-white font-bold py-2 px-5 rounded-full">
                    Go Home →
                </a>
            </div>
        </div>
    </div>
</body>
</html>
