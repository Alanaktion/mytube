<div x-data="{ open: false }" class="relative inline-block text-left">
    <button type="button"
        class="p-2 rounded-full text-sm font-medium focus:outline-none text-blue-600 focus:bg-gray-300 dark:focus:bg-trueGray-700 dark:text-blue-400 hover:bg-gray-200 dark:hover:bg-trueGray-800 tooltip-right"
        aria-label="{{ __('Change language') }}"
        :data-tooltip="!open"
        @click="open = true">
        <svg class="w-4 h-4" viewBox="0 0 52 52" fill="currentColor">
            <path d="M39,18.67H35.42l-4.2,11.12A29,29,0,0,1,20.6,24.91a28.76,28.76,0,0,0,7.11-14.49h5.21a2,2,0,0,0,0-4H19.67V2a2,2,0,1,0-4,0V6.42H2.41a2,2,0,0,0,0,4H7.63a28.73,28.73,0,0,0,7.1,14.49A29.51,29.51,0,0,1,3.27,30a2,2,0,0,0,.43,4,1.61,1.61,0,0,0,.44-.05,32.56,32.56,0,0,0,13.53-6.25,32,32,0,0,0,12.13,5.9L22.83,52H28l2.7-7.76H43.64L46.37,52h5.22Zm-15.3-8.25a23.76,23.76,0,0,1-6,11.86,23.71,23.71,0,0,1-6-11.86Zm8.68,29.15,4.83-13.83L42,39.57Z" />
        </svg>
    </button>

    <div class="origin-bottom-left absolute left-0 bottom-7 mb-2 w-56 rounded-md shadow-lg bg-white dark:bg-trueGray-800 border dark:border-trueGray-850 focus:outline-none z-10"
        role="menu"
        aria-orientation="vertical"
        aria-labelledby="source-menu"
        x-show="open"
        @click.away="open = false"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95">
        <div class="py-1" role="none">
            @foreach (config('app.locale_list') as $code => $name)
                <a href="?lang={{ $code }}"
                    class="flex items-center px-4 py-2 text-sm {{ app()->getLocale() == $code ? 'text-white bg-blue-400 dark:bg-blue-600 hover:bg-blue-500 dark:hover:bg-blue-500' : 'text-gray-700 dark:text-trueGray-300 hover:bg-gray-100 dark:hover:bg-trueGray-700' }}"
                    role="menuitem">
                    {{ $name }}
                </a>
            @endforeach
        </div>
    </div>
</div>
