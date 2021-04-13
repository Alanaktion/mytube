<a
    href="{{ $href }}"
    class="px-3 py-2 rounded-md text-sm font-medium focus:outline-none focus:text-white focus:bg-gray-700 dark:focus:bg-trueGray-700 {{ $active ? 'text-white bg-gray-700 dark:bg-trueGray-700' : 'text-gray-300 dark:text-trueGray-300 hover:text-white hover:bg-gray-700 dark:hover:bg-trueGray-700' }} focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800 focus:ring-blue-500 dark:focus:ring-offset-trueGray-800 dark:focus:ring-blue-600"
    {{ $attributes }}
>
    {{ __($text) }}
</a>
