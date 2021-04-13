<div class="relative" x-data="themeData()" x-init="$watch('currentTheme', val => setTheme(val))">
    <button type="button"
        class="p-2 rounded-full text-sm font-medium text-blue-600 focus:bg-gray-200 dark:focus:bg-trueGray-800 dark:text-blue-400 hover:bg-gray-200 dark:hover:bg-trueGray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-trueGray-900 dark:focus:ring-blue-600 tooltip-left"
        aria-label="{{ __('Toggle Dark Theme') }}"
        :data-tooltip="!open"
        @click="open = true">
        <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor">
            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
        </svg>
    </button>
    <div class="origin-bottom-right absolute right-0 bottom-7 w-40 py-1 mb-2 z-10 rounded-md shadow-lg bg-white dark:bg-trueGray-800 border dark:border-trueGray-850"
        role="menu"
        aria-orientation="vertical"
        aria-labelledby="theme-menu"
        x-show="open"
        @click.away="open = false"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        x-cloak>
        <template x-for="item in themes" :key="item">
            <button type="button"
                :class="{
                    'flex items-center appearance-none w-full px-4 py-2 text-sm': true,
                    'text-gray-700 dark:text-trueGray-300 hover:bg-gray-100 dark:hover:bg-trueGray-700': currentTheme != item,
                    'text-white bg-blue-400 dark:bg-blue-600 hover:bg-blue-500 dark:hover:bg-blue-500': currentTheme == item,
                }"
                @click="currentTheme = item">
                {{-- TODO: modify this to support i18n --}}
                <span class="capitalize" x-text="item"></span>
                <template x-if="currentTheme == item">
                    <div class="ml-auto">
                        <span class="sr-only">(active)</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </template>
            </button>
        </template>
    </div>
</div>
<script>
function themeData() {
    return {
        open: false,
        themes: ['auto', 'light', 'dark'],
        currentTheme: localStorage.theme || 'auto',
    }
}
function setTheme(val) {
    const docCL = document.documentElement.classList
    let theme
    if (val === 'auto') {
        localStorage.removeItem('theme')
        theme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'
    } else {
        localStorage.theme = val
        theme = val
    }
    docCL.add(theme)
    docCL.remove(theme === 'light' ? 'dark' : 'light')
}
</script>
