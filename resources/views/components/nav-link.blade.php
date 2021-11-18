<a
    href="{{ $href }}"
    @class([
        'px-3 py-2 rounded-md text-sm font-medium focus:text-white',
        'text-white bg-gradient-to-b from-blue-500 to-blue-600 border-blue-500 shadow-inner-white-top' => $active,
        'text-gray-300 dark:text-trueGray-300 hover:text-white hover:bg-gray-700 dark:hover:bg-trueGray-700 focus:bg-gray-700 dark:focus:bg-trueGray-700' => !$active,
        'focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800 focus:ring-blue-500 dark:focus:ring-offset-trueGray-800 dark:focus:ring-blue-600',
    ])
    {{ $attributes }}
>
    {{ __($text) }}
</a>
