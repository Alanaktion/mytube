<a
    href="{{ $href }}"
    class="px-3 py-2 rounded-md text-sm font-medium focus:outline-none focus:text-white focus:bg-gray-700 dark:focus:bg-trueGray-700 {{ $active ? 'text-white bg-gray-700 dark:bg-trueGray-700' : 'text-gray-300 dark:text-trueGray-300 hover:text-white hover:bg-gray-700 dark:hover:bg-trueGray-700' }}"
    {{ $attributes }}
>
    {{ $text }}
</a>
