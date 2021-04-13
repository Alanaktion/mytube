<button
    type="button"
    class="md:hidden ml-auto px-3 py-2 rounded-md text-sm font-medium focus:outline-none focus:text-white focus:bg-gray-700 dark:focus:bg-trueGray-700 {{ false ? 'text-white bg-gray-700 dark:bg-trueGray-700' : 'text-gray-300 dark:text-trueGray-300 hover:text-white hover:bg-gray-700 dark:hover:bg-trueGray-700' }} focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800 focus:ring-blue-500 dark:focus:ring-offset-trueGray-800 dark:focus:ring-blue-600"
    @click="open = !open"
    {{ $attributes }}
>
    <span class="sr-only">{{ __('Toggle Navigation') }}</span>
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
    </svg>
</button>
