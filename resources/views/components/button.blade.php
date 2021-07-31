@php
/** @var \Illuminate\View\ComponentAttributeBag $attributes */
$tag = $attributes->has('href') ? 'a' : 'button';
$classList = [
    'inline-flex items-center justify-center font-medium border py-2',
    'border-transparent bg-blue-600 hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-600 text-white' => $primary,
    'border-gray-300 shadow-sm text-gray-700 bg-white hover:bg-gray-50 dark:border-trueGray-600 dark:hover:border-trueGray-500 dark:bg-trueGray-700 dark:hover:bg-trueGray-600 dark:text-trueGray-100' => !$primary,
    'focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-trueGray-900 dark:focus:ring-blue-600',
    'rounded-full' => $rounded,
    'rounded-md' => !$rounded,
    'text-sm' => !$large,
    'px-4' => !$rounded && !$large,
    'px-5' => $rounded && !$large,
    'px-6' => $large,
];
@endphp
<{{ $tag }}
    {{ $tag == 'button' ? 'type=' . ($attributes->get('type') ?? 'button') : null }}
    {{ $attributes->class($classList) }}>
    {{ $slot }}
</{{ $tag }}>
