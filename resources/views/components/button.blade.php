@php
/** @var $attributes \Illuminate\View\ComponentAttributeBag */
$tag = $attributes->has('href') ? 'a' : 'button';
$classList = [
    'inline-flex items-center justify-center font-medium border border-transparent',
    'bg-blue-600 hover:bg-blue-500 dark:bg-blue-700 dark:hover:bg-blue-600 text-white' => $primary,
    'border-gray-300 shadow-sm text-gray-700 bg-white hover:bg-gray-50 dark:border-trueGray-600 dark:hover:border-trueGray-500 dark:bg-trueGray-700 dark:hover:bg-trueGray-600 dark:text-trueGray-100' => !$primary,
    'focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-trueGray-900 dark:focus:ring-blue-600',
    'rounded-full' => $rounded,
    'rounded-md' => !$rounded,
    'text-sm py-1' => $small,
    'py-2' => !$small,
    'px-3' => !$rounded && $small,
    'px-4' => ($rounded && $small) || (!$rounded && !$small),
    'px-5' => $rounded && !$small,
];
@endphp
<{{ $tag }}
    {{ $tag == 'button' ? 'type=' . ($attributes->get('type') ?? 'button') : null }}
    {{ $attributes->class($classList) }}>
    {{ $slot }}
</{{ $tag }}>
