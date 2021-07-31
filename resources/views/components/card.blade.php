<div class="shadow overflow-hidden sm:rounded-md">
    <div class="px-4 py-5 bg-white dark:bg-trueGray-800 sm:p-6">
        {{ $slot }}
    </div>
    @if (isset($footer))
        <div class="px-4 py-3 bg-gray-50 dark:bg-trueGray-850 text-right sm:px-6">
            {{ $footer }}
        </div>
    @endif
</div>
