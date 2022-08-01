@php
$containerClass = match($type) {
    'error' => 'bg-red-100 dark:bg-red-900 border-red-100 dark:border-red-600',
    default => 'bg-emerald-100 dark:bg-emerald-900 border-emerald-100 dark:border-emerald-600',
};
$iconClass = match($type) {
    'error' => 'text-red-500 dark:text-red-300',
    default => 'text-emerald-500 dark:text-emerald-300',
};
$textClass = match($type) {
    'error' => 'text-red-900 dark:text-red-100',
    default => 'text-emerald-900 dark:text-emerald-100',
};
@endphp
<div class="flex items-center bg-opacity-50 border p-3 rounded {{ $containerClass }}">
    <svg class="w-6 h-6 mr-2 {{ $iconClass }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
    </svg>
    <span class="{{ $textClass }}">
        {{ $slot }}
    </span>
</div>
