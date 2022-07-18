<a
    href="{{ $href }}"
    @class([
        'inline-flex gap-1 px-3 py-2 rounded-md text-sm font-medium focus:text-white truncate',
        'text-white bg-gradient-to-b from-primary-500 to-primary-600 border-primary-500 shadow-inner-white-top' => $active,
        'text-slate-300 dark:text-neutral-300 hover:text-white hover:bg-slate-700 dark:hover:bg-neutral-700 focus:bg-slate-700 dark:focus:bg-neutral-700' => !$active,
        'focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-slate-800 focus:ring-primary-500 dark:focus:ring-offset-neutral-800 dark:focus:ring-primary-600',
    ])
    {{ $attributes }}
>
    {{ $slot }}
</a>
