<div class="shadow dark:shadow-inner-white-top overflow-hidden sm:rounded-md bg-white dark:bg-neutral-800">
    <div class="px-4 py-5 sm:p-6">
        {{ $slot }}
    </div>
    @if (isset($footer))
        <div class="px-4 py-3 bg-slate-100 dark:bg-neutral-850 shadow-inner text-right sm:px-6">
            {{ $footer }}
        </div>
    @endif
</div>
